var languageText = {};
var datetimeFormat = {};
var langInit = setInterval(function(){
    if (typeof _export_sbe_t1t !== 'undefined') {
        var langText = _export_sbe_t1t.variables.langText;
        $.each(langText, function(i, val) {
            languageText[i] = val;
        });
        var dateFormat = _export_sbe_t1t.variables.server_datetime;
        $.each(dateFormat, function(i, val) {
            datetimeFormat[i] = val;
        });
        //use php render no more js template
        // load_template();
        clearInterval(langInit);
    }
}, 1000);

function load_template() {
    if (typeof _export_sbe_t1t !== 'undefined') {
        //alert();
        var template = $('#tmpl').html();

        var templateCount = $('#header_template').html();
        var dataVariables = _export_sbe_t1t.variables;
        var model = {
                        url: dataVariables.hosts.www ,
                        logo: dataVariables.templates.playercenter_logo  ,
                        textLang : languageText ,
                        imgProvider : dataVariables.templates.img_dir+'/gameProviders',
                        dateFormat : datetimeFormat,
                    };

        if (typeof templateCount !== 'undefined') {
            if(templateCount.length == 0){
                rendered = Mustache.render(unescape(template),model);

                $('#header_template').html(rendered);
                //---- this is for player center only, please remove this in cms site --
                if(!dataVariables.logged) {
                    $('#_player_login_area').remove();
                }
                //auto timer & updat timer
                setInterval(function () { this.autoTimingV2() }, 1000);
                //----------------------------------------------------------------------
                _export_sbe_t1t.renderUI.buildLogin();
            }
              $(".preloader").addClass("preloader-out");
        }
        var templateFooterCount = $('#footer_template').html();
        var template_footer = $('#tmpl_footer').html();
        if (typeof templateFooterCount !== 'undefined') {
            if(templateFooterCount.length == 0){
                rendered_footer = Mustache.render(unescape(template_footer),model);

                $('#footer_template').html(rendered_footer);
            }
              $(".preloader").addClass("preloader-out");
        }
    }

}

function autoTimingV2() {
    serverDatetime = $("#date").text()+' '+$("#time").text();
    var date = new Date(serverDatetime);
    date.setSeconds(date.getSeconds() + 1);
    var yyyy = date.getFullYear().toString();
    var mm = (date.getMonth() + 1).toString();
    var dd = date.getDate().toString();
    $("#date").html(  yyyy  + "/" + (mm.length==2 ? mm : "0" + mm) + "/" + (dd.length==2 ? dd : "0" + dd) );
    $("#time").html(('0' + date.getHours()).slice(-2) + ":" + ('0' + date.getMinutes()).slice(-2) + ":" + ('0' + date.getSeconds()).slice(-2));
}