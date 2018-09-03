(function() {
    var faq = $('#about-faq')[0].children;

    $('.faq h5').click(function(){
        var index = $(this).index();
        $(faq[index + 1]).slideToggle("slow");
        $(this).toggleClass('active');

        var icon = $(this).find('span');
        if ($(this).hasClass('active')){
            icon.removeClass('fa-plus').addClass('fa-minus');
        } else {
            icon.removeClass('fa-minus').addClass('fa-plus')
        }
    })

    $('.faq h2').append('<hr>');
    $('.faq h5').addClass('card-header px-3');
    $('.faq p').addClass('card-block px-3');
    $('.faq h5').append('<span class="fa fa-plus"></span>');

    var icons = $('#about-faq').find('span');
    icons.css({"float": "right", "flex" : "1", "align-self": "right", "vertical-align": "middle"});

})();
