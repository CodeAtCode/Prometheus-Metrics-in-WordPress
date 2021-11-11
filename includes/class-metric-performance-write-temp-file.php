<?php

namespace WP_Prometheus_Metrics;


class Performance_Write_Temp_File_Metric extends Metric {

	public function __construct() {
		parent::__construct( 'perf_write_temp_file', );
	}

	function get_metric_value() {
		$start = hrtime( true );
		$temp  = tmpfile();
		for ( $i = 0; $i < 1024 * 1024; $i ++ ) {
			fwrite( $temp, 'a' );
		}
		fclose( $temp );
		$end = hrtime( true );

		return $end - $start;
	}

	function get_help_text(): string {
		return _x( 'Measure the time in ns of writing a large file', 'Metric Help Text', 'prometheus-metrics-for-wp' );
	}
}

new Performance_Write_Temp_File_Metric();

