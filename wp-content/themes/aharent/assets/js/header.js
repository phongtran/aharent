(function($) {
    $(window).bind( 'scroll', function() {
        if ( $(window).width() < 1000 ) {
            if ($(this).scrollTop() > 50) {
                $('.desktop-header').addClass('fixed');
                $('.search').addClass('fixed');
                $('.breadcrumb').addClass('fixed');
                $('.shop-sidebar').addClass('fixed');
            } else {
                $('.desktop-header').removeClass('fixed');
                $('.search').removeClass('fixed');
                $('.breadcrumb').removeClass('fixed');
                $('.shop-sidebar').removeClass('fixed');
            }
        }
    });
})(jQuery)