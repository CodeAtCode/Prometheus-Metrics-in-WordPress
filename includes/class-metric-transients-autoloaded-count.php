<?php

namespace WP_Prometheus_Metrics;


class Transients_Autoloaded_Count_Metric extends Metric {

	public function __construct() {
		parent::__construct( 'wp_transient_autoload', 'DB Transient in autoload.', 'gauge', 'transient' );
	}

	function get_metric_value() {
		global $wpdb;

		return $wpdb->get_var( "SELECT count(*) FROM $wpdb->options WHERE `autoload` = 'yes' AND `option_name` LIKE '%transient%'" ); // phpcs:ignore WordPress.DB
	}
}

new Transients_Autoloaded_Count_Metric();
