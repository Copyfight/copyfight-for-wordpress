<?php

/**
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

class Copyfight
{
    private static $initiated = false;

    public static function init() {
        if ( ! self::$initiated ) {
            self::init_hooks();
        }
    }

    /**
     * Initializes WordPress hooks
     */
    private static function init_hooks() {
        self::$initiated = true;

        add_filter( 'the_content', array( COPYFIGHT_CLASS, 'copyfight_content' ) );
        add_filter( 'the_content_feed', array( COPYFIGHT_CLASS, 'copyfight_the_content_feed' ) );
        add_filter( 'the_excerpt_rss', array( COPYFIGHT_CLASS, 'copyfight_the_excerpt_rss' ) );

        wp_register_style( 'copyfight', COPYFIGHT_PLUGIN_URL . '_inc/copyfight.css', array(), COPYFIGHT_VERSION );
        wp_enqueue_style( 'copyfight');

        wp_register_script( 'copyfight', COPYFIGHT_PLUGIN_URL . '_inc/copyfight.js', array('jquery'), COPYFIGHT_VERSION );
        wp_enqueue_script( 'copyfight' );
    }

    public static function view( $name, array $args = array() ) {
        $args = apply_filters( 'copyfight_view_arguments', $args, $name );
        foreach ( $args AS $key => $val ) {
            $$key = $val;
        }
        load_plugin_textdomain( 'copyfight' );
        $file = COPYFIGHT_PLUGIN_DIR . 'views/' . $name . '.php';
        include( $file );
    }

    public static function copyfight_content( $content ) {

        global $post;
        $copyfight_content = get_post_meta( $post->ID, '_copyfight_content', true );
        $copyfight_status = get_post_meta( $post->ID, '_copyfight_status', true );

        if ( strlen( $copyfight_content ) && $copyfight_status == 'enabled' && is_singular() ) {
            $content = $copyfight_content;
            $copyfight_typeface = get_post_meta( $post->ID, '_copyfight_typeface', true );
            $copyfight_hash = get_post_meta( $post->ID, '_copyfight_hash', true );
            wp_register_style( 'copyfight-cdn', COPYFIGHT_CDN . 'copyfight.php?hash=' . $copyfight_hash . '&font=' . $copyfight_typeface, array(), COPYFIGHT_VERSION );
            wp_enqueue_style( 'copyfight-cdn' );

            $tpl  = '';
            $tpl .= '<!--googleoff: anchor-->';
            $tpl .= '<div class="copyfight copyfight_content robots-nocontent"><noindex>{content}</noindex></div>';
            $tpl .= '<!--googleon: anchor-->';
            $tpl .= '<input id="copyfight_notice" type="hidden" value="There is no content copied because this is a Copyfight protected post.">';
            $content = str_replace('{content}', $content, $tpl);
        }

        return $content;
    }

    public static function copyfight_the_content_feed( $content ) {
        global $post;
        $copyfight_content = get_post_meta( $post->ID, '_copyfight_content', true );
        if ( strlen( $copyfight_content ) ) {
            $content = __( 'There is no content because this is a <a target="_blank" href="https://getcopyfight.com/">Copyfight</a> protected post.' );
        }
        return $content;
    }

    public static function copyfight_the_excerpt_rss( $content ) {
        global $post;
        $copyfight_content = get_post_meta( $post->ID, '_copyfight_content', true );
        if ( strlen( $copyfight_content ) ) {
            $content = __( 'There is no content because this is a <a target="_blank" href="https://getcopyfight.com/">Copyfight</a> protected post.' );
        }
        return $content;
    }

}

