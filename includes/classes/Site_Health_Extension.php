<?php

namespace WP_Prometheus_Metrics;

use WP_Prometheus_Metrics\metrics\Abstract_Metric;

class Site_Health_Extension
{
    public function __construct()
    {
        add_filter('plugin_action_links_' . PROMETHEUS_PLUGIN_FILE, [$this, 'add_plugin_action_links'], 0, 1);
        add_filter('site_health_navigation_tabs', [$this, 'add_site_health_tab']);
        add_action('site_health_tab_content', [$this, 'render_site_health_tab_content']);
    }

    function add_plugin_action_links($actions = [])
    {
        $actions[] = '<a href="' . admin_url('site-health.php?tab=prometheus') . '">' . _x('Settings', 'Site Health', 'prometheus-metrics-for-wp') . '</a>';

        return $actions;
    }

    function add_site_health_tab($tabs)
    {
        $tabs['prometheus'] = esc_html_x('Prometheus', 'Site Health', 'prometheus-metrics-for-wp');

        return $tabs;
    }

    function render_site_health_tab_content($tab)
    {
        // Do nothing if this is not our tab.
        if ('prometheus' !== $tab) {
            return;
        }
        if (!current_user_can('administrator')) {
            echo _x('Access denied', 'Site Health', 'prometheus-metrics-for-wp');

            return;
        }

        ?>
        <div class="health-check-body health-check-prometheus-tab hide-if-no-js">
            <h2><?= _x('Prometheus', 'Site Health', 'prometheus-metrics-for-wp') ?></h2>
            <?php
            foreach ([
                         "global-keys" => _x('Global key', 'Site Health', 'prometheus-metrics-for-wp'),
                         "user-keys" => _x('Keys', 'Site Health', 'prometheus-metrics-for-wp'),
                         "additinal-labels" => _x('Additional labels', 'Site Health', 'prometheus-metrics-for-wp'),
                         "registered-metrics" => _x('Registered metrics', 'Site Health', 'prometheus-metrics-for-wp'),
                         "influxdb-task" => _x('InfluxDB task', 'Site Health', 'prometheus-metrics-for-wp'),
                     ] as $section => $title) {

                echo "<section><h3>$title</h3>";
                include_once PROMETHEUS_PLUGIN_DIR . "includes/templates/site-health-section-$section.php";
                echo "</section>";
            }
            ?>
        </div>
        <?php
    }
}
