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