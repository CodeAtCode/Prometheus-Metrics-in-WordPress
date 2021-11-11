<?php

namespace WP_Prometheus_Metrics;


class Transients_Autoloaded_Count_Metric extends Metric {

	public function __construct() {
		parent::__construct( 'wp_transient_autoload', 'gauge', 'transient' );
	}

	function get_metric_value() {
		global $wpdb;

		return $wpdb->get_var( "SELECT count(*) FROM $wpdb->options WHERE `autoload` = 'yes' AND `option_name` LIKE '%transient%'" ); // phpcs:ignore WordPress.DB
	}

	function get_help_text() {
		return _x( 'DB Transient in autoload.', 'Metric Help Text', 'prometheus-metrics-for-wp' );
	}
}

new Transients_Autoloaded_Count_Metric();
