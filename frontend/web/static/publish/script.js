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
    if ($('.auth__text').length) {
        var popupOpened = false;
        var $auth = $('.auth');
        var $popup  = $('.popup');

        $('.auth__text').on('click', function () {
            showPopup();
        });

        $('.popup__wrp').on('click', function (event) {
            if ($(event.target).closest('.popup').length == 0) {
                closePopup();
            }
        });

        $(document).on('keyup', function (event) {
            var keyCode = event.keyCode;
            if (keyCode == 27 && popupOpened) {
                closePopup();
            }
        })
    }

    function showPopup () {

            var leftStart = $auth.offset().left;
            var topStart = $auth.offset().top;

            popupOpened = true;

            $('.popup__wrp').addClass('show');
            $($auth).css('visibility', 'hidden');

            var authHeight = $auth.height();

            $popup.css({
                'left': leftStart + 'px',
                'top': topStart + 'px',
                'height': authHeight
            })
                .animate({
                    'height': '170px'
                });
            $('.popup__login-form').fadeIn(3000);

            $('input[name="login"]', $popup).focus();

            var leftFinish = (parseInt($(window).width()) - parseInt($popup.width()))/2;
            var topFinish = (parseInt($(window).height()) - parseInt($popup.height()))/2;

            $popup.animate({
                'left': leftFinish,
                'top': topFinish
            }, 600)
    }

    function closePopup () {
        $('.popup__login-form').hide();
        $('.popup__wrp').removeClass('show');
        $($auth).css('visibility', 'visible');
        popupOpened = false;
    }

});

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
        $carousel = $carouselWrap.find('.slider__list-items');
    $carousel.owlCarousel({
        items: 12,
        loop: true,
        dots: false,
        autoWidth: true
    });
    $($carouselWrap).find('.slider__control_left').on('click', function() {
        $carousel.trigger('prev.owl.carousel');
    });
    $carouselWrap.find('.slider__control_right').on('click', function() {
        $carousel.trigger('next.owl.carousel');
    });
});
