<?php
/**
 * Plugin Name: Prometheus Metrics in WordPress
 * Plugin URI: https://github.com/codeatcode/prometheus-metrics-for-wp/
 * Description: Add a custom endpoint for Prometheus
 * Version: 3.0.1
 * Requires at least: 5.6
 * Requires PHP: 7.3
 * Text Domain: prometheus-metrics-for-wp
 */

/**
 * WordPress 5.6 is required for Health Check integration
 * PHP 7.3 is required for hrtime() usage
 */

// https://developer.wordpress.org/reference/hooks/plugin_action_links_plugin_file/
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;
use WP_Prometheus_Metrics\Default_Metrics_Loader;
use WP_Prometheus_Metrics\Site_Health_Extension;

define('PROMETHEUS_PLUGIN_FILE', plugin_basename(__FILE__));

include "vendor/autoload.php";

new Site_Health_Extension();
new Default_Metrics_Loader();

add_filter('rest_pre_serve_request', 'prometheus_serve_request', 10, 4);
add_action('rest_api_init', 'prometheus_register_route');
add_action('wp_ajax_prometheus_metrics_get_url', 'prometheus_get_url');

add_filter('prometheus-metrics-for-wp/is_access_allowed', 'prometheus_is_access_allowed', 10, 2);

/**
 * @param bool $for_rest Prints the URL and "dies" if true, else returns the url
 * @param bool $generate_if_requested Generates a new key if the POST parma generate_key is set
 *
 * @return string Url for the endpoint
 */
function prometheus_get_url(bool $for_rest = true, bool $generate_if_requested = true): string
{
    if (!current_user_can('administrator')) {
        echo prometheus_empty_func();
    }

    $prometheusKey = defined('PROMETHEUS_KEY') ? PROMETHEUS_KEY : '';

    if ($generate_if_requested && filter_input(INPUT_POST, 'generate_key', FILTER_VALIDATE_BOOL)) {
        $prometheusKey = wp_generate_uuid4();

        $prometheusKeys = get_option('prometheus-metrics-for-wp-keys', []);
        $prometheusKeys[date('Y-m-d H:i:s') . ' User: ' . wp_get_current_user()->user_login] = md5($prometheusKey);
        update_option('prometheus-metrics-for-wp-keys', $prometheusKeys, false);
    }

    $url = add_query_arg([
        'all' => 'yes',
        'prometheus' => $prometheusKey,
    ], get_rest_url(null, '/metrics'));

    if ($for_rest) {
        echo $url;
        wp_die();
    }

    return $url;
}

/**
 * @param $default bool
 * @param $request WP_REST_Request
 */
function prometheus_is_access_allowed(bool $default, WP_REST_Request $request): bool
{
    if ($default) {
        return true;
    }

    $prometheusKey = filter_input(INPUT_GET, 'prometheus', FILTER_SANITIZE_STRING);

    if (defined('PROMETHEUS_KEY') && $prometheusKey === PROMETHEUS_KEY) {
        return true;
    }

    if (in_array(md5($prometheusKey), get_option('prometheus-metrics-for-wp-keys', []))) {
        return true;
    }

    return false;
}

function prometheus_empty_func(): string
{
    return '{ "error": "You cannot access to that page" }';
}

/**
 * @param $served bool
 * @param $result WP_HTTP_Response
 * @param $request WP_REST_Request
 * @param $server WP_REST_Server
 *
 * @return bool True, if the request was processed
 */
function prometheus_serve_request(bool $served, WP_HTTP_Response $result, WP_REST_Request $request, WP_REST_Server $server): bool
{
    if ($request->get_route() !== '/metrics') {
        return $served;
    }

    if (!apply_filters('prometheus-metrics-for-wp/is_access_allowed', false, $request)) {
        echo prometheus_empty_func(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return true;
    }
    $measure_all = filter_input(INPUT_GET, 'all', FILTER_SANITIZE_STRING) === 'yes';

    header('Content-type: ' . RenderTextFormat::MIME_TYPE);

    $metrics = get_transient('prometheus-metrics-for-wp-cached-metrics');
    if ($metrics && (!defined('PROMETHEUS_METRICS_FOR_WP_DISABLE_CACHE') || !PROMETHEUS_METRICS_FOR_WP_DISABLE_CACHE)) {
        echo $metrics;
        return true;
    }

    /** @var CollectorRegistry $registry */
    $registry = apply_filters('prometheus-metrics-for-wp/get_registry', new CollectorRegistry(new InMemory()));

    $metrics = apply_filters('prometheus-metrics-for-wp/get_metrics', []);
    foreach ($metrics as $metric) {
        $metric->add_metric($registry, $measure_all);
    }
    $renderer = new RenderTextFormat();
    $metrics = $renderer->render($registry->getMetricFamilySamples());

    set_transient('prometheus-metrics-for-wp-cached-metrics', $metrics, apply_filters('prometheus-metrics-for-wp/cache_timeout', 3600));

    echo $metrics;
    return true;
}

function prometheus_register_route()
{
    register_rest_route(
        'metrics',
        '/',
        array(
            'methods' => 'GET',
            'callback' => 'prometheus_empty_func',
            'permission_callback' => '__return_true',
        )
    );
}
