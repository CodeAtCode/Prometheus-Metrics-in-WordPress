<?php

namespace WP_Prometheus_Metrics\metrics;


class Performance_Write_File_To_WP_Upload_Dir_Metric extends Abstract_Gauge_Metric
{

    public function __construct()
    {
        parent::__construct('write_temp_file_to_upload', 'performance');
    }

    function get_metric_value()
    {
        $file = trailingslashit(wp_upload_dir()['basedir']) . 'prometheus-temp-' . wp_generate_uuid4();

        $start = hrtime(true);
        $temp = fopen($file, 'w');
        for ($i = 0; $i < 1024 * 1024; $i++) {
            fwrite($temp, 'a');
        }
        fclose($temp);
        $end = hrtime(true);
        unlink($file);

        return $end - $start;
    }

    function get_help_text(): string
    {
        return _x('Measure the time in ns of writing a large file to WordPress upload directory', 'Metric Help Text', 'prometheus-metrics-for-wp');
    }
}

