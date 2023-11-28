<?php

namespace WP_Prometheus_Metrics\metrics;
class Pending_Plugin_Updates_Metric extends Abstract_Gauge_Metric
{

    public function __construct()
    {
        parent::__construct('pending_updates_plugins');
    }

    function get_metric_value()
    {
        return count(get_plugin_updates());
    }

    function get_help_text(): string
    {
        return _x('Pending plugin updates in the WordPress website', 'Metric Help Text', 'prometheus-metrics-for-wp');
    }
}
