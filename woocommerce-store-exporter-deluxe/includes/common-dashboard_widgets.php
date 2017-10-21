<?php
/*

Filename: common-dashboard_widgets.php
Description: common-dashboard_widgets.php loads commonly access Dashboard widgets across the Visser Labs suite.
Version: 1.4

*/

/* Start of: WooCommerce News - by Visser Labs */

if( !function_exists( 'woo_vl_dashboard_setup' ) ) {

	function woo_vl_dashboard_setup() {

		// Limit the Dashboard widget to Users with the Manage Options capability
		if( current_user_can( 'manage_options' ) ) {
			wp_add_dashboard_widget( 'woo_vl_news_widget', __( 'Plugin News - by Visser Labs', 'woo_ce' ), 'woo_vl_news_widget' );
		}

	}
	add_action( 'wp_dashboard_setup', 'woo_vl_dashboard_setup' );

	function woo_vl_news_widget() {

		include_once( ABSPATH . WPINC . '/feed.php' );

		// Get the RSS feed for WooCommerce Plugins
		$rss = fetch_feed( 'http://www.visser.com.au/blog/category/woocommerce/feed/' );
		$output = '<div class="rss-widget">';
		if( !is_wp_error( $rss ) ) {
			$maxitems = $rss->get_item_quantity( 5 );
			$rss_items = $rss->get_items( 0, $maxitems );
			$output .= '<ul>';
			foreach ( $rss_items as $item ) :
				$output .= '<li>';
				$output .= '<a href="' . $item->get_permalink() . '" title="' . 'Posted ' . $item->get_date( 'j F Y | g:i a' ) . '" class="rsswidget">' . $item->get_title() . '</a>';
				$output .= '<span class="rss-date">' . $item->get_date( 'j F, Y' ) . '</span>';
				$output .= '<div class="rssSummary">' . $item->get_description() . '</div>';
				$output .= '</li>';
			endforeach;
			$output .= '</ul>';
		} else {
			$message = __( 'Connection failed. Please check your network settings.', 'woo_ce' );
			$output .= '<p>' . $message . '</p>';
		}
		$output .= '</div>';

		echo $output;

	}

}

/* End of: WooCommerce News - by Visser Labs */

/* Start of: WooCommerce Plugins - by Visser Labs */

if( !function_exists( 'woo_vm_dashboard_setup' ) ) {

	function woo_vm_dashboard_setup() {

		$plugin_slug = WOO_CD_DIRNAME;

		// Limit the Dashboard widget to Users with the Manage Options capability
		if( current_user_can( 'manage_options' ) ) {
			wp_add_dashboard_widget( 'woo_vm_status_widget', __( 'Plugins - by Visser Labs', 'woo_ce' ), 'woo_vm_status_widget', 'woo_vm_status_widget_configure' );
			// Check if the required stylesheet is saved as a Theme override
			if( file_exists( STYLESHEETPATH . '/woocommerce-admin_dashboard_vm-plugins.css' ) )
				wp_enqueue_style( 'woo_vm_styles', get_bloginfo( 'stylesheet_directory' ) . '/woocommerce-admin_dashboard_vm-plugins.css', false );
			else
				wp_enqueue_style( 'woo_vm_styles', plugins_url( $plugin_slug . '/templates/admin/woocommerce-admin_dashboard_vm-plugins.css' ) );
		}

	}
	add_action( 'wp_dashboard_setup', 'woo_vm_dashboard_setup' );

	function woo_vm_status_widget() {

		$plugin_path = WOO_CD_PATH;

		// Get widget options
		if( !$widget_options = get_option( 'woo_vm_status_widget_options', array() ) ) {
			$widget_options = array(
				'enable' => 0
			);
		}

		// Display notice if Enable update monitor is not turned on
		if( $widget_options['enable'] == 0 ) {
			echo '<p>' . __( 'Open the Configure screen of this Dashboard widget to monitor the version and update status of your Visser Labs Plugins', 'woo_ce' ) . '</p>';
			return;
		}

		// Whether to display the Update available for download notice
		$update_available = false;

		// Get the list of WooCommerce Plugins from Visser Labs
		$vl_plugins = array();
		$size = 0;
		if( $check = wp_remote_fopen( 'http://www.visser.com.au/?woo_vm_data' ) ) {
			$raw_plugins = explode( '<br />', $check );
			foreach( $raw_plugins as $raw_plugin ) {
				$raw_plugin = explode( '@', $raw_plugin );
				$vl_plugins[] = array(
					'name' => $raw_plugin[1],
					'version' => $raw_plugin[3],
					'url' => $raw_plugin[5],
					'installed' => false,
					'version_existing' => false,
					'version_beta' => false
				);
			}
		}

		// Get the list of all available WordPress Plugins from this site
		if( $wp_plugins = get_plugins() ) {
			foreach( $wp_plugins as $wp_plugin ) {
				// Check if its one of our own
				if( $wp_plugin['Author'] == 'Visser Labs' ) {
					if( !empty( $vl_plugins ) ) {
						$size = count( $vl_plugins );
						for( $i = 0; $i < $size; $i++ ) {
							// Compare the Plugin name against our list of Plugins
							if( $vl_plugins[$i]['name'] == $wp_plugin['Name'] ) {

								// Clean the Plugin name
								$vl_plugins[$i]['name'] = str_replace( array( 'WooCommerce - ', ' for WooCommerce' ), '', $vl_plugins[$i]['name'] );

								// Check if this Plugin requires a Plugin update or is up to date
								$vl_plugins[$i]['installed'] = true;
								if( ( version_compare( strval( $vl_plugins[$i]['version'] ), strval( $wp_plugin['Version'] ), '>' ) == 1 ) ) {
									$update_available = true;
									$vl_plugins[$i]['version_existing'] = $wp_plugin['Version'];
									continue;
								}

								// Check if this Plugin is from the future
								if( strval( $wp_plugin['Version'] ) > strval( $vl_plugins[$i]['version'] ) ) {
									$vl_plugins[$i]['version_beta'] = $wp_plugin['Version'];
									continue;
								}

							}
						}
					}
				}
			}
			unset( $wp_plugins );

			// Clean the Plugin name for any legacy Plugins
			if( !empty( $vl_plugins ) ) {
				for( $i = 0; $i < $size; $i++ ) {
					$vl_plugins[$i]['name'] = str_replace( array( 'WooCommerce - ', ' for WooCommerce' ), '', $vl_plugins[$i]['name'] );
				}
			}
		}

		include_once( $plugin_path . 'templates/admin/woocommerce-admin_dashboard_vm-plugins.php' );

	}

	function woo_vm_status_widget_configure() {

		// Get widget options
		if( !$widget_options = get_option( 'woo_vm_status_widget_options', array() ) ) {
			$widget_options = array(
				'enable' => 0
			);
		}

		// Update widget options
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['woo_vm_widget_post'] ) )
			update_option( 'woo_vm_status_widget_options', array_map( 'sanitize_text_field', $_POST['woo_vm_widget'] ) ); ?>
<div>
	<label for="woo_vm_widget-enable"><?php _e( 'Enable update monitor', 'woo_ce'); ?>:</label><br />
	<select id="woo_vm_widget-enable" name="woo_vm_widget[enable]">
		<option value="1"<?php selected( $widget_options['enable'], 1 ); ?>><?php _e( 'Yes', 'woo_ce' ); ?></option>
		<option value="0"<?php selected( $widget_options['enable'], 0 ); ?>><?php _e( 'No', 'woo_ce' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Turning the update monitor on will notify you of Plugin updates to activated Visser Labs Plugins. By default this is turned off.', 'woo_ce' ); ?></p>
</div>
<input name="woo_vm_widget_post" type="hidden" value="1" />
<?php

	}

}

/* End of: WooCommerce Plugins - by Visser Labs */
?>