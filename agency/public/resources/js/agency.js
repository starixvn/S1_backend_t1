var base_url = "/";
var imgloader = "/resources/images/ajax-loader.gif";
var MAX_ALLOWED_LEVEL = 10;
var agent_suspended = false;

function isEmptyObject(obj){
    for(var key in obj){
        return false
    }
    return true
}
// set operations for suspended agent users {{{1
function set_suspended_operations(){
    agent_suspended = true;
    $(".agent-oper").addClass("disabled");
    /*
    $(".agent-oper").click(function(e){
        e.preventDefault();
        alert('Operation FORBIDDEN! Current User is Suspended!');
        return false;
    });
    */
}
function set_agent_operations() {
    if (agent_suspended) {
        $(".agent-oper").addClass("disabled");
        $(".agent-oper").removeAttr("onclick");
        $(".agent-oper").click(function(e){
            e.preventDefault();
            alert('Operation FORBIDDEN! Current User is Suspended!');
            return false;
        });
    }
    /*
    else {
        $(".agent-oper").click(function(e){
            e.preventDefault();
            window.location = base_url + "agency/check_login_status";
        });
    }
    */
}
// set operations for suspended agent users }}}1

function clipboard_success(){
    $.notify({
        icon: 'glyphicon glyphicon-info-sign',
        message: LANG_COPY_SUCCESS
    }, {
        type: 'success',
        showProgressbar: true,
        delay: 1000,
        timer: 100,
        placement: {
            from: "bottom",
            align: "right"
        },
        animate: {
            enter: 'animated bounceInDown',
            exit: 'animated bounceOutDown'
        }
    });
}

// form validation {{{1
$(document).ready(function(){
    error = [];
    v_status = {};
    parent_game_types = {};
    parent_details = {};

    if(typeof ClipboardJS === "function"){
        var clipboard = new ClipboardJS('.btn-copy', {
            text: function(trigger) {
                return trigger.getAttribute('data-clipboard-text');
            }
        });
        clipboard.on('success', function(e){
            clipboard_success();
        });
    }
});

// add_player form validation {{{2
function add_player_form_validation(url, fields_str, labels_str) {
    var labels = JSON.parse(labels_str);
    var fields = JSON.parse(fields_str);
    // console.log(fields);
    // console.log(fields.length);
    for (var i = 0; i < fields.length; i++) {
        var id = fields[i];
        var label = labels[id];
        v_status[id] = true;
        // console.log(id);
        // console.log(label);
        check_field_on_blur(url, id, label);
    }
}
// add_player form validation }}}2
// agency form validation {{{2
function agency_form_validation(url, fields_str, labels_str) {
    var labels = JSON.parse(labels_str);
    var fields = JSON.parse(fields_str);
    // console.log(fields);
    // console.log(fields.length);
    for (var i = 0; i < fields.length; i++) {
        var id = fields[i];
        var label = labels[id];
        v_status[id] = true;
        // console.log(id);
        // console.log(label);
        check_field_on_blur(url, id, label);
    }
    set_submit(fields);
}
// agency form validation }}}2
// agency form validation for edit {{{2
function agency_form_validation_edit(url, fields_str, labels_str) {
    var labels = JSON.parse(labels_str);
    var fields = JSON.parse(fields_str);
    // console.log(fields);
    // console.log(fields.length);
    for (var i = 0; i < fields.length; i++) {
        var id = fields[i];
        var label = labels[id];
        v_status[id] = true;
        // console.log(id);
        // console.log(label);
        if (id == 'agent_name' || id == 'structure_name') continue;
        check_field_on_blur(url, id, label);
    }
    set_submit(fields);
}
// agency form validation }}}2
// check_field_on_blur {{{2
function check_field_on_blur(url, id, label) {
    $('#'+id).blur(function(){
        if (requiredCheck($(this).val(), id, label)){
            if (id.indexOf("game_types") != -1){
                validateGameRevShareAndRollingComm($(this).val(), id, label);
            } else {
                validateThruAjax(url, $(this).val(), id);
            }
        } else {
            v_status[id] = false;
        }
    });
}
// check_field_on_blur }}}2
// getParentGameTypeAjax {{{2
function getParentGameTypeAjax(ajax_url, parent_id){
    var data={};
    data["parent_id"] = parent_id;
    // console.log(data);
    if(data){
        $.ajax({
            url : ajax_url,
            type : 'POST',
            data : data,
            dataType : "json",
            cache : false,
        }).done(function (data) {
            // console.log(data);
            if (data.status == "success") {
                parent_game_types = data.parent_game_types;
                parent_details = data.parent_details;
            }
            if (data.status == "error") {
                parent_game_types = {};
                parent_details = {};
            }
        }).fail(function (jqXHR, textStatus) {
            /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
            // location.reload();
        });
    }
} // getParentGameTypeAjax }}}2
function agency_form_validation(url, fields_str, labels_str) {
    var labels = JSON.parse(labels_str);
    var fields = JSON.parse(fields_str);
    // console.log(fields);
    // console.log(fields.length);
    for (var i = 0; i < fields.length; i++) {
        var id = fields[i];
        var label = labels[id];
        v_status[id] = true;
        // console.log(id);
        // console.log(label);
        check_field_on_blur(url, id, label);
    }
    set_submit(fields);
}
// agency form validation }}}2
// validateGameRevShareAndRollingComm {{{2
function validateGameRevShareAndRollingComm(v, id, label) {
    v = parseFloat(v);
    if (id.indexOf("rev_share") > 0){
        validateGameRevShare(v, id, label);
    } else if (id.indexOf("rolling_comm") > 0){
        validateGameRollingComm(v, id, label);
    }
}
// validateGameRevShareAndRollingComm }}}2
// validateGameRevShare {{{2
function validateGameRevShare(v, id, label) {
    if (v < 0.00 || v > 100.00) {
        var message = label + " must be >= 0 and <= 100.00.";
        showErrorOnField(id,message);
        addErrorItem(id);
        return false;
    }

    if (!isEmptyObject(parent_game_types)) {
        game_id = id.replace(/[^0-9]/ig,"");
        parent_v = parent_game_types[game_id].rev_share;
        if (v > parent_v) {
            var message = label + " must be <= parent game rev share.";
            showErrorOnField(id,message);
            addErrorItem(id);
            return false;
        }
    }
    return true;
}
// validateGameRevShare }}}2
// validateGameRollingComm {{{2
function validateGameRollingComm(v, id, label) {
    if (v < 0.00 || v > 3.00) {
        var message = label + " must be >= 0.00 and <= 3.00.";
        showErrorOnField(id,message);
        addErrorItem(id);
        return false;
    }
    //if (parent_game_types && parent_details) {
    if (!isEmptyObject(parent_game_types)) {
        game_id = id.replace(/[^0-9]/ig,"");
        parent_v = parent_game_types[game_id].rolling_comm;
        if (v > 0 && v > parent_v - parent_details.min_rolling_comm) {
            var message = label + " must be <= parent game rolling comm - min keeping rolling comm.";
            showErrorOnField(id,message);
            addErrorItem(id);
            return false;
        }
    }
    return true;
}
// validateGameRollingComm }}}2
// requiredCheck {{{2
function requiredCheck(fieldVal,id,label){
    if (id == 'except_game_type'){
        return true;
    }
    var message = label+" is required";
    if(!fieldVal || (fieldVal == "")){
        showErrorOnField(id,message);
        addErrorItem(id);
        return false;
    }else{
        removeErrorOnField(id);
        removeErrorItem(id);
        return true;
    }
}
// requiredCheck }}}2
// error {{{2
function showErrorOnField(id,message){
    $('#error-'+id).html(message);
}

function removeErrorOnField(id){
    $('#error-'+id).html("");
}

function removeErrorItem(item){
    var i = error.indexOf(item);
    if(i != -1) {
        error.splice(i, 1);
    }
    // console.log(error)
}

function addErrorItem(item){
    if(jQuery.inArray(item, error) == -1){
        error.push(item);
        // console.log(error);
        // console.log(error.length);
    }
}
// error }}}2
// validateThruAjax {{{2
function validateThruAjax(ajax_url, fieldVal, id){
    var data={};
    data[id] = fieldVal;
    if (id == "confirm_password"){
        data["password"] = $("#password").val();
    }
    if (id == "available_credit") {
        data["credit_limit"] = $("#credit_limit").val();
        data["before_credit"] = $("#before_credit").val();
        data["agent_count"] = $("#agent_count").val();
    }
    data["parent_id"] = $("#parent_id").val();
    if (id == "name") {
        if($('#batch_add_players').is(":checked")){
            data["batch_add_players"] = $("#batch_add_players").val();
        }
        data["count"] = $("#count").val();
    }
    // console.log(data);
    if(data){
        $.ajax({
            url : ajax_url,
            type : 'POST',
            data : data,
            dataType : "json",
            cache : false,
        }).done(function (data) {
            // console.log(id);
            // console.log(data);
            if (data.status == "success") {
                removeErrorItem(id);
                removeErrorOnField(id);
                v_status[id] = true;
                // console.log(v_status);
            }
            if (data.status == "error") {
                var message = data.msg;
                showErrorOnField(id,message);
                addErrorItem(id);
                v_status[id] = false;
                // console.log(v_status);
            }
        }).fail(function (jqXHR, textStatus) {
            /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
            // location.reload();
        });
    }
}
// validateThruAjax }}}2

// set_submit {{{2
function set_submit(fields){
    var submit = $('#submit');
    submit.on('click',function(e){
        if (!submitForm(fields)) {
            e.preventDefault();
        }
    });
}
// set_submit }}}2
// check_all_fields {{{2
function check_all_fields(fields) {
    for (var i = 0; i < fields.length; i++) {
        var id = fields[i];
        $('#'+id).focus();
    }
    $('#submit').focus();
}
// check_all_fields }}}2
// submitForm {{{2
function submitForm(fields){
    check_all_fields(fields);
    for (var i = 0; i < fields.length; i++) {
        var id = fields[i];
        if (v_status[id] != true) {
            ableSubmitButton();
            return false;
        }
    }
    if(error.length > 0) {
        ableSubmitButton();
        return false;
    }else{
        $('#agency_main_form').submit();
        //disableSubmitButton();
        return true;
    }

}
function disableSubmitButton(){
    var submit = $('#submit');
    var cancel = $('#cancel');
    $("#agency_main_form :input").attr("disabled", true);
    submit.prop('disabled', true);
    cancel.prop('disabled', true);
}
function ableSubmitButton(){
    var cancel = $('#cancel');
    cancel.prop('disabled', false);
}
// submitForm }}}2
// form validation }}}1

// agency rolling comm setting {{{1
$(document).ready(function(){
    $('#rolling_comm_setting_form').submit(function(e){
        var selected_game=$('#gameTree').jstree('get_checked');
        if(selected_game.length>0){
            var v = selected_game.join();
            $('#rolling_comm_setting_form input[name=selected_game_tree]').val(v);
            $('#gameTree').jstree('generate_number_fields');
        }
    });
});
// agency rolling comm setting }}}1

// modal operations {{{1
function open_modal(name, dst_url, title) {
    var main_selector = '#' + name;

    var label_selector = '#label_' + name;
    $(label_selector).html(title);

    var body_selector = main_selector + ' .modal-body';
    var target = $(body_selector);
    target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(dst_url);

    $(main_selector).modal('show');
}

function close_modal(name) {
    var selector = '#' + name;
    $(selector).modal('hide');
}
// modal operations }}}1
// structure and agent {{{1
function remove_agent(agent_id, agent_name) {
    if (confirm('Are you sure you want to delete this agent: ' + agent_name + '?')) {
        window.location = base_url + "agency/remove_agent/" + agent_id + "/" + agent_name;
    }
}
function setDisplayWeeklyStartDay(){
    if($('#settlement_period_weekly').is(":checked")){
        $('#weekly_start_day').show();
    } else {
        $('#weekly_start_day').hide();
    }
}
function setDisplayTotalBetsExcept() {
    if ($('#basis_total_bets').is(":selected")) {
        $('#total_bets_except').show();
    } else {
        $('#total_bets_except').hide();
    }
}

function show_level_names() {
    var level_count = $('#allowed_level').val();
    for (var i = 0; i < level_count; i++) {
        var agent_level_select = '#agent_level_' + i;
        var level_select = '#allowed_level_' + i;
        var name_select  = '#level_name_' + i;
        var name = $(name_select).val();
        $(agent_level_select).val(name);
        $(level_select).html('Level ' + i + ': <b>' + name + '</b>');
        $(level_select).show();
    }
    close_modal('level_name_modal');
    return;
}
    // Hierarchical Tree {{{2
function set_player_vip_level_tree(data_url){
    $('#vip_level_tree').jstree({
        'core' : {
            'data' : {
                "url" : data_url,
                "dataType" : "json" // needed only if you do not supply JSON headers
            }
        },
        "checkbox":{
            "tie_selection": false,
        },
        "plugins":[
        "search","checkbox"
        ]
    });
}
    // Hierarchical Tree }}}2
$(document).ready(function(){
    if($('#settlement_period_weekly').is(":checked")){
        $('#weekly_start_day').show();
    } else {
        $('#weekly_start_day').hide();
    }
    if ($('#basis_total_bets').is(":selected")) {
        $('#total_bets_except').show();
    } else {
        $('#total_bets_except').hide();
    }

    $('#agency_main_form').submit(function(e){
        var selected_levels = $('#vip_level_tree').jstree('get_checked');
        var levels_str = selected_levels.join();
        $('#selected_vip_levels').val(levels_str);
        $('#currency').prop('disabled', false);
    });
});
// edit agent}}}1

// operations for an player {{{1
$(document).ready(function(){
    if($('#batch_add_players').is(":checked")){
        $('#count').removeAttr('readonly');
        $('.batch_help_info').show();
    } else {
        $('#count').attr('readonly', 'readonly');
        $('.batch_help_info').hide();
    }
});
function enableCountOrNot(){
    if($('#batch_add_players').is(":checked")){
        $('#count').removeAttr('readonly');
        $('.batch_help_info').show();
        $('[name="registered_by"]').prop( "disabled", true );
    } else {
        $('#count').val('');
        $('#count').attr('readonly', 'readonly');
        $('.batch_help_info').hide();
        $('[name="registered_by"]').prop( "disabled", false );
    }
}
function show_player_info(player_id){
    var dst_url = base_url + "agency/player_information/" + player_id;
    window.location = dst_url;
}
function show_player_game_history(player_username, by_date_from, by_date_to){
    var dst_url = base_url + "agency/game_history?player_username=" + player_username + "&by_date_from=" + by_date_from + "&by_date_to=" + by_date_to;
    window.location = dst_url;
}
function reset_player_password(player_id){
    var ajax_url = base_url + "agency/reset_random_password/" + player_id;
    get_random_password_ajax(ajax_url);
}
// get_random_password_ajax {{{2
function get_random_password_ajax(ajax_url){
    var data = {'extra_search':1, 'draw':1, 'length':-1, 'start':0};
    // console.log(data);
    // console.log(ajax_url);
    if(data){
        $.ajax({
            url : ajax_url,
            type : 'POST',
            data : data,
            dataType : "json",
            cache : false,
        }).done(function (data) {
            // console.log(data);
            if (data.status == "success") {
                var pass = data.result;
                // console.log(pass);
                var id = 'random_password_span';
                $('#' + id).html(pass);
            }
            if (data.status == "error") {
            }
        }).fail(function (jqXHR, textStatus) {
            /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
            // location.reload();
        });
    }
}
// get_random_password_ajax }}}2
// operations for an player }}}1

// operations for an agent {{{1
// show hierarchical tree {{{2
function show_hierarchical_tree(agent_id) {
    var dst_url = base_url + "agency/agent_hierarchical_tree/" + agent_id;
    open_modal('add_players_modal', dst_url, 'Agent Hierarchical Tree');
}
// show hierarchical tree }}}2
// show agent info
function show_agent_info(agent_id){
    var dst_url = base_url + "agency/agent_information/" + agent_id;
    window.location = dst_url;
}
// adjust credit
function show_agent_players_commission(sub_agent_username, date_from, date_to, status){
    var dst_url = base_url + 'agency/player_rolling_comm?sub_agent_username='+sub_agent_username+'&date_from='+date_from+'&date_to='+date_to+'&status='+status;
    window.location = dst_url;
}
function show_agent_players_win_loss(agent_id, date_from, date_to, status){
    var dst_url = base_url + 'agency/win_loss_report?agent_id='+agent_id+'&date_from='+date_from+'&date_to='+date_to;
    window.location = dst_url;
}
// adjust credit
function agent_adjust_credit(agent_id) {
    var dst_url = base_url + "agency/adjust_credit/" + agent_id;
    window.location = dst_url;
    //open_modal('add_players_modal', dst_url, 'Adjust Credit');
}
// add players for a given agent
function agent_add_players(agent_id) {
    var dst_url = base_url + "agency/agent_add_players/" + agent_id;
    window.location = dst_url;
    //open_modal('add_players_modal', dst_url, 'Add Players');
}
// add sub agents for a given agent
function agent_add_sub_agents(agent_id) {
    var dst_url = base_url + "agency/create_sub_agent/" + agent_id;
    window.location = dst_url;
}
// edit a given agent
function edit_this_agent(agent_id) {
    var dst_url = base_url + "agency/edit_agent/" + agent_id;
    window.location = dst_url;
}
// activate a given agent
function activate_this_agent(agent_id) {
    var dst_url = base_url + "agency/activate_agent/" + agent_id;
    window.location = dst_url;
}
// suspend a given agent
function suspend_this_agent(agent_id) {
    var dst_url = base_url + "agency/suspend_agent/" + agent_id;
    window.location = dst_url;
}
// freeze a given agent
function freeze_this_agent(agent_id) {
    var dst_url = base_url + "agency/freeze_agent/" + agent_id;
    window.location = dst_url;
}
// operations for an agent }}}1

// agent list {{{1
// select all in agent list {{{2
function checkAll(id) {
    var list = document.getElementsByClassName(id);
    var all = document.getElementById(id);

    if (all.checked) {
        for (i = 0; i < list.length; i++) {
            list[i].checked = 1;
        }
    } else {
        all.checked;

        for (i = 0; i < list.length; i++) {
            list[i].checked = 0;
        }
    }
}
// select all in agent list }}}2
// uncheckAll {{{2
function uncheckAll(id) {
    //var list = document.getElementById(id).className;
    var list = 'check_all_agents';
    var all = document.getElementById(list);

    var item = document.getElementById(id);
    var allitems = document.getElementsByClassName(list);
    var cnt = 0;

    if (item.checked) {
        for (i = 0; i < allitems.length; i++) {
            if (allitems[i].checked) {
                cnt++;
            }
        }

        if (cnt == allitems.length) {
            all.checked = 1;
        }
    } else {
        all.checked = 0;
    }
}
// uncheckAll }}}2
// activate_selected_agents {{{2
function activate_selected_agents(activate_url) {
    var agent_ids = getAgentsId();
    if(agent_ids == '') {
        return false;
    }
    var data = {};
    data["agent_ids"] = agent_ids;
    $.post(activate_url, data, function(data){
        location.reload();
        //window.location = base_url + "agency_management/agent_list";
    });
}
// activate_selected_agents }}}2
// suspend_selected_agents {{{2
function suspend_selected_agents(suspend_url) {
    var agent_ids = getAgentsId();
    if(agent_ids == '') {
        return false;
    }
    var data = {};
    data["agent_ids"] = agent_ids;
    $.post(suspend_url, data, function(data){
        location.reload();
    });
}
// suspend_selected_agents }}}2
// freeze_selected_agents {{{2
function freeze_selected_agents(freeze_url) {
    var agent_ids = getAgentsId();
    if(agent_ids == '') {
        return false;
    }
    var data = {};
    data["agent_ids"] = agent_ids;
    $.post(freeze_url, data, function(data){
        location.reload();
    });
}
// freeze_selected_agents }}}2

function getAgentsId() {
    agentIDs = Array();
    $('input[name^="agents"]:checked').each(function() {
        agentIDs.push($(this).val());
    });

    return agentIDs.join(",");
}
// agent list }}}1

// operations for player {{{1
// player_deposit {{{2
function player_deposit(player_id) {
    var dst_url = base_url + "agency/player_deposit/" + player_id;
    open_modal('add_players_modal', dst_url, 'Player Deposit');
} // player_deposit }}}2
// player_withdraw {{{2
function player_withdraw(player_id) {
    var dst_url = base_url + "agency/player_withdraw/" + player_id;
    open_modal('add_players_modal', dst_url, 'Player Withdraw');
} // player_withdraw }}}2
// freeze_player {{{2
function freeze_player(player_id) {
    var dst_url = base_url + "agency/freeze_player/" + player_id;
    window.location = dst_url;
} // freeze_player }}}2
// unfreeze_player {{{2
function unfreeze_player(player_id) {
    var dst_url = base_url + "agency/unfreeze_player/" + player_id;
    window.location = dst_url;
} // unfreeze_player }}}2
// operations for player }}}1

// general {{{1
function GetXmlHttpObject()
{
    if (window.XMLHttpRequest)
    {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        return new XMLHttpRequest();
    }
    if (window.ActiveXObject)
    {
        // code for IE6, IE5
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
}

function specify(e) {
    if (e.value == 'specify') {
        $("#start_date").attr("disabled", false);
        $("#end_date").attr("disabled", false);
    } else {
        $("#start_date").attr("disabled", true);
        $("#end_date").attr("disabled", true);
    }
}

// payment {{{1
function deactivate_payment(payment_id, bank_name, agent_id) {
    if (confirm('Are you sure you want to Deactivate this payment method: ' + bank_name + '?')) {
        window.location = base_url + "agency_management/deactivate_payment/" + payment_id + "/" + bank_name + "/" + agent_id;
    }
}

function activate_payment(payment_id, bank_name, agent_id) {
    if (confirm('Are you sure you want to Activate this payment method: ' + bank_name + '?')) {
        window.location = base_url + "agency_management/activate_payment/" + payment_id + "/" + bank_name + "/" + agent_id;
    }
}

function delete_payment(payment_id, bank_name, agent_id) {
    if (confirm('Are you sure you want to Delete this payment method: ' + bank_name + '?')) {
        window.location = base_url + "agency_management/delete_payment/" + payment_id + "/" + bank_name + "/" + agent_id;
    }
}
// payment }}}1

function imCheck(value, im) {
    if (value == "") {
        $('#im'+im).attr('readonly', true);
    } else {
        $('#im'+im).attr('readonly', false);
    }
}
//cashier
function setModify(id) {
    $('#modify').attr('href', base_url + 'affiliate/modifyPayment/' + id);
}
// general }}}1

// month, day and year {{{1
function getDays(month) {
    if (month == 2) {
        if (!leapYear()) {
            $("#twenty_eight").show();
        } else {
            $("#twenty_nine").show();
        }
    } else if (month == 4 || month == 6 || month == 9 || month == 11) {
        $("#thirty").show();
    } else {
        $("#thirty_one").show();
    }
}

function leapYear() {
    var year = new Date().getFullYear();
    return ((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0);
}
// month, day and year }}}1

//change language {{{1
function changeLanguage() {
    var lang = $('#language').val();
    // console.log(lang);
    // var new_site = 'online/setCurrentLanguage/' + lang;
    // var method = '/' + $('#method').val();
    // $(location).attr('href','<?= BASEURL ?>'+ new_site);

    $.ajax({
        'url' : base_url +'agency/changeLanguage/'+lang,
        'type' : 'GET',
        'dataType' : "json",
        'success' : function(data){
            // console.log(data);
            location.reload();
        }
    });
}
//end of changeLanguage }}}1

// daterange picker {{{1
$(document).ready(function(){
    daterange();
});

// daterange {{{2
function daterange() {
    var cb = function(start, end, label) {
        // console.log(start.toISOString(), end.toISOString(), label);
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#dateRangeValue').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#dateRangeValueStart').val(start.format('YYYY-MM-DD'));
        $('#dateRangeValueEnd').val(end.format('YYYY-MM-DD'));
        //console.log(start.format('MMMM D, YYYY'));
        //alert("Callback has fired: [" + start.format('MMMM D, YYYY') + " to " + end.format('MMMM D, YYYY') + ", label = " + label + "]");
    }

    var optionSet1 = {
        startDate: moment().subtract(29, 'days'),
        endDate: moment(),
        minDate: '01/01/2012',
        maxDate: '12/31/2015',
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            // 'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        opens: 'left',
        buttonClasses: ['btn btn-default'],
        applyClass: 'btn-small btn-primary',
        cancelClass: 'btn-small',
        format: 'MM/DD/YYYY',
        separator: ' to ',
        locale: {
            applyLabel: 'Submit',
            cancelLabel: 'Clear',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1
        }
    };

    // $('#reportrange span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
    //$('#reportrange span').html(moment().format('MMMM D, YYYY')+ ' - ' +moment().format('MMMM D, YYYY'));
    if($('#dateRangeValue').val() == ''){ $('#dateRangeValue').val(moment().subtract(6, 'days').format('MMMM D, YYYY')+ ' - ' +moment().format('MMMM D, YYYY')); }
    if($('#dateRangeValueStart').val() == ''){ $('#dateRangeValueStart').val(moment().subtract(6, 'days').format('YYYY-MM-DD')); }
    if($('#dateRangeValueEnd').val() == ''){ $('#dateRangeValueEnd').val(moment().format('YYYY-MM-DD')); }
    // console.log(moment().format('MMMM D, YYYY'));
    $('#reportrange').daterangepicker(optionSet1, cb);

    $('#reportrange').on('show.daterangepicker', function() { //console.log("show event fired");
    });
    $('#reportrange').on('hide.daterangepicker', function() { //console.log("hide event fired");
    });
    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
        // console.log("apply event fired, start/end dates are "
        // + picker.startDate.format('MMMM D, YYYY')
        // + " to "
        // + picker.endDate.format('MMMM D, YYYY')
        // );
    });
    $('#reportrange').on('cancel.daterangepicker', function(ev, picker) { //console.log("cancel event fired");
    });

    $('#options1').click(function() {
        $('#reportrange').data('daterangepicker').setOptions(optionSet1, cb);
    });

    $('#destroy').click(function() {
        $('#reportrange').data('daterangepicker').remove();
    });
}
// end of daterangepicker }}}2
// daterange picker }}}1

// settlement {{{1
// show unsettled settlement records under given agent
function show_unsettled_settlements(agent_name, stat){
    if (stat == 'unsettled'){
        var dst_url = base_url + "agency/settlement/" + agent_name + "/" + stat;
        window.location = dst_url;
    }
}
function do_settlement(settlement_id, stat) {
    if (stat == 'unsettled') {
        if (confirm('Are you sure you want to do settlement?')) {
            window.location = base_url + "agency/do_settlement/" + settlement_id;
        }
    } else {
        alert('This operation can only be done to items in "unsettled" status!');
    }
}

function do_settlement_wl(user_id, stat, date_start, date_end) {

    // console.log(user_id);
    // console.log(stat);
    // console.log(date_start);
    // console.log(date_end);

    if (stat == 'unsettled') {
        if (confirm('Are you sure you want to do settlement?')) {

            $.ajax({
                method: "POST",
                url: base_url + "agency/do_settlement_wl/",
                data: {
                    user_id: user_id,
                    date_start: date_start,
                    date_end: date_end
                }
            }).done(function (msg) {
                $('html,body').css('cursor','wait'); // change cursor to wait, so that user knows we are waiting for something to happen
                setTimeout(function(){ // delay here to allow session to take effect
                    window.location = base_url + "agency/settlement_wl?status=settled&date_from=" + date_start + "&date_to=" + date_end;;
                }, 1500);
            });
        }
    } else {
        alert('This operation can only be done to items in "unsettled" status!');
    }
}

function close_settlement_wl(user_id, stat, date_start, date_end) {

    if (stat == 'unsettled') {
        if (confirm('Are you sure you want to close this settlement?')) {

            $.ajax({
                method: "POST",
                url: base_url + "agency/close_settlement_wl/",
                data: {
                    user_id: user_id,
                    date_start: date_start,
                    date_end: date_end
                }
            }).done(function (msg) {
                $('html,body').css('cursor','wait'); // change cursor to wait, so that user knows we are waiting for something to happen
                setTimeout(function(){ // delay here to allow session to take effect
                    window.location = base_url + "agency/settlement_wl?status=closed&date_from=" + date_start + "&date_to=" + date_end;;
                }, 1500);
            });
        }
    } else {
        alert('This operation can only be done to items in "unsettled" status!');
    }
}


function settlement_send_invoice_wl(invoice_id) {

    //TODO
    // var target = base_url + "agency/settlement_wl";
    // window.location = target;

    window.location = base_url + "agency/invoice_wl/" + invoice_id;
    // open_modal('send_invoice_modal', dst_url, 'Send Invoice');
}

function pay_rolling_comm(settlement_id) {
    if (confirm('Are you sure you want to pay rolling comm?')) {
        window.location = base_url + "agency/pay_rolling_comm/" + settlement_id;
    }
}
function pay_rolling_comm_to_players(settlement_id) {
    if (confirm('Are you sure you want to pay rolling comm?')) {
        window.location = base_url + "agency/pay_player_rolling_comm/" + settlement_id;
        //dst_url = base_url + "agency/pay_player_rolling_comm/" + settlement_id;
        //open_modal('send_invoice_modal', dst_url, 'Pay Rolling Comm to Players');
    }
}
function freeze_settlement(settlement_id) {
    if (confirm('Are you sure you want to freeze this settlement?')) {
        window.location = base_url + "agency/freeze_settlement/" + settlement_id;
    }
}
function unfreeze_settlement(settlement_id) {
    if (confirm('Are you sure you want to unfreeze this settlement?')) {
        window.location = base_url + "agency/unfreeze_settlement/" + settlement_id;
    }
}
function settlement_send_invoice(settlement_id) {
    //var dst_url = base_url + "agency/settlement_send_invoice/" + settlement_id;
    //open_modal('send_invoice_modal', dst_url, 'Send Invoice');
    window.location = base_url + "agency/invoice/" + settlement_id;
}
function send_invoice_email() {
    alert('Will send invoice through e-mail');
    close_modal('send_invoice_modal');
}
function send_invoice_skype() {
    alert('Will send invoice through skype');
    close_modal('send_invoice_modal');
}
// settlement }}}1

// Invoice {{{1
//$(document).ready(function(){
//});

function set_invoice_select_action(ajax_url){
    //ajax_url = base_url + "agency/get_invoice_info_ajax";
    $('#invoice_select_list').on('change', function(){
        set_hidden_input_fields($(this).val());
        get_invoice_info_ajax(ajax_url, $(this).val());
    });
    $('#invoice_select_list').blur(function(){
        set_hidden_input_fields($(this).val());
        get_invoice_info_ajax(ajax_url, $(this).val());
    });
}
function set_hidden_input_fields(fieldVal) {
    fields = ['agent_name', 'period', 'date_from', 'date_to'];
    var val = JSON.parse(fieldVal);
    for (i = 0; i < fields.length; i++) {
        id = fields[i];
        $('#' + id).val(val[id]);
    }
}
// get_invoice_info_ajax {{{2
function get_invoice_info_ajax(ajax_url, fieldVal){
    var data = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
    //var data = JSON.parse(fieldVal);
    //data["include_all_downlines"] = "true";
    // console.log(data);
    // console.log(ajax_url);
    if(data){
        $.ajax({
            url : ajax_url,
            type : 'POST',
            data : data,
            dataType : "json",
            cache : false,
        }).done(function (data) {
            // console.log(data);
            if (data.status == "success") {
                var arr = data.result;
                // console.log(arr);
                var info_arr = ['settlement', 'player', 'game'];
                for(var i = 0; i < info_arr.length; i++) {
                    var tbody_str = create_tbody_str(arr[i].data);
                    var id = info_arr[i] + '_tbody';
                    $('#' + id).html(tbody_str);
                }
            }
            if (data.status == "error") {
            }
        }).fail(function (jqXHR, textStatus) {
            /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
            // location.reload();
        });
    }
}
// get_invoice_info_ajax }}}2
// create_tbody_str {{{2
function create_tbody_str(data) {
    var tbody_str = '';
    for (var i = 0; i < data.length; i++) {
        tbody_str += '<tr>';
        var rec = data[i];
        for (var j = 0; j < rec.length; j++) {
            tbody_str += '<td>' + rec[j] + '</td>';
        }
        tbody_str += '</tr>';
    }
    return tbody_str;
}
// create_tbody_str }}}2
// make_invoice_options_searchable {{{2
function make_invoice_options_searchable(){
    $('#invoice_select_list').multiselect({
        enableFiltering: true,
    includeSelectAllOption: true,
    selectAllJustVisible: false,
    buttonWidth: '100%',


    buttonText: function(options, select) {
        if (options.length === 0) {
            return '';
        }
        else {
            var labels = [];
            options.each(function() {
                if ($(this).attr('label') !== undefined) {
                    labels.push($(this).attr('label'));
                }
                else {
                    labels.push($(this).html());
                }
            });
            return labels.join(', ') + '';
        }
    }
    });
} // make_invoice_options_searchable }}}2
// Invoice }}}1

// zR to open all folded lines
// vim:ft=javascript:fdm=marker
// end of agency_management.js
