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
        });
    }

    function showPopup () {

            var leftStart = $auth.offset().left;
            var topStart = $auth.offset().top - $(document).scrollTop();

            popupOpened = true;

            $('.popup__wrp').addClass('show');
            $auth.css('visibility', 'hidden');

            var authHeight = $auth.height();

            $popup.css({
                'left': leftStart + 'px',
                'top': topStart + 'px',
                'height': authHeight
            })
                .animate({
                    'height': '170px'
                }, function() {
                    $popup.css('height', 'auto');
                });
            $('.popup__login-form').fadeIn(3000);

            $('input[name="login"]', $popup).focus();

            var leftFinish = ($(window).width() - $popup.width())/2;
            var topFinish = ($(window).height() - $popup.height())/2;

            $popup.animate({
                'left': leftFinish,
                'top': topFinish
            }, 600);
    }

    function closePopup () {
        $('.popup__login-form').hide();
        $('.popup__wrp').removeClass('show');
        $auth.css('visibility', 'visible');
        popupOpened = false;
    }

});

$(function () {
    var $form = $('.popup__login-form'),
        error;

    if (!$form.length) return;

    error = new errorConstructor($form.find('.popup__error'));

    $form.find('input').on('keyup', function() {
        error.hide();
    });

    $form.on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: this.action,
            data: $form.serialize(),
            beforeSend: function() {
                error.hide();
            },
            success: function(data) {
                if (data.result === true) {
                    location.reload();
                } else if (data.error) {
                    error.show(data.error);
                } else {
                    error.show('Непредвиденная ошибка');
                }
            },
            error: function(xhr, desc, err) {
                error.show('[' + desc + '] ' + err);
            }
        });
    });

    function errorConstructor($el) {
        this.$el = $el;
        this.show = function(msg) {
            this.$el.html(msg).show();
        };
        this.hide = function() {
            this.$el.hide().empty();
        };
    }
});