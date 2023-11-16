<?php

namespace WP_Prometheus_Metrics;

use WP_Prometheus_Metrics\metrics\Sizes_Information_Metric;
use WP_Prometheus_Metrics\metrics\Database_Size_Metric;
use WP_Prometheus_Metrics\metrics\Info_Metric;
use WP_Prometheus_Metrics\metrics\Options_Autoloaded_Count_Metric;
use WP_Prometheus_Metrics\metrics\Options_Autoloaded_Size_Metric;
use WP_Prometheus_Metrics\metrics\Pending_Plugin_Updates_Metric;
use WP_Prometheus_Metrics\metrics\Pending_Theme_Updates_Metric;
use WP_Prometheus_Metrics\metrics\Pending_Translation_Updates_Metric;
use WP_Prometheus_Metrics\metrics\Pending_Updates_Metric;
use WP_Prometheus_Metrics\metrics\Performance_Count_Posts_Metric;
use WP_Prometheus_Metrics\metrics\Performance_Write_File_To_WP_Upload_Dir_Metric;
use WP_Prometheus_Metrics\metrics\Performance_Write_Temp_File_Metric;
use WP_Prometheus_Metrics\metrics\PHP_Information_Metric;
use WP_Prometheus_Metrics\metrics\Post_Types_Count_Metric;
use WP_Prometheus_Metrics\metrics\Posts_Without_Content_Metric;
use WP_Prometheus_Metrics\metrics\Posts_Without_Title_Metric;
use WP_Prometheus_Metrics\metrics\Transients_Autoloaded_Count_Metric;
use WP_Prometheus_Metrics\metrics\Users_Count_Metric;
use WP_Prometheus_Metrics\metrics\Users_Sessions_Metric;

class Default_Metrics_Loader
{
    private $metrics_loaded = false;

    public function __construct()
    {
        add_filter('prometheus-metrics-for-wp/get_metrics', [$this, 'load_default_metrics'], 0, 2);
    }

    function load_default_metrics($metrics = [])
    {
        if (!$this->metrics_loaded) {
            new Sizes_Information_Metric();
            new Info_Metric();
            new Options_Autoloaded_Count_Metric();
            new Options_Autoloaded_Size_Metric();
            new Pending_Plugin_Updates_Metric();
            new Pending_Theme_Updates_Metric();
            new Pending_Translation_Updates_Metric();
            new Performance_Count_Posts_Metric();
            new Performance_Write_File_To_WP_Upload_Dir_Metric();
            new Performance_Write_Temp_File_Metric();
            new PHP_Information_Metric();
            new Post_Types_Count_Metric();
            new Posts_Without_Content_Metric();
            new Posts_Without_Title_Metric();
            new Transients_Autoloaded_Count_Metric();
            new Users_Count_Metric();
            new Users_Sessions_Metric();

            /** Deprecated metrics */
            new Database_Size_Metric();
            new Pending_Updates_Metric();

            $this->metrics_loaded = true;
        }

        return $metrics;
    }
}
