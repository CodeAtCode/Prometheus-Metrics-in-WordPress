<?php

namespace WP_Prometheus_Metrics;


class Options_Autoloaded_Size_Metric extends Metric {

	public function __construct() {
		parent::__construct( 'wp_options_autoload_size', 'gauge', 'autoload' );
	}

	function get_metric_value() {
		global $wpdb;

		return $wpdb->get_var( "SELECT ROUND(SUM(LENGTH(option_value))/ 1024) FROM $wpdb->options WHERE `autoload` = 'yes'" ); // phpcs:ignore WordPress.DB
	}

	function get_help_text(): string {
		return _x( 'Options in autoload', 'Metric Help Text', 'prometheus-metrics-for-wp' );
	}
}

new Options_Autoloaded_Size_Metric();
