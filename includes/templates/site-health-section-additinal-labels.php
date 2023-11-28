<p>
    <?php
    _ex('It is possible to append additional labels to the metrics. Just add them with the "label_" prefix to the url.<br />I.e. to a hosting vendor and hosting rate:', 'Site Health', 'prometheus-metrics-for-wp');
    ?>
</p>
<div>
<textarea onclick="this.focus();this.select()" readonly="readonly"
          style="width:100%;"><?= prometheus_get_url(false, false) ?>&label_hosting_vendor=MyExampleHoster&label_hosting_rate=FastWPHosting</textarea>
</div>
<div>
<textarea readonly="readonly" style="width:100%; margin-block: 15px;" rows="3">
# HELP any_example <?= _x('Labels will look like this', 'Site Health', 'prometheus-metrics-for-wp') ?><?= "\n" ?>
# TYPE any_example gauge<?= "\n" ?>
any_example{host="https://...",hosting_vendor="MyExampleHoster",hosting_rate="FastWPHosting"} 545 <?= hrtime(true) ?>
</textarea>

</div>
