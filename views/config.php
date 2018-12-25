<?php if ( strlen(get_option( 'copyfight_api_key')) > 0 ) {
    $response = call_user_func( COPYFIGHT_CLASS_ADMIN . '::get_api_health' );
    if ( !empty( $response->status ) && ( $response->status->http == '0' || $response->status->mysql == '0' ) ) {
        $api_status = __( 'Service disruption', 'copyfight' );
        $api_status_image = '<img alt="' . $api_status . '" src="' . COPYFIGHT_PLUGIN_URL . '_inc/img/icn-red.png" />';
    } else {
        $api_status = __( 'Service is operating normally', 'copyfight' );
        $api_status_image = '<img alt="' . $api_status . '" src="' . COPYFIGHT_PLUGIN_URL . '_inc/img/icn-green.png" />';
    }
}
?>

<div class="cf_settings">

    <div class="version"><?php _e( 'Version', 'copyfight' ); ?> <?php echo COPYFIGHT_VERSION; ?></div>
    <a href="<?php echo COPYFIGHT_HOME; ?>" target="_blank"><div class="logo" style="background: url('<?php echo COPYFIGHT_PLUGIN_URL; ?>_inc/img/copyfight-logo-color.svg'); background-size: 100%;"></div></a>

    <form action="options.php" method="POST" id="copyfight-config">
        <?php settings_fields( 'copyfight_options_group' ); ?>
        <div class="activate-highlight activate-option">
            <div class="option-description">
                <strong><?php _e( 'API Key', 'copyfight' ); ?></strong>
                <?php if ( strlen(get_option( 'copyfight_api_key')) > 0 ) { ?>
                <br/><br/>
                <strong><?php _e( 'API Status', 'copyfight' ); ?></strong> <?php echo $api_status_image; ?><br/>
                <?php echo $api_status; ?>
                <?php } ?>
            </div>
            <div class="right">
                <?php if ( strlen( get_option( 'copyfight_api_key' ) ) == 0 ) { ?>
                <input id="copyfight_api_key" name="copyfight_api_key" type="text" size="15" value="<?php echo get_option( 'copyfight_api_key' ); ?>" placeholder="<?php _e( 'Enter your email', 'copyfight' ); ?>" class="regular-text code">
                <input type="submit" name="submit" id="copyfight_api_key_reset" class="button button-primary" value="<?php _e( 'Activate Copyfight', 'copyfight' ); ?>">
                <p class="description"><?php _e( 'Enter your email address or API Key if you have one', 'copyfight' ); ?></p>
                <p>
                    <input type="checkbox" id="copyfight_newsletter" name="copyfight_newsletter" value="true" checked="checked">
                    <label for="copyfight_newsletter"><?php _e( 'Receive email updates about Copyfight', 'copyfight' ); ?></label>
                </p>
                <?php } else { ?>
                <input id="copyfight_api_key" name="copyfight_api_key" type="text" size="15" value="<?php echo get_option( 'copyfight_api_key' ); ?>" class="regular-text code" readonly>
                <input type="submit" name="submit" id="copyfight_api_key_reset" class="button button-primary" value="<?php _e( 'Reset API Key', 'copyfight' ); ?>">
                <?php } ?>
            </div>
        </div>

        <h3><?php _e( 'Settings', 'copyfight' ); ?></h3>
        <p><?php _e( 'These settings are triggered right after a post or page has been saved. This means your posts might not have been protected, yet. Manually save your individual posts and pages first.', 'copyfight' ); ?></p>

        <fieldset class="fieldset">
            <legend><?php _e( 'Default settings', 'copyfight' ); ?></legend>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="copyfight_status"><?php _e( 'Default status', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <select id="copyfight_status" name="copyfight_status">
                            <?php
                            $copyfight_status = get_option( 'copyfight_status' );
                            if ( strlen( $copyfight_status ) == 0 ) {
                                update_option( 'copyfight_status', 'enabled' );
                                $copyfight_status = get_option( 'copyfight_status' );
                            }
                            ?>
                            <option <?php if ( $copyfight_status == 'disabled') { echo 'selected '; } ?>value="disabled"><?php _e( 'Disabled', 'copyfight' ); ?></option>';
                            <option <?php if ( $copyfight_status == 'enabled') { echo 'selected '; } ?>value="enabled"><?php _e( 'Enabled', 'copyfight' ); ?></option>';
                        </select>
                        <a id="copyfight_status_info_link" target="_blank" title="<?php _e( 'Default status', 'copyfight' ); ?>" href="<?php echo COPYFIGHT_HOME; ?>support/wordpress-default-status/">
                            <div class="dashicons dashicons-info"></div>
                        </a>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="copyfight_tags"><?php _e( 'Default tags', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <select id="copyfight_tags" name="copyfight_tags">
                            <?php
                            $copyfight_tags = get_option( 'copyfight_tags' );
                            if ( strlen( $copyfight_tags ) == 0 ) {
                                update_option( 'copyfight_tags', 'enabled' );
                                $copyfight_tags = get_option( 'copyfight_tags' );
                            }
                            ?>
                            <option <?php if ( $copyfight_tags == 'disabled') { echo 'selected '; } ?>value="disabled"><?php _e( 'Disabled', 'copyfight' ); ?></option>';
                            <option <?php if ( $copyfight_tags == 'enabled') { echo 'selected '; } ?>value="enabled"><?php _e( 'Enabled', 'copyfight' ); ?></option>';
                        </select>
                        <a id="copyfight_tags_info_link" target="_blank" title="<?php _e( 'Default tags', 'copyfight' ); ?>" href="<?php echo COPYFIGHT_HOME; ?>support/wordpress-default-tags/">
                            <div class="dashicons dashicons-info"></div>
                        </a>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="copyfight_blur"><?php _e( 'Default blur', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <select id="copyfight_blur" name="copyfight_blur">
                            <?php
                            $copyfight_blur = get_option( 'copyfight_blur' );
                            if ( strlen( $copyfight_blur ) == 0 ) {
                                update_option( 'copyfight_blur', 'disabled' );
                                $copyfight_blur = get_option( 'copyfight_blur' );
                            }
                            ?>
                            <option <?php if ( $copyfight_blur == 'disabled') { echo 'selected '; } ?>value="disabled"><?php _e( 'Disabled', 'copyfight' ); ?></option>';
                            <option <?php if ( $copyfight_blur == 'enabled') { echo 'selected '; } ?>value="enabled"><?php _e( 'Enabled', 'copyfight' ); ?></option>';
                        </select>
                        <a id="copyfight_blur_info_link" target="_blank" title="<?php _e( 'Default blur', 'copyfight' ); ?>" href="<?php echo COPYFIGHT_HOME; ?>support/wordpress-default-blur/">
                            <div class="dashicons dashicons-info"></div>
                        </a>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="copyfight_typeface"><?php _e( 'Typeface', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <select id="copyfight_typeface" name="copyfight_typeface">
                            <?php
                            $timestamp = false;
                            $fontlist = file_get_contents( COPYFIGHT_FONTLIST );
                            $fonts = unserialize( $fontlist );
                            $copyfight_typeface = get_option( 'copyfight_typeface' );
                            if ( strlen( $copyfight_typeface ) == 0 ) {
                                update_option( 'copyfight_typeface', 'opensans/OpenSans-Regular.ttf' );
                                $copyfight_typeface = get_option( 'copyfight_typeface' );
                            }
                            asort($fonts);
                            foreach ($fonts as $file => $name) {
                                if ($copyfight_typeface == $file) {
                                    echo '  <option selected value="' . $file . '">' . $name[0] . '</option>';
                                } else {
                                    echo '  <option value="' . $file . '">' . $name[0] . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <?php add_thickbox(); ?>
                        <div id="copyfight_typeface_info_loader"><img src="/wp-admin/images/wpspin_light-2x.gif" /></div>
                        <a id="copyfight_typeface_info_link" title="<?php _e( 'Typeface', 'copyfight' ); ?>" href="#TB_inline?width=640&height=320&inlineId=copyfight_typeface_info" class="thickbox">
                            <div class="dashicons dashicons-info copyfight_typeface_info"></div>
                        </a>
                        <div id="copyfight_typeface_info" class="cf_modal"></div>
                    </td>
                </tr>
            </table>
        </fieldset>

        <p><?php _e( 'These settings have an immediate effect on your content.', 'copyfight' ); ?></p>

        <fieldset class="fieldset">
            <legend><?php _e( 'General settings', 'copyfight' ); ?></legend>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="copyfight_cdn"><?php _e( 'Content Delivery Network', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <select id="copyfight_cdn" name="copyfight_cdn">
                            <?php
                            $copyfight_cdn = get_option( 'copyfight_cdn' );
                            if ( strlen( $copyfight_cdn ) == 0 ) {
                                update_option( 'copyfight_cdn', 'false' );
                                $copyfight_cdn = get_option( 'copyfight_cdn' );
                            }
                            ?>
                            <option <?php if ( $copyfight_cdn == 'false') { echo 'selected '; } ?>value="false"><?php _e( 'Disabled', 'copyfight' ); ?></option>';
                            <option <?php if ( $copyfight_cdn == 'true') { echo 'selected '; } ?>value="true"><?php _e( 'Enabled', 'copyfight' ); ?></option>';
                        </select>
                        <a id="copyfight_cdn_info_link" target="_blank" title="<?php _e( 'Content Delivery Network', 'copyfight' ); ?>" href="<?php echo COPYFIGHT_HOME; ?>support/wordpress-content-delivery-network/">
                            <div class="dashicons dashicons-info"></div>
                        </a>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="copyfight_protocol"><?php _e( 'Protocol', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <select id="copyfight_protocol" name="copyfight_protocol">
                            <?php
                            $copyfight_protocol = get_option( 'copyfight_protocol' );
                            if ( strlen( $copyfight_protocol ) == 0 ) {
                                update_option( 'copyfight_protocol', 'https' );
                                $copyfight_protocol = get_option( 'copyfight_protocol' );
                            }
                            ?>
                            <option <?php if ( $copyfight_protocol == 'http') { echo 'selected '; } ?>value="http"><?php _e( 'http', 'copyfight' ); ?></option>';
                            <option <?php if ( $copyfight_protocol == 'https') { echo 'selected '; } ?>value="https"><?php _e( 'https', 'copyfight' ); ?></option>';
                        </select>
                        <a id="copyfight_protocol_info_link" target="_blank" title="<?php _e( 'Protocol', 'copyfight' ); ?>" href="<?php echo COPYFIGHT_HOME; ?>support/wordpress-protocol/">
                            <div class="dashicons dashicons-info"></div>
                        </a>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="copyfight_excerpt"><?php _e( 'Use excerpt', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <select id="copyfight_excerpt" name="copyfight_excerpt">
                            <?php
                            $copyfight_excerpt = get_option( 'copyfight_excerpt' );
                            if ( strlen( $copyfight_excerpt ) == 0 ) {
                                update_option( 'copyfight_excerpt', 'true' );
                                $copyfight_excerpt = get_option( 'copyfight_excerpt' );
                            }
                            ?>
                            <option <?php if ( $copyfight_excerpt == 'false') { echo 'selected '; } ?>value="false"><?php _e( 'Disabled', 'copyfight' ); ?></option>';
                            <option <?php if ( $copyfight_excerpt == 'true') { echo 'selected '; } ?>value="true"><?php _e( 'Enabled', 'copyfight' ); ?></option>';
                        </select>
                        <a id="copyfight_excerpt_info_link" target="_blank" title="<?php _e( 'Use excerpt', 'copyfight' ); ?>" href="<?php echo COPYFIGHT_HOME; ?>support/wordpress-excerpt/">
                            <div class="dashicons dashicons-info"></div>
                        </a>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="copyfight_fouc"><?php _e( 'FOUC Protection', 'copyfight' ); ?><br/><?php _e( '(Flash Of Unstyled Content)', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <select id="copyfight_fouc" name="copyfight_fouc">
                            <?php
                            $copyfight_fouc = get_option( 'copyfight_fouc' );
                            if ( strlen( $copyfight_fouc ) == 0 ) {
                                update_option( 'copyfight_fouc', 'true' );
                                $copyfight_fouc = get_option( 'copyfight_fouc' );
                            }
                            ?>
                            <option <?php if ( $copyfight_fouc == 'false') { echo 'selected '; } ?>value="false"><?php _e( 'Disabled', 'copyfight' ); ?></option>';
                            <option <?php if ( $copyfight_fouc == 'true') { echo 'selected '; } ?>value="true"><?php _e( 'Enabled', 'copyfight' ); ?></option>';
                        </select>
                        <a id="copyfight_fouc_info_link" target="_blank" title="FOUC" href="<?php echo COPYFIGHT_HOME; ?>support/flash-of-unstyled-content-fouc/">
                            <div class="dashicons dashicons-info"></div>
                        </a>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="copyfight_select"><?php _e( 'Text selection', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <select id="copyfight_select" name="copyfight_select">
                            <?php
                            $copyfight_select = get_option( 'copyfight_select' );
                            if ( strlen( $copyfight_select ) == 0 ) {
                                update_option( 'copyfight_select', 'true' );
                                $copyfight_select = get_option( 'copyfight_select' );
                            }
                            ?>
                            <option <?php if ( $copyfight_select == 'false') { echo 'selected '; } ?>value="false"><?php _e( 'Disabled', 'copyfight' ); ?></option>';
                            <option <?php if ( $copyfight_select == 'true') { echo 'selected '; } ?>value="true"><?php _e( 'Enabled', 'copyfight' ); ?></option>';
                        </select>
                        <label for="copyfight_select_length"><?php _e( 'Length', 'copyfight' ); ?></label>
                        <select id="copyfight_select_length" name="copyfight_select_length">
                            <?php
                            $copyfight_select_length = get_option( 'copyfight_select_length' );
                            if ( strlen( $copyfight_select_length ) == 0 ) {
                                update_option( 'copyfight_select_length', '25' );
                                $copyfight_select_length = get_option( 'copyfight_select_length' );
                            }
                            ?>
                            <option <?php if ( $copyfight_select_length == '10') { echo 'selected '; } ?>value="10">10</option>';
                            <option <?php if ( $copyfight_select_length == '25') { echo 'selected '; } ?>value="25">25</option>';
                            <option <?php if ( $copyfight_select_length == '50') { echo 'selected '; } ?>value="50">50</option>';
                            <option <?php if ( $copyfight_select_length == '100') { echo 'selected '; } ?>value="100">100</option>';
                            <option <?php if ( $copyfight_select_length == '250') { echo 'selected '; } ?>value="250">250</option>';
                            <option <?php if ( $copyfight_select_length == '500') { echo 'selected '; } ?>value="500">500</option>';
                            <option <?php if ( $copyfight_select_length == '1000') { echo 'selected '; } ?>value="1000">1000</option>';
                        </select>
                        <a id="copyfight_select_info_link" target="_blank" title="<?php _e( 'Text selection', 'copyfight' ); ?>" href="<?php echo COPYFIGHT_HOME; ?>support/wordpress-text-selection/">
                            <div class="dashicons dashicons-info"></div>
                        </a>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="copyfight_sev"><?php _e( 'Search Engine Visibility', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <select id="copyfight_sev" name="copyfight_sev">
                            <?php
                            $copyfight_sev = get_option( 'copyfight_sev' );
                            if ( strlen( $copyfight_sev ) == 0 ) {
                                update_option( 'copyfight_sev', 'true' );
                                $copyfight_sev = get_option( 'copyfight_sev' );
                            }
                            ?>
                            <option <?php if ( $copyfight_sev == 'false') { echo 'selected '; } ?>value="false"><?php _e( 'Disabled', 'copyfight' ); ?></option>';
                            <option <?php if ( $copyfight_sev == 'true') { echo 'selected '; } ?>value="true"><?php _e( 'Enabled', 'copyfight' ); ?></option>';
                        </select>
                        <a id="copyfight_sev_info_link" target="_blank" title="<?php _e( 'Search Engine Visibility', 'copyfight' ); ?>" href="<?php echo COPYFIGHT_HOME; ?>support/wordpress-search-engine-visibility/">
                            <div class="dashicons dashicons-info"></div>
                        </a>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="copyfight_right_click"><?php _e( 'Right click', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <select id="copyfight_right_click" name="copyfight_right_click">
                            <?php
                            $copyfight_right_click = get_option( 'copyfight_right_click' );
                            if ( strlen( $copyfight_right_click ) == 0 ) {
                                update_option( 'copyfight_right_click', 'true' );
                                $copyfight_right_click = get_option( 'copyfight_right_click' );
                            }
                            ?>
                            <option <?php if ( $copyfight_right_click == 'false') { echo 'selected '; } ?>value="false"><?php _e( 'Disabled', 'copyfight' ); ?></option>';
                            <option <?php if ( $copyfight_right_click == 'true') { echo 'selected '; } ?>value="true"><?php _e( 'Enabled', 'copyfight' ); ?></option>';
                        </select>
                        <a id="copyfight_right_click_info_link" target="_blank" title="<?php _e( 'Right click', 'copyfight' ); ?>" href="<?php echo COPYFIGHT_HOME; ?>support/wordpress-right-click/">
                            <div class="dashicons dashicons-info"></div>
                        </a>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="copyfight_print"><?php _e( 'Print', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <select id="copyfight_print" name="copyfight_print">
                            <?php
                            $copyfight_print = get_option( 'copyfight_print' );
                            if ( strlen( $copyfight_print ) == 0 ) {
                                update_option( 'copyfight_print', 'false' );
                                $copyfight_print = get_option( 'copyfight_print' );
                            }
                            ?>
                            <option <?php if ( $copyfight_print == 'false') { echo 'selected '; } ?>value="false"><?php _e( 'Disabled', 'copyfight' ); ?></option>';
                            <option <?php if ( $copyfight_print == 'true') { echo 'selected '; } ?>value="true"><?php _e( 'Enabled', 'copyfight' ); ?></option>';
                        </select>
                        <a id="copyfight_print_info_link" target="_blank" title="<?php _e( 'Print', 'copyfight' ); ?>" href="<?php echo COPYFIGHT_HOME; ?>support/wordpress-print/">
                            <div class="dashicons dashicons-info"></div>
                        </a>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="copyfight_printscreen"><?php _e( 'Printscreen', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <select id="copyfight_printscreen" name="copyfight_printscreen">
                            <?php
                            $copyfight_printscreen = get_option( 'copyfight_printscreen' );
                            if ( strlen( $copyfight_printscreen ) == 0 ) {
                                update_option( 'copyfight_printscreen', 'false' );
                                $copyfight_printscreen = get_option( 'copyfight_printscreen' );
                            }
                            ?>
                            <option <?php if ( $copyfight_printscreen == 'false') { echo 'selected '; } ?>value="false"><?php _e( 'Disabled', 'copyfight' ); ?></option>';
                            <option <?php if ( $copyfight_printscreen == 'true') { echo 'selected '; } ?>value="true"><?php _e( 'Enabled', 'copyfight' ); ?></option>';
                        </select>
                        <a id="copyfight_printscreen_info_link" target="_blank" title="<?php _e( 'Printscreen', 'copyfight' ); ?>" href="<?php echo COPYFIGHT_HOME; ?>support/wordpress-printscreen/">
                            <div class="dashicons dashicons-info"></div>
                        </a>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="copyfight_copyright"><?php _e( 'Copyright notice', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <?php
                        $copyfight_copyright = get_option( 'copyfight_copyright' );
                        if ( $copyfight_copyright === false ) {
                            $copyfight_copyright = 'Copyright &copy; ' . date('Y') . ' ' . get_bloginfo( 'name' ) . '.';
                            update_option( 'copyfight_copyright', $copyfight_copyright);
                        }
                        ?>
                        <input id="copyfight_copyright" name="copyfight_copyright" type="text" size="15" value="<?php echo $copyfight_copyright; ?>" class="regular-text code">
                        <a id="copyfight_copyright_info_link" target="_blank" title="<?php _e( 'Copyright notice', 'copyfight' ); ?>" href="<?php echo COPYFIGHT_HOME; ?>support/wordpress-copyright-notice/">
                            <div class="dashicons dashicons-info"></div>
                        </a>
                    </td>
                </tr>
            </table>
        </fieldset>

        <p></p>

        <fieldset class="fieldset">
            <legend><?php _e( 'Other settings', 'copyfight' ); ?></legend>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e( 'Widget', 'copyfight' ); ?></th>
                    <td style="white-space: nowrap">
                        <p class="widget"><a target="_blank" href="<?php echo get_admin_url() . 'widgets.php'; ?>" title="<?php _e( 'Widget settings', 'copyfight' ); ?>"><?php _e( 'Click here', 'copyfight' ); ?></a> <?php _e( 'to add and edit the Copyfight widget settings.', 'copyfight' ); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="copyfight_debugging"><?php _e( 'Debugging', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <select id="copyfight_debugging" name="copyfight_debugging">
                            <?php
                            $copyfight_debugging = get_option( 'copyfight_debugging' );
                            if ( strlen( $copyfight_debugging ) == 0 ) {
                                update_option( 'copyfight_debugging', 'true' );
                                $copyfight_debugging = get_option( 'copyfight_debugging' );
                            }
                            ?>
                            <option <?php if ( $copyfight_debugging == 'false') { echo 'selected '; } ?>value="false"><?php _e( 'Disabled', 'copyfight' ); ?></option>';
                            <option <?php if ( $copyfight_debugging == 'true') { echo 'selected '; } ?>value="true"><?php _e( 'Enabled', 'copyfight' ); ?></option>';
                        </select>
                        <a id="copyfight_debugging_info_link" target="_blank" title="<?php _e( 'Debugging', 'copyfight' ); ?>" href="<?php echo COPYFIGHT_HOME; ?>support/wordpress-debugging/">
                            <div class="dashicons dashicons-info"></div>
                        </a>
                        <?php
                        global $wpdb;
                        $sql = 'SELECT * FROM ' . $wpdb->prefix . 'options WHERE 1 AND option_name LIKE "%s" AND option_name != \'copyfight_settings\'';
                        $query = $wpdb->prepare( $sql, 'copyfight_%' );
                        $copyfight_settings = $wpdb->get_results( $query );
                        update_option( 'copyfight_settings', $copyfight_settings );
                        if ( $copyfight_debugging == 'false' ) {
                            if ( file_exists( COPYFIGHT_DEBUG_LOG ) && !unlink( COPYFIGHT_DEBUG_LOG ) ) {
                                echo 'Error deleting ' . COPYFIGHT_DEBUG_FILE;
                            }
                        } else { ?>
                            <a href="<?php echo COPYFIGHT_PLUGIN_URL . COPYFIGHT_DEBUG_FILE; ?>" target="_blank"><?php echo COPYFIGHT_DEBUG_FILE; ?></a>
                        <?php } ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="copyfight_console"><?php _e( 'Console', 'copyfight' ); ?></label></th>
                    <td style="white-space: nowrap">
                        <select id="copyfight_console" name="copyfight_console">
                            <?php
                            $copyfight_console = get_option( 'copyfight_console' );
                            if ( strlen( $copyfight_console ) == 0 ) {
                                update_option( 'copyfight_console', 'false' );
                                $copyfight_console = get_option( 'copyfight_console' );
                            }
                            ?>
                            <option <?php if ( $copyfight_console == 'false') { echo 'selected '; } ?>value="false"><?php _e( 'Disabled', 'copyfight' ); ?></option>';
                            <option <?php if ( $copyfight_console == 'true') { echo 'selected '; } ?>value="true"><?php _e( 'Enabled', 'copyfight' ); ?></option>';
                        </select>
                        <a id="copyfight_console_info_link" target="_blank" title="<?php _e( 'Console', 'copyfight' ); ?>" href="<?php echo COPYFIGHT_HOME; ?>support/wordpress-console/">
                            <div class="dashicons dashicons-info"></div>
                        </a>
                    </td>
                </tr>
            </table>
        </fieldset>

        <?php submit_button(); ?>

    </form>

    <?php include 'plans.php'; ?>

</div>
