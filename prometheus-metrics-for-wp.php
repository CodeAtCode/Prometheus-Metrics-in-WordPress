<?php

/**
 * Plugin Name: Prometheus Metrics in WordPress
 * Plugin URI: https://github.com/codeatcode/prometheus-metrics-for-wp/
 * Description: Add a custom json endpoint for Prometheus
 * Version: 1.0
 */

add_filter( 'rest_pre_serve_request', 'prometheus_serve_request', 10, 4 );
add_action( 'rest_api_init', 'prometheus_register_route' );

function prometheus_get_metrics() {
	global $wpdb, $table_prefix;

	$result = '';

	if ( filter_input( INPUT_GET, 'users', FILTER_SANITIZE_STRING ) === 'yes' ) {
		$users   = count_users();
		$result .= "# HELP wp_users_total Total number of users.\n";
		$result .= "# TYPE wp_users_total counter\n";
		$result .= 'wp_users_total{host="' . get_site_url() . '"} ' . $users[ 'total_users' ] . "\n";
	}

	if ( filter_input( INPUT_GET, 'posts', FILTER_SANITIZE_STRING ) === 'yes' ) {
		$posts       = wp_count_posts();
		$n_posts_pub = $posts->publish;
		$n_posts_dra = $posts->draft;
		$result .= "# HELP wp_posts_total Total number of posts published.\n";
		$result .= "# TYPE wp_posts_total counter\n";
		$result .= 'wp_posts_total{host="' . get_site_url() . '", status="published"} ' . $n_posts_pub . "\n";
		$result .= 'wp_posts_total{host="' . get_site_url() . '", status="draft"} ' . $n_posts_dra . "\n";
	}

	if ( filter_input( INPUT_GET, 'pages', FILTER_SANITIZE_STRING ) === 'yes' ) {
		$n_pages = wp_count_posts( 'page' );
		$result .= "# HELP wp_pages_total Total number of pages published.\n";
		$result .= "# TYPE wp_pages_total counter\n";
		$result .= 'wp_pages_total{host="' . get_site_url() . '", status="published"} ' . $n_pages->publish . "\n";
		$result .= 'wp_pages_total{host="' . get_site_url() . '", status="draft"} ' . $n_pages->draft . "\n";
	}

	if ( filter_input( INPUT_GET, 'autoload', FILTER_SANITIZE_STRING ) === 'yes' ) {
		$query   = $wpdb->get_results( 'SELECT * FROM `' . $table_prefix . "options` WHERE `autoload` = 'yes'", ARRAY_A ); // phpcs:ignore WordPress.DB
		$result .= "# HELP wp_options_autoload Options in autoload.\n";
		$result .= "# TYPE wp_options_autoload counter\n";
		$result .= 'wp_options_autoload{host="' . get_site_url() . '"} ' . count( $query ) . "\n";
		$query   = $wpdb->get_results( 'SELECT ROUND(SUM(LENGTH(option_value))/ 1024) as value FROM `' . $table_prefix . "options` WHERE `autoload` = 'yes'", ARRAY_A ); // phpcs:ignore WordPress.DB
		$result .= "# HELP wp_options_autoload_size Options size in KB in autoload.\n";
		$result .= "# TYPE wp_options_autoload_size counter\n";
		$result .= 'wp_options_autoload_size{host="' . get_site_url() . '"} ' . $query[ 0 ][ 'value' ] . "\n";
	}

	if ( filter_input( INPUT_GET, 'transient', FILTER_SANITIZE_STRING ) === 'yes' ) {
		$query   = $wpdb->get_results( 'SELECT * FROM `' . $table_prefix . "options` WHERE `autoload` = 'yes' AND `option_name` LIKE '%transient%'", ARRAY_A ); // phpcs:ignore WordPress.DB
		$result .= "# HELP wp_transient_autoload DB Transient in autoload.\n";
		$result .= "# TYPE wp_transient_autoload counter\n";
		$result .= 'wp_transient_autoload{host="' . get_site_url() . '"} ' . count( $query ) . "\n";
	}

	if ( filter_input( INPUT_GET, 'user_sessions', FILTER_SANITIZE_STRING ) === 'yes' ) {
		$query   = $wpdb->get_results( 'SELECT * FROM `' . $table_prefix . "options` WHERE `option_name` LIKE '_wp_session_%'", ARRAY_A ); // phpcs:ignore WordPress.DB
		$result .= "# HELP wp_user_sessions User sessions.\n";
		$result .= "# TYPE wp_user_sessions counter\n";
		$result .= 'wp_user_sessions{host="' . get_site_url() . '"} ' . count( $query ) . "\n";
	}

	if ( filter_input( INPUT_GET, 'posts_without_title', FILTER_SANITIZE_STRING ) === 'yes' ) {
		$query   = $wpdb->get_results( 'SELECT * FROM `' . $table_prefix . "posts` WHERE post_title='' AND post_status!='auto-draft' AND post_status!='draft' AND post_status!='trash' AND (post_type='post' OR post_type='page')", ARRAY_A ); // phpcs:ignore WordPress.DB
		$result .= "# HELP wp_posts_without_title Post/Page without title.\n";
		$result .= "# TYPE wp_posts_without_title counter\n";
		$result .= 'wp_posts_without_title{host="' . get_site_url() . '"} ' . count( $query ) . "\n";
	}

	if ( filter_input( INPUT_GET, 'posts_without_content', FILTER_SANITIZE_STRING ) === 'yes' ) {
		$query   = $wpdb->get_results( 'SELECT * FROM `' . $table_prefix . "posts` WHERE post_content='' AND post_status!='draft' AND post_status!='trash' AND post_status!='auto-draft' AND (post_type='post' OR post_type='page')", ARRAY_A ); // phpcs:ignore WordPress.DB
		$result .= "# HELP wp_posts_without_content Post/Page without content.\n";
		$result .= "# TYPE wp_posts_without_content counter\n";
		$result .= 'wp_posts_without_content{host="' . get_site_url() . '"} ' . count( $query ) . "\n";
	}

	if ( filter_input( INPUT_GET, 'db_size', FILTER_SANITIZE_STRING ) === 'yes' ) {
		$query   = $wpdb->get_results( "SELECT SUM(ROUND(((data_length + index_length) / 1024 / 1024), 2)) as value FROM information_schema.TABLES WHERE table_schema = '" . DB_NAME . "'", ARRAY_A ); // phpcs:ignore WordPress.DB
		$result .= "# HELP wp_db_size Total DB size in MB.\n";
		$result .= "# TYPE wp_db_size counter\n";
		$result .= 'wp_db_size{host="' . get_site_url() . '"} ' . $query[ 0 ][ 'value' ] . "\n";
	}

	if ( filter_input( INPUT_GET, 'pending_updates', FILTER_SANITIZE_STRING ) === 'yes' ) {
		$status = get_site_transient('update_plugins');
		$result .= "# HELP wp_pending_updates Pending updates in the WordPress website.\n";
		$result .= "# TYPE wp_pending_updates counter\n";
		$result .= 'wp_pending_updates{host="' . get_site_url() . '"} ' . (count($status->response) + count($status->translations)) . "\n";
	}

	/**
	 * Filter database metrics result
	 *
	 * @var string $result The database metrics result
	 */
	$result = apply_filters( 'prometheus_custom_metrics', $result );

	return $result;
}

function prometheus_empty_func() {
	return '{ "error": "You cannot access to that page" }';
	}

function prometheus_serve_request( $served, $result, $request, $server ) {
	if ( !defined( 'PROMETHEUS_KEY' ) && $request->get_route() === '/metrics' ) {
		echo prometheus_empty_func(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$served = true;
	}

	if ( isset( $_GET[ 'prometheus' ] ) && esc_html( $_GET[ 'prometheus' ] ) === PROMETHEUS_KEY ) {
		header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ) );
		$metrics = prometheus_get_metrics();
		echo $metrics; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
