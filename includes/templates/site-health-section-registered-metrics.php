<?php

use WP_Prometheus_Metrics\metrics\Abstract_Metric;

?>
<table class="widefat striped health-check-table" role="presentation">
    <tbody>
    <thead>
    <tr>
        <th><?= _x('Name', 'Site Health', 'prometheus-metrics-for-wp') ?></th>
        <th><?= _x('Description', 'Site Health', 'prometheus-metrics-for-wp') ?></th>
        <th><?= _x('URL param', 'Site Health', 'prometheus-metrics-for-wp') ?></th>
    </tr>
    </thead>
    <?php
    $metrics = apply_filters('prometheus-metrics-for-wp/get_metrics', []);
    /** @var $metric Abstract_Metric */
    foreach ($metrics as $metric) {
        ?>
        <tr>
            <th><?= $metric->metric_name ?></th>
            <td><?= $metric->get_help_text() ?></td>
            <td><code><?= $metric->namespace ?>_<?= $metric->metric_name ?>=yes</code> <br/>
                <code><?= $metric->namespace ?>_<?= $metric->metric_name ?>=no</code></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>