<?php

namespace WP_Prometheus_Metrics\metrics;
class Pending_Translation_Updates_Metric extends Abstract_Gauge_Metric
{

    public function __construct()
    {
        parent::__construct('pending_updates_translations');
    }

    function get_metric_value()
    {
        $status = get_site_transient('update_plugins');

        return is_countable($status->translations) ? count($status->translations) : 0;

    }

    function get_help_text(): string
    {
        return _x('Pending translation updates in the WordPress website', 'Metric Help Text', 'prometheus-metrics-for-wp');
    }
}
