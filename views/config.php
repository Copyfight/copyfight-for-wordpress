<div class="cf_settings">

    <div class="version">version <?php echo COPYFIGHT_VERSION; ?></div>
    <a href="<?php echo COPYFIGHT_HOME; ?>" target="_blank"><div class="logo"></div></a>

    <form action="options.php" method="POST" id="copyfight-config">
        <?php settings_fields('copyfight_options_group'); ?>
        <div class="activate-highlight activate-option">
            <div class="option-description">
                <strong><?php _e('API Key', 'copyfight'); ?></strong>
            </div>
            <div class="right">
                <?php if ( strlen(get_option('copyfight_api_key')) == 0 ) { ?>
                <input id="copyfight_api_key" name="copyfight_api_key" type="text" size="15" value="<?php echo get_option('copyfight_api_key'); ?>" placeholder="email@addre.ss" class="regular-text code">
                <input type="submit" name="submit" id="copyfight_api_key_reset" class="button button-primary" value="<?php _e('Activate Copyfight', 'copyfight'); ?>">
                <p class="description"><?php _e('Enter your email address or API Key if you have one', 'copyfight'); ?></p>
                <p>
                    <input type="checkbox" id="copyfight_newsletter" name="copyfight_newsletter" value="true" checked="checked">
                    <label for="copyfight_newsletter"> <?php _e('Receive email updates about Copyfight', 'copyfight'); ?></label>
                </p>
                <?php } else { ?>
                <input id="copyfight_api_key" name="copyfight_api_key" type="text" size="15" value="<?php echo get_option('copyfight_api_key'); ?>" class="regular-text code" readonly>
                <input type="submit" name="submit" id="copyfight_api_key_reset" class="button button-primary" value="<?php _e('Reset API Key', 'copyfight'); ?>">
                <?php } ?>
            </div>
        </div>

        <h3><?php _e('Default settings', 'copyfight'); ?></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="copyfight_typeface"><?php _e('Default typeface', 'copyfight'); ?></label></th>
                <td style="white-space: nowrap">
                    <select id="copyfight_typeface" name="copyfight_typeface">
                        <?php
                        $timestamp = false;
                        $fontlist = file_get_contents( COPYFIGHT_FONTLIST );
                        $fonts = unserialize( $fontlist );
                        $copyfight_typeface = get_option('copyfight_typeface');
                        if ( strlen( $copyfight_typeface ) == 0 ) {
                            update_option('copyfight_typeface', 'opensans/OpenSans-Regular.ttf');
                            $copyfight_typeface = get_option('copyfight_typeface');
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
                    <a id="copyfight_typeface_info_link" title="" href="#TB_inline?width=640&height=320&inlineId=copyfight_typeface_info" class="thickbox">
                        <div class="dashicons dashicons-info"></div>
                    </a>
                    <div id="copyfight_typeface_info" class="cf_modal"></div>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>

    </form>

    <?php include 'plans.php'; ?>

</div>
