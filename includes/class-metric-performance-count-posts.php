<?php

namespace WP_Prometheus_Metrics;


class Performance_Count_Posts_Metric extends Metric {

	public function __construct() {
		parent::__construct( 'perf_count_posts', 'Measure the time in ns for a count query on the posts table' );
	}

	function get_metric_value() {
		global $wpdb;
		$start = hrtime( true );
		$wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts}" );
		$end = hrtime( true );

		return $end - $start;
	}
}

new Performance_Count_Posts_Metric();
