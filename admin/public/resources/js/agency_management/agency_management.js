var base_url = "/";
var imgloader = "/resources/images/ajax-loader.gif";
var MAX_ALLOWED_LEVEL = 10;

function isEmptyObject(obj){
    for(var key in obj){
        return false
    }
    return true
}
// form validation {{{1
$(document).ready(function(){
    error = [];
    v_status = {};
    parent_game_types = {};
    parent_details = {}; // contains parent agent detail and game type settings
});

// agency form validation {{{2
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
            } else if(id.match(/(admin|transaction|cashback|bonus)_fee/i)) {
                validateFees($(this).val(), id, label);
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
    if (id.indexOf("rev_share") > 0 || id.indexOf("platform_fee") > 0){
        validateGameRevShare(v, id, label);
    } else if (id.indexOf("rolling_comm") > 0){
        validateGameRollingComm(v, id, label);
    }
}
// validateGameRevShareAndRollingComm }}}2
function validateFees(v, id, label) {
    var value = parseFloat(v);
    if (value < 0.00 || value > 100.00) {
        var message = label + " must be >= 0 and <= 100.00.";
        showErrorOnField(id,message);
        addErrorItem(id);
        return false;
    }
    // Element ID is also the name of the field in agent detail, i.e. admin_fee etc
    parentValue = parseFloat(parent_details[id]); // note parent_details contains all strings
    if (value > parentValue) {
        var message = label + " must be <= parent " + label + " (" + parentValue.toFixed(2) + ")";
        showErrorOnField(id,message);
        addErrorItem(id);
        return false;
    }
    return true;
}
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
        if (id.indexOf("rev_share") > 0){
            parent_v = parent_game_types[game_id].rev_share;
        } else if (id.indexOf("platform_fee") > 0){
            parent_v = parent_game_types[game_id].platform_fee;
        }
        if (v > parent_v) {
            var message = label + " must be <= parent " + label;
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
// check_all_fields {{{2
function check_all_fields(fields) {
    for (var i = 0; i < fields.length; i++) {
        var id = fields[i];
        $('#'+id).focus();
        //$('#'+id).blur();
    }
    $('#submit').focus();
}
// check_all_fields }}}2
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

// sidebar.php {{{1
$(document).ready(function() {
    //tooltip
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    //$("a#structure_list").addClass("active");
    //$("a#agent_list").addClass("active");

    var url = document.location.pathname;
    var res = url.split("/");
    var selector = 'a#' + res[res.length - 1];
    $(selector).addClass("active");

    // allowed level
    for (var i = 0; i < MAX_ALLOWED_LEVEL; i++) {
        var level_select = '#allowed_level_' + i;
        $(level_select).hide();
    }
});
// sidebar }}}1
// structure and agent {{{1
function remove_structure(structure_id, structure_name) {
    if (confirm('Are you sure you want to delete this structure: ' + structure_name + '?')) {
        window.location = base_url + "agency_management/remove_structure/" + structure_id + "/" + structure_name;
    }
}
function remove_agent(agent_id, agent_name) {
    if (confirm('Are you sure you want to delete this agent: ' + agent_name + '?')) {
        window.location = base_url + "agency_management/remove_agent/" + agent_id + "/" + agent_name;
    }
}
function setDisplayWeeklyStartDay(){
    if($('#settlement_period_weekly').is(":checked")){
        $('#weekly_start_day').show();
    } else {
        $('#weekly_start_day').hide();
        $('#weekly_start_day input[name="start_day"]').prop('checked', false);
    }
}
function setDisplayTotalBetsExcept() {
    if ($('#basis_total_bets').is(":selected")) {
        $('#total_bets_except').show();
    } else {
        $('#total_bets_except').hide();
    }
    $('#total_bets_except').show();
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
    $('#total_bets_except').show();

    $('#agency_main_form').submit(function(e){
        // var selected_levels = $('#vip_level_tree').jstree('get_checked');
        // var levels_str = selected_levels.join();
        // $('#selected_vip_levels').val(levels_str);
        $('#currency').prop('disabled', false);
    });
});

function set_level_names() {
    var level_count = $('#allowed_level').val();
    if (level_count <= 0) {
        alert('Allowed level must > 0!');
        return;
    } else if (level_count > MAX_ALLOWED_LEVEL) {
        alert('Allowed level CANNOT > ' + MAX_ALLOWED_LEVEL);
        return;
    }
    var dst_url = base_url + "agency_management/set_level_names/" + level_count;
    open_modal('level_name_modal', dst_url, 'Set Level Names');
}

function show_level_names() {
    var level_count = $('#allowed_level').val();
    for (var i = 0; i < level_count; i++) {
        var agent_level_select = '#agent_level_' + i;
        var level_select = '#allowed_level_' + i;
        var level_select_old = '#old_level_' + i;
        var name_select  = '#level_name_' + i;
        var name = $(name_select).val();
        $(agent_level_select).val(name);
        $(level_select).html('Level ' + i + ': <b>' + name + '</b>');
        $(level_select).show();
        $(level_select_old).hide();
    }
    for (var i = level_count; i < MAX_ALLOWED_LEVEL; i++) {
        var level_select = '#allowed_level_' + i;
        var level_select_old = '#old_level_' + i;
        $(level_select).hide();
        $(level_select_old).hide();
    }
    close_modal('level_name_modal');
    return;
}
// structure }}}1

// operations for an agent {{{1
// batch create agent {{{2
// need to select a parent agent
function set_agent_select_action(){
    $('#agent_select_list').on('change', function(){
        set_agent_hidden_input_fields($(this).val());
        set_agent_currency($(this).val());
        $('#available_credit').focus();
        $('#can_have_sub_agents').focus();
    });
}
function set_agent_currency(fieldVal) {
    var id = 'currency';
    var val = JSON.parse(fieldVal);
    $('#' + id).prop('disabled', false);
    var option = val[id];
    //$('#' + option).selected = true;
    document.getElementById(option).selected=true;
    $('#' + id).prop('disabled', true);
}
function set_agent_hidden_input_fields(fieldVal) {
    fields = ['parent_id', 'agent_level', 'agent_level_name'];
    var val = JSON.parse(fieldVal);
    for (i = 0; i < fields.length; i++) {
        id = fields[i];
        $('#' + id).val(val[id]);
    }
}
// batch create agent }}}2
// make_agent_options_searchable {{{2
function make_agent_options_searchable(){
    $('#agent_select_list').multiselect({
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
} // make_agent_options_searchable }}}2

// show hierarchical tree {{{2
function show_hierarchical_tree(agent_id) {
    var dst_url = base_url + "agency_management/agent_hierarchical_tree/" + agent_id;
    open_modal('add_players_modal', dst_url, 'Agent Hierarchy');
}
// show hierarchical tree }}}2
// show agent info
function reset_agent_password(agent_id){
    var ajax_url = base_url + "agency_management/reset_random_password/" + agent_id;
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
                $('#random_password_span_lbl').addClass("random_password_span_lbl");
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
function show_agent_info(agent_id){
    var dst_url = base_url + "agency_management/agent_information/" + agent_id;
    window.location = dst_url;
}
// adjust credit
function agent_adjust_credit(agent_id) {
    var dst_url = base_url + "agency_management/adjust_credit/" + agent_id;
    window.location = dst_url;
    //open_modal('add_players_modal', dst_url, 'Adjust Credit');
}
// add players for a given agent
function agent_add_players(agent_id) {
    var dst_url = base_url + "agency_management/agent_add_players/" + agent_id;
    open_modal('add_players_modal', dst_url, 'Add Players');
}
//function agent_add_players(agent_id, agent_name) {
//    var dst_url = base_url + "agency_management/agent_add_players/" + agent_id +"/" + agent_name;
//    open_modal('add_players_modal', dst_url, 'Add Players');
//}
// add sub agents for a given agent
function agent_add_sub_agents(agent_id) {
    var dst_url = base_url + "agency_management/create_sub_agent/" + agent_id;
    window.location = dst_url;
}
// edit a given agent
function edit_this_agent(agent_id) {
    var dst_url = base_url + "agency_management/edit_agent/" + agent_id;
    window.location = dst_url;
}
// activate a given agent
function activate_this_agent(agent_id) {
    var dst_url = base_url + "agency_management/activate_agent/" + agent_id;
    window.location = dst_url;
}
// suspend a given agent
function suspend_this_agent(agent_id) {
    var dst_url = base_url + "agency_management/suspend_agent/" + agent_id;
    window.location = dst_url;
}
// freeze a given agent
function freeze_this_agent(agent_id) {
    var dst_url = base_url + "agency_management/freeze_agent/" + agent_id;
    window.location = dst_url;
}
// open modal and show keys with given agent
function open_agent_keys(agent_id) {
    var url = base_url + "agency_management/get_agent_details/" + agent_id;
    $('#staging_secure_key').html('N/A');
    $('#staging_sign_key').html('N/A');
    $('#live_secure_key').html('N/A');
    $('#live_sign_key').html('N/A');

    $.get(url, function(data){
        if (data.agent.staging_secure_key) {
            $('#staging_secure_key').html(data.agent.staging_secure_key);
        }
        if (data.agent.staging_sign_key) {
            $('#staging_sign_key').html(data.agent.staging_sign_key);
        }
        if (data.agent.live_secure_key) {
            $('#live_secure_key').html(data.agent.live_secure_key);
        }
        if (data.agent.live_sign_key) {
            $('#live_sign_key').html(data.agent.live_sign_key);
        }
        if (data.agent.agent_name) {
            $('#agent_name_note').html("(" + data.agent.agent_name + ")");
            $('#agent_id').val(data.agent_id);
            $('#agent_name').val(data.agent.agent_name);
        }
    });
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
        window.location = base_url + "agency_management/agent_list";
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

//function verifyAddAccountProcess() {
//window.location = base_url + "agency_management/agent_add_players/" + agent_id;
//   close_modal('add_players_modal');
//}
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

// settlement  {{{1
// show unsettled settlement records under given agent
function show_unsettled_settlements(agent_name, stat){
    if (stat == 'unsettled'){
        var dst_url = base_url + "agency_management/settlement/" + agent_name + "/" + stat;
        window.location = dst_url;
    }
}
function do_settlement(settlement_id, stat) {
    if (stat == 'unsettled') {
        if (confirm('Are you sure you want to do settlement?')) {
            window.location = base_url + "agency_management/do_settlement/" + settlement_id;
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
                url: base_url + "agency_management/do_settlement_wl/",
                data: {
                    user_id: user_id,
                    date_start: date_start,
                    date_end: date_end
                }
            }).done(function (msg) {
                $('html,body').css('cursor','wait'); // change cursor to wait, so that user knows we are waiting for something to happen
                setTimeout(function(){ // delay here to allow session to take effect
                    window.location = base_url + "agency_management/settlement_wl?status=settled&date_from=" + date_start + "&date_to=" + date_end;;
                }, 1500);
            });
        }
    } else {
        alert('This operation can only be done to items in "unsettled" status!');
    }
}
function freeze_settlement(settlement_id) {
    if (confirm('Are you sure you want to freeze this settlement?')) {
        window.location = base_url + "agency_management/freeze_settlement/" + settlement_id;
    }
}
function unfreeze_settlement(settlement_id) {
    if (confirm('Are you sure you want to unfreeze this settlement?')) {
        window.location = base_url + "agency_management/unfreeze_settlement/" + settlement_id;
    }
}
function settlement_send_invoice(settlement_id) {
    //var dst_url = base_url + "agency_management/settlement_send_invoice/" + settlement_id;
    window.location = base_url + "agency_management/invoice/" + settlement_id;
    //open_modal('send_invoice_modal', dst_url, 'Send Invoice');
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
