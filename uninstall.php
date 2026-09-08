<?php
/**
 * Runs when the plugin is deleted from the Plugins screen. Removes the only
 * thing it ever stored: the per-user `oad_scheme` preference.
 *
 * @package OnygoAdminDark
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_metadata( 'user', 0, 'oad_scheme', '', true );
