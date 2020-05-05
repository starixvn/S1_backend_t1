var base_url = "/";
var imgloader = "/resources/images/ajax-loader.gif";
var MAX_ALLOWED_LEVEL = 10;

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
    }
}
function setDisplayTotalBetsExcept() {
    if ($('#basis_total_bets').is(":selected")) {
        $('#total_bets_except').show();
    } else {
        $('#total_bets_except').hide();
    }
}
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
        var level_select = '#allowed_level_' + i;
        var name_select  = '#level_name_' + i;
        var name = $(name_select).val();
        $(level_select).html('Level ' + i + ': <b>' + name + '</b>');
        $(level_select).show();
    }
    close_modal('level_name_modal');
    return;
}
// structure }}}1

// operations for an agent {{{1
// add players for a given agent 
function agent_add_players(agent_id, agent_name) {
    var dst_url = base_url + "agency_management/agent_add_players/" + agent_id +"/" + agent_name;
    open_modal('add_players_modal', dst_url, 'Add Players');
}
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
// operations for an agent }}}1

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
function inactivate_payment(payment_id, bank_name, agent_id) {
    if (confirm('Are you sure you want to Deactivate this payment method: ' + bank_name + '?')) {
        window.location = base_url + "agency_management/inactivate_payment/" + payment_id + "/" + bank_name + "/" + agent_id;
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

// settlement {{{1
function freeze_settlement(settlement_id) {
    if (confirm('Are you sure you want to freeze this settlement?')) {
        window.location = base_url + "agency_management/freeze_settlement/" + settlement_id;
    }
}
function freeze_settlement(settlement_id) {
    if (confirm('Are you sure you want to unfreeze this settlement?')) {
        window.location = base_url + "agency_management/unfreeze_settlement/" + settlement_id;
    }
}
function settlement_send_invoice(settlement_id) {
    var dst_url = base_url + "agency_management/settlement_send_invoice/" + settlement_id
    open_modal('send_invoice_modal', dst_url, 'Send Invoice');
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

// zR to open all folded lines
// vim:ft=javascript:fdm=marker
// end of agency_management.js
