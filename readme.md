# Prometheus Metrics in WordPress

## Settings  

In wp-config.php you need to settings that constant that will be used to expose those metrics in the url.

`define( 'PROMETHEUS_KEY', 'i8w374sdkfjg' );`

The url: http://domain.tld/wp-json/metrics?prometheus=i8w374sdkfjg

## URL parameters

###` Output

`users=yes` enable:
```
# HELP wp_users_total Total number of users.
# TYPE wp_users_total counter
wp_users_total 117
```

`posts=yes` enable:
```
wp_posts_total{status="published"} 11786
# HELP wp_posts_draft_total Total number of posts published.
# TYPE wp_posts_draft_total counter
wp_posts_total{status="draft"} 134
```

`pages=yes` enable:
```
# HELP wp_pages_total Total number of posts published.
# TYPE wp_pages_total counter
wp_pages_total{status="published"} 56
wp_pages_total{status="draft"} 4
```

`autoload=yes` enable:
```
# HELP wp_options_autoload Options in autoload.
# TYPE wp_options_autoload counter
wp_options_autoload 4194
```

`transient=yes` enable:
```
# HELP wp_transient_autoload DB Transient in autoload.
# TYPE wp_transient_autoload counter
wp_transient_autoload 3681
```

`user_sessions=yes` enable:
```
# HELP wp_user_sessions User sessions.
# TYPE wp_user_sessions counter
wp_user_sessions 0
```

`posts_without_title=yes` enable:
```
# HELP wp_posts_without_title Post/Page without title.
# TYPE wp_posts_without_title counter
wp_posts_without_title 0
```

`posts_without_content=yes` enable:
```
# HELP wp_posts_without_content Post/Page without content.
# TYPE wp_posts_without_content counter
wp_posts_without_content 15
```

`db_size=yes` enable:
```
# HELP wp_db_size Total DB size in MB.
# TYPE wp_db_size counter
wp_db_size 580.35
```
