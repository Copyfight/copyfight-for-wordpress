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

class Copyfight
{
    private static $initiated = false;

    public static function init() {
        if ( !self::$initiated ) {
            self::init_hooks();
        }
    }

    /**
     * Initializes WordPress hooks
     */
    private static function init_hooks() {
        self::$initiated = true;

        add_action( 'wp_head', array( COPYFIGHT_CLASS, 'copyfight_search_engine_visiblity' ), 2 );
        add_action( 'wp_ajax_nopriv_copyfight_ajax', array( COPYFIGHT_CLASS, 'ajax' ) );
        add_action( 'wp_ajax_copyfight_ajax', array( COPYFIGHT_CLASS, 'ajax' ) );

        add_filter( 'the_content', array( COPYFIGHT_CLASS, 'copyfight_content' ) );
        add_filter( 'the_content_feed', array( COPYFIGHT_CLASS, 'copyfight_the_content_feed' ) );
        add_filter( 'the_excerpt_rss', array( COPYFIGHT_CLASS, 'copyfight_the_excerpt_rss' ) );
        add_filter( 'language_attributes', array( COPYFIGHT_CLASS, 'copyfight_language_attributes' ) );

        add_shortcode( 'copyfight', array( COPYFIGHT_CLASS, 'copyfight_shortcode' ) );

        wp_register_style( 'copyfight', COPYFIGHT_PLUGIN_URL . '_inc/css/copyfight.min.css', array(), COPYFIGHT_VERSION );
        wp_enqueue_style( 'copyfight' );

        wp_dequeue_style( 'genericons' );
        wp_deregister_style( 'genericons' );
        wp_register_style( 'genericons', COPYFIGHT_PLUGIN_URL . '_inc/css/genericons.min.css', array(), COPYFIGHT_VERSION );
        wp_enqueue_style( 'genericons' );
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

    public static function copyfight_search_engine_visiblity() {

        global $post;

        if ($post) {
            $copyfight_content = get_post_meta( $post->ID, '_copyfight_content', true );
            $copyfight_status = get_post_meta( $post->ID, '_copyfight_status', true );

            if ( strlen( $copyfight_content ) && $copyfight_status == 'enabled' && is_singular() ) {
                echo '<meta name="generator" content="Copyfight" />' . "\n";
                echo '<meta name="google" content="notranslate" />' . "\n";
                $copyfight_sev = get_option( 'copyfight_sev' );
                if ( $copyfight_sev == 'false' ) {
                    echo '<meta name="robots" content="noindex,nofollow,noarchive" />' . "\n";
                    echo '<meta name="googlebot" content="noindex,nofollow,noarchive,noodp,nosnippet" />' . "\n";
                    echo '<meta name="slurp" content="noindex,nofollow,noarchive,noodp,noydir" />' . "\n";
                    echo '<meta name="msnbot" content="noindex,nofollow,noarchive,noodp" />' . "\n";
                    echo '<meta name="teoma" content="noindex,nofollow,noarchive" />' . "\n";
                }
            }
        }
    }

    public static function copyfight_content( $content ) {

        global $post;
        $copyfight_api_key = get_option( 'copyfight_api_key' );
        $copyfight_content = get_post_meta( $post->ID, '_copyfight_content', true );
        $copyfight_status = get_post_meta( $post->ID, '_copyfight_status', true );

        if ( strlen( $copyfight_api_key ) && strlen( $copyfight_content ) && $copyfight_status == 'enabled' && is_singular() ) {

            $content = $copyfight_content;

            //blurred lines
            $copyfight_blur = get_post_meta( $post->ID, '_copyfight_blur', true );
            if ( !empty( $copyfight_blur ) && $copyfight_blur == 'enabled' && !is_user_logged_in() ) {
                $content = '<p class="copyfight_blurred_lines">' . $content . '</p>';
            }

            //content delivery network
            $copyfight_cdn = get_option( 'copyfight_cdn' );
            if ( empty( $copyfight_cdn ) || $copyfight_cdn == 'false' ) {
                require_once( COPYFIGHT_PLUGIN_DIR . 'class.copyfight-admin.php' );
                call_user_func( COPYFIGHT_CLASS_ADMIN . '::copyfight_save_fonts', $post->ID, false );
            }

            //common font
            $copyfight_hash = get_post_meta( $post->ID, '_copyfight_hash', true );
            $subdir = substr( $copyfight_hash, 0, 1 );
            if ( !file_exists( COPYFIGHT_CACHE . $subdir . '/' . $copyfight_hash . '.woff' ) ) {
                $local_fonts_available = false;
            } else {
                $local_fonts_available = true;
            }

            //uncommon font
            $copyfight_typeface = get_post_meta( $post->ID, '_copyfight_typeface', true );
            if ( $local_fonts_available ) {
                $typeface_filename = substr( $copyfight_typeface, 0, -4 );
                $path_parts = pathinfo( $typeface_filename );
                $typeface_filename = $path_parts['filename'];
                $subdir = $path_parts['dirname'];
                if ( !file_exists( COPYFIGHT_CACHE . $subdir . '/' . $typeface_filename . '.woff' ) ) {
                    $local_fonts_available = false;
                } else {
                    $local_fonts_available = true;
                }
            }

            $copyfight_cdn = get_option( 'copyfight_cdn' );
            if ( $local_fonts_available && ( empty( $copyfight_cdn ) || $copyfight_cdn == 'false') ) {
                wp_register_style( 'copyfight-cdn', COPYFIGHT_PLUGIN_URL . '_inc/css/copyfight.php?hash=' . $copyfight_hash . '&font=' . $copyfight_typeface,
                    array(), COPYFIGHT_VERSION );
            } else {
                wp_register_style( 'copyfight-cdn', COPYFIGHT_CDN . 'copyfight.php?hash=' . $copyfight_hash . '&font=' . $copyfight_typeface,
                    array(), COPYFIGHT_VERSION );
            }
            wp_enqueue_style( 'copyfight-cdn' );

            //template
            $tpl  = '';
            $tpl .= '<!--googleoff: anchor-->';
            $copyfight_fouc = get_option( 'copyfight_fouc' );
            $copyfight_select = get_option( 'copyfight_select' );
            if ( !empty( $copyfight_fouc ) && $copyfight_fouc == 'true' ) {
                if ( !empty( $copyfight_select ) && $copyfight_select == 'false' ) {
                    $tpl .= '<div style="display:none;" id="copyfight_content" class="copyfight_noselect robots-nocontent"><noindex>{content}</noindex></div>';
                } else {
                    $tpl .= '<div style="display:none;" id="copyfight_content" class="robots-nocontent"><noindex>{content}</noindex></div>';
                }
            } else {
                if ( !empty( $copyfight_select ) && $copyfight_select == 'false' ) {
                    $tpl .= '<div id="copyfight_content" class="copyfight_noselect robots-nocontent"><noindex>{content}</noindex></div>';
                } else {
                    $tpl .= '<div id="copyfight_content" class="robots-nocontent"><noindex>{content}</noindex></div>';
                }
            }
            $tpl .= '<!--googleon: anchor-->';


            //copyfight print
            $copyfight_print = get_option( 'copyfight_print' );
            if ( !empty( $copyfight_print ) && $copyfight_print == 'true' ) {
                wp_register_style( 'copyfight-print', COPYFIGHT_PLUGIN_URL . '_inc/css/print.min.css', array(), COPYFIGHT_VERSION );
                wp_enqueue_style( 'copyfight-print');
            } else {
                wp_register_style( 'copyfight-noprint', COPYFIGHT_PLUGIN_URL . '_inc/css/noprint.min.css', array(), COPYFIGHT_VERSION );
                wp_enqueue_style( 'copyfight-noprint');
                $tpl .= '<div id="copyright_print_notice">' .
                    __( 'There is no content because this is a <a target="_blank" href="https://getcopyfight.com/">Copyfight</a> protected article.', 'copyfight' ) . '</div>';
            }

            //copyright notice
            $copyfight_copyright = get_option( 'copyfight_copyright' );
            if ( strlen( $copyfight_copyright ) > 0 ) {
                $tpl .= '<div id="copyright_notice">' . $copyfight_copyright . '</div>';
            }

            //copyfight entry links
            $tpl .= '<span id="copyfight_entry_links">';
            $tpl .= '   <a href="' . COPYFIGHT_HOME . '" target="_blank"><img src="' . COPYFIGHT_PLUGIN_URL .
                '_inc/img/copyfight-logo-dark.svg" alt="' . __('Protected by Copyfight', 'copyfight') . '"></a>';
            $tpl .= '   <a class="copyfight_unselect_link">Unselect</a>';
            $tpl .= '   <a class="copyfight_copy_link">Copy</a>';
            $tpl .= '   <a class="copyfight_download_link">Download</a>';
            if ( !empty( $copyfight_print ) && $copyfight_print == 'true' ) {
                $tpl .= '   <a class="copyfight_print_link">Print</a>';
            }
            $tpl .= '</span>';

            //copyfight copy
            $tpl .= '<input id="copyfight_copy" type="hidden" value="">';

            $copyfight_excerpt = get_option( 'copyfight_excerpt' );
            if ( !empty( $copyfight_excerpt ) && $copyfight_excerpt == 'true' && strlen( $post->post_excerpt ) > 0 ) {
                $tpl = '<div class="copyfight_excerpt">' . $post->post_excerpt . '</div>' . $tpl;
            }

            $content = str_replace('{content}', $content, $tpl);

            wp_register_script( 'copyfight', COPYFIGHT_PLUGIN_URL . '_inc/js/copyfight.min.js',
                array( 'jquery' ), COPYFIGHT_VERSION );

            //wp_localize_script
            $wp_localize_script = array();
            $wp_localize_script['ajax_url'] = admin_url( 'admin-ajax.php' );

            //right click
            $copyfight_right_click = get_option( 'copyfight_right_click' );
            if ( !empty( $copyfight_right_click ) && $copyfight_right_click == 'false' ) {
                $wp_localize_script['copyfight_right_click'] = 'false';
            }

            //text selection
            $copyfight_select = get_option( 'copyfight_select' );
            $copyfight_mapping = get_post_meta( $post->ID, '_copyfight_mapping', true );
            if ( !empty( $copyfight_select ) && $copyfight_select == 'true' && !empty( $copyfight_mapping ) ) {
                $wp_localize_script['copyfight_select'] = 'true';
            }

            //printscreen
            $copyfight_printscreen = get_option( 'copyfight_printscreen' );
            if ( !empty( $copyfight_printscreen ) && $copyfight_printscreen == 'false' ) {
                $wp_localize_script['copyfight_printscreen'] = 'false';
            }

            //console
            $copyfight_console = get_option( 'copyfight_console' );
            if ( !empty( $copyfight_console ) && $copyfight_console == 'false' ) {
                $wp_localize_script['copyfight_console'] = 'false';
                $wp_localize_script['console_message'] = __('Protected by Copyfight', 'copyfight') . '. ' . get_option( 'copyfight_copyright' );
            }

            wp_localize_script( 'copyfight', 'copyfight', $wp_localize_script );
            wp_localize_script( 'copyfight', 'post', array( 'id' => $post->ID ) );

            wp_enqueue_script( 'copyfight' );

        } else {

            $copyfight_excerpt = get_option( 'copyfight_excerpt' );
            if ( !empty( $copyfight_excerpt ) && $copyfight_excerpt == 'true' && strlen( $post->post_excerpt ) > 0 ) {
                $content = $post->post_excerpt;
            }

        }

        return $content;
    }

    public static function copyfight_the_content_feed( $content ) {
        global $post;
        $copyfight_content = get_post_meta( $post->ID, '_copyfight_content', true );
        if ( strlen( $copyfight_content ) ) {
            $content = __( 'There is no content because this is a <a target="_blank" href="https://getcopyfight.com/">Copyfight</a> protected article.', 'copyfight' );
        }
        return $content;
    }

    public static function copyfight_the_excerpt_rss( $content ) {
        global $post;
        $copyfight_content = get_post_meta( $post->ID, '_copyfight_content', true );
        if ( strlen( $copyfight_content ) ) {
            $content = __( 'There is no content because this is a <a target="_blank" href="https://getcopyfight.com/">Copyfight</a> protected article.', 'copyfight' );
        }
        return $content;
    }

    public static function copyfight_language_attributes( $content ) {
        global $post;
        $copyfight_content = get_post_meta( $post->ID, '_copyfight_content', true );
        $copyfight_status = get_post_meta( $post->ID, '_copyfight_status', true );
        if ( strlen( $copyfight_content ) && $copyfight_status == 'enabled' && is_singular() ) {
            $content = '';
        }
        return $content;
    }

    /**
     * Copyfight shortcode
     */
    public static function copyfight_shortcode( $atts, $content = null ) {
        return $content;
    }

    public static function ajax() {

        $response = '';

        //get selected text
        if ( !empty( $_POST['selections'] ) && !empty( $_POST['postid'] ) ) {

            $copyfight_mapping = (array) get_post_meta( $_POST['postid'], '_copyfight_mapping', true );

            $selections = array();
            foreach ( $_POST['selections'] as $index => $selection ) {
                $selections[] = $selection;
            }
            $string = implode( ';', $selections );

            $json = json_encode( $string );
            $string = str_replace( '"', '', $json) ;
            $string = str_replace( '\u', ';', $string );
            $unicodes = preg_split( "/( |;)/", $string );

            foreach ( $unicodes as $unicode ) {
                $unicode = strtoupper( $unicode );
                if ( array_key_exists( $unicode, $copyfight_mapping ) ) {
                    for ( $i = 0; $i < strlen( $copyfight_mapping[$unicode] ); $i = $i + 4 ) {
                        $response .= json_decode( '"\u' . substr( $copyfight_mapping[$unicode], $i, 4 ) . '"' );
                    }
                } else {
                    $response .= ' ';
                }
            }

            $response = trim( preg_replace( '/ +/', ' ', $response ) );

            //text selection length
            $copyfight_select_length = intval( get_option('copyfight_select_length') );
            $length = strlen( $response );
            $response = substr( $response, 0, $copyfight_select_length );
            $response = trim( $response );

            if ( $length > $copyfight_select_length ) {
                $response .= '...';
            }

            $copyfight_copyright = get_option( 'copyfight_copyright' );
            if ( strlen( $copyfight_copyright ) > 0 ) {
                $response .= ' ' . $copyfight_copyright;
            }

            die( $response );
        }

        return false;

    }
}

