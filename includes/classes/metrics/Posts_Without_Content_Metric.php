<?php

namespace WP_Prometheus_Metrics\metrics;


class Posts_Without_Content_Metric extends Abstract_Gauge_Metric
{


    public function __construct()
    {
        parent::__construct('posts_without_content');
    }

    function get_metric_value()
    {
        global $wpdb;

        return $wpdb->get_var("SELECT count(*) FROM " . $wpdb->posts . " WHERE post_content='' AND post_status!='draft' AND post_status!='trash' AND post_status!='auto-draft' AND (post_type='post' OR post_type='page')"); // phpcs:ignore WordPress.DB
    }

    function get_help_text(): string
    {
        return _x('Posts and Pages without content', 'Metric Help Text', 'prometheus-metrics-for-wp');
    }
}
