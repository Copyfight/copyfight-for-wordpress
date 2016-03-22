jQuery(function($) {

    $('body').on('copy', function(e) {
        parentEl = getSelectionParentElement();
        if ($(parentEl).hasClass('copyfight_content')) {
            $('#copyfight_notice').attr('type', 'text');
            $('#copyfight_notice').select();
            document.execCommand('copy');
            $('#copyfight_notice').focus();
            $('#copyfight_notice').prop('disabled', true);
        }
    });

    function getSelectionParentElement() {
        var parentEl = null, sel;
        if (window.getSelection) {
            sel = window.getSelection();
            if (sel.rangeCount) {
                parentEl = sel.getRangeAt(0).commonAncestorContainer;
                if (parentEl.nodeType != 1) {
                    parentEl = parentEl.parentNode.parentNode;
                }
            }
        } else if ( (sel = document.selection) && sel.type != "Control") {
            parentEl = sel.createRange().parentElement().parentElement();
        }
        return parentEl;
    }

});