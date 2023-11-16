<?php

namespace WP_Prometheus_Metrics\metrics;

use WP_Site_Health;

class PHP_Information_Metric extends Abstract_Metric
{

    public function __construct()
    {
        parent::__construct('info', 'php');
    }

    function get_help_text(): string
    {
        return _x('Information about PHP', 'Metric Help Text', 'prometheus-metrics-for-wp');
    }

    function internal_add_metric($registry): void
    {
        $labels = $this->get_metric_labels();
        $labels['label'] = PHP_VERSION;
        $labels['type'] = 'version';

        $gauge = $registry->getOrRegisterGauge($this->namespace, $this->metric_name, $this->get_help_text(), array_keys($labels));

        $gauge->set(PHP_VERSION_ID, array_values($labels));

        $labels['type'] = 'major_version';
        $gauge->set(PHP_MAJOR_VERSION, array_values($labels));

        $labels['type'] = 'minor_version';
        $gauge->set(PHP_MINOR_VERSION, array_values($labels));

        $labels['type'] = 'release_version';
        $gauge->set(PHP_RELEASE_VERSION, array_values($labels));

        if (!function_exists('ini_get')) {
            // Not enabled
            return;
        }

        foreach (['max_input_vars', 'max_execution_time', 'admin_memory_limit', 'memory_limit', 'max_input_time', 'upload_max_filesize', 'post_max_size'] as $php_variable) {
            $labels['type'] = $php_variable;
            $value = ini_get($php_variable);
            $labels['label'] = $value;

            $value = preg_replace('/\D/', '', $value);
            $gauge->set((float)$value, array_values($labels));
        }
    }
}
