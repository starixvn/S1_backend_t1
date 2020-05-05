$(document).ready(function(){
    MessageBox.setOptions({
        'header_title_success': lang('alert-success'),
        'header_title_info': lang('alert-info'),
        'header_title_warning': lang('alert-warning'),
        'header_title_danger': lang('alert-danger'),
        'close_button_text': lang('lang.close')
    });

    window.show_loading = function(){
        Loader.show();
    };

    window.stop_loading = function(){
        Loader.hide();
    };

    hack_virtual_keyboard();
});

var hack_virtual_keyboard_timer;

function hack_virtual_keyboard(){
    var query = $('input, select, textarea');

    var check = function(){
        var is_focused = false;

        query.each(function(){
            is_focused = $(this).is(':focus') ? true : is_focused;
        });

        if(is_focused){
            $('.container-fluid').addClass('kbactive');
        }else{
            $('.container-fluid').removeClass('kbactive');
        }
    };

    if(query.length){
        if(hack_virtual_keyboard_timer){
            clearInterval(hack_virtual_keyboard_timer);
        }
        hack_virtual_keyboard_timer = window.setInterval(function(){
            check();
        }, 500);
    }

    $(window).resize(function(){
        check();
    });
}