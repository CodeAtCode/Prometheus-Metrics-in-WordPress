<?php

namespace WP_Prometheus_Metrics\metrics;

use WP_Debug_Data;

class Sizes_Information_Metric extends Abstract_Metric
{

    public function __construct()
    {
        parent::__construct('info_sizes');
    }

    function get_help_text(): string
    {
        return _x('Information about several sizes in MB', 'Metric Help Text', 'prometheus-metrics-for-wp');
    }

    function internal_add_metric($registry): void
    {
        require_once ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';

        $labels = $this->get_metric_labels();
        $labels['label'] = 'size';

        $gauge = $registry->getOrRegisterGauge($this->namespace, $this->metric_name, $this->get_help_text(), array_keys($labels));

        foreach (WP_Debug_Data::get_sizes() as $key => $value) {
            $labels['label'] = $key;
            $gauge->set($value['raw'] / 1024 / 1024, array_values($labels));
        }
    }
}
