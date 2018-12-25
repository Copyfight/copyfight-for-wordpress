<div class="copyfight_plans">

    <h3><?php _e('Subscription plans', 'copyfight'); ?></h3>

    <img id="free_plan" src="<?php echo COPYFIGHT_PLUGIN_URL; ?>_inc/img/copyfight-freemium-plan.png" />
    <img id="premium_plan" src="<?php echo COPYFIGHT_PLUGIN_URL; ?>_inc/img/copyfight-premium-plan.png" />
    <img id="business_plan" src="<?php echo COPYFIGHT_PLUGIN_URL; ?>_inc/img/copyfight-business-plan.png" />
    <img id="enterprise_plan" src="<?php echo COPYFIGHT_PLUGIN_URL; ?>_inc/img/copyfight-enterprise-plan.png" />

    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" id="subscription_plan" style="display:none;">

        <input type="hidden" name="hosted_button_id" value="D3CJK5E73NCFU">

        <input type="hidden" name="on0" value="">
        <select name="os0" id="selected_plan">
            <option value="Free plan">Free plan : $0,00 USD - yearly</option>
            <option value="Premium plan">Premium plan : $99,00 USD - yearly</option>
            <option value="Business plan">Business plan : $299,00 USD - yearly</option>
            <option value="Enterprise plan">Enterprise plan : $999,00 USD - yearly</option>
        </select>

        <input type="hidden" name="on1" value="API Key">
        <input type="text" name="os1" value="<?php echo get_option('copyfight_api_key'); ?>">

        <?php $current_user = wp_get_current_user(); ?>
        <input type="hidden" name="on2" value="User">
        <input type="text" name="os2" value="<?php echo $current_user->user_email; ?> | <?php echo get_site_url(); ?>" >

        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="return" id="return" value="<?php echo get_admin_url() . 'options-general.php?page=copyfight'; ?>">
        <input type="hidden" name="currency_code" value="USD">
        <input type="hidden" name="business" value="info@getcopyfight.com">
        <input type="hidden" name="charset" value="utf-8">

        <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
        <!-- <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribe_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"> -->
        <!-- <img alt="" border="0" src="https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif" width="1" height="1"> -->

    </form>

    <?php _e('Our <a href="https://getcopyfight.com/fair-use-policy/" target="_blank">Fair Use Policy</a> applies to all promos and subscription plans.', 'copyfight'); ?>

</div>