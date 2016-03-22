<?php
/**
Plugin Name:        Copyfight
Plugin URI:         https://getcopyfight.com/
Description:        Copyright protection
Version:            1.3.5
Author:             Copyfight
Author URI:         https://getcopyfight.com/
First authors:      Bryan Salter, Martha Alvarado and Arthur Dragoo
Main contributors:  Christopher Starke, Roman Macek, Marcus Juhl, Farhan Burns, Gerasimos Sozonov, Uno Dahlberg, Jason Hamburg, Amanda Seltzer, Eduard Stula
Text Domain:        copyfight
Domain Path:        /languages
License:            GPL
License URI:        https://www.gnu.org/licenses/gpl-2.0.html

Copyright 2016 at getcopyfight.com (email: info@getcopyfight.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

defined('ABSPATH') or die();

load_plugin_textdomain( 'copyfight', false, basename(dirname(__FILE__)) . '/languages' );

define( 'COPYFIGHT_VERSION', '1.3.5' );
define( 'COPYFIGHT_MINIMUM_WP_VERSION', '3.2' );
define( 'COPYFIGHT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'COPYFIGHT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'COPYFIGHT_CACHE', COPYFIGHT_PLUGIN_DIR . 'cache/' );
define( 'COPYFIGHT_HOME', 'https://getcopyfight.com/' );
define( 'COPYFIGHT_CLASS', 'Copyfight' );
define( 'COPYFIGHT_CLASS_ADMIN', 'Copyfight_Admin' );
define( 'COPYFIGHT_FONTLIST', COPYFIGHT_PLUGIN_DIR . 'cache/fontlist.txt' );
define( 'API_HOST', 'https://getcopyfight.com/' );
define( 'COPYFIGHT_CDN', 'https://getcopyfight.com/wp-content/plugins/copyfight-api/cdn/' );

register_activation_hook( __FILE__, array( COPYFIGHT_CLASS_ADMIN, 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( COPYFIGHT_CLASS_ADMIN, 'plugin_deactivation' ) );

require_once( COPYFIGHT_PLUGIN_DIR . 'class.copyfight.php' );
add_action( 'init', array( COPYFIGHT_CLASS, 'init' ) );

if ( is_admin() ) {
    require_once( COPYFIGHT_PLUGIN_DIR . 'class.copyfight-admin.php' );
    add_action( 'init', array( COPYFIGHT_CLASS_ADMIN, 'init' ) );
}

/* Updater */

if ( !class_exists( 'Copyfight_Update' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . 'update.php' );
}

$updater = new Copyfight_Update( __FILE__ );
$updater->set_username( 'Copyfight' );
$updater->set_repository( 'copyfight-for-wordpress' );
$updater->initialize();