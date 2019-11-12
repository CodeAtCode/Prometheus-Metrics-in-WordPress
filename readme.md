# Prometheus Metrics in WordPress
[![License](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](http://www.gnu.org/licenses/gpl-3.0)   

It is based on [https://github.com/CodeAtCode/WPDB-Status](https://github.com/CodeAtCode/WPDB-Status).

Grafana dashboard avalaible on [official website](https://grafana.com/grafana/dashboards/11178).

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
    scheme: "https"
```

### WordPress customization

This plugin include an hook to append new metrics: `prometheus_custom_metrics`

## URL parameters

### Output

`users=yes` enable:
```
# HELP wp_users_total Total number of users.
# TYPE wp_users_total counter
wp_users_total{host="https://domain.tld"} 117
```

`posts=yes` enable:
```
# HELP wp_posts_total Total number of posts published.
# TYPE wp_posts_total counter
wp_posts_total{host="https://domain.tld", status="published"} 11786
wp_posts_total{host="https://domain.tld", status="draft"} 134
```

`pages=yes` enable:
```
# HELP wp_pages_total Total number of posts published.
# TYPE wp_pages_total counter
wp_pages_total{host="https://domain.tld", status="published"} 56
wp_pages_total{host="https://domain.tld", status="draft"} 4
```

`autoload=yes` enable:
```
# HELP wp_options_autoload Options in autoload.
# TYPE wp_options_autoload counter
wp_options_autoload{host="https://domain.tld"} 4194
# HELP wp_options_autoload_size Options size in KB in autoload.
# TYPE wp_options_autoload_size counter
wp_options_autoload_size{host="https://domain.tld"} 186
```

`transient=yes` enable:
```
# HELP wp_transient_autoload DB Transient in autoload.
# TYPE wp_transient_autoload counter
wp_transient_autoload{host="https://domain.tld"} 3681
```

`user_sessions=yes` enable:
```
# HELP wp_user_sessions User sessions.
# TYPE wp_user_sessions counter
wp_user_sessions{host="https://domain.tld"} 0
```

`posts_without_title=yes` enable:
```
# HELP wp_posts_without_title Post/Page without title.
# TYPE wp_posts_without_title counter
wp_posts_without_title{host="https://domain.tld"} 0
```

`posts_without_content=yes` enable:
```
# HELP wp_posts_without_content Post/Page without content.
# TYPE wp_posts_without_content counter
wp_posts_without_content{host="https://domain.tld"} 15
```

`db_size=yes` enable:
```
# HELP wp_db_size Total DB size in MB.
# TYPE wp_db_size counter
wp_db_size{host="https://domain.tld"} 580.35
```
