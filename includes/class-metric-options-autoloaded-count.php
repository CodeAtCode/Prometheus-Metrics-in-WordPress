<?php

namespace WP_Prometheus_Metrics;


class Options_Autoloaded_Count_Metric extends Metric {

	public function __construct() {
		parent::__construct( 'wp_options_autoload', 'gauge', 'autoload' );
	}

	function get_metric_value() {
		global $wpdb;

		return $wpdb->get_var( "SELECT count(*) FROM $wpdb->options WHERE `autoload` = 'yes'" ); // phpcs:ignore WordPress.DB
	}

	function get_help_text(): string {
		return _x( 'Options size in KB in autoload', 'Metric Help Text', 'prometheus-metrics-for-wp' );
	}
}

new Options_Autoloaded_Count_Metric();
