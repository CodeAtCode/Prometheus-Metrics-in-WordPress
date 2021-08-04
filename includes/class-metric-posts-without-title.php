<?php

namespace WP_Prometheus_Metrics;


class Posts_Without_Title_Metric extends Metric {


	public function __construct() {
		parent::__construct( 'wp_posts_without_title', 'Posts and Pages without title', 'gauge', 'posts_without_title' );
	}

	function get_metric_value() {
		global $wpdb;

		return $wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts} WHERE post_title='' AND post_status!='auto-draft' AND post_status!='draft' AND post_status!='trash' AND (post_type='post' OR post_type='page')" ); // phpcs:ignore WordPress.DB
	}
}

new Posts_Without_Title_Metric();
