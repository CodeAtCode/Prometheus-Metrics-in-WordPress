<?php

/**
 * Plugin Name: Prometheus Metrics in WordPress
 * Plugin URI: https://github.com/codeatcode/prometheus-metrics-for-wp/
 * Description: Add a custom json endpoint for Prometheus
 * Version: 2.0-b13
 * Requires PHP: 7.3
 */

include "vendor/autoload.php";
include "includes/class-site-health-extension.php";

add_filter( 'rest_pre_serve_request', 'prometheus_serve_request', 10, 4 );
add_action( 'rest_api_init', 'prometheus_register_route' );
add_action( 'wp_ajax_prometheus_metrics_get_url', 'prometheus_get_url' );

add_filter( 'prometheus-metrics-for-wp/is_access_allowed', 'prometheus_is_access_allowed', 10, 2 );
/**
 * @deprecated
 */
function prometheus_get_metrics() {

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

function prometheus_get_url( $for_rest = true ) {
	if ( ! current_user_can( 'administrator' ) ) {
		echo prometheus_empty_func();
	}


	$prometheusKey = defined( 'PROMETHEUS_KEY' ) ? PROMETHEUS_KEY : '';

	if ( empty( $prometheusKey ) && filter_input( INPUT_GET, 'generate_key', FILTER_VALIDATE_BOOL ) ) {
		$prometheusKey = wp_generate_uuid4();

		$prometheusKeys                                                                          = get_option( 'prometheus-metrics-for-wp-keys', [] );
		$prometheusKeys[ date( 'Y-m-d H:i:s' ) . ' User: ' . wp_get_current_user()->user_login ] = md5( $prometheusKey );
		update_option( 'prometheus-metrics-for-wp-keys', $prometheusKeys, false );
	}

	$url = add_query_arg( [
		'all'                  => 'yes',
		'prometheus'           => $prometheusKey,
		'label_hosting_vendor' => 'Unknown',
		'label_hosting_rate'   => 'Unknown',
	],
		get_rest_url( null, '/metrics' ) );

	if ( $for_rest ) {
		echo $url;
		wp_die();
	}

	return $url;
}

/**
 * @param false $default bool
 * @param $request WP_REST_Request
 */
function prometheus_is_access_allowed( $default, $request ) {
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

function prometheus_empty_func() {
	return '{ "error": "You cannot access to that page" }';
}

/**
 * @param $served bool
 * @param $result WP_HTTP_Response
 * @param $request WP_REST_Request
 * @param $server WP_REST_Server
 *
 * @return bool|mixed
 */
function prometheus_serve_request( $served, $result, $request, $server ) {
	if ( $request->get_route() !== '/metrics' ) {
		return $served;
	}

	if ( ! apply_filters( 'prometheus-metrics-for-wp/is_access_allowed', false, $request ) ) {
		echo prometheus_empty_func(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		return true;
	}

	header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ) );
	$metrics = prometheus_get_metrics();
	echo $metrics; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$measure_all = filter_input( INPUT_GET, 'all', FILTER_SANITIZE_STRING ) === 'yes';

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

	do_action( 'prometheus_print_metrics', $measure_all );

	return true;
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
