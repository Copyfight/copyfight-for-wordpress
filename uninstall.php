<?php

if ( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete options from options table
delete_option( 'copyfight_typeface' );
delete_option( 'copyfight_api_key' );
