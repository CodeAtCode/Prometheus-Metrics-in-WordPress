<p>
    <?php
    _ex('You can use this template to create an InfluxDB task for fetching the metrics.', 'Site Health', 'prometheus-metrics-for-wp');
    ?>
</p>
<div>
    <?php
    $site_url = get_site_url();
    ?>
    <textarea onclick="this.focus();this.select()" readonly="readonly"
              style="width:100%;" rows="18">
import "experimental/prometheus"

baseUrl = "<?= $site_url ?>"

prometheusKey = "YOUR_PROMETHEUS_KEY"
buckedId = "YOUR_INFLUX_DB_BUCKED_ID"

hostingVendor = "ExampleHostingProvider"
hostingRate = "Webhosting"

url = baseUrl + "/wp-json/metrics?all=yes&prometheus=" + prometheusKey + "&label_hosting_vendor="
        + hostingVendor + "&label_hosting_rate=" + hostingRate

prometheus.scrape(url: url) |> to(bucketID: buckedId)
</textarea>
</div>