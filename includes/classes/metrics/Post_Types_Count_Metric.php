<?php

namespace WP_Prometheus_Metrics\metrics;

class Post_Types_Count_Metric extends Abstract_Metric
{

    public function __construct()
    {
        parent::__construct('post_types_total');
    }

    function get_help_text(): string
    {
        return _x('Total number of content by type and status', 'Metric Help Text', 'prometheus-metrics-for-wp');
    }

    function internal_add_metric($registry): void
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
