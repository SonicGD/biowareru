$(function () {
    if ($('.auth__text').length) {
        var $auth = $('.auth');
        var $popup  = $('.popup');

        var leftStart = $auth.offset().left;
        var topStart = $auth.offset().top;


        $('.auth__text').on('click', function () {
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
        });

        $('.popup__wrp').on('click', function (event) {
            if ($(event.target).closest('.popup').length == 0) {
                $('.popup__login-form').hide();
                $('.popup__wrp').removeClass('show');
                $($auth).css('visibility', 'visible');
            }
        })
    }

});
