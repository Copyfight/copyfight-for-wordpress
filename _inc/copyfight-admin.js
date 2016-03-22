jQuery(function($) {

    /* Default settings */

    $('#copyfight_api_key_reset').click(function() {
        if ($('#copyfight_api_key').val() == '') {
            alert(copyfight.noticeEmptyApiKey);
            return false;
        } else if ($("#copyfight_api_key").is('[readonly]')) {
            $('#copyfight_api_key').val('');
        }
    });

    $('#copyfight_typeface').change(function() {
        $('.dashicons-info').hide();
        if ($('#copyfight_typeface').val().length) {
            $('#copyfight_typeface_info_loader').show();
            $.ajax({
                url: copyfight.ajax_url,
                type: 'POST',
                data: {
                    action: 'copyfight_ajax',
                    typeface: $('#copyfight_typeface').val()
                },
                success: function(response) {
                    if (response.length) {
                        $('#copyfight_typeface_info_link').attr('title', $('#copyfight_typeface option:selected').text());
                        $('#copyfight_typeface_info').html(response);
                        $('.dashicons-info').show();
                    }
                    $('#copyfight_typeface_info_loader').hide();
                }
            });
        }
    });

    /* Subscription plans */

    $('#free_plan').click(function() {
        $('input[name*="cmd"]').val('_donations');
        $('input[name*="amount"]').val('1');
        $('#free_plan').val('Free plan');
        $('#subscription_plan').submit();
    });
    $('#premium_plan').click(function() {
        $('input[name*="cmd"]').val('_s-xclick');
        $('input[name*="amount"]').val('99');
        $('#selected_plan').val('Premium plan');
        $('#subscription_plan').submit();
    });
    $('#business_plan').click(function() {
        $('input[name*="cmd"]').val('_s-xclick');
        $('input[name*="amount"]').val('299');
        $('#selected_plan').val('Business plan');
        $('#subscription_plan').submit();
    });
    $('#enterprise_plan').click(function() {
        $('input[name*="cmd"]').val('_s-xclick');
        $('input[name*="amount"]').val('299');
        $('#selected_plan').val('Enterprise plan');
        $('#subscription_plan').submit();
    });

    /* Referral program */

    var socialMessage;
    socialMessage = $('#socialMessage');

    $('#share_facebook').click(function() {
        var u = copyfight.refurl;
        var t = socialMessage.val();
        window.open('http://www.facebook.com/sharer.php?u=' + encodeURIComponent(u) + '&t=' + encodeURIComponent(t), 'sharer', 'toolbar=0,status=0,width=626,height=436');
        return false;
    });

    $('#share_twitter').click(function() {
        var u = copyfight.refurl;
        var t = 'Earn a bonus by signing up to a Copyfight account at the following link:';
        var url = 'http://twitter.com/share?url=' + encodeURIComponent(u) + '&text=' + encodeURIComponent(t) + '&count=none';
        window.open(url, 'sharer', 'toolbar=0,status=0,width=626,height=436');
        return false;
    });

});
