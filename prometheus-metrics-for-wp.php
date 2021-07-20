<?php

/**
 * Plugin Name: Prometheus Metrics in WordPress
 * Plugin URI: https://github.com/codeatcode/prometheus-metrics-for-wp/
 * Description: Add a custom json endpoint for Prometheus
 * Version: 2.0-b5
 * Requires PHP: 7.3
 */

include "vendor/autoload.php";

add_filter( 'rest_pre_serve_request', 'prometheus_serve_request', 10, 4 );
add_action( 'rest_api_init', 'prometheus_register_route' );

function prometheus_get_metrics() {
	global $wpdb, $table_prefix;

	$measure_all = filter_input( INPUT_GET, 'all', FILTER_SANITIZE_STRING ) === 'yes';
	$result      = '';

	if ( $measure_all || filter_input( INPUT_GET, 'posts', FILTER_SANITIZE_STRING ) === 'yes' ) {
		$posts       = wp_count_posts();
		$n_posts_pub = $posts->publish;
		$n_posts_dra = $posts->draft;
		$result      .= "# HELP wp_posts_total Total number of posts published.\n";
		$result      .= "# TYPE wp_posts_total counter\n";
		$result      .= 'wp_posts_total{host="' . get_site_url() . '", status="published"} ' . $n_posts_pub . "\n";
		$result      .= 'wp_posts_total{host="' . get_site_url() . '", status="draft"} ' . $n_posts_dra . "\n";
	}

	if ( $measure_all || filter_input( INPUT_GET, 'pages', FILTER_SANITIZE_STRING ) === 'yes' ) {
		$n_pages = wp_count_posts( 'page' );
		$result  .= "# HELP wp_pages_total Total number of pages published.\n";
		$result  .= "# TYPE wp_pages_total counter\n";
		$result  .= 'wp_pages_total{host="' . get_site_url() . '", status="published"} ' . $n_pages->publish . "\n";
		$result  .= 'wp_pages_total{host="' . get_site_url() . '", status="draft"} ' . $n_pages->draft . "\n";
	}

	/**
	 * Filter database metrics result
	 *
	 * @var string $result The database metrics result
	 * @var boolean $measure_all True, if the "all" parameter is send
	 */
	$result = apply_filters( 'prometheus_custom_metrics', $result, $measure_all );

	return $result;
}

function prometheus_empty_func() {
	return '{ "error": "You cannot access to that page" }';
}

function prometheus_serve_request( $served, $result, $request, $server ) {
	if ( ! defined( 'PROMETHEUS_KEY' ) && $request->get_route() === '/metrics' ) {
		echo prometheus_empty_func(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$served = true;
	}

	if ( isset( $_GET['prometheus'] ) && esc_html( $_GET['prometheus'] ) === PROMETHEUS_KEY ) {
		header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ) );
		$metrics = prometheus_get_metrics();
		echo $metrics; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$measure_all = filter_input( INPUT_GET, 'all', FILTER_SANITIZE_STRING ) === 'yes';

		include_once "includes/class-abstract-metric.php";

		// "Count" metrics
		include_once "includes/class-metric-database-size.php";
		include_once "includes/class-metric-users-total.php";
		include_once "includes/class-metric-users-sessions.php";
		include_once "includes/class-metric-options-autoloaded-count.php";
		include_once "includes/class-metric-options-autoloaded-size.php";
		include_once "includes/class-metric-posts-without-content.php";
		include_once "includes/class-metric-posts-without-title.php";
		include_once "includes/class-metric-pending-updates.php";
		include_once "includes/class-metric-transients-autoloaded-count.php";

		// Performance metrics
		include_once "includes/class-metric-performance-count-posts.php";
		include_once "includes/class-metric-performance-write-temp-file.php";

		do_action( 'prometheus_print_metrics', $measure_all );

		$served = true;
	}

	return $served;
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
