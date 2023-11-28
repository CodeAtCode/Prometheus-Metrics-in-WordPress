<?php

namespace WP_Prometheus_Metrics\metrics;
class Pending_Theme_Updates_Metric extends Abstract_Gauge_Metric
{

    public function __construct()
    {
        parent::__construct('pending_updates_themes');
    }

    function get_metric_value()
    {
        return count(get_theme_updates());
    }

    function get_help_text(): string
    {
        return _x('Pending theme updates in the WordPress website', 'Metric Help Text', 'prometheus-metrics-for-wp');
    }
}
