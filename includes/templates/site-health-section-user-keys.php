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