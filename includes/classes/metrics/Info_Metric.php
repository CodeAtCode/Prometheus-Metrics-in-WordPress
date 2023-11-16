<?php

namespace WP_Prometheus_Metrics\metrics;


/**
 * A metric that gives information about the WordPress environment.
 */
class Info_Metric extends Abstract_Gauge_Metric
{
    public function __construct()
    {
        parent::__construct('info');
    }

    public function get_metric_labels(): array
    {
        global $wp_db_version;

        return array_merge(
            parent::get_metric_labels(),
            [
                'version' => get_bloginfo('version'),
                'db_version' => $wp_db_version,
            ]
        );
    }

    function get_metric_value()
    {
        // This metric as no value, the interesting part is in it's labels.
        // Returning a standard 1 value here.
        return 1;
    }

    function get_help_text(): string
    {
        return _x('Information about the WordPress environment', 'Metric Help Text', 'prometheus-metrics-for-wp');
    }
}
