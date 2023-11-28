<?php

namespace WP_Prometheus_Metrics\metrics;

/**
 * @deprecated Pending updates are seperated metrics now
 */
class Pending_Updates_Metric extends Abstract_Gauge_Metric
{

    public function __construct()
    {
        parent::__construct('pending_updates');
    }

    function get_metric_value()
    {
        $status = get_site_transient('update_plugins');

        $pluginUpdates = is_countable($status->response) ? count($status->response) : 0;
        $translationUpdates = is_countable($status->translations) ? count($status->translations) : 0;

        return $pluginUpdates + $translationUpdates;
    }

    function get_help_text(): string
    {
        return _x('(Deprecated) Pending updates in the WordPress website', 'Metric Help Text', 'prometheus-metrics-for-wp');
    }
}
