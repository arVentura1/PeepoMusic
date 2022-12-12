"use strict";

$(function(){

    //acceder animation
    $('.message a').click(function(){
        $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
    });

    //mostrar contraseña cuando el botón está activo
    $('.ojo').click(function () {

        $(this).toggleClass("ojoTapado");

        let inputLogin = $("#contraLogin");
        let inputRegister = $("#contraRegister");
        let inputClaveEditarPerfil = $("#contraEditarPerfil");

        if (
            inputLogin.attr("type") === "password" ||
            inputRegister.attr("type") === "password" ||
            inputClaveEditarPerfil.attr("type") === "password"
        ) {
            inputLogin.attr("type", "text");
            inputRegister.attr("type", "text");
            inputClaveEditarPerfil.attr("type", "text");
        } else {
            inputLogin.attr("type", "password");
            inputRegister.attr("type", "password");
            inputClaveEditarPerfil.attr("type", "password");
        }
    });

    // caracteres restantes de la biografia
    $("#biografia").on('keyup', function() {
        var maxLength = 350;
        var textlen = maxLength - $(this).val().length;
        $('#rchars').text(textlen);
    });

    //lista de canciones activos (hover no funciona del demasiado fino)
    $(".lista-elementos").mouseenter(function(){
        $(this).addClass("active");
    });
    $(".lista-elementos").mouseleave(function(){
        $(this).removeClass("active");
    });


});

function readURL(input) {
    if (input.files && input.files[0]) {

        var reader = new FileReader();

        reader.onload = function(e) {
            $('.image-upload-wrap').hide();

            $('.file-upload-image').attr('src', e.target.result);
            $('.file-upload-content').show();

            $('.image-title').html(input.files[0].name);
        };

        reader.readAsDataURL(input.files[0]);
    }
}