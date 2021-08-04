<?php

namespace WP_Prometheus_Metrics;


class Options_Autoloaded_Count_Metric extends Metric {

	public function __construct() {
		parent::__construct( 'wp_options_autoload', 'Options size in KB in autoload', 'gauge', 'autoload' );
	}

	function get_metric_value() {
		global $wpdb;

		return $wpdb->get_var( "SELECT count(*) FROM $wpdb->options WHERE `autoload` = 'yes'" ); // phpcs:ignore WordPress.DB
	}
}

new Options_Autoloaded_Count_Metric();
