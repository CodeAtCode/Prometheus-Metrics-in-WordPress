<?php

namespace WP_Prometheus_Metrics\metrics;


class Users_Count_Metric extends Abstract_Metric {

	public function __construct() {
		parent::__construct( 'wp_users_total', 'gauge', 'users' );
	}

	function get_metric_value() {
		return count_users()['total_users'];
	}

	function get_help_text(): string {
		return _x( 'Total number of users', 'Metric Help Text', 'prometheus-metrics-for-wp' );
	}
}
