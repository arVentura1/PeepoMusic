"use strict";

$(function() {

    //progress-bar in webpage scroll
    $(document).on("scroll", function(){
        let pixels = $(document).scrollTop();
        let webHeight = $(document).height() - $(window).height();
        let progress = 100 * pixels/webHeight;

        $(".progress-bar").css({"width":progress+"%"});
    });

    //faq wrapper
    $(".pregunta-titulo").click(function() {
        $(this).toggleClass("pregunta-titulo-activo");
        $(this).children(".inner-pt").children(".flechita").toggleClass("flecha-activa");
        $(this).next(".inner-pt-text").slideToggle();
    });
    //
    var swiper = new Swiper(".mySwiper", {
        spaceBetween: 100,
        speed: 750,
        loop: true,

        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },

        effect: "coverflow",
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: "auto",
        coverflowEffect: {
            rotate: 50,
            stretch: 0,
            depth: 100,
            modifier: 1,
            slideShadows: true,
        }
    });

    //swiper de los likes del frontpage
    var swiper2 = new Swiper(".mySwiper2", {
        spaceBetween: 50,
        speed: 750,
        loop: true,

        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        slidesPerView: "auto",
        centeredSlides: true,
        //spaceBetween: 30,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
    });

    //swiper de las canciones mas escuchadas
    var swiper = new Swiper(".mySwiper3", {
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });

    //
    //back to top button
    //$(".back-to-top").click(function(){
    //    $("html, body").animate({scrollTop: 0}, 0);
    //});

});

function myCopy() {
    var copyText = document.getElementById("compartirEnlace");

    copyText.select();
    copyText.setSelectionRange(0, 99999);
    
    document.execCommand("copy");
}
