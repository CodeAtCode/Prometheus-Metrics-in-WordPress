<?php

namespace WP_Prometheus_Metrics;

class Site_Health_Extension {
	public function __construct() {
		add_filter( 'site_health_navigation_tabs', [ $this, 'add_site_health_tab' ] );
		add_action( 'site_health_tab_content', [ $this, 'render_site_health_tab_content' ] );
	}


	function add_site_health_tab( $tabs ) {
		$tabs['prometheus'] = esc_html_x( 'Prometheus', 'Site Health', 'prometheus-metrics-for-wp' );

		return $tabs;
	}

	function render_site_health_tab_content( $tab ) {
		// Do nothing if this is not our tab.
		if ( 'prometheus' !== $tab ) {
			return;
		}
		if ( ! current_user_can( 'administrator' ) ) {
			echo _x( 'Access denied', 'Site Health', 'prometheus-metrics-for-wp' );

			return;
		}

		?>
		<div class="health-check-body health-check-prometheus-tab hide-if-no-js">
			<h2><?= _x( 'Prometheus', 'Site Health', 'prometheus-metrics-for-wp' ) ?></h2>

			<h3><?= _x( 'Keys', 'Site Health', 'prometheus-metrics-for-wp' ) ?></h3>
			<?php


			if ( filter_input( INPUT_GET, 'generate_key', FILTER_VALIDATE_BOOL ) ) {
				$prometheusUrl = prometheus_get_url( false );
				?>
				<strong><?= _x( 'This is the full URL you can use for your scraper. <strong>Please write down they key, you won\'t see it here again.', 'Site Health', 'prometheus-metrics-for-wp' ) ?></strong>
				<pre><code><?= $prometheusUrl ?></code></pre>
				<?php
			}
			$prometheusKeys = get_option( 'prometheus-metrics-for-wp-keys', [] );
			if ( empty( $prometheusKeys ) ) {
				_ex( 'Your don\'t have any keys yet.', 'Site Health', 'prometheus-metrics-for-wp' );
			} else {
				?>
				<ol>
					<?php
					foreach ( $prometheusKeys as $date => $key ) {
						?>
						<li><?= $date ?></li><?php
					}
					?>
				</ol>
				<?php
			}

			?>
			<p>
				<a class="button"
				   href="<?= admin_url( 'site-health.php?tab=prometheus&generate_key=yes' ) ?>"><?= _x( 'Generate new key', 'Site Health', 'prometheus-metrics-for-wp' ) ?></a>
				<strong><?= _x( 'The generated key will be hashed and therefor only be visible once.', 'Site Health', 'prometheus-metrics-for-wp' ) ?></strong>
			</p>
			<h3><?= _x( 'Global key', 'Site Health', 'prometheus-metrics-for-wp' ) ?></h3>
			<?php
			if ( defined( 'PROMETHEUS_KEY' ) ) {
				?>
				<p>
					<?php printf( _x( 'Your global <code>PROMETHEUS_KEY</code> is: %s', 'Site Health', 'prometheus-metrics-for-wp' ), PROMETHEUS_KEY ); ?>
				</p>
				<?php
			} else {
				?>
				<p>
					<?= _x( 'You don\'t have a global <code>PROMETHEUS_KEY</code> set.', 'Site Health', 'prometheus-metrics-for-wp' ) ?>
				</p>
				<p>
					<?= _x( 'You may add it to your <code>wp-config.php</code>, i.e. like: ', 'Site Health', 'prometheus-metrics-for-wp' ) ?>
					<code>define('PROMETHEUS_KEY','<?= wp_generate_uuid4() ?>')</code>
				</p>
				<?php
			}
			?>
		</div>
		<?php
	}

}

new Site_Health_Extension();
