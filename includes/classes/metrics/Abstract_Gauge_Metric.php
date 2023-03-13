<?php

namespace WP_Prometheus_Metrics\metrics;


abstract class Abstract_Gauge_Metric extends Abstract_Metric
{

    public function __construct(string $metric_name, string $namespace = 'wp')
    {
        parent::__construct($metric_name, $namespace);
    }

    abstract function get_metric_value();

    function internal_add_metric($registry)
    {
        $labels = $this->get_metric_labels();
        $gauge = $registry->getOrRegisterGauge($this->namespace, $this->metric_name, $this->get_help_text(), array_keys($labels));
        $gauge->set($this->get_metric_value(), array_values($labels));
    }
}
