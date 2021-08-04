<?php

namespace WP_Prometheus_Metrics;


class Options_Autoloaded_Size_Metric extends Metric {

	public function __construct() {
		parent::__construct( 'wp_options_autoload_size', 'Options in autoload', 'gauge', 'autoload' );
	}

	function get_metric_value() {
		global $wpdb;

		return $wpdb->get_var( "SELECT ROUND(SUM(LENGTH(option_value))/ 1024) FROM $wpdb->options WHERE `autoload` = 'yes'" ); // phpcs:ignore WordPress.DB
	}
}

new Options_Autoloaded_Size_Metric();
