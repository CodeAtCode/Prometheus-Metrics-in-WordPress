<?php

namespace WP_Prometheus_Metrics\metrics;

class Post_Types_Count_Metric extends Abstract_Metric
{

    public function __construct()
    {
        parent::__construct('post_types_total');
    }

    public function print_metric($measure_all = false)
    {
        if (!$this->is_enabled($measure_all)) {
            return;
        }

        $transientKey = 'prometheus-metrics-for-wp/' . $this->metric_name;
        $value = get_transient($transientKey);

        if ($value) {
            echo $value;

            return;
        }

        ob_start();

        echo "# HELP $this->metric_name {$this->get_help_text()}\n";
        echo "# TYPE $this->metric_name $this->type\n";

        $post_types = array_merge([
            'page',
            'post',
            'attachment'
        ], get_post_types(['publicly_queryable' => true]));

        $post_types = apply_filters('prometheus-metrics-for-wp/wp_post_types_total/type', $post_types);

        foreach ($post_types as $post_type) {
            $counts = wp_count_posts($post_type);
            $time = time() * 1000;
            foreach (get_object_vars($counts) as $post_status => $count) {
                if (get_post_status_object($post_status) && $count > 0) {
                    echo $this->metric_name . '{' . $this->get_metric_labels() . ',post_type="' . $post_type . '",status="' . $post_status . '"} ' . $count . " " . $time . "\n";
                }
            }
        }

        $value = ob_get_clean();
        $timeout = apply_filters('prometheus-metrics-for-wp/timeout', 3600, $this->metric_name); // 1h by default
        set_transient($transientKey, $value, $timeout);

        echo $value;


    }


    function get_help_text(): string
    {
        return _x('Total number of content by type and status', 'Metric Help Text', 'prometheus-metrics-for-wp');
    }

    function internal_add_metric($registry)
    {
        $labels = $this->get_metric_labels();
        $labels['post_type'] = false;
        $labels['post_status'] = false;

        $gauge = $registry->getOrRegisterGauge($this->namespace, $this->metric_name, $this->get_help_text(), array_keys($labels));

        $post_types = array_merge([
            'page',
            'post',
            'attachment'
        ], get_post_types(['publicly_queryable' => true]));

        $post_types = apply_filters('prometheus-metrics-for-wp/wp_post_types_total/type', $post_types);

        foreach ($post_types as $post_type) {
            $counts = wp_count_posts($post_type);
            $time = time() * 1000;
            foreach (get_object_vars($counts) as $post_status => $count) {
                if (get_post_status_object($post_status) && $count > 0) {
                    $labels['post_type'] = $post_type;
                    $labels['post_status'] = $post_status;
                    $gauge->set($count, array_values($labels));
                }
            }
        }
    }
}
