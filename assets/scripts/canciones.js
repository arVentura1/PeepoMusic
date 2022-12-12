"use strict";

$( document ).ready(function() {
    duration();
});

$(function(){
    //duracion de la cancion
    duration();

    //si hago click en volume slider, poner muted = false
    $("#volume-slider").click(function(){
        document.getElementById("player").muted = false;
    });

    /*
    //poner estilos del boton play pause
    $(".iconoRepro").mouseenter(function (){
        $(this).css({"color":"#1abc9c"});
    });
    //quitar estilos del boton de play pause
    $(".iconoRepro").mouseleave(function (){
        $(this).css({"color":"#fff"});
    });
    */

    $('#player').on('timeupdate', function() {
        let $audio = document.getElementById("player");
        let $seekbar = document.getElementById("duration_slider");

        if ($audio.currentTime == $audio.duration) {
            //$seekbar.value = 0;
            $('#duration_slider').val('0');
            $(".fotoCancion").removeClass("rotating2");
            $(".fa-pause").addClass("ocultar-icono");
            $(".fa-play").removeClass("ocultar-icono");
        }
        //progreso de la cancion
        //formato 00:00
        let minutos = Math.floor($audio.currentTime / 60);
        minutos = (minutos >= 10) ? minutos : "0" + minutos;
        let segundos = Math.floor($audio.currentTime % 60);
        segundos = (segundos >= 10) ? segundos : "0" + segundos;
        // empieza en 0:0 y avanza mejor
        var minutos2 = parseInt($audio.currentTime / 60, 10);
        var segundos2 = parseInt($audio.currentTime % 60);
        //
        $(".progreso-cancion").text(minutos + ":" + segundos);
    });

    //reproductor de cancion
    $(".accionCancion").click(function(){

        let $audio = document.getElementById("player");
        let $seekbar = document.getElementById("duration_slider");

        //console.log("$seekbar.max: ", $seekbar.max);
        console.log("$audio.duration: ", $audio.duration);

        let duracionBarra = $seekbar.max = $audio.duration;
        console.log("duracionBarra: ",duracionBarra);

        $audio.onloadedmetadata = () => duracionBarra;
        $seekbar.onchange = () => $audio.currentTime = $seekbar.value;
        $audio.ontimeupdate = () => $seekbar.value = $audio.currentTime;

        $(this).children(".fa-pause").toggleClass("ocultar-icono");
        $(this).children(".fa-play").toggleClass("ocultar-icono");
        //
        $(".fotoCancion").toggleClass("rotating2");
    });

    //mutear/desmutear volumen
    $(".botonSonido").click(function (){
        if (document.getElementById("player").muted == true) {
            $("#volume-slider").val(100);
            document.getElementById("player").muted = false;
            document.getElementById("player").volume = 1;
            //no está muteado
        }
        else {
            $("#volume-slider").val(0);
            document.getElementById("player").muted = true;
            document.getElementById("player").volume = 0;
            //si está muteado
        }
    });

    //
});


//cantidad de volumen de audio
let audio = document.getElementById("player");
let volume = document.getElementById('volume-slider');
volume.addEventListener("change", function(e) {
    audio.volume = e.currentTarget.value / 100;
})


//let $audio = document.getElementById("player");
//let $seekbar = document.getElementById("duration_slider");!

function duration() {
    var audio = document.getElementById("player");
    if(audio.readyState > 0) {
        var minutes = parseInt(audio.duration / 60, 10);
        var seconds = parseInt(audio.duration % 60);
        //alert(minutes+":"+seconds);
        $(".duracion-cancion").text(minutes + ":" + seconds);
    }
}

//subir cancion
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

//subir portada cancion
function readURL2(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('.image-upload-wrap2').hide();

            $('.file-upload-image2').attr('src', e.target.result);
            $('.file-upload-content2').show();

            $('.image-title2').html(input.files[0].name);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

$('.image-upload-wrap').bind('dragover', function () {
    $('.image-upload-wrap').addClass('image-dropping');
});

$('.image-upload-wrap').bind('dragleave', function () {
    $('.image-upload-wrap').removeClass('image-dropping');
});

function copiarEnlace() {
    var copyText = document.getElementById("compartirEnlace");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
}