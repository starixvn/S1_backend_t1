$(function(){
    var clipboard = null;

    var datatable = $('.agent_source_code_list_container .agent_source_code_list').DataTable({
        "columnDefs": [
            {
                "targets": [3],
                "render": function(data, type, row, meta){
                    var text = null;
                    switch(parseInt(data)){
                        case AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_AGENT:
                            text = LANG_AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_AGENT;
                            break;
                        case AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_PLAYER:
                        default:
                            text = LANG_AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_PLAYER;
                            break;
                    }
                    return text;
                }
            },
            {
                "targets": [4],
                "render": function(data, type, row, meta){
                    if(!datatable){
                        return false;
                    }

                    var link = datatable_generator_link(row[0]);

                    return '<a href="' + link + '" target="_blank">' + link + '</a>';
                }
            },
            {
                "targets": [5],
                "render": function(data, type, row, meta){
                    if(!datatable){
                        return false;
                    }

                    var jElement = $('<div>' + data + '</div>');
                    $('.btn-copy', jElement).attr('data-clipboard-text', datatable_generator_link(row[0]));

                    return jElement.html();
                }
            }
        ],
        "dom": '<\'row\'<\'col-sm-3\'l><\'col-sm-6 toolbar\'><\'col-sm-3\'f>>rtip',
        "buttons": [],
        "searching": false,
        "order": [[1, 'desc']],
        "fnInitComplete": function(){
            var tpl = $($('.tpl_datatable_toolbar')[0].outerHTML.replace(/(\r|\n)/gm,""));

            var select = $('select', tpl);
            select.addClass('datatable_domain_list_field');
            select.change(function(){
                datatable.rows().invalidate('data').draw(false);
            });

            $('.toolbar', datatable.table().container()).html(tpl);

            datatable.rows().invalidate('data').draw(false);
        }
    });

    function datatable_generator_link(code){
        var toolbar = $('.toolbar', datatable.table().container());
        var select = $('select', toolbar);

        if(select.length <= 0){
            return false;
        }

        var text = $.trim($('.tpl_tracking_source_code_link').text());

        text = text.replace('{DOMAIN}', select.val());
        text = text.replace('{CODE}', code);
        return text;
    }

    $('#random_code').on('click', function(){
        var code = null;
        if(SYSTEM_FEATURE_AGENT_TRACKING_CODE_NUMBERS_ONLY){
            code = randomNumber(AGENCY_TRACKING_CODE_RANDOM_GENERATE_LANGTH);
        }else{
            code = randomCode(AGENCY_TRACKING_CODE_RANDOM_GENERATE_LANGTH);
        }

        $('#tracking_code').val(code);
    });
});

function agency_tracking_ajax_submit(form_action, form_data, callback){
    var ajax_options = {
        type: "POST",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        url: form_action,
        data: form_data,
        success: function(data) {
            return agency_tracking_ajax_response(data, callback);
        },
        error: function(){
            window.location.reload();
        }
    };

    $.ajax(ajax_options);
}

function agency_tracking_ajax_response(data, callback){
    var bootstrap_dialog_optiosn = {
        message: data.message,
        onhidden: function(){
            if(typeof callback === "function"){
                callback(data);
            }else{
                window.location.reload();
            }
        }
    };
    if(data.status === "success"){
        bootstrap_dialog_optiosn['type'] = BootstrapDialog.TYPE_INFO;
        bootstrap_dialog_optiosn['title'] = LANG_ALERT_INFO;
    }else{
        bootstrap_dialog_optiosn['type'] = BootstrapDialog.TYPE_DANGER;
        bootstrap_dialog_optiosn['title'] = LANG_ALERT_DANGER;
    }
    BootstrapDialog.show(bootstrap_dialog_optiosn);
}

function agency_tracking_ajax_form_init(dialogRef){
    var frm = dialogRef.getModalBody().find('form');

    frm.on('keypress keydown keyup', function(e){
        if(e.keyCode == 13){
            e.preventDefault();
        }
    });

    frm.on('submit', function(){
        agency_tracking_ajax_submit(frm.attr('action'), frm.serialize());
        return false;
    });
}

function generate_source_code_shorturl(agentTrackingId, agentTrackingCode, protocol, shorturl){
    var domain = $('.datatable_domain_list_field').val();

    try {
        shorturl = JSON.parse(decodeURIComponent(shorturl));
    }catch(e){
        shorturl = {};
    }

    if(shorturl.hasOwnProperty(agentTrackingCode) && shorturl[agentTrackingCode].hasOwnProperty(domain) && !!shorturl[agentTrackingCode][domain][protocol]){
        show_source_code_shorturl(shorturl[agentTrackingCode][domain], protocol);

        return;
    }

    $.ajax({
        'url' : '/' + controller_name + '/generate_source_code_shorturl?agentTrackingId=' + agentTrackingId + '&agentTrackingDomain=' + domain,
        'type' : 'GET',
        'success' : function(data) {
            if(data.status !== "success"){
                var bootstrap_dialog_optiosn = {
                    message: data.message,
                    onhidden: function(){
                        if(typeof callback === "function"){
                            callback(data);
                        }else{
                            window.location.reload();
                        }
                    }
                };
                bootstrap_dialog_optiosn['type'] = BootstrapDialog.TYPE_DANGER;
                bootstrap_dialog_optiosn['title'] = LANG_ALERT_DANGER;

                BootstrapDialog.show(bootstrap_dialog_optiosn);

                return;
            }

            show_source_code_shorturl(data['short_url'], protocol, function(){
                window.location.reload();
            });
        }
    },'json');
}

function show_source_code_shorturl(shorturl, protocol, callback){
    var tpl = $($('.tpl_generate_source_code_shorturl')[0].outerHTML.replace(/(\r|\n)/gm,""));

    $('.agency_source_code_shorturl_field', tpl).attr('id', 'agency_source_code_shorturl_field');

    if(!!shorturl){
        if("string" === typeof shorturl){
            $('.agency_source_code_shorturl_field', tpl).attr('value', shorturl);
        }else if("object" === typeof shorturl && shorturl.hasOwnProperty(protocol)){
            $('.agency_source_code_shorturl_field', tpl).attr('value', shorturl[protocol]);
        }
    }

    var clipboard;

    BootstrapDialog.show({
        title: LANG_LABEL_AGENT_ADDITIONAL_DOMAIN,
        message: tpl.html(),
        spinicon: 'fa fa-spinner fa-spin',
        autodestroy: true,
        onshow: function(dialog) {
            var button = dialog.getButton('bootstrap-dialog-button-copy');
            button.attr('data-clipboard-target', '#agency_source_code_shorturl_field');
        },
        onshown: function(dialog){
            clipboard = new ClipboardJS('#bootstrap-dialog-button-copy');
            clipboard.on('success', function(e){
                clipboard_success();
            });
        },
        onhidden: function(dialog){
            clipboard.destroy();

            if(typeof callback === "function"){
                callback();
            }
        },
        buttons: [{
            id: 'bootstrap-dialog-button-copy',
            label: LANG_LABEL_COPY
        }, {
            label: LANG_LABEL_CLOSE,
            action: function(dialogRef){
                dialogRef.close();
            }
        }]
    });
}

function unlock_tracking_code(){
    $.ajax({
        'url' : '/' + controller_name + '/log_unlock_trackingcode',
        'type' : 'GET',
        'success' : function(data) {
            $('.btn_update_tracking_code').show();
            $('#random_code_lock').hide();
            $('#tracking_code').prop("readonly",false);
        }
    },'json');
}

function cancel_update_tracking_code(){

    $('.btn_update_tracking_code').hide();
    $('#random_code_lock').show();
    $('#tracking_code').prop("readonly",true);

}

function submit_update_tracking_code(){
    var form = $('#update_agent_tracking_code');
    if(form.length <= 0){
        return false;
    }

    form.on('submit', function(){
        return false;
    });

    form.on('keypress keydown keyup', function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
        }
    });

    agency_tracking_ajax_submit(form.attr('action'), form.serialize());

    return false;
}

function randomCode(len){
    var text = '';

    var charset = "abcdefghijklmnopqrstuvwxyz0123456789";

    for(var i = 0; i < len; i++){
        text += charset.charAt(Math.floor(Math.random() * charset.length));
    }

    return text;
}

function randomNumber(len){
    var text = '';

    var charset = "0123456789";

    for(var i = 0; i < len; i++){
        text += charset.charAt(Math.floor(Math.random() * charset.length));
    }

    return text;
}

function newAdditionalDomain(){
    var tpl = $($('.tpl_new_add_domain')[0].outerHTML.replace(/(\r|\n)/gm,""));

    BootstrapDialog.show({
        title: LANG_LABEL_AGENT_ADDITIONAL_DOMAIN,
        message: tpl.html(),
        spinicon: 'fa fa-spinner fa-spin',
        onshow: agency_tracking_ajax_form_init,
        buttons: [{
            icon: 'fa fa-save',
            label: LANG_LABEL_SAVE,
            cssClass: 'btn-primary',
            autospin: true,
            action: function(dialogRef){
                dialogRef.enableButtons(false);
                dialogRef.setClosable(false);
                // utils.safelog(dialogRef);

                var frm=dialogRef.getModalBody().find('.frm_new_add_domain');
                frm.submit();
            }
        }, {
            label: LANG_LABEL_CLOSE,
            action: function(dialogRef){
                dialogRef.close();
            }
        }]
    });
}

function editAdditionalDomain(agentTrackingId, agent_domain){
    var tpl = $($('.tpl_edit_add_domain')[0].outerHTML.replace(/(\r|\n)/gm,""));
    $('form', tpl).attr('action', $('form', tpl).attr('action') + '/' + agentTrackingId);
    $('.agency_domain_field', tpl).attr('value', agent_domain);

    BootstrapDialog.show({
        title: LANG_LABEL_AGENT_ADDITIONAL_DOMAIN,
        message: tpl.html(),
        spinicon: 'fa fa-spinner fa-spin',
        onshow: agency_tracking_ajax_form_init,
        buttons: [{
            label: LANG_RANDOM_GERNERATE,
            action: function(dialogRef){
                var code = null;
                if(SYSTEM_FEATURE_AGENT_TRACKING_CODE_NUMBERS_ONLY){
                    code = randomNumber(AGENCY_TRACKING_CODE_RANDOM_GENERATE_LANGTH);
                }else{
                    code = randomCode(AGENCY_TRACKING_CODE_RANDOM_GENERATE_LANGTH);
                }

                $('.agency_domain_field', dialogRef.getModalBody()).val(code);
            }
        }, {
            icon: 'fa fa-save',
            label: LANG_LABEL_SAVE,
            cssClass: 'btn-primary',
            autospin: true,
            action: function(dialogRef){
                dialogRef.enableButtons(false);
                dialogRef.setClosable(false);
                // utils.safelog(dialogRef);

                var frm=dialogRef.getModalBody().find('.frm_edit_add_domain');
                frm.submit();
            }
        }, {
            label: LANG_LABEL_CLOSE,
            action: function(dialogRef){
                dialogRef.close();
            }
        }]
    });
}

function removeAdditionalDomain(agentTrackingId, agent_domain){
    var tpl = $($('.tpl_remove_add_domain')[0].outerHTML.replace(/(\r|\n)/gm,""));
    $('form', tpl).attr('action', $('form', tpl).attr('action') + '/' + agentTrackingId);
    $('.agency_domain_field', tpl).attr('value', agent_domain);

    BootstrapDialog.show({
        title: LANG_LABEL_AGENT_ADDITIONAL_DOMAIN,
        message: tpl.html(),
        spinicon: 'fa fa-spinner fa-spin',
        onshow: agency_tracking_ajax_form_init,
        buttons: [{
            icon: 'fa fa-save',
            label: LANG_LABEL_REMOVE,
            cssClass: 'btn-danger',
            autospin: true,
            action: function(dialogRef){
                dialogRef.enableButtons(false);
                dialogRef.setClosable(false);
                // utils.safelog(dialogRef);

                var frm=dialogRef.getModalBody().find('.frm_remove_add_domain');
                frm.submit();
            }
        }, {
            label: LANG_LABEL_CLOSE,
            action: function(dialogRef){
                dialogRef.close();
            }
        }]
    });

}

function newSourceCode(){
    var tpl = $($('.tpl_new_source_code')[0].outerHTML.replace(/(\r|\n)/gm,""));

    var code = null;
    if(SYSTEM_FEATURE_AGENT_TRACKING_CODE_NUMBERS_ONLY){
        code = randomNumber(AGENCY_TRACKING_CODE_RANDOM_GENERATE_LANGTH);
    }else{
        code = randomCode(AGENCY_TRACKING_CODE_RANDOM_GENERATE_LANGTH);
    }

    $('.agency_source_code_field', tpl).attr('value', code);

    BootstrapDialog.show({
        title: LANG_LABEL_AGENT_SOURCE_CODE,
        message: tpl.html(),
        spinicon: 'fa fa-spinner fa-spin',
        onshow: agency_tracking_ajax_form_init,
        buttons: [{
            label: LANG_RANDOM_GERNERATE,
            action: function(dialogRef){
                var code = null;
                if(SYSTEM_FEATURE_AGENT_TRACKING_CODE_NUMBERS_ONLY){
                    code = randomNumber(AGENCY_TRACKING_CODE_RANDOM_GENERATE_LANGTH);
                }else{
                    code = randomCode(AGENCY_TRACKING_CODE_RANDOM_GENERATE_LANGTH);
                }

                $('.agency_source_code_field', dialogRef.getModalBody()).val(code);
            }
        }, {
            icon: 'fa fa-save',
            label: LANG_LABEL_SAVE,
            cssClass: 'btn-primary',
            autospin: true,
            action: function(dialogRef){
                dialogRef.enableButtons(false);
                dialogRef.setClosable(false);
                // utils.safelog(dialogRef);

                var frm=dialogRef.getModalBody().find('.frm_new_source_code');
                frm.submit();
            }
        }, {
            label: LANG_LABEL_CLOSE,
            action: function(dialogRef){
                dialogRef.close();
            }
        }]
    });

}

function editSourceCode(agentTrackingId, bonusRate, playerType, sourceCode, rebateRate){
    var tpl = $($('.tpl_edit_source_code')[0].outerHTML.replace(/(\r|\n)/gm,""));
    $('form', tpl).attr('action', $('form', tpl).attr('action') + '/' + agentTrackingId);
    $('.agency_bonus_rate_field', tpl).attr('value', bonusRate);
    $('.agency_bonus_rate_field option', tpl).each(function(){
        if(parseInt(bonusRate) === parseInt($(this).attr('value'))){
            $(this).attr('selected', 'selected');
        }
    });
    $('.agency_rebate_rate_field', tpl).attr('value', rebateRate);

    $('.agency_player_type_field', tpl).attr('value', playerType);
    $('.agency_player_type_field option', tpl).each(function(){
        if(parseInt(playerType) === parseInt($(this).attr('value'))){
            $(this).attr('selected', 'selected');
        }
    });
    $('.agency_source_code_field', tpl).attr('value', sourceCode);

    BootstrapDialog.show({
        title: LANG_LABEL_AGENT_SOURCE_CODE,
        message: tpl.html(),
        spinicon: 'fa fa-spinner fa-spin',
        onshow: agency_tracking_ajax_form_init,
        buttons: [{
            label: LANG_RANDOM_GERNERATE,
            action: function(dialogRef){
                var code = null;
                if(SYSTEM_FEATURE_AGENT_TRACKING_CODE_NUMBERS_ONLY){
                    code = randomNumber(AGENCY_TRACKING_CODE_RANDOM_GENERATE_LANGTH);
                }else{
                    code = randomCode(AGENCY_TRACKING_CODE_RANDOM_GENERATE_LANGTH);
                }

                $('.agency_source_code_field', dialogRef.getModalBody()).val(code);
            }
        }, {
            icon: 'fa fa-save',
            label: LANG_LABEL_SAVE,
            cssClass: 'btn-primary',
            autospin: true,
            action: function(dialogRef){
                dialogRef.enableButtons(false);
                dialogRef.setClosable(false);
                // utils.safelog(dialogRef);

                var frm=dialogRef.getModalBody().find('.frm_edit_source_code');
                frm.submit();
            }
        }, {
            label: LANG_LABEL_CLOSE,
            action: function(dialogRef){
                dialogRef.close();
            }
        }]
    });

}

function removeSourceCode(agentTrackingId, bonusRate, playerType, sourceCode, rebateRate){
    var tpl = $($('.tpl_remove_source_code')[0].outerHTML.replace(/(\r|\n)/gm,""));
    $('form', tpl).attr('action', $('form', tpl).attr('action') + '/' + agentTrackingId);
    $('.agency_bonus_rate_field', tpl).attr('value', bonusRate).prop('disabled', true).attr('disabled', 'disabled');
    $('.agency_bonus_rate_field option', tpl).each(function(){
        if(parseInt(bonusRate) === parseInt($(this).attr('value'))){
            $(this).attr('selected', 'selected');
        }
    });
    $('.agency_rebate_rate_field', tpl).attr('value', rebateRate).prop('disabled', true).attr('disabled', 'disabled');

    $('.agency_player_type_field', tpl).attr('value', playerType).prop('disabled', true).attr('disabled', 'disabled');
    $('.agency_player_type_field option', tpl).each(function(){
        if(parseInt(playerType) === parseInt($(this).attr('value'))){
            $(this).attr('selected', 'selected');
        }
    });
    $('.agency_source_code_field', tpl).attr('value', sourceCode).prop('disabled', true).attr('disabled', 'disabled');

    BootstrapDialog.show({
        title: LANG_LABEL_AGENT_SOURCE_CODE,
        message: tpl.html(),
        spinicon: 'fa fa-spinner fa-spin',
        onshow: agency_tracking_ajax_form_init,
        buttons: [{
            icon: 'fa fa-save',
            label: LANG_LABEL_REMOVE,
            cssClass: 'btn-danger',
            autospin: true,
            action: function(dialogRef){
                dialogRef.enableButtons(false);
                dialogRef.setClosable(false);
                // utils.safelog(dialogRef);

                var frm=dialogRef.getModalBody().find('.frm_remove_source_code');
                frm.submit();
            }
        }, {
            label: LANG_LABEL_CLOSE,
            action: function(dialogRef){
                dialogRef.close();
            }
        }]
    });

}