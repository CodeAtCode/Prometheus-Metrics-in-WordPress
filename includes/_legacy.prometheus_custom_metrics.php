<?php

add_filter( 'prometheus_custom_metrics', 'prometheus_render_legacy_metric', 10, 2 );


function prometheus_render_legacy_metric( $result = '', $measure_all ) {
	if ( ( $measure_all && filter_input( INPUT_GET, 'posts', FILTER_SANITIZE_STRING ) !== 'no' )
	     || filter_input( INPUT_GET, 'posts', FILTER_SANITIZE_STRING ) === 'yes' ) {
		$posts       = wp_count_posts();
		$n_posts_pub = $posts->publish;
		$n_posts_dra = $posts->draft;
		$result      .= "# HELP wp_posts_total Deprecated. Total number of posts published.\n";
		$result      .= "# TYPE wp_posts_total counter\n";
		$result      .= 'wp_posts_total{host="' . get_site_url() . '", status="published"} ' . $n_posts_pub . "\n";
		$result      .= 'wp_posts_total{host="' . get_site_url() . '", status="draft"} ' . $n_posts_dra . "\n";
	}

	if ( ( $measure_all && filter_input( INPUT_GET, 'pages', FILTER_SANITIZE_STRING ) !== 'no' )
	     || filter_input( INPUT_GET, 'pages', FILTER_SANITIZE_STRING ) === 'yes' ) {
		$n_pages = wp_count_posts( 'page' );
		$result  .= "# HELP wp_pages_total Deprecated. Total number of pages published.\n";
		$result  .= "# TYPE wp_pages_total counter\n";
		$result  .= 'wp_pages_total{host="' . get_site_url() . '", status="published"} ' . $n_pages->publish . "\n";
		$result  .= 'wp_pages_total{host="' . get_site_url() . '", status="draft"} ' . $n_pages->draft . "\n";
	}

	return $result;
}