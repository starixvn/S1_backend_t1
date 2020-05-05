var lang = window._sbe_i18n = function(){
    var args = Array.prototype.slice.call(arguments);

    if(args.length <= 0){
        return '';
    }

    var lang_key = args.shift();

    var lang_text = (_export_sbe_t1t.variables.langText.hasOwnProperty(lang_key)) ? _export_sbe_t1t.variables.langText[lang_key] : lang_key;

    lang_text = lang_text.replace(/{(\d+)}/g, function(match, number) {
        return typeof args[number] != 'undefined' ? args[number] : match;
    });

    return lang_text;
};

var language = {
    "switch_language": function(lang_id, lang_code, redirect_url){
        var self = this;
        var deferred_set_player_center_language = $.Deferred();
        var deferred_set_static_site_language = $.Deferred();
        var deferred_set_cookie_language = $.Deferred();

        $.when(deferred_set_player_center_language, deferred_set_static_site_language, deferred_set_cookie_language).done(function(){
            var event = $.Event("switch.t1t.language", {
                "delegateTarget": self,
                "currentTarget": self,
                "relatedTarget": self,
                "target": self
            });
            utils.events.trigger(event, self, lang_id, lang_code);

            if(event.isDefaultPrevented()) return;

            if(!!redirect_url){
                window.location.href = redirect_url;
            }else{
                window.location.reload(true);
            }
        });

        // process deferred_set_player_center_language
        var player_center_url = utils.getApiUrl('set_language') + '/' + lang_id;
        utils.getJSONP(player_center_url, null, function(result){
            if(result['status'] === 'success'){
                deferred_set_player_center_language.resolve(true);
            }else{
                deferred_set_player_center_language.resolve(false);
            }
        }, function(){
            deferred_set_player_center_language.resolve(false);
        });

        // process deferred_set_static_site_language
        if(variables.enabled_switch_language_also_set_to_static_site){
            var static_site_url = (utils.is_mobile()) ? utils.getSystemUrl('m', '/?lang=' + lang_code) : utils.getSystemUrl('www', '/?lang=' + lang_code);

            // static site results do not affect the result
            utils.getJSONP(static_site_url, null, function(){
                deferred_set_static_site_language.resolve(true);
            }, function(){
                deferred_set_static_site_language.resolve(true);
            });
        }else{
            deferred_set_static_site_language.resolve(true);
        }

        // process deferred_set_cookie_language
        cookies.set('_lang', lang_code, {
            "domain": '.' + variables.main_host
        });
        deferred_set_cookie_language.resolve(true);
    }
};