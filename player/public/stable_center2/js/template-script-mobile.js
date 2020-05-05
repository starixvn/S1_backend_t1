$(function(){

});

//=====_pubutils============================
if(typeof _pubutils == "undefined"){
  _pubutils={
    variables: {
      debugLog: true
    },
    safelog: function(msg){
        //check exists console.log
        if(variables.debugLog && typeof(console)!='undefined' && console.log){
            console.log(msg);
        }
    }
  };
}

_pubutils._init_lock_page=function(){
    if($('#_lock_screen').length<=0){
        $('body').append('<div style="display: none" id="_lock_screen"></div>');
    }
}

_pubutils._lock_page=function(msg){
    _pubutils._init_lock_page();
    $('#_lock_screen').addClass('_overlay_screen').html(msg).fadeTo(0, 0.4).css('display', 'flex');
}

_pubutils._unlock_page=function(){
    _pubutils._init_lock_page();
    $('#_lock_screen').removeClass('_overlay_screen').html('').css('display', 'none');
}

_pubutils._switchPlayerCurrencyOnLogged=function(key){
    var url= '/iframe/auth/change_active_currency_for_logged/'+key+'/false';

    _pubutils._lock_page(lang('Changing Currency'));
    $.ajax(
    {
        url: url,
        cache: false,
        dataType: 'json',
        success: function(data){
            if(data && data['success']){
                // changing_currency=false;
                window.location.reload();
            }else{
                alert(lang('Change Currency Failed'));
                _pubutils._unlock_page();
            }
        },
        error: function(){
            alert(lang('Change Currency Failed'));
            _pubutils._unlock_page();
        }
    }
    );
};

_pubutils._switchPlayerCurrencyOnLogin=function(key){
    var url= '/iframe/auth/change_active_currency/false?__OG_TARGET_DB='+key;

    _pubutils._lock_page(lang('Changing Currency'));
    $.ajax(
    {
        url: url,
        cache: false,
        dataType: 'json',
        success: function(data){
            if(data && data['success']){
                // changing_currency=false;
                window.location.reload();
            }else{
                alert(lang('Change Currency Failed'));
                _pubutils._unlock_page();
            }
        },
        error: function(){
            alert(lang('Change Currency Failed'));
            _pubutils._unlock_page();
        }
    }
    );
};

_pubutils.init=function(){
    $('._select_currecny_on_login').change(function(){
        var key=$(this).val();
        _pubutils._switchPlayerCurrencyOnLogin(key);
    });

    $('._select_currecny_on_logged').change(function(){
        var key=$(this).val();
        _pubutils._switchPlayerCurrencyOnLogged(key);
    });
};

$(function(){
    _pubutils.init();
});
//=====_pubutils============================

