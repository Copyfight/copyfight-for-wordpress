<?php

/**
Copyright 2018 at getcopyfight.com (email: info@getcopyfight.com)
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

if ( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete options
delete_option( 'copyfight_api_key' );
delete_option( 'copyfight_newsletter' );
delete_option( 'copyfight_status' );
delete_option( 'copyfight_tags' );
delete_option( 'copyfight_blur' );
delete_option( 'copyfight_typeface' );
delete_option( 'copyfight_excerpt' );
delete_option( 'copyfight_fouc' );
delete_option( 'copyfight_select' );
delete_option( 'copyfight_select_length' );
delete_option( 'copyfight_sev' );
delete_option( 'copyfight_copyright' );
delete_option( 'copyfight_cdn' );
delete_option( 'copyfight_protocol' );
delete_option( 'copyfight_debugging' );
delete_option( 'copyfight_right_click' );
delete_option( 'copyfight_print' );
delete_option( 'copyfight_printscreen' );
delete_option( 'copyfight_settings' );
delete_option( 'copyfight_console' );