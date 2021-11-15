<?php

namespace WP_Prometheus_Metrics\metrics;

class Posts_Without_Title_Metric extends Abstract_Metric {


	public function __construct() {
		parent::__construct( 'wp_posts_without_title', 'gauge', 'posts_without_title' );
	}

	function get_metric_value() {
		global $wpdb;

		return $wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts} WHERE post_title='' AND post_status!='auto-draft' AND post_status!='draft' AND post_status!='trash' AND (post_type='post' OR post_type='page')" ); // phpcs:ignore WordPress.DB
	}

	function get_help_text(): string {
		return _x( 'Posts and Pages without title', 'Metric Help Text', 'prometheus-metrics-for-wp' );
	}
}

