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
