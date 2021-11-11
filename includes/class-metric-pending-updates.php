<?php

namespace WP_Prometheus_Metrics;


class Pending_Updates_Metric extends Metric {

	public function __construct() {
		parent::__construct( 'wp_pending_updates', 'gauge', 'pending_updates' );
	}

	function get_metric_value() {
		$status = get_site_transient( 'update_plugins' );

		$pluginUpdates      = is_countable( $status->response ) ? count( $status->response ) : 0;
		$translationUpdates = is_countable( $status->translations ) ? count( $status->translations ) : 0;

		return $pluginUpdates + $translationUpdates;
	}

	function get_help_text() {
		return _x( 'Pending updates in the WordPress website', 'Metric Help Text', 'prometheus-metrics-for-wp' );
	}
}

new Pending_Updates_Metric();
