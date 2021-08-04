<?php

namespace WP_Prometheus_Metrics;

abstract class Metric {

	protected $metric_name;
	protected $help_text;
	protected $type;

	private $legacy_get_param;

	/**
	 * Metric constructor.
	 *
	 * @param $metric_name String Name of the metric
	 * @param $help_text String The help text.
	 * @param $type String The metrics type, defaults to "gauge"
	 * @param $legacy_get_param String (Deprecated) The legacy GET parameter, which is checked. Use metric_name instead.
	 */
	public function __construct( $metric_name, $help_text, $type = 'gauge', $legacy_get_param = false ) {
		$this->metric_name = str_replace( '-', '_', $metric_name );

		// For legacy reasons, this is still support. Just use the metric name instead
		if ( $legacy_get_param ) {
			$this->legacy_get_param = $legacy_get_param;
		}

		$this->help_text = $help_text;
		$this->type      = $type;
		add_action( 'prometheus_print_metrics', [ $this, 'print_metric' ], 10, 1 );
	}

	public function print_metric( $measure_all = false ) {
		if ( ! $this->is_enabled( $measure_all ) ) {
			return;
		}
		echo "# HELP $this->metric_name $this->help_text\n";
		echo "# TYPE $this->metric_name $this->type\n";
		echo $this->metric_name . '{' . $this->get_metric_labels() . '} ' . $this->get_cached_metric_value() . "\n";
	}

	public function get_metric_labels() {
		$labels = [ 'host="' . get_site_url() . '"' ];
		foreach ( $_GET as $label => $value ) {
			if ( str_starts_with( $label, 'label_' ) ) {
				$label    = sanitize_title( str_replace( [ 'label_', '-' ], [ '', '_' ], $label ) );
				$labels[] = $label . '="' . esc_attr( $value ) . '"';
			}
		}
		$labels = apply_filters( 'prometheus-metrics-for-wp/labels', $labels, $this->metric_name );

		return join( ',', $labels );
	}

	public function is_enabled( $measure_all ) {
		if ( $measure_all && filter_input( INPUT_GET, $this->metric_name, FILTER_SANITIZE_STRING ) !== 'no' ) {
			return $measure_all;
		}

		if ( filter_input( INPUT_GET, $this->metric_name, FILTER_SANITIZE_STRING ) === 'yes' ) {
			return true;
		}

		if ( filter_input( INPUT_GET, $this->legacy_get_param, FILTER_SANITIZE_STRING ) === 'yes' ) {
			_deprecated_argument( __FUNCTION__, '2.0', "Usage of legacy parameter $this->legacy_get_param is deprecated. Please use metric name instead: $this->metric_name" );

			return true;
		}

		return false;
	}

	abstract function get_metric_value();

	private function get_cached_metric_value() {
		$transientKey = 'prometheus-metrics-for-wp/' . $this->metric_name;
		$value        = get_transient( $transientKey );
		if ( ! $value ) {
			$value   = $this->get_metric_value();
			$value   = ( $value != 0 && empty( $value ) ? '-1' : $value ) . " " . ( time() * 1000 );
			$timeout = apply_filters( 'prometheus-metrics-for-wp/timeout', 3600, $this->metric_name ); // 1h by default
			set_transient( $transientKey, $value, $timeout );
		}

		return $value;
	}

}