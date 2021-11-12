<?php

namespace WP_Prometheus_Metrics;

use WP_Prometheus_Metrics\metrics\Database_Size_Metric;
use WP_Prometheus_Metrics\metrics\Options_Autoloaded_Count_Metric;
use WP_Prometheus_Metrics\metrics\Options_Autoloaded_Size_Metric;
use WP_Prometheus_Metrics\metrics\Pending_Updates_Metric;
use WP_Prometheus_Metrics\metrics\Performance_Count_Posts_Metric;
use WP_Prometheus_Metrics\metrics\Performance_Write_Temp_File_Metric;
use WP_Prometheus_Metrics\metrics\Post_Types_Count_Metric;
use WP_Prometheus_Metrics\metrics\Posts_Without_Content_Metric;
use WP_Prometheus_Metrics\metrics\Posts_Without_Title_Metric;
use WP_Prometheus_Metrics\metrics\Transients_Autoloaded_Count_Metric;
use WP_Prometheus_Metrics\metrics\Users_Count_Metric;
use WP_Prometheus_Metrics\metrics\Users_Sessions_Metric;

class Default_Metrics_Loader {
	private $metrics = false;

	public function __construct() {
		add_filter( 'prometheus_get_metrics', [ $this, 'load_default_metrics' ], 0 );
	}


	function load_default_metrics( $metrics = [] ) {
		if ( ! $this->metrics ) {

			$this->metrics   = [];
			$this->metrics[] = new Database_Size_Metric();
			$this->metrics[] = new Users_Count_Metric();
			$this->metrics[] = new Users_Sessions_Metric();
			$this->metrics[] = new Options_Autoloaded_Count_Metric();
			$this->metrics[] = new Options_Autoloaded_Size_Metric();
			$this->metrics[] = new Posts_Without_Content_Metric();
			$this->metrics[] = new Posts_Without_Title_Metric();
			$this->metrics[] = new Post_Types_Count_Metric();
			$this->metrics[] = new Pending_Updates_Metric();
			$this->metrics[] = new Transients_Autoloaded_Count_Metric();
			$this->metrics[] = new Performance_Count_Posts_Metric();
			$this->metrics[] = new Performance_Write_Temp_File_Metric();
		}

		return array_merge( $metrics, $this->metrics );
	}
}