<div class="copyfight_updated" style="padding: 0; margin: 0; border: none; background: none; -webkit-box-shadow: none;">
    <form name="copyfight_activate" action="<?php echo call_user_func( COPYFIGHT_CLASS_ADMIN . '::get_page_url' ); ?>" method="POST">
        <div class="copyfight_activate">
            <div class="aa_button_container">
                <div class="aa_button_border">
                    <input type="submit" class="aa_button" value="<?php esc_attr_e( 'Activate your Copyfight account', 'copyfight' ); ?>" />
                </div>
            </div>
            <div class="aa_description"><?php _e( '<strong>Almost done</strong> - activate Copyfight and protect your content', 'copyfight' );?></div>
        </div>
    </form>
</div>