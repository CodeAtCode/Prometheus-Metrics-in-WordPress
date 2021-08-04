<?php

namespace WP_Prometheus_Metrics;


class Users_Count_Metric extends Metric {

	public function __construct() {
		parent::__construct( 'wp_users_total', 'Total number of users', 'gauge', 'users' );
	}

	function get_metric_value() {
		return count_users()['total_users'];
	}
}

new Users_Count_Metric();
