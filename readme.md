# Prometheus Metrics in WordPress

[![License](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](http://www.gnu.org/licenses/gpl-3.0)

A WordPress plugin, based on [https://github.com/CodeAtCode/WPDB-Status](https://github.com/CodeAtCode/WPDB-Status).

Grafana dashboard avalaible on [official website](https://grafana.com/grafana/dashboards/11178).

![](https://grafana.com/api/dashboards/11178/images/7117/image)

## Settings

In wp-config.php you need to settings that constant that will be used to expose those metrics in the url.

`define( 'PROMETHEUS_KEY', 'fg98dfgkj' );`

## Prometheus

To add a new target:

```
  - job_name: "WordPress metrics"
    static_configs:
      - targets: ["domain.tld"]
    scrape_interval: "5m"
    metrics_path: "/wp-json/metrics"
    params:
      prometheus: ['fg98dfgkj']
      users: ['yes']
      posts: ['yes']
      pages: ['yes']
      autoload: ['yes']
      transient: ['yes']
      user_sessions: ['yes']
      posts_without_content: ['yes']
      posts_without_title: ['yes']
      db_size: ['yes']
      pending_updates: ['yes']
    scheme: "https"
```

### WordPress customization

This plugin includes a hook to append new metrics: `prometheus_custom_metrics`

## URL parameters

### Enable all

`all=yes` enables all of the default filters at once

### Enable or disable specific metrics

See the included page at `Tools` -> `Site Health` -> `Prometheus` with specific metric parameters.

## Changelog

### 2.0 ###

* **Major rewrite, which may break your current metrics!**
* Requires at least WordPress 5.6 and PHP 7.3
    * Added PHP 8.0 polyfill
* Use 'gauge' instead of 'counter'
    * Define `PROMETHEUS_LEGACY_TYPE` with true to change this
* Added more metrics
    * You can add custom metrics by implementing WP_Prometheus_Metrics\Metric
* Added timestamps to metrics
* Metrics will be cached for 1h by default (use filter `prometheus-metrics-for-wp/timeout` to change this)
* Seperated metrics for different post types
* Site health check integration
    * View url for endpoint
    * Ability to generate authentication keys

**Legacy support**

To use 'counter' instead of 'gauge', add the following to your `wp-config.php`

```php
define('PROMETHEUS_LEGACY_TYPE', true);
```