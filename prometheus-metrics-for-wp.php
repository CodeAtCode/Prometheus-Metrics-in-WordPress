<?php
/**
 * Plugin Name: Prometheus Metrics in WordPress
 * Plugin URI: https://github.com/codeatcode/prometheus-metrics-for-wp/
 * Description: Add a custom endpoint for Prometheus
 * Version: 2.0
 * Requires at least: 5.6
 * Requires PHP: 7.3
 * Text Domain: prometheus-metrics-for-wp
 */

/**
 * WordPress 5.6 is required for Health Check integration
 * PHP 7.3 is required for hrtime() usage
 */

use WP_Prometheus_Metrics\Metric;

// https://developer.wordpress.org/reference/hooks/plugin_action_links_plugin_file/
define( 'PROMETHEUS_PLUGIN_FILE', plugin_basename( __FILE__ ) );

include "vendor/autoload.php";
include "includes/class-site-health-extension.php";

add_filter( 'rest_pre_serve_request', 'prometheus_serve_request', 10, 4 );
add_action( 'rest_api_init', 'prometheus_register_route' );
add_action( 'wp_ajax_prometheus_metrics_get_url', 'prometheus_get_url' );

add_filter( 'prometheus-metrics-for-wp/is_access_allowed', 'prometheus_is_access_allowed', 10, 2 );

// Trick to load all metrics first. Better idea?
add_filter( 'prometheus_get_metrics', 'prometheus_load_metrics', 0 );
/**
 * @deprecated
 * @var boolean $measure_all True, if the "all" parameter is send
 */
function prometheus_get_metrics( bool $measure_all ): string {

	include 'includes/_legacy.prometheus_custom_metrics.php';
	/**
	 * Filter database metrics result
	 *
	 * @var string $result The database metrics result
	 * @var boolean $measure_all True, if the "all" parameter is send
	 */
	$result = apply_filters( 'prometheus_custom_metrics', '', $measure_all );
	if ( ! empty( $result ) ) {
		_deprecated_hook( 'prometheus_custom_metrics', '2.0', 'prometheus_print_metrics', 'Use the new action instead' );
	}

	return $result;
}

/**
 * @param bool $for_rest Prints the URL and "dies" if true, else returns the url
 * @param bool $generate_if_requested Generates a new key if the POST parma generate_key is set
 *
 * @return string Url for the endpoint
 */
function prometheus_get_url( bool $for_rest = true, bool $generate_if_requested = true ): string {
	if ( ! current_user_can( 'administrator' ) ) {
		echo prometheus_empty_func();
	}

	$prometheusKey = defined( 'PROMETHEUS_KEY' ) ? PROMETHEUS_KEY : '';

	if ( $generate_if_requested && filter_input( INPUT_POST, 'generate_key', FILTER_VALIDATE_BOOL ) ) {
		$prometheusKey = wp_generate_uuid4();

		$prometheusKeys                                                                          = get_option( 'prometheus-metrics-for-wp-keys', [] );
		$prometheusKeys[ date( 'Y-m-d H:i:s' ) . ' User: ' . wp_get_current_user()->user_login ] = md5( $prometheusKey );
		update_option( 'prometheus-metrics-for-wp-keys', $prometheusKeys, false );
	}

	$url = add_query_arg( [
		'all'        => 'yes',
		'prometheus' => $prometheusKey,
	], get_rest_url( null, '/metrics' ) );

	if ( $for_rest ) {
		echo $url;
		wp_die();
	}

	return $url;
}

/**
 * @param $default bool
 * @param $request WP_REST_Request
 */
function prometheus_is_access_allowed( bool $default, WP_REST_Request $request ): bool {
	if ( $default ) {
		return true;
	}

	$prometheusKey = filter_input( INPUT_GET, 'prometheus', FILTER_SANITIZE_STRING );

	if ( defined( 'PROMETHEUS_KEY' ) && $prometheusKey === PROMETHEUS_KEY ) {
		return true;
	}

	if ( in_array( md5( $prometheusKey ), get_option( 'prometheus-metrics-for-wp-keys', [] ) ) ) {
		return true;
	}

	return false;
}

function prometheus_empty_func(): string {
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
function prometheus_serve_request( bool $served, WP_HTTP_Response $result, WP_REST_Request $request, WP_REST_Server $server ): bool {
	if ( $request->get_route() !== '/metrics' ) {
		return $served;
	}

	if ( ! apply_filters( 'prometheus-metrics-for-wp/is_access_allowed', false, $request ) ) {
		echo prometheus_empty_func(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		return true;
	}
	$measure_all = filter_input( INPUT_GET, 'all', FILTER_SANITIZE_STRING ) === 'yes';

	header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ) );
	$legacy_metrics = prometheus_get_metrics( $measure_all );
	echo $legacy_metrics; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$metrics = apply_filters( 'prometheus_get_metrics', [] );
	/** @var $metric Metric */
	foreach ( $metrics as $metric ) {
		$metric->print_metric( $measure_all );
	}

	return true;
}

function prometheus_load_metrics( $metrics = [] ) {
	include_once "includes/class-abstract-metric.php";

	include_once "includes/class-metric-database-size.php";
	include_once "includes/class-metric-users-total.php";
	include_once "includes/class-metric-users-sessions.php";
	include_once "includes/class-metric-options-autoloaded-count.php";
	include_once "includes/class-metric-options-autoloaded-size.php";
	include_once "includes/class-metric-posts-without-content.php";
	include_once "includes/class-metric-posts-without-title.php";
	include_once "includes/class-metric-post-types-count.php";
	include_once "includes/class-metric-pending-updates.php";
	include_once "includes/class-metric-transients-autoloaded-count.php";

	include_once "includes/class-metric-performance-count-posts.php";
	include_once "includes/class-metric-performance-write-temp-file.php";

	return $metrics;
}

function prometheus_register_route() {
	register_rest_route(
		'metrics',
		'/',
		array(
			'methods'  => 'GET',
			'callback' => 'prometheus_empty_func',
		)
	);
}
