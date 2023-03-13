<?php

namespace WP_Prometheus_Metrics\metrics;


class Performance_Write_Temp_File_Metric extends Abstract_Gauge_Metric
{

    public function __construct()
    {
        parent::__construct('write_temp_file', 'performance');
    }

    function get_metric_value()
    {
        $start = hrtime(true);
        $temp = tmpfile();
        for ($i = 0; $i < 1024 * 1024; $i++) {
            fwrite($temp, 'a');
        }
        fclose($temp); // This will remove the file too
        $end = hrtime(true);

        return $end - $start;
    }

    function get_help_text(): string
    {
        return _x('Measure the time in ns of writing a large file to system temp directory', 'Metric Help Text', 'prometheus-metrics-for-wp');
    }
}

