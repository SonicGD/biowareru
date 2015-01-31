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
