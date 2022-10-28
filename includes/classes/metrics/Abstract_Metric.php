<?php

namespace WP_Prometheus_Metrics\metrics;

use Exception;
use Prometheus\CollectorRegistry;

abstract class Abstract_Metric
{

    public $metric_name;
    public $namespace;


    public $scheme;
    public $host;
    public $port;

    /**
     * Metric constructor.
     *
     * @param $metric_name String Name of the metric
     */
    public function __construct(string $metric_name, string $namespace = 'wp')
    {
        $this->metric_name = str_replace('-', '_', $metric_name);
        $this->namespace = $namespace;

        add_filter('prometheus-metrics-for-wp/get_metrics', [$this, 'get_metric'], 10, 1);

        $this->scheme = parse_url(get_site_url(), PHP_URL_SCHEME);
        $this->host = parse_url(get_site_url(), PHP_URL_HOST);
        $this->port = parse_url(get_site_url(), PHP_URL_PORT);
    }

    /**
     * @param $registry CollectorRegistry
     * @param $measure_all bool
     */
    public function add_metric($registry, $measure_all)
    {
        if (!$this->is_enabled($measure_all)) {
            return;
        }

        try {
            $this->internal_add_metric($registry);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    abstract function internal_add_metric($registry);

    public function get_metric($metrics = [])
    {
        $metrics[] = $this;

        return $metrics;
    }

    public function get_metric_labels(): array
    {
        $labels = [
            'host' => $this->host,
            'scheme' => $this->scheme
        ];
        if (!empty($this->port)) {
            $labels['port'] = $this->port;
        }
        foreach ($_GET as $label => $value) {
            if (str_starts_with($label, 'label_')) {
                $label = sanitize_title(str_replace(['label_', '-'], ['', '_'], $label));
                $labels[$label] = $value;
            }
        }
        return apply_filters('prometheus-metrics-for-wp/labels', $labels, $this->metric_name);
    }

    public function is_enabled($measure_all)
    {
        if ($measure_all && filter_input(INPUT_GET, $this->namespace . '_' . $this->metric_name, FILTER_SANITIZE_STRING) !== 'no') {
            return $measure_all;
        }

        if (filter_input(INPUT_GET, $this->namespace . '_' . $this->metric_name, FILTER_SANITIZE_STRING) === 'yes') {
            return true;
        }

        return false;
    }

    /**
     * @return String Must be a function so i18n is supported
     */
    abstract function get_help_text(): string;
}