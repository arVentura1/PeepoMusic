"use strict";
$(function(){
    //
    $(".pregunta-gestionar").click(function(){
        $(this).siblings(".gestion-administracion").slideToggle();
        $(this).children(".flechita").toggleClass("flecha-activa");
    });
});