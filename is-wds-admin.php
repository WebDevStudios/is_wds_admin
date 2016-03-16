<?php
/**
 * Plugin Name: is_wds_admin
 * Description: Adds a custom capability and some helper functions to determine if the current user is a privileged WDS user. NOT TO BE INSTALLED AS A NORMAL PLUGIN.
 * Version:     1.0.0
 * Author:      WebDevStudios
 * Author URI:  http://webdevstudios.com
 * License:     GPLv2
 */

/**
 * Copyright (c) 2016 WebDevStudios (email : contact@webdevstudios.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


/**
 * Main initiation class
 *
 * @since  NEXT
 * @var  string $version  Plugin version
 * @var  string $basename Plugin basename
 * @var  string $url      Plugin URL
 * @var  string $path     Plugin Path
 */
class Is_WDS_Admin {

	/**
	 * Current version
	 *
	 * @var  string
	 * @since  NEXT
	 */
	const VERSION = '1.0.0';

	/**
	 * Plugin basename
	 *
	 * @var string
	 * @since  NEXT
	 */
	protected $basename = '';

	/**
	 * Priveleged username
	 *
	 * @var string
	 * @since  NEXT
	 */
	protected $priveleged_user = '';

	/**
	 * Singleton instance of plugin
	 *
	 * @var Is_WDS_Admin
	 * @since  NEXT
	 */
	protected static $single_instance = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  NEXT
	 * @return Is_WDS_Admin A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin
	 *
	 * @since  NEXT
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->priveleged_user = 'wds_admin'; // This can be whatever user is the priveleged user.
	}

	/**
	 * Add hooks and filters
	 *
	 * @since  NEXT
	 * @return void
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Init hooks
	 *
	 * @since  NEXT
	 * @return void
	 */
	public function init() {

	}

	/**
	 * Check if the current user is the priveleged user.
	 *
	 * @since NEXT
	 * @return bool True/false depending if the current user is the one defined in the __construct as the priveleged user.
	 */
	private function is_priveleged_user() {
		$current_user = wp_get_current_user();

		// Check if the current user is 'wds_admin'.
		if ( ! is_wp_error( $current_user ) ) {
			return ( $this->priveleged_user == $current_user->user_login );
		}
		return false;
	}

	/**
	 * Add the 'is_wds_admin' capability if it doesn't exist.
	 *
	 * @since NEXT
	 */
	public function add_cap_if_not_exists() {
		// Check to see if the current user is defined as the priveleged user and if they don't already have the 'is_wds_admin' capability.
		if ( $this->is_priveleged_user() && ! current_user_can( 'is_wds_admin' ) ) {
			$user = new WP_User( get_current_user_id() ); // Get the WP_User object.
			$user->add_cap( 'is_wds_admin' );             // Add the cap.
		}
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  NEXT
	 * @param string $field Field to get.
	 * @throws Exception Throws an exception if the field is invalid.
	 * @return mixed
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
				return $this->$field;
			default:
				throw new Exception( 'Invalid '. __CLASS__ .' property: ' . $field );
		}
	}
}

/**
 * Grab the Is_WDS_Admin object and return it.
 * Wrapper for Is_WDS_Admin::get_instance()
 *
 * @since  NEXT
 * @return Is_WDS_Admin  Singleton instance of plugin class.
 */
function is_wds_admin() {
	$wds_admin = Is_WDS_Admin::get_instance();
	return $wds_admin->is_wds_admin();
}

// Kick it off.
add_action( 'plugins_loaded', array( is_wds_admin(), 'hooks' ) );
