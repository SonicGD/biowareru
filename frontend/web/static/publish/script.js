$(function () {
   $('.header__menu-item').on('click', function () {
       if ($('.header__submenu', this).length && !$(this).hasClass('header__menu-item_open')) {
           closeSubmenu();
           openSubmenu(this);
           return false;
       }
   });
    $(document).on('click', function() {
        if ($(event.target).closest('.header__submenu').length == 0 ) {
            closeSubmenu();
        }
    })
});

function openSubmenu (activeItem) {
    $(activeItem).addClass('header__menu-item_open');
    $('.header__submenu', activeItem).slideDown(300);
}

function closeSubmenu () {
    $('.header__menu-item_open').removeClass('header__menu-item_open');
    $('.header__submenu').slideUp(300);
}
$(function () {

    var $skin = $('#skin');

    if (!$skin.length) {
        return;
    }

    $skin.on('click', 'button', function () {
        var $this = $(this),
            skinName = $this.data('name');
        $('#stylesheet').attr('href', 'publish/style_' + skinName + '.css');
        $this.addClass('skin__item_active').siblings().removeClass('skin__item_active');
    });

});
$(function () {
    var $carouselWrap = $('.slider'),
        $carousel = $carouselWrap.find('.slider__list');
    $carousel.owlCarousel({
        items: 12,
        loop: true,
        dots: false,
        autoWidth: true
    });
    $($carouselWrap).find('.slider__control_left').on("click", function() {
        $carousel.trigger('prev.owl.carousel');
    });
    $carouselWrap.find('.slider__control_right').on("click", function() {
        $carousel.trigger('next.owl.carousel');
    });
});
