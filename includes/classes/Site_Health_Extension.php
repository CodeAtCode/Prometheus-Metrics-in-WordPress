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
            <section>
                <h3><?= _x('Global key', 'Site Health', 'prometheus-metrics-for-wp') ?></h3>
                <?php
                if (defined('PROMETHEUS_KEY')) {
                    ?>
                    <p>
                        <?php printf(_x('Your global <code>PROMETHEUS_KEY</code> is: %s', 'Site Health', 'prometheus-metrics-for-wp'), PROMETHEUS_KEY); ?>
                    </p>
                    <div>
                        <textarea onclick="this.focus();this.select()"
                                  readonly="readonly"
                                  style="width:100%;"><?= prometheus_get_url(false, false) ?></textarea>
                    </div>
                    <?php
                } else {
                    ?>
                    <p>
                        <?= _x('You don\'t have a global <code>PROMETHEUS_KEY</code> set.', 'Site Health', 'prometheus-metrics-for-wp') ?>
                    </p>
                    <p>
                        <?= _x('You may add it to your <code>wp-config.php</code>, i.e. like: ', 'Site Health', 'prometheus-metrics-for-wp') ?>
                        <code>define('PROMETHEUS_KEY','<?= wp_generate_uuid4() ?>')</code>
                    </p>
                    <?php
                }
                ?>
            </section>
            <section>
                <h3><?= _x('Keys', 'Site Health', 'prometheus-metrics-for-wp') ?></h3>
                <?php

                if (filter_input(INPUT_POST, 'generate_key', FILTER_VALIDATE_BOOL)) {
                    $prometheusUrl = prometheus_get_url(false);
                    ?>
                    <p><?= _x('This is the full URL you can use for your scraper. <strong>Please write down they key, you won\'t see it here again.', 'Site Health', 'prometheus-metrics-for-wp') ?></p>
                    <div>
                        <textarea onclick="this.focus();this.select()"
                                  readonly="readonly" style="width:100%;"><?= $prometheusUrl ?></textarea>
                    </div>
                    <?php
                }
                $prometheusKeys = get_option('prometheus-metrics-for-wp-keys', []);
                if (empty($prometheusKeys)) {
                    _ex('Your don\'t have any keys yet.', 'Site Health', 'prometheus-metrics-for-wp');
                } else {
                    ?>
                    <ol>
                        <?php
                        foreach ($prometheusKeys as $date => $key) {
                            ?>
                            <li><?= $date ?></li><?php
                        }
                        ?>
                    </ol>
                    <?php
                }

                ?>
                <p>
                <form method="post">
                    <input type="hidden" name="generate_key" value="yes"/>
                    <button class="button"
                            type="submit"><?= _x('Generate new key', 'Site Health', 'prometheus-metrics-for-wp') ?></button>
                </form>
                </p>
                <strong><?= _x('The generated key will be hashed and therefor only be visible once.', 'Site Health', 'prometheus-metrics-for-wp') ?></strong>
            </section>
            <section>
                <h3><?= _x('Additional labels', 'Site Health', 'prometheus-metrics-for-wp') ?></h3>
                <p>
                    <?php
                    _ex('It is posible to append additional labels to the metrics. Just add them with the "label_" prefix to the url.<br />I.e. to a hosting vendor and hosting rate:', 'Site Health', 'prometheus-metrics-for-wp');
                    ?>
                </p>
                <div>
                        <textarea onclick="this.focus();this.select()"
                                  readonly="readonly"
                                  style="width:100%;"><?= prometheus_get_url(false, false) ?>&label_hosting_vendor=MyExampleHoster&label_hosting_rate=FastWPHosting</textarea>
                </div>
                <div>
<textarea readonly="readonly" style="width:100%; margin-block: 15px;" rows="3">
# HELP any_example <?= _x('Labels will look like this', 'Site Health', 'prometheus-metrics-for-wp') ?><?= "\n" ?>
# TYPE any_example gauge<?= "\n" ?>
any_example{host="https://...",hosting_vendor="MyExampleHoster",hosting_rate="FastWPHosting"} 545 <?= hrtime(true) ?>
</textarea>

                </div>

            </section>
            <section>
                <h3><?= _x('Registered metrics', 'Site Health', 'prometheus-metrics-for-wp') ?></h3>


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
            </section>
        </div>
        <?php
    }

}
