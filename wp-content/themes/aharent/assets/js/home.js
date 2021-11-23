(function($) {

    $('.modal-open-link').click(function(e) {

        var content = $(this).find('.testi-item').first().attr('data-content');
        $('.testimonial-modal .testi-body p').html(content);
        $('.testimonial-modal .testi-head .person-image').html($(this).find('.testi-item .testi-head .person-image').first().html());
        $('.testimonial-modal .testi-head .person-name').html($(this).find('.testi-item .testi-head .person-name').first().html());
        
        $('.testimonial-modal').show();

        e.preventDefault();
    });

    $('.modal-close-link').click(function(e) {
        $('.testimonial-modal').hide();
        e.preventDefault();
    });

})(jQuery)