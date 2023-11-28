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