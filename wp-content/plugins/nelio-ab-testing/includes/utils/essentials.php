<?php
/**
 * Copyright 2015 Nelio Software S.L.
 * This script is distributed under the terms of the GNU General Public
 * License.
 *
 * This script is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 3 of the License.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * This function determines whether the current user is able to manage the plugin or not.
 *
 * @return boolean PHPDOC
 *
 * @since PHPDOC
 */
function nelioab_can_user_manage_plugin() {
	$result = false;

	// If the user is super admin, she can use the plugin
	if ( is_super_admin() ) {
		$result = true;
	}

	// If WordPress is in a multisite environment, things are slightly
	// different. The plugin should be available, at least, for editors.
	if ( NelioABSettings::regular_admins_can_manage_plugin() ) {
		if ( current_user_can( 'delete_others_posts' ) ) {
			$result = true;
		}
	}

	/**
	 * Filter whether current user can manage the plugin or not.
	 *
	 * @since 4.3.1
	 *
	 * @param  boolean  $result  Whether the current user can manage the plugin
	 */
	$result = apply_filters( 'nelioab_can_user_manage_plugin', $result );

	return $result;
}


/**
 * Checks if our plugin is up-to-date.
 *
 * This function is called every time the plugins are loaded, and checks
 * whether our plugin is up-to-date or not. If it isn't, the
 * nelioab_activate_plugin is called.
 *
 * @return void
 *
 * @since PHPDOC
 */
function nelioab_update_plugin_info_if_required() {
	$last_available_version = get_option( 'nelioab_last_version_installed', false );
	if ( $last_available_version !== NELIOAB_PLUGIN_VERSION ) {
		add_filter( 'init', 'nelioab_activate_plugin' );
	}
}


/**
 * Activates Nelio A/B Testing
 *
 * This function is called by the "registed_activation_hook". It is the
 * opposite of the nelioab_deactivate_plugin function. Its aim is to make sure
 * that alternatives (draft post/pages with a metatype) are not visible in the
 * admin area, but can be editted and used.
 *
 * We also make sure that it's called after an update.
 *
 * @return void
 *
 * @since PHPDOC
 */
function nelioab_activate_plugin() {
	/** @var wpdb $wpdb */
	global $wpdb;

	// Old Stuff Compa: rename the meta key that identifies post/page alternatives...
	$wpdb->update(
		$wpdb->postmeta,
		array( 'meta_key' => '_is_nelioab_alternative' ),
		array( 'meta_key' => 'is_nelioab_alternative' )
	);

	// We remove all information about "_is_nelioab_alternative" for posts whose
	// IDs are less than 15. In previous versions of the plugin, Title experiments
	// marked those posts as alternatives (negative IDs from -1 to -15 were used
	// and WordPress interpreted them as positive IDs).
	$query = '' .
		'DELETE FROM ' . $wpdb->postmeta . ' WHERE ' .
			'post_id < 15 AND meta_key = \'_is_nelioab_alternative\'';
	$wpdb->query( $query );

	// Showing previous page and post alternatives
	require_once( NELIOAB_UTILS_DIR . '/wp-helper.php' );
	$cpts = NelioABWpHelper::get_custom_post_types();
	$post_types = array( 'post', 'page' );
	foreach ( $cpts as $post_type ) {
		array_push( $post_types, $post_type->name );
	}

	$query = 'UPDATE ' . $wpdb->posts . ' SET post_type = %s WHERE post_type = %s';

	// execute the query
	foreach ( $post_types as $post_type ) {
		$wpdb->query( $wpdb->prepare( $query, $post_type, 'nelioab_alt_' . $post_type ) );
	}

	// Recover previous widget alternatives
	require_once( NELIOAB_EXP_CONTROLLERS_DIR . '/widget-experiment-controller.php' );
	NelioABWidgetExpAdminController::restore_alternative_widget_backup();

	// Recover previous menu alternatives
	require_once( NELIOAB_EXP_CONTROLLERS_DIR . '/menu-experiment-controller.php' );
	NelioABMenuExpAdminController::restore_alternative_menu_backup();

	// Save the latest version we use for "(re)activating" the plugin and
	// show the welcome message
	$version = get_option( 'nelioab_last_version_installed', false );
	update_option( 'nelioab_last_version_installed', NELIOAB_PLUGIN_VERSION );

	// Prepare the code for showing the welcome message
	if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
		if ( NELIOAB_PLUGIN_VERSION === $version ) {
			set_transient( '_nelioab_welcome_user', 'nelioab-reactivated', 30 );
		} else if ( NELIOAB_PLUGIN_VERSION !== $version ) {
			set_transient( '_nelioab_welcome_user', 'nelioab-updated', 30 );
		} else {
			set_transient( '_nelioab_welcome_user', 'nelioab-installed', 30 );
		}
	}

}

/**
 * Deactivates our plugin.
 *
 * This function is called by the "registed_deactivation_hook". Alternatives
 * are regular pages or posts (draft status) with a special metaoption that
 * is used to hide them from the admin menu. When the plugin is deactivated,
 * no one hides the alternatives... In order to prevent them from appearing,
 * we change their post_type to a fake type.
 *
 * @return void
 *
 * @since PHPDOC
 */
function nelioab_deactivate_plugin() {
	/** @var wpdb $wpdb */
	global $wpdb;
	require_once( NELIOAB_EXP_CONTROLLERS_DIR . '/widget-experiment-controller.php' );
	require_once( NELIOAB_EXP_CONTROLLERS_DIR . '/menu-experiment-controller.php' );

	if ( isset( $_GET['action'] ) && 'clean-and-deactivate' == $_GET['action'] ) {
		// Remove all alternative widgets
		NelioABWidgetExpAdminController::clean_all_alternative_widgets();

		// Remove all alternative menus
		NelioABMenuExpAdminController::clean_all_alternative_menus();

		// Remove all alternative pages and posts
		$query = '' .
			'DELETE FROM ' . $wpdb->posts . ' WHERE ' .
				'id IN (' .
					'SELECT post_id FROM ' . $wpdb->postmeta . ' WHERE ' .
						'meta_key = \'_is_nelioab_alternative\' ' .
				')';
		$wpdb->query( $query );

		// Remove all alternative experiments
		$query = 'DELETE FROM ' . $wpdb->posts . ' WHERE post_type = \'nelioab_local_exp\'';
		$wpdb->query( $query );

		// Clean all experiments in AE
		require_once( NELIOAB_UTILS_DIR . '/backend.php' );
		for ( $i = 0; $i < 5; ++$i ) {
			try {
				NelioABBackend::remote_get( sprintf(
					NELIOAB_BACKEND_URL . '/site/%s/clean',
					NelioABAccountSettings::get_site_id()
				) );
				break;
			}
			catch ( Exception $e ) {}
		}

		// Remove all Nelio options
		$query = 'DELETE FROM ' . $wpdb->postmeta . ' WHERE meta_key LIKE \'%nelioab%\'';
		$wpdb->query( $query );
		$query = 'DELETE FROM ' . $wpdb->options . ' WHERE option_name LIKE \'%nelioab%\'';
		$wpdb->query( $query );
	}
	else {
		// Hiding alternative posts
		$query = '' .
			'UPDATE ' . $wpdb->posts . ' SET post_type = %s WHERE ' .
				'id IN (' .
					'SELECT post_id FROM ' . $wpdb->postmeta . ' WHERE ' .
						'meta_key = \'_is_nelioab_alternative\' ' .
				') AND ' .
				'post_type = %s';

		// recover post type names
		require_once( NELIOAB_UTILS_DIR . '/wp-helper.php' );
		$cpts = NelioABWpHelper::get_custom_post_types();
		$post_types = array( 'post', 'page' );
		foreach ( $cpts as $post_type ) {
			array_push( $post_types, $post_type->name );
		}

		// execute the query
		foreach ( $post_types as $post_type ) {
			$wpdb->query( $wpdb->prepare( $query, 'nelioab_alt_' . $post_type, $post_type ) );
		}

		// Hiding widget alternatives
		NelioABWidgetExpAdminController::backup_alternative_widgets();
		// Hiding menu alternatives
		NelioABMenuExpAdminController::backup_alternative_menus();
	}
}


/**
 * Remind users that they have to clean their cache after an update
 *
 * @return void
 *
 * @since PHPDOC
 */
function nelioab_add_cache_notice() {
	global $pagenow;
	if ( 'plugins.php' == $pagenow || 'update.php' == $pagenow )
		return;
	try {
		NelioABAccountSettings::check_user_settings();
	}
	catch ( Exception $e ) {
		return;
	}
	$message = sprintf(
			__( 'You\'ve recently upgraded to <strong>Nelio A/B Testing %s</strong>. <strong>If you\'re running a cache system</strong> (such as <em>W3 Total Cache</em> or <em>WP Super Cache</em>) <strong>or if your server is behind a CDN</strong>, please <strong>clean all your caches</strong>. Otherwise, you may serve old versions of our tracking scripts and, therefore, the plugin may not work properly.', 'nelioab' ),
			NELIOAB_PLUGIN_VERSION
		);
	?>
	<div class="updated">
		<p>
			<?php echo $message; ?>
			<a id="dismiss-nelioab-cache-notice" style="font-size:80%;" href="#"><?php _e( 'Dismiss' ); ?></a>
		</p>
		<script style="display:none;" type="text/javascript">
		(function($) {
			$('a#dismiss-nelioab-cache-notice').on('click', function() {
				$.post( ajaxurl, {action:'nelioab_dismiss_cache_notice'} );
				$(this).parent().parent().fadeOut();
			});
		})(jQuery);
		</script>
	</div>
	<?php
}
if ( get_option( 'nelioab_cache_notice', false ) !== NELIOAB_PLUGIN_VERSION  )
	add_action( 'admin_notices', 'nelioab_add_cache_notice' );


/**
 * PHPDOC
 *
 * @return void
 *
 * @since PHPDOC
 */
function nelioab_dismiss_cache_notice() {
	update_option( 'nelioab_cache_notice', NELIOAB_PLUGIN_VERSION );
	die();
}
add_action( 'wp_ajax_nelioab_dismiss_cache_notice', 'nelioab_dismiss_cache_notice' );


/**
 * This function returns the URL of the given resource, appending the current
 * version of the plugin. The resource has to be a file in NELIOAB_ASSETS_DIR
 *
 * @param string  $resource        PHPDOC
 * @param boolean $include_version PHPDOC
 *                                 Default: `true`.
 *
 * @return string PHPDOC
 *
 * @since PHPDOC
 */
function nelioab_asset_link( $resource, $include_version = true ) {
	$link = NELIOAB_ASSETS_URL . $resource;
	if ( $include_version )
		$link = esc_url( add_query_arg( array( 'ver' => NELIOAB_PLUGIN_VERSION ), $link ) );
	return $link;
}


/**
 * This function returns the URL of the given resource, appending the current
 * version of the plugin. The resource has to be a file in NELIOAB_ASSETS_DIR
 *
 * @param string  $resource        PHPDOC
 * @param boolean $include_version PHPDOC
 *                                 Default: `true`.
 *
 * @return string PHPDOC
 *
 * @since PHPDOC
 */
function nelioab_admin_asset_link( $resource, $include_version = true ) {
	return nelioab_asset_link( '/admin' . $resource, $include_version );
}


/**
 * Real one time nonces
 *
 * @param string|int $action PHPDOC
 *
 * @return string PHPDOC
 *
 * @since PHPDOC
 */
function nelioab_onetime_nonce( $action = -1 ) {
	$time = time();
	$nonce = wp_create_nonce( $time . $action );
	return $nonce . '-' . $time;
}


/**
 * PHPDOC
 *
 * @param string $action PHPDOC
 * @param string $url    PHPDOC
 * @param string $name   PHPDOC
 *                       Default: `_nonce`.
 *
 * @return string PHPDOC
 *
 * @since PHPDOC
 */
function nelioab_onetime_nonce_url( $url, $action, $name = '_nonce' ) {
	return esc_url( add_query_arg( $name, $action, $url ) );
}


/**
 * PHPDOC
 *
 * @param string     $_nonce PHPDOC
 * @param string|int $action PHPDOC
 *
 * @return boolean PHPDOC
 *
 * @since PHPDOC
 */
function nelioab_verify_onetime_nonce( $_nonce, $action = -1) {
	// Extract timestamp and nonce part of $_nonce
	$parts = explode( '-', $_nonce );
	$nonce = $parts[0]; // Original nonce generated by WordPress.
	$gen_time = $parts[1]; // Time when generated
	$nonce_life = 30 * 60; // We want these nonces to have a 30min lifespan
	$expires = (int) $gen_time + $nonce_life;
	$time = time(); // Current time

	// Verify the nonce part and check that it has not expired
	if( ! wp_verify_nonce( $nonce, $gen_time . $action ) || $time > $expires )
		return false;

	// Get used nonces
	$used_nonces = get_option('nelioab_used_nonces');

	// Nonce already used.
	if ( isset( $used_nonces[$nonce] ) )
		return false;

	if ( is_array( $used_nonces ) ) {
		foreach ( $used_nonces as $aux_nonce => $aux_expiration_date ) {
			if ( $aux_expiration_date > $time )
				break;
			// This nonce has expired, so we don't need to keep it any longer
			unset( $used_nonces[$aux_nonce] );
		}
	}
	else {
		$used_nonces = array();
	}

	// Add nonce to used nonces and sort
	$used_nonces[$nonce] = $expires;
	asort( $used_nonces );
	update_option( 'nelioab_used_nonces', $used_nonces );
	return true;
}


/**
 * This function always returns the REAL page on front.
 *
 * @return int PHPDOC
 *
 * @since PHPDOC
 */
function nelioab_get_page_on_front() {
	global $nelioab_controller;
	$hook = has_filter( 'option_page_on_front', array( $nelioab_controller, 'fix_page_on_front' ) );
	if ( false !== $hook )
		remove_filter( 'option_page_on_front', array( $nelioab_controller, 'fix_page_on_front' ) );
	$res = get_option( 'page_on_front', 0 );
	if ( false !== $hook )
		add_filter( 'option_page_on_front', array( $nelioab_controller, 'fix_page_on_front' ) );
	return $res;
}

/**
 * TODO
 *
 * @return string TODO.
 *
 * @since 4.4.0
 */
function nelioab_get_time() {

	if ( function_exists( 'date_default_timezone_set' ) &&
		   function_exists( 'date_default_timezone_get' ) ) {
		$tz = date_default_timezone_get();
		date_default_timezone_set( 'UTC' );
		$date = date( 'Y-m-d\TH:i:s.000\Z' );
		date_default_timezone_set( $tz );
	} else {
		$date = date( 'Y-m-d\TH:i:s.000\Z' );
	}//end if

	return $date;

}//end nelioab_get_time()
