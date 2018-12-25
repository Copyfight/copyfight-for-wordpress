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

class Copyfight_Admin
{
    private static $initiated = false;

    public static function plugin_activation() {

    }

    public static function plugin_deactivation() {

    }

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

        wp_register_style( 'copyfight-admin', COPYFIGHT_PLUGIN_URL .
            '_inc/css/copyfight-admin.min.css', array(), COPYFIGHT_VERSION );
        wp_enqueue_style( 'copyfight-admin' );

        wp_register_script( 'copyfight-admin', COPYFIGHT_PLUGIN_URL .
            '_inc/js/copyfight-admin.min.js', array( 'jquery' ), COPYFIGHT_VERSION );
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
        add_action( 'wp_dashboard_setup', array( COPYFIGHT_CLASS_ADMIN, 'copyfight_dashboard_widget' ), 10, 2 );
        add_action( 'admin_menu', array( COPYFIGHT_CLASS_ADMIN, 'load_menu' ) );

        add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __FILE__ ) . 'copyfight.php' ),
            array( COPYFIGHT_CLASS_ADMIN, 'admin_plugin_settings_link' ) );

        add_action( 'manage_posts_custom_column', array( COPYFIGHT_CLASS_ADMIN, 'display_posts_copyfight' ), 10, 2 );
        add_action( 'manage_pages_custom_column', array( COPYFIGHT_CLASS_ADMIN, 'display_posts_copyfight' ), 10, 2 );
        add_filter( 'manage_posts_columns', array( COPYFIGHT_CLASS_ADMIN, 'add_copyfight_column' ) );
        add_filter( 'manage_pages_columns', array( COPYFIGHT_CLASS_ADMIN, 'add_copyfight_column' ) );

        add_action( 'save_post', array( COPYFIGHT_CLASS_ADMIN, 'copyfight_save_post' ), 100, 1 );

        add_action( 'add_meta_boxes', array( COPYFIGHT_CLASS_ADMIN, 'copyfight_add_meta_box' ) );
        add_action( 'save_post', array( COPYFIGHT_CLASS_ADMIN, 'copyfight_save_meta_box_data' ) );

        add_action( 'admin_notices', array( COPYFIGHT_CLASS_ADMIN, 'copyfight_api_key_notice' ) );
        add_action( 'admin_notices', array( COPYFIGHT_CLASS_ADMIN, 'copyfight_activate_transient' ) );
        add_action( 'admin_notices', array( COPYFIGHT_CLASS_ADMIN, 'copyfight_transient' ) );
    }

    public static function register_settings() {
        register_setting( 'copyfight_options_group', 'copyfight_api_key' );
        register_setting( 'copyfight_options_group', 'copyfight_newsletter' );
        register_setting( 'copyfight_options_group', 'copyfight_status' );
        register_setting( 'copyfight_options_group', 'copyfight_tags' );
        register_setting( 'copyfight_options_group', 'copyfight_blur' );
        register_setting( 'copyfight_options_group', 'copyfight_typeface' );
        register_setting( 'copyfight_options_group', 'copyfight_excerpt' );
        register_setting( 'copyfight_options_group', 'copyfight_fouc' );
        register_setting( 'copyfight_options_group', 'copyfight_select' );
        register_setting( 'copyfight_options_group', 'copyfight_select_length' );
        register_setting( 'copyfight_options_group', 'copyfight_sev' );
        register_setting( 'copyfight_options_group', 'copyfight_copyright' );
        register_setting( 'copyfight_options_group', 'copyfight_cdn' );
        register_setting( 'copyfight_options_group', 'copyfight_protocol' );
        register_setting( 'copyfight_options_group', 'copyfight_debugging' );
        register_setting( 'copyfight_options_group', 'copyfight_right_click' );
        register_setting( 'copyfight_options_group', 'copyfight_print' );
        register_setting( 'copyfight_options_group', 'copyfight_printscreen' );
        register_setting( 'copyfight_options_group', 'copyfight_settings' );
        register_setting( 'copyfight_options_group', 'copyfight_console' );
    }

    public static function admin_plugin_settings_link( $links ) {
        $settings_link = '<a href="' . esc_url( self::get_page_url() ) . '">' . __( 'Settings', 'copyfight' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    public static function load_menu() {
        $hook = add_options_page( 'Copyfight', 'Copyfight', 'manage_options', 'copyfight', array( COPYFIGHT_CLASS_ADMIN, 'copyfight_configuration_page' ) );
        add_action( "load-$hook", array( COPYFIGHT_CLASS_ADMIN, 'admin_help' ) );
        $dashicon = file_get_contents( COPYFIGHT_PLUGIN_DIR . '_inc/img/copyfight-dashicon.data' );
        add_menu_page( 'Copyfight', 'Copyfight', 'manage_options', 'options-general.php?page=copyfight', '', $dashicon, 79 ); // 80 = Settings
    }

    public static function copyfight_configuration_page() {
        call_user_func( COPYFIGHT_CLASS . '::view', 'config' );
    }

    public static function copyfight_api_key_notice() {
        global $hook_suffix;
        $copyfight_api_key = get_option( 'copyfight_api_key' );
        if ( $hook_suffix == 'plugins.php' && strlen( $copyfight_api_key ) == 0 ) {
            self::display_api_key_warning();
        } elseif ( strlen( $copyfight_api_key ) == 0 ) {
            self::display_api_key_warning();
        }
    }

    public static function display_api_key_warning() {
        call_user_func( COPYFIGHT_CLASS . '::view', 'notice' );
    }

    public static function copyfight_activate_transient() {
        $message = get_transient( 'copyfight_activate_transient' );
        delete_transient( 'copyfight_activate_transient' );
        $html = '';
        if ( $message ) {
            $html  = "<div class='error notice is-dismissible'><p>";
            $html .= "{$message}";
            $html .= "</p></div>";
        }
        echo $html;
    }

    public static function copyfight_transient() {
        $post_id = get_transient( 'copyfight_transient' );
        delete_transient( 'copyfight_transient' );
        $html = '';
        if ( $post_id ) {
            $message = __( 'A Copyfight error has occurred, your article is not protected.', 'copyfight' );
            $html  = "<div class='error notice is-dismissible'><p>{$message} ";
            $html .= "<a href='" . get_post_permalink($post_id) . "'>View post</a>";
            $html .= "</p></div>";
        }
        echo $html;
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
            set_transient( 'copyfight_activate_transient', $response->error, 60 );
            return '';
        }
    }

    public static function copyfight_dashboard_widget() {

        wp_add_dashboard_widget(
            'dashboard_copyfight',
            'Copyfight',
            array( COPYFIGHT_CLASS_ADMIN, 'copyfight_dashboard_widget_content' )
        );

    }

    public static function copyfight_dashboard_widget_content() {

        $stats = call_user_func( COPYFIGHT_CLASS_ADMIN . '::copyfight_statistics' );

        $tpl  = '';
        $tpl .= '<div class="main">';
        $tpl .= '   <ul>';
        $tpl .= '       <li class="post-count">' . number_format_i18n( $stats['post'] ) . ' ' . _n( 'Post', 'Posts', $stats['post'], 'copyfight' ) . '</li>';
        $tpl .= '       <li class="page-count">' . number_format_i18n( $stats['page'] ) . ' ' . _n( 'Page', 'Pages', $stats['page'], 'copyfight' ) . '</li>';
        $tpl .= '       <li class="char-count">' . number_format_i18n( $stats['chars'] ) . ' ' . _n( 'Character', 'Characters', $stats['chars'], 'copyfight' ) . '</li>';
        $tpl .= '       <li class="word-count">' . number_format_i18n( $stats['words'] ) . ' ' . _n( 'Word', 'Words', $stats['words'], 'copyfight' ) . '</li>';
        $tpl .= '   </ul>';
	    $tpl .= '   <p id="wp-version-message">';
        $tpl .= '       <span id="wp-version">Copyfight ' . COPYFIGHT_VERSION . '</span> | ';
        $tpl .= '       <span><a href="' . get_admin_url() . 'options-general.php?page=copyfight">' . __( 'Settings', 'copyfight' ) . '</a></span> | ';
        $tpl .= '       <span><a href="' . get_admin_url() . 'widgets.php">' . __( 'Widget', 'copyfight' ) . '</a></span>';
        $tpl .= '   </p>';
        $tpl .= '</div>';

        echo $tpl;
    }

    public static function copyfight_statistics() {
        global $wpdb;
        $stats = array(
            'post'  => 0,
            'page'  => 0,
            'chars' => 0,
            'words' => 0
        );
        $sql = 'SELECT * FROM ' . $wpdb->prefix . 'postmeta WHERE 1 AND meta_key = "%s" AND meta_value = "%s"';
        $query = $wpdb->prepare( $sql, '_copyfight_status', 'enabled' );
        $results = $wpdb->get_results( $query );

        foreach ( $results as $result ) {
            $post = get_post( $result->post_id );
            $content = get_post_meta( $result->post_id, '_copyfight_content', true );
            if ( $post->post_status == 'publish' && strlen( $content ) > 0 ) {
                $stats[$post->post_type] += 1;
                $stats['chars'] += strlen( strip_tags( get_post_field( 'post_content', $result->post_id ) ) );
                $stats['words'] += str_word_count( strip_tags( get_post_field( 'post_content', $result->post_id ) ) );
            }
        }

        return $stats;
    }

    public static function display_posts_copyfight( $column, $post_id ) {
        if ( $column == 'copyfight' ) {
            $status = get_post_meta( $post_id, '_copyfight_status', true );
            if ( $status == 'enabled' ) {
                echo '<input type="checkbox" disabled checked />';
            } else {
                echo '<input type="checkbox" disabled />';
            }
        }
    }

    public static function add_copyfight_column( $columns ) {
        $post_type = get_query_var( 'post_type', false );
        if ( $post_type == 'post' || $post_type == 'page' ) {
            return array_merge( $columns, array( 'copyfight' => 'Copyfight' ) );
        } else {
            return $columns;
        }
    }

    public static function is_copyfight_enabled( $post_id ) {
        $status = get_post_meta( $post_id, '_copyfight_status', true );
        if ( strlen( $status ) ) {
            $status = ( $status == 'disabled' ) ? 'disabled' : 'enabled';
        // get default status setting
        } else {
            $status = get_option( 'copyfight_status' );
        }
        return $status;
    }

    public static function is_tags_enabled( $post_id ) {
        $status = get_post_meta( $post_id, '_copyfight_tags', true );
        if ( strlen( $status ) ) {
            $status = ( $status == 'disabled' ) ? 'disabled' : 'enabled';
        // get default tag setting
        } else {
            $status = get_option( 'copyfight_tags' );
        }
        return $status;
    }

    public static function is_blur_enabled( $post_id ) {
        $status = get_post_meta( $post_id, '_copyfight_blur', true );
        if ( strlen( $status ) ) {
            $status = ( $status == 'disabled' ) ? 'disabled' : 'enabled';
        // get default blur setting
        } else {
            $status = get_option( 'copyfight_blur' );
        }
        return $status;
    }

    public static function copyfight_add_meta_box() {
        add_meta_box( 'copyfight_post', 'Copyfight', array( COPYFIGHT_CLASS_ADMIN, 'copyfight_meta_box_callback' ), 'post', 'side', 'high' );
        add_meta_box( 'copyfight_page', 'Copyfight', array( COPYFIGHT_CLASS_ADMIN, 'copyfight_meta_box_callback' ), 'page', 'side', 'high' );
    }

    public static function copyfight_meta_box_callback( $post ) {
        wp_nonce_field( 'copyfight_save_meta_box_data', 'copyfight_meta_box_nonce' );

        // Status
        $status = call_user_func( COPYFIGHT_CLASS_ADMIN . '::is_copyfight_enabled', $post->ID );
        $options = array( 'enabled' => __( 'Enabled', 'copyfight' ), 'disabled' => __( 'Disabled', 'copyfight' ) );
        echo '<p>';
        echo '<label for="copyfight_status">' . __( 'Status', 'copyfight' ) . ':</label> ';
        echo '<select name="copyfight_status">';
        foreach ( $options as $value => $option ) {
            if ( $value == $status ) {
                echo '  <option selected value="' . $value . '">' . $option . '</option>';
            } else {
                echo '  <option value="' . $value . '">' . $option . '</option>';
            }
        }
        echo '</select>';
        echo '</p>';

        if ( $status == 'enabled' ) {

            // Tags
            $status = call_user_func( COPYFIGHT_CLASS_ADMIN . '::is_tags_enabled', $post->ID );
            $options = array( 'enabled' => __( 'Enabled', 'copyfight' ), 'disabled' => __( 'Disabled', 'copyfight' ));
            echo '<p>';
            echo '<label for="copyfight_tags">' . __( 'Tags', 'copyfight' ) . ':</label> ';
            echo '<select name="copyfight_tags">';
            foreach ($options as $value => $option) {
                if ($value == $status) {
                    echo '  <option selected value="' . $value . '">' . $option . '</option>';
                } else {
                    echo '  <option value="' . $value . '">' . $option . '</option>';
                }
            }
            echo '</select>';
            echo '</p>';

            // Blur
            $status = call_user_func( COPYFIGHT_CLASS_ADMIN . '::is_blur_enabled', $post->ID );
            $options = array( 'enabled' => __( 'Enabled', 'copyfight' ), 'disabled' => __( 'Disabled', 'copyfight' ));
            echo '<p>';
            echo '<label for="copyfight_blur">' . __( 'Blur', 'copyfight' ) . ':</label> ';
            echo '<select name="copyfight_blur">';
            foreach ($options as $value => $option) {
                if ($value == $status) {
                    echo '  <option selected value="' . $value . '">' . $option . '</option>';
                } else {
                    echo '  <option value="' . $value . '">' . $option . '</option>';
                }
            }
            echo '</select>';
            echo '</p>';

        }
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

        // status
        $copyfight_status = sanitize_text_field( $_POST['copyfight_status'] );
        update_post_meta( $post_id, '_copyfight_status', $copyfight_status );

        // tags
        if ( isset( $_POST['copyfight_tags'] ) ) {
            $copyfight_tags = sanitize_text_field( $_POST['copyfight_tags'] );
        } else {
            $copyfight_tags = get_post_meta( $post_id, '_copyfight_tags', true );
            if ( strlen( $copyfight_tags ) == 0 ) {
                $copyfight_tags = get_option( 'copyfight_tags' );
            }
        }
        update_post_meta( $post_id, '_copyfight_tags', $copyfight_tags );

        // blur
        if ( isset( $_POST['copyfight_blur'] ) ) {
            $copyfight_blur = sanitize_text_field( $_POST['copyfight_blur'] );
        } else {
            $copyfight_blur = get_post_meta( $post_id, '_copyfight_blur', true );
            if ( strlen( $copyfight_blur ) == 0 ) {
                $copyfight_blur = get_option( 'copyfight_blur' );
            }
        }
        update_post_meta( $post_id, '_copyfight_blur', $copyfight_blur );
    }

    /**
     * Create Copyfight version of content
     */
    public static function copyfight_save_post( $post_id ) {

        $post = get_post( $post_id );

        $is_copyfight_enabled = call_user_func( COPYFIGHT_CLASS_ADMIN . '::is_copyfight_enabled', $post_id );

        if ( $post->post_status !== 'publish' || $is_copyfight_enabled == 'disabled' ) {
            return;
        }

        $copyfight_debugging = get_option( 'copyfight_debugging' );

        $response = call_user_func( COPYFIGHT_CLASS_ADMIN . '::get_api_health' );
        if ( !empty( $response->status ) && ( $response->status->http == '0' || $response->status->mysql == '0' ) ) {
            set_transient( 'copyfight_transient', $post_id, 60 );
            update_post_meta( $post_id, '_copyfight_status', 'disabled' );
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

            update_post_meta( $post_id, '_copyfight_typeface',  $copyfight_typeface );
            update_post_meta( $post_id, '_copyfight_content',   $response->content );
            update_post_meta( $post_id, '_copyfight_hash',      $response->hash );
            update_post_meta( $post_id, '_copyfight_keywords',  $response->keywords );
            update_post_meta( $post_id, '_copyfight_mapping',   $response->mapping );

            //tags
            $status = get_post_meta( $post_id, '_copyfight_tags', true );
            if ( $status == 'enabled' ) {
                $keywords = array();
                foreach ( (array) $response->keywords as $keyword => $frequency ) {
                    $keywords[] = $keyword;
                }
                wp_set_post_terms( $post_id, $keywords );
            }

            //content delivery network
            $copyfight_cdn = get_option( 'copyfight_cdn' );
            if ( empty( $copyfight_cdn ) || $copyfight_cdn == 'false' ) {
                call_user_func( COPYFIGHT_CLASS_ADMIN . '::copyfight_save_fonts', $post_id );
            }

            //debugging
            if ( $copyfight_debugging == 'true' ) {
                call_user_func( COPYFIGHT_CLASS_ADMIN . '::debug_log', 'post: id=' . $post_id . ', status=succes' );
            }

        } else {

            //debugging
            if ( $copyfight_debugging == 'true' ) {
                call_user_func( COPYFIGHT_CLASS_ADMIN . '::debug_log', 'post: id=' . $post_id . ', status=failed' );
            }
            update_post_meta( $post_id, '_copyfight_status', 'disabled' );
            set_transient( 'copyfight_transient', $post_id, 60 );

        }
    }

    /**
     * Save fonts for local storage to bypass CDN
     */
    public static function copyfight_save_fonts( $post_id, $overwrite = true ) {

        $error = false;
        $permissions = 0755;

        //common font
        $copyfight_hash = get_post_meta( $post_id, '_copyfight_hash', true );
        $subdir = substr( $copyfight_hash, 0, 1 );

        if ( is_writable( COPYFIGHT_CACHE ) ) {
            if ( !file_exists( COPYFIGHT_CACHE . $subdir ) ) {
                mkdir( COPYFIGHT_CACHE . $subdir, $permissions, true );
            }
            $font = COPYFIGHT_CACHE . $subdir . '/' . $copyfight_hash . '.woff';
            if ( !file_exists( $font ) || $overwrite ) {
                $url = COPYFIGHT_CDN . $subdir . '/' . $copyfight_hash . '.woff';
                //$response = wp_remote_fopen( $url );
                $response = file_get_contents( $url );
                file_put_contents( $font, $response );
                chmod( $font, $permissions );
            }

        } else {
            $error = true;
        }

        //uncommon font
        $copyfight_typeface = get_post_meta( $post_id, '_copyfight_typeface', true );
        $typeface_filename = substr( $copyfight_typeface, 0, -4 );
        $path_parts = pathinfo( $typeface_filename );
        $subdir = $path_parts['dirname'];

        if ( is_writable( COPYFIGHT_CACHE ) ) {
            if ( !file_exists( COPYFIGHT_CACHE . $subdir ) ) {
                mkdir( COPYFIGHT_CACHE . $subdir, $permissions, true );
            }
            $typeface_filename = $path_parts['filename'];
            $font = COPYFIGHT_CACHE . $subdir . '/' . $typeface_filename . '.woff';
            if ( !file_exists( $font ) ) {
                $url = COPYFIGHT_CDN . $subdir . '/' . $typeface_filename . '.woff';
                //$response = wp_remote_fopen( $url );
                $response = file_get_contents( $url );
                file_put_contents( $font, $response );
                chmod( $font, $permissions );
            }
        } else {
            $error = true;
        }

        if ( $error ) {
            update_option( 'copyfight_cdn', 'true' );
            echo '<p>' . __( 'A Copyfight error has occurred...', 'copyfight' );
            echo ' <a href="javascript:window.location.reload();">' . __( 'Click here to try again...' ) . '</a></p>';
            die();
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
                    'title' => __( 'Overview', 'copyfight' ),
                    'content' =>
                        '<p><strong>' . esc_html__( 'Copyfight Overview', 'copyfight' ) . '</strong></p>' .
                        '<p>' . esc_html__( 'Copyfight protects your content, so you can focus on more important things.', 'copyfight' ) . '</p>' .
                        '<p>' . esc_html__( 'On this page, you are able to enter/remove an API key, view account information and view stats.', 'copyfight' ) . '</p>',
                )
            );
            $current_screen->add_help_tab(
                array(
                    'id' => 'settings',
                    'title' => __( 'Settings', 'copyfight' ),
                    'content' =>
                        '<p><strong>' . esc_html__( 'Copyfight Settings', 'copyfight' ) . '</strong></p>' .
                        '<p><strong>' . esc_html__( 'API Key', 'copyfight' ) . '</strong> - ' . esc_html__( 'Enter/remove an API key.', 'copyfight' ) . '</p>' .
                        '<p><strong>' . esc_html__( 'Default settings', 'copyfight' ) . '</strong> - ' . esc_html__( 'Edit your default settings.', 'copyfight' ) . '</p>' .
                        '<p><strong>' . esc_html__( 'General settings', 'copyfight' ) . '</strong> - ' . esc_html__( 'Edit your general settings.', 'copyfight' ) . '</p>' .
                        '<p><strong>' . esc_html__( 'Other settings', 'copyfight' ) . '</strong> - ' . esc_html__( 'Edit your other settings.', 'copyfight' ) . '</p>',
                )
            );
            $current_screen->add_help_tab(
                array(
                    'id' => 'account',
                    'title' => __( 'Account', 'copyfight' ),
                    'content' =>
                        '<p><strong>' . esc_html__( 'Copyfight Account', 'copyfight' ) . '</strong></p>' .
                        '<p><strong>' . esc_html__( 'Subscription Type', 'copyfight' ) . '</strong> - ' . esc_html__( 'The Copyfight subscription plan', 'copyfight' ) . '</p>' .
                        '<p><strong>' . esc_html__( 'Status', 'copyfight' ) . '</strong> - ' . esc_html__( 'The subscription status - active, cancelled or suspended', 'copyfight' ) . '</p>',
                )
            );
            $current_screen->set_help_sidebar(
                '<p><strong>' . esc_html__( 'For more information:' , 'copyfight' ) . '</strong></p>' .
                '<p><a href="' . COPYFIGHT_HOME . 'faq/" target="_blank">'     . esc_html__( 'Copyfight FAQ', 'copyfight' ) . '</a></p>' .
                '<p><a href="' . COPYFIGHT_HOME . 'support/" target="_blank">' . esc_html__( 'Copyfight Support', 'copyfight' ) . '</a></p>'
            );
        }
    }

    public static function debug_log( $string ) {

        if ( !file_exists( COPYFIGHT_DEBUG_LOG ) ) {
            touch( COPYFIGHT_DEBUG_LOG );
        }

        if ( is_writable( COPYFIGHT_DEBUG_LOG ) ) {
            $timestamp = date( 'Y-m-d H:i:s' );
            file_put_contents( COPYFIGHT_DEBUG_LOG, $timestamp . ' ' . $string . "\n", FILE_APPEND );
        }
    }

    public static function get_api_health() {
        $url = API_HOST . 'wp-json/api/v1/canary';
        $response = call_user_func( COPYFIGHT_CLASS_ADMIN . '::api_get', $url );

        $copyfight_debugging = get_option( 'copyfight_debugging' );
        if ( $copyfight_debugging == 'true' ) {
            //api health
            call_user_func( COPYFIGHT_CLASS_ADMIN . '::debug_log', 'api health: ' . serialize( $response ) );

            //active plugins
            $active_plugins = get_option( 'active_plugins' );
            call_user_func( COPYFIGHT_CLASS_ADMIN . '::debug_log', 'active plugins: ' . serialize( $active_plugins ) );

            //permissions
            $permissions = substr(sprintf( '%o', fileperms( COPYFIGHT_CACHE ) ), -4 );
            call_user_func( COPYFIGHT_CLASS_ADMIN . '::debug_log', 'permissions: ' . $permissions );

            //wordpress version
            global $wp_version;
            call_user_func( COPYFIGHT_CLASS_ADMIN . '::debug_log', 'wordpress version: ' . $wp_version );

            //copyfight version
            call_user_func( COPYFIGHT_CLASS_ADMIN . '::debug_log', 'copyfight version: ' . COPYFIGHT_VERSION );

            //copyfight settings
            $copyfight_settings = get_option( 'copyfight_settings' );
            call_user_func( COPYFIGHT_CLASS_ADMIN . '::debug_log', 'copyfight settings: ' . serialize( $copyfight_settings ) );
        }
        return $response;
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
            echo '<p>' . __( 'A Copyfight error has occurred...', 'copyfight' ) . ' ' . $error_message;
            echo ' <a href="javascript:window.location.reload();">' . __( 'Click here to try again...' ) . '</a></p>';
            die();
        } else {
            $response = json_decode( $response['body'] );
            return $response;
        }
    }

    public static function api_get( $url ) {

        $response = wp_remote_get( $url, array( 'timeout' => 45 ) );

        if ( is_array( $response ) ) {
            $response = json_decode( $response['body'] );
            return $response;
        } else {
            echo '<p>' . __( 'A Copyfight error has occurred...', 'copyfight' );
            echo ' <a href="javascript:window.location.reload();">' . __( 'Click here to try again...' ) . '</a></p>';
        }
    }

    public static function ajax() {

        //get_font_info
        if ( !empty( $_POST['typeface'] ) ) {
            $typeface = explode( '/', $_POST['typeface'] );
            $typeface = $typeface[0];
            $response = call_user_func( COPYFIGHT_CLASS_ADMIN . '::get_font_info', $typeface );
            $response = stripslashes( $response );
            $response = preg_replace( '/<a\s+/', '<a target="_blank" ', $response);
            die( $response );
        }

        return false;
    }

}