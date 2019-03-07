jQuery(document).ready(function(){
    if( jQuery('#page_search').length == 1 )
    {
        jQuery('#page_search').scrollToFixed({marginTop: -5,
            limit: function() {
                var limit = jQuery('#mk-footer').offset().top-400;
                return limit;
            },
            zIndex: 199});
    }

});