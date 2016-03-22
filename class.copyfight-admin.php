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

class Copyfight_Admin
{
    private static $initiated = false;

    public static function plugin_activation() {

    }

    public static function plugin_deactivation() {

    }

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

        wp_register_style( 'copyfight-admin', COPYFIGHT_PLUGIN_URL . '_inc/copyfight-admin.css', array(), COPYFIGHT_VERSION );
        wp_enqueue_style( 'copyfight-admin');

        wp_register_script( 'copyfight-admin', COPYFIGHT_PLUGIN_URL . '_inc/copyfight-admin.js', array('jquery'), COPYFIGHT_VERSION );
        wp_localize_script( 'copyfight-admin', 'copyfight', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'noticeEmptyApiKey' => __( 'Error: empty API Key field', 'copyfight' ),
            'refurl' => COPYFIGHT_HOME . '?' . substr(get_option( 'copyfight_api_key' ), 0, 8 )
        ));
        wp_enqueue_script( 'copyfight-admin' );
        add_action( 'wp_ajax_nopriv_copyfight_ajax', array( COPYFIGHT_CLASS_ADMIN, 'ajax' ) );
        add_action( 'wp_ajax_copyfight_ajax', array( COPYFIGHT_CLASS_ADMIN, 'ajax' ) );

        add_action( 'admin_init', array( COPYFIGHT_CLASS_ADMIN, 'register_settings' ) );
        add_filter( 'pre_update_option_copyfight_api_key', array( COPYFIGHT_CLASS_ADMIN, 'activate_copyfight' ), 10, 2 );
        add_action( 'admin_menu', array( COPYFIGHT_CLASS_ADMIN, 'load_menu' ) );
        add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __FILE__ ) . 'copyfight.php'), array( COPYFIGHT_CLASS_ADMIN, 'admin_plugin_settings_link' ) );

        add_action( 'manage_posts_custom_column', array( COPYFIGHT_CLASS_ADMIN, 'display_posts_copyfight'), 10, 2 );
        add_action( 'manage_pages_custom_column', array( COPYFIGHT_CLASS_ADMIN, 'display_posts_copyfight'), 10, 2 );
        add_filter( 'manage_posts_columns', array( COPYFIGHT_CLASS_ADMIN, 'add_copyfight_column') );
        add_filter( 'manage_pages_columns', array( COPYFIGHT_CLASS_ADMIN, 'add_copyfight_column') );

        add_action( 'save_post', array( COPYFIGHT_CLASS_ADMIN, 'copyfight_save_post' ), 100, 1 );

        add_action( 'add_meta_boxes', array( COPYFIGHT_CLASS_ADMIN, 'copyfight_add_meta_box' ) );
        add_action( 'save_post', array( COPYFIGHT_CLASS_ADMIN, 'copyfight_save_meta_box_data' ) );

        add_action( 'admin_notices', array( COPYFIGHT_CLASS_ADMIN, 'display_notice' ) );

    }

    public static function register_settings() {
        register_setting( 'copyfight_options_group', 'copyfight_api_key' );
        register_setting( 'copyfight_options_group', 'copyfight_newsletter' );
        register_setting( 'copyfight_options_group', 'copyfight_typeface' );
    }

    public static function admin_plugin_settings_link( $links ) {
        $settings_link = '<a href="' . esc_url( self::get_page_url() ) . '">' . __( 'Settings', 'copyfight' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    public static function load_menu() {
        $hook = add_options_page( __('Copyfight', 'copyfight'), __('Copyfight', 'copyfight'), 'manage_options', 'copyfight', array( COPYFIGHT_CLASS_ADMIN, 'copyfight_configuration_page' ) );
        add_action( "load-$hook", array( COPYFIGHT_CLASS_ADMIN, 'admin_help' ) );
    }

    public static function copyfight_configuration_page() {
        self::display_configuration_page();
    }

    public static function display_configuration_page() {
        call_user_func( COPYFIGHT_CLASS . '::view', 'config');
    }

    public static function display_notice() {
        global $hook_suffix;
        $copyfight_api_key = get_option( 'copyfight_api_key' );
        if ( $hook_suffix == 'plugins.php' && strlen( $copyfight_api_key ) == 0 ) {
            self::display_api_key_warning();
        } elseif ( strlen( $copyfight_api_key ) == 0 ) {
            self::display_api_key_warning();
        }
    }

    public static function display_api_key_warning() {
        call_user_func( COPYFIGHT_CLASS . '::view', 'notice');
    }

    /**
     * Activate Copyfight by API Key or email, hook: pre_update_option_copyfight_api_key
     */
    public static function activate_copyfight( $new_value, $old_value ) {

        if ( $new_value == $old_value || !strlen( $new_value ) ) {
            return $new_value;
        }

        $copyfight_newsletter = !empty($_POST['copyfight_newsletter']) ? $_POST['copyfight_newsletter'] : false;

        $body = array(
            'new_value'             => $new_value,
            'old_value'             => $old_value,
            'copyfight_newsletter'  => $copyfight_newsletter,
            'copyfight_site_url'    => get_site_url()
        );
        $url = API_HOST . 'wp-json/api/v1/users';

        $response = call_user_func( COPYFIGHT_CLASS_ADMIN . '::api_post', $url, $body );

        if ( $response->status ) {
            return $response->apikey;
        } else {
            return '';
        }
    }

    public static function display_posts_copyfight( $column, $post_id ) {
        if ($column == 'copyfight') {
            echo '<input type="checkbox" disabled', call_user_func(COPYFIGHT_CLASS_ADMIN . '::is_copyfight_enabled', $post_id) ? ' checked' : '', '/>';
        }
    }

    public static function add_copyfight_column( $columns ) {
        return array_merge( $columns,
            array( 'copyfight' => __( 'Copyfight', 'copyfight' ) ) );
    }

    public static function is_copyfight_enabled( $post_id ) {
        $status = get_post_meta( $post_id, '_copyfight_status', true );
        $status = ( $status == 'enabled' ) ? true : false;
        return $status;
    }

    public static function copyfight_add_meta_box() {
        add_meta_box('copyfight_post', __('Copyfight', 'copyfight'), array(COPYFIGHT_CLASS_ADMIN, 'copyfight_meta_box_callback'), 'post');
        add_meta_box('copyfight_page', __('Copyfight', 'copyfight'), array(COPYFIGHT_CLASS_ADMIN, 'copyfight_meta_box_callback'), 'page');
    }

    public static function copyfight_meta_box_callback( $post ) {
        wp_nonce_field( 'copyfight_save_meta_box_data', 'copyfight_meta_box_nonce' );
        $options = array( 'Enabled', 'Disabled' );
        $status = get_post_meta( $post->ID, '_copyfight_status', true );

        echo '<select name="copyfight_status">';
        foreach ( $options as $option ) {
            if ( strtolower( $option ) == $status ) {
                echo '  <option selected value="' . strtolower( $option ) . '">' . $option . '</option>';
            } else {
                echo '  <option value="' . strtolower( $option ) . '">' . $option . '</option>';
            }
        }
        echo '</select>';
    }

    public static function copyfight_save_meta_box_data( $post_id ) {
        if ( !isset( $_POST['copyfight_meta_box_nonce'] ) ) {
            return;
        }
        if ( !wp_verify_nonce( $_POST['copyfight_meta_box_nonce'], 'copyfight_save_meta_box_data' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
            if ( !current_user_can( 'edit_page', $post_id ) ) {
                return;
            }
        } else {
            if ( !current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }
        if ( !isset( $_POST['copyfight_status'] ) ) {
            return;
        }
        $copyfight_status = sanitize_text_field( $_POST['copyfight_status'] );
        update_post_meta( $post_id, '_copyfight_status', $copyfight_status );
    }

    /**
     * Create Copyfight version of content
     */
    public static function copyfight_save_post( $post_id ) {

        $post = get_post( $post_id );

        if ( $post->post_status !== 'publish' ) {
            return;
        }

        $copyfight_typeface = get_option( 'copyfight_typeface' );
        $copyfight_api_key = get_option( 'copyfight_api_key' );

        $body = array(
            'post_title'            => $post->post_title,
            'post_name'             => $post->post_name,
            'post_content'          => $post->post_content,
            'post_excerpt'          => $post->post_excerpt,
            'post_date'             => $post->post_date,
            'post_date_gmt'         => $post->post_date_gmt,
            'post_modified'         => $post->post_modified,
            'post_modified_gmt'     => $post->post_modified_gmt,
            'copyfight_typeface'    => $copyfight_typeface,
            'copyfight_api_key'     => $copyfight_api_key,
            'copyfight_site_url'    => get_site_url(),
            'copyfight_guid'        => $post->guid
        );

        $url = API_HOST . 'wp-json/api/v1/posts';
        $response = call_user_func( COPYFIGHT_CLASS_ADMIN . '::api_post', $url, $body );

        if ( !empty( $response->status ) && $response->status && strlen( $response->content ) ) {
            update_post_meta( $post_id, '_copyfight_typeface', $copyfight_typeface );
            update_post_meta( $post_id, '_copyfight_content', $response->content );
            update_post_meta( $post_id, '_copyfight_hash', $response->hash );

            update_post_meta( $post_id, '_copyfight_keywords', $response->keywords );
            $keywords = array();
            foreach ( (array) $response->keywords as $keyword => $frequency ) {
                $keywords[] = $keyword;
            }
            wp_set_post_terms( $post_id, $keywords );
        }
    }

    /**
     * Add help to the Copyfight page
     *
     * @return false if not the Copyfight page
     */
    public static function admin_help() {
        $current_screen = get_current_screen();

        if ( current_user_can( 'manage_options' ) ) {

            $current_screen->add_help_tab(
                array(
                    'id' => 'overview',
                    'title' => __('Overview', 'copyfight'),
                    'content' =>
                        '<p><strong>' . esc_html__('Copyfight Configuration', 'copyfight') . '</strong></p>' .
                        '<p>' . esc_html__('Copyfight protects your content, so you can focus on more important things.', 'copyfight') . '</p>' .
                        '<p>' . esc_html__('On this page, you are able to enter/remove an API key, view account information and view stats.', 'copyfight') . '</p>',
                )
            );

            $current_screen->add_help_tab(
                array(
                    'id' => 'settings',
                    'title' => __('Settings', 'copyfight'),
                    'content' =>
                        '<p><strong>' . esc_html__('Copyfight Configuration', 'copyfight') . '</strong></p>' .
                        '<p><strong>' . esc_html__('API Key', 'copyfight') . '</strong> - ' . esc_html__('Enter/remove an API key.', 'copyfight') . '</p>'
                )
            );

            $current_screen->add_help_tab(
                array(
                    'id' => 'account',
                    'title' => __('Account', 'copyfight'),
                    'content' =>
                        '<p><strong>' . esc_html__('Copyfight Configuration', 'copyfight') . '</strong></p>' .
                        '<p><strong>' . esc_html__('Subscription Type', 'copyfight') . '</strong> - ' . esc_html__('The Copyfight subscription plan', 'copyfight') . '</p>' .
                        '<p><strong>' . esc_html__('Status', 'copyfight') . '</strong> - ' . esc_html__('The subscription status - active, cancelled or suspended', 'copyfight') . '</p>',
                )
            );

            $current_screen->set_help_sidebar(
                '<p><strong>' . esc_html__( 'For more information:' , 'copyfight') . '</strong></p>' .
                '<p><a href="' . COPYFIGHT_HOME . 'faq/" target="_blank">'     . esc_html__( 'Copyfight FAQ', 'copyfight') . '</a></p>' .
                '<p><a href="' . COPYFIGHT_HOME . 'support/" target="_blank">' . esc_html__( 'Copyfight Support', 'copyfight') . '</a></p>'
            );
        }
    }

    public static function get_page_url( $page = 'copyfight' ) {
        $args = array( 'page' => $page );
        $url = add_query_arg( $args, admin_url( 'options-general.php' ) );
        return $url;
    }

    public static function get_fonts() {
        $url = API_HOST . 'wp-json/api/v1/fonts';
        $response = call_user_func( COPYFIGHT_CLASS_ADMIN . '::api_get', $url );
        return $response;
    }

    public static function get_font_info( $typeface ) {
        $url = API_HOST . 'wp-json/api/v1/fonts/' . $typeface;
        $response = call_user_func( COPYFIGHT_CLASS_ADMIN . '::api_get', $url );
        return $response;
    }

    public static function api_post( $url, $body ) {

        $args = array(
            'method'        => 'POST',
            'timeout'       => 45,
            'redirection'   => 5,
            'httpversion'   => '1.0',
            'blocking'      => true,
            'headers'       => array(),
            'body'          => $body,
            'cookies'       => array()
        );

        $response = wp_remote_post( $url, $args );

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "An error occurred...: $error_message";
        } else {
            $response = json_decode( $response['body'] );
            return $response;
        }
    }

    public static function api_get( $url ) {

        $response = wp_remote_get( $url );

        if ( is_array( $response ) ) {
            $response = json_decode( $response['body'] );
            return $response;
        } else {
            die( 'An error occurred...' );
        }
    }

    public static function ajax() {

        //get_font_info
        if ( !empty( $_POST['typeface'] ) ) {
            $typeface = explode( '/', $_POST['typeface'] )[0];
            $response = call_user_func( COPYFIGHT_CLASS_ADMIN . '::get_font_info', $typeface );
            $response = stripslashes( $response );
            $response = preg_replace('/<a\s+/', '<a target="_blank" ', $response);
            die( $response );
        }

        return false;
    }

}