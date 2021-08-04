<?php

namespace WP_Prometheus_Metrics;

class Users_Sessions_Metric extends Metric {

	public function __construct() {
		parent::__construct( 'wp_user_sessions', 'User sessions', 'gauge', 'user_sessions' );
	}

	function get_metric_value() {
		global $wpdb;

// TODO Check: is this still working? I don't think so
		return $wpdb->get_var( "SELECT count(*) FROM $wpdb->options WHERE `option_name` LIKE '_wp_session_%'" );
	}
}

new Users_Sessions_Metric();
