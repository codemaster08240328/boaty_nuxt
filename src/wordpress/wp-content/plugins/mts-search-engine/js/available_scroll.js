jQuery(document).ready(function(){
    if( jQuery('#available').length == 1  )
    {
        jQuery('#available').scrollToFixed({marginTop: jQuery('.mk-header-inner').outerHeight(true) + 30,
            limit: function() {
                var limit = jQuery('.mk-about-author-wrapper').offset().top-jQuery('#available').outerHeight(true) ;
                return limit;
            },
            zIndex: 99,
              fixed: function() {  },
            dontCheckForPositionFixedSupport: true});
            
            
        jQuery('#available').bind('unfixed.ScrollToFixed', function() {
            if (window.console) console.log('summary unfixed');
            jQuery('.mk-header-inner').trigger('unfixed.ScrollToFixed');
        });
    }
});