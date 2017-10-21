<?php
/**
*
* Filename: common.php
* Description: common.php loads commonly accessed functions across the Visser Labs suite.
* 
* Premium
* - vl_common_updater_install
* - vl_common_updater_notice
* - vl_common_check_for_plugin_update
* - vl_common_make_request
*
* Free
* - woo_get_action
* - woo_is_wpsc_activated
* - woo_is_woo_activated
* - woo_is_jigo_activated
* - woo_is_exchange_activated
* - woo_get_woo_version
*
*/

if( is_admin() ) {

	/* Start of: WordPress Administration */

	/**
	 * Load installer for the Visser Labs Updater.
	 */
	if ( !class_exists( 'VL_Updater' ) && ! function_exists( 'vl_common_updater_install' ) ) {
		function vl_common_updater_install( $api, $action, $args ) {

			// Return the default API if this is not the Visser Labs Updater being installed
			if ( 'plugin_information' != $action || false !== $api || !isset( $args->slug ) || 'visser-labs-updater' != $args->slug )
				return $api;

			$download_url = 'http://updates.visser.com.au/downloads/visser-labs-updater.zip';
			$api = new stdClass();
			$api->name = 'Visser Labs Updater';
			$api->version = '1.1';
			$api->download_link = esc_url( $download_url );
			return $api;

		}
		add_filter( 'plugins_api', 'vl_common_updater_install', 10, 3 );
	}

	/**
	 * Visser Labs Updater Installation Prompts
	 */
	if ( !class_exists( 'VL_Updater' ) && !function_exists( 'vl_common_updater_notice' ) ) {
		function vl_common_updater_notice() {

			// No not display a notice if we're installing a Plugin
			if ( isset( $_GET['action'] ) && $_GET['action'] == 'install-plugin' )
				return;
			// Set the WordPress option to signal the Visser Labs Updater notice has been dismissed
			if ( isset( $_GET['action'] ) && $_GET['action'] == 'dismiss-update-notice' ) {
				update_option( 'vl_dismiss_update_notice', 1 );
				return;
			}

			// Check if Visser Labs Updater is already installed and activated
			$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) );
			if( in_array( 'visser-labs-updater/visser-labs-updater.php', $active_plugins ) )
				return;

			// Check if the WordPress option to signal the Visser Labs Updater notice has not yet been dismissed
			if( get_option( 'vl_dismiss_update_notice', false ) == false ) {

				// Generate links for WordPress to download, install, activate or dimiss the Visser Labs Updater
				$slug = 'visser-labs-updater';
				$download_url = 'http://updates.visser.com.au/downloads/visser-labs-updater.zip';
				$install_url = wp_nonce_url( self_admin_url( esc_url( add_query_arg( array( 'action' => 'install-plugin', 'plugin' => $slug ), 'update.php' ) ) ), sprintf( 'install-plugin_%s', $slug ) );
				$activate_url = esc_url( add_query_arg( array( 'action' => 'activate', 'plugin' => urlencode( 'visser-labs-updater/visser-labs-updater.php' ), 'plugin_status' => 'all', 'paged' => 1, '_wpnonce' => urlencode( wp_create_nonce( 'activate-plugin_visser-labs-updater/visser-labs-updater.php' ) ) ), 'plugins.php' ) );
				$dismiss_url = esc_url( add_query_arg( 'action', 'dismiss-update-notice' ) );

				// Set the default message to install the Visser Labs Updater
				$message = sprintf( __( '<a href="%s">Install the Visser Labs Updater Plugin</a> (or download and manually install it from <a href="%s">here</a>) to get automatic update notifications for your Visser Labs Plugins.', 'woo_ce' ), esc_url( $install_url ), esc_url( $download_url ) );

				// Check if the Visser Labs Updater Plugin has been installed but is not activated
				if( $plugins = array_keys( get_plugins() ) ) {
					foreach( $plugins as $plugin ) {
						if( strpos( $plugin, 'visser-labs-updater.php' ) !== false ) {
							$message = sprintf( __( '<a href="%s">Activate the Visser Labs Updater Plugin</a> to get automatic update notifications for your Visser Labs Plugins.', 'woo_ce' ), esc_url( admin_url( $activate_url ) ) );
							break;
						}
					}
				}
				printf( '<div class="updated"><p>%s<span style="float:right;"><a href="%s">%s</a></span></p></div>' . "\n", $message, $dismiss_url, __( 'Dismiss', 'woo_ce' ) );
			}

		}
		add_action( 'admin_notices', 'vl_common_updater_notice' );
	}

	if( !class_exists( 'VL_Updater' ) && !function_exists( 'vl_common_check_for_plugin_update' ) ) {
		function vl_common_check_for_plugin_update( $checked_data ) {

			if( empty( $checked_data->checked ) )
				return $checked_data;
	
			$args = (object)array(
				'slug' => WOO_CD_DIRNAME
			);

			$plugin_info = vl_common_make_request( 'plugin_information', $args );

			if( is_wp_error( $plugin_info ) )
				return;

			// Set the default message to install the Visser Labs Updater
			$message = null;
			if( !class_exists( 'VL_Updater' ) )
				$message = __( 'Install the Visser Labs Updater from the Plugins screen to install this Plugin update.', 'woo_ce' );

			// Check if Store Exporter Deluxe is in the Plugins list
			if( isset( $checked_data->checked[WOO_CD_RELPATH] ) && version_compare( $checked_data->checked[WOO_CD_RELPATH], $plugin_info->version, '<' ) ) {
				$checked_data->response[WOO_CD_RELPATH] = (object)array(
					'slug' => $plugin_info->slug,
					'new_version' => $plugin_info->version,
					'url' => $plugin_info->homepage,
					'package' => '',
					'name' => $plugin_info->slug,
					'requires' => $plugin_info->requires,
					'tested' => $plugin_info->tested,
					'upgrade_notice' => $message
				);
			}
			return $checked_data;

		}
		add_filter( 'pre_set_site_transient_update_plugins', 'vl_common_check_for_plugin_update', 20 );
	}

	if( !class_exists( 'VL_Updater' ) && !function_exists( 'vl_common_make_request' ) ) {
		function vl_common_make_request( $action, $args ) {

			global $wp_version;

			$update_uri = 'http://updates.visser.com.au/index.php';
			$request_string = array(
				'body' => array(
					'action' => $action, 
					'request' => serialize( $args ),
					'api-key' => md5( get_bloginfo( 'url' ) )
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
			);
			$request = wp_remote_post( $update_uri, $request_string );

			if( is_wp_error( $request ) )
				$response = new WP_Error( 'plugins_api_failed', __( 'An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>', 'woo_ce' ), $request->get_error_message() );
			else
				$response = unserialize( $request['body'] );

			if ( $response === false )
				$response = new WP_Error( 'plugins_api_failed', __( 'An unknown error occurred.', 'woo_ce' ), $request['body'] );
			return $response;

		}
	}

	// Load Dashboard widgets
	include_once( WOO_CD_PATH . 'includes/common-dashboard_widgets.php' );

	/* End of: WordPress Administration */

}

if( !function_exists( 'woo_get_action' ) ) {
	function woo_get_action( $prefer_get = false ) {

		if ( isset( $_GET['action'] ) && $prefer_get )
			return sanitize_text_field( $_GET['action'] );

		if ( isset( $_POST['action'] ) )
			return sanitize_text_field( $_POST['action'] );

		if ( isset( $_GET['action'] ) )
			return sanitize_text_field( $_GET['action'] );

		return false;

	}
}

if( !function_exists( 'woo_is_wpsc_activated' ) ) {
	function woo_is_wpsc_activated() {

		if( class_exists( 'WP_eCommerce' ) || defined( 'WPSC_VERSION' ) )
			return true;

	}
}

if( !function_exists( 'woo_is_woo_activated' ) ) {
	function woo_is_woo_activated() {

		if( class_exists( 'Woocommerce' ) )
			return true;

	}
}

if( !function_exists( 'woo_is_jigo_activated' ) ) {
	function woo_is_jigo_activated() {

		if( function_exists( 'jigoshop_init' ) )
			return true;

	}
}

if( !function_exists( 'woo_is_exchange_activated' ) ) {
	function woo_is_exchange_activated() {

		if( function_exists( 'IT_Exchange' ) )
			return true;

	}
}

if( !function_exists( 'woo_get_woo_version' ) ) {
	function woo_get_woo_version() {

		$version = false;
		if( defined( 'WC_VERSION' ) ) {
			$version = WC_VERSION;
		// Backwards compatibility
		} else if( defined( 'WOOCOMMERCE_VERSION' ) ) {
			$version = WOOCOMMERCE_VERSION;
		}
		return $version;
	
	}
}
?>