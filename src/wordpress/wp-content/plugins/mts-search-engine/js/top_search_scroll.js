jQuery(document).ready(function(){
    if( jQuery('#post_type_top').length == 1 )
    {
        jQuery('#post_type_top').scrollToFixed({marginTop: jQuery('.mk-header-inner').outerHeight(true)-50,
            limit: function() {
                var limit = jQuery('.mk-about-author-wrapper').offset().top+400;
                return limit;
            },
            zIndex: 199});
    }

});