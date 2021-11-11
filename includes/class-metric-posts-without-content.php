<?php

namespace WP_Prometheus_Metrics;


class Posts_Without_Content_Metric extends Metric {


	public function __construct() {
		parent::__construct( 'wp_posts_without_content', 'gauge', 'posts_without_content' );
	}

	function get_metric_value() {
		global $wpdb;

		return $wpdb->get_var( "SELECT count(*) FROM " . $wpdb->posts . " WHERE post_content='' AND post_status!='draft' AND post_status!='trash' AND post_status!='auto-draft' AND (post_type='post' OR post_type='page')" ); // phpcs:ignore WordPress.DB
	}

	function get_help_text() {
		return _x( 'Posts and Pages without content', 'Metric Help Text', 'prometheus-metrics-for-wp' );
	}
}

new Posts_Without_Content_Metric();
