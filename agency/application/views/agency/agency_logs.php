<?php
/**
 *   filename:   agency_logs.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-02
 *   @brief:     view for agency logs in agency sub-system
 */

// set display according to configurations
$panelOpenOrNot = $this->config->item('default_open_search_panel') ? '' : 'collapsed';
$panelDisplayMode = $this->config->item('default_open_search_panel') ? '' : 'in';
?>
<!-- search form {{{1 -->
<form class="form-horizontal" id="search-form">
    <div class="panel panel-primary hidden">
        <!-- panel heading {{{2 -->
        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="fa fa-search"></i> 
                <?=lang("lang.search")?>
                <span class="pull-right">
                    <a data-toggle="collapse" href="#collapseAgentList" 
                        class="btn btn-info btn-xs <?=$panelOpenOrNot?>">
                    </a>
                </span>
                <a href="javascript:void(0);" class="bookmark-this btn btn-info btn-xs pull-right" 
                    style="margin-right: 4px;">
                    <i class="fa fa-bookmark"></i> 
                    <?php echo lang('Add to bookmark'); ?>
                </a>
            </h4>
        </div>
        <!-- panel heading }}}2 -->


        <div id="collapseAgentList" class="panel-collapse collapse <?=$panelDisplayMode?>">
            <!-- panel body {{{2 -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label" for="log_date_input"><?=lang('report.sum02')?></label>
                        <input id="log_date_input" class="form-control input-sm dateInput" 
                        data-start="#date_from" data-end="#date_to" data-time="true"/>
                        <input type="hidden" id="date_from" name="date_from" />
                        <input type="hidden" id="date_to" name="date_to" />
                    </div>
                </div>
                <!-- input row {{{3 -->
                <div class="row">
                    <div class="col-md-4 col-lg-4">
                        <label class="control-label"><?=lang('Agent Name');?></label>
                        <input type="text" name="agent_name" class="form-control input-sm" 
                        placeholder=' <?=lang('Enter Agent Name');?>'
                        value="<?php echo $conditions['agent_name']; ?>"/>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <label class="control-label"><?=lang('Account Type');?></label>
                        <select name="account_type" id="account_type" class="form-control input-sm">
                            <option value="" <?=empty($conditions['account_type']) ? 'selected' : ''?>>
                            --  <?=lang('lang.selectall');?> --
                            </option>
                            <option value="Agent" <?=(set_value('account_type') == "Agent") ? 'selected' : ''?> >
                            <?=lang('Agent');?>
                            </option>
                            <option value="Member" <?=(set_value('account_type') == "Member") ? 'selected' : ''?> >
                            <?=lang('Member');?>
                            </option>
                            <option value="Admin" <?=(set_value('account_type') == "Admin") ? 'selected' : ''?> >
                            <?=lang('Admin');?>
                            </option>
                            <option value="System" <?=(set_value('account_type') == "System") ? 'selected' : ''?> >
                            <?=lang('System');?>
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <label class="control-label"><?=lang('Action');?></label>
                        <select name="agent_action" id="agent_action" class="form-control input-sm">
                            <option value="">-- <?=lang('None');?> --</option>
                            <option value="account_login" <?=(set_value('agent_action') == "Account_Login") ? 'selected' : ''?> >
                            <?=lang('Account Login');?>
                            </option>
                            <option value="account_logout" <?=(set_value('agent_action') == "Account_Logout") ? 'selected' : ''?> >
                            <?=lang('Account Logout');?>
                            </option>
                            <option value="login_as" <?=(set_value('agent_action') == "Login_as") ? 'selected' : ''?> >
                            <?=lang('Login As');?>
                            </option>
                            <option value="credit_in" <?=(set_value('agent_action') == "Credit_in") ? 'selected' : ''?> >
                            <?=lang('Credit In');?>
                            </option>
                            <option value="credit_out" <?=(set_value('agent_action') == "Credit_out") ? 'selected' : ''?> >
                            <?=lang('Credit Out');?>
                            </option>
                            <option value="create_member" <?=(set_value('agent_action') == "Create_member") ? 'selected' : ''?> >
                            <?=lang('Create Member');?>
                            </option>
                            <option value="create_structure" <?=(set_value('agent_action') == "Create_structure") ? 'selected' : ''?> >
                            <?=lang('Create Structure');?>
                            </option>
                            <option value="create_agent" <?=(set_value('agent_action') == "Create_agent") ? 'selected' : ''?> >
                            <?=lang('Create Agent');?>
                            </option>
                            <option value="add_manual_bonus" <?=(set_value('agent_action') == "Add_manual_bonus") ? 'selected' : ''?> >
                            <?=lang('Add Manual Bonus');?>
                            </option>
                            <option value="remove_manual_bonus" <?=(set_value('agent_action') == "Remove_manual_bonus") ? 'selected' : ''?> >
                            <?=lang('Remove Manual Bonus');?>
                            </option>
                            <option value="remove_cashback_bonus" <?=(set_value('agent_action') == "Remove_cashback_bonus") ? 'selected' : ''?> >
                            <?=lang('Remove Cashback Bonus');?>
                            </option>
                            <option value="add_cashback_bonus" <?=(set_value('agent_action') == "Add_cashback_bonus") ? 'selected' : ''?> >
                            <?=lang('Add Cashback Bonus');?>
                            </option>
                            <option value="system_error" <?=(set_value('agent_action') == "System_error") ? 'selected' : ''?> >
                            <?=lang('System Error');?>
                            </option>
                            <option value="modify_structures" <?=(set_value('agent_action') == "Modify_structures") ? 'selected' : ''?> >
                            <?=lang('Modify Structures');?>
                            </option>
                            <option value="modify_agent_information" <?=(set_value('agent_action') == "Modify_agent_information") ? 'selected' : ''?> >
                            <?=lang('Modify Agent Information');?>
                            </option>
                            <option value="modify_player_information" <?=(set_value('agent_action') == "Modify_player_information") ? 'selected' : ''?> >
                            <?=lang('Modify Player Information');?>
                            </option>
                        </select>
                    </div>
                </div> <!-- input row }}}3 -->
                <!-- button row {{{3 -->
                <div class="row">
                    <div class="col-md-6 col-lg-6 pull-right" style="padding: 10px;">
                        <input type="button" value="<?=lang('lang.reset');?>" 
                        class="btn btn-default btn-sm" 
                        onclick="window.location.href='<?php echo site_url('agency_management/agency_logs'); ?>'">

						<button type="submit" class="btn btn-sm btn-primary"><?=lang('lang.search')?></button>
                    </div>
                </div> <!-- button row }}}3 -->
            </div>
            <!-- panel body }}}2 -->
        </div>
    </div>
</form> <!-- end of search form }}}1 -->

<!-- panel for agency logs table {{{1 -->
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-bullhorn"></i> 
            <?=lang('Agent List');?>
        </h4>
    </div>
    <div class="panel-body">
        <!-- agency logs table {{{2 -->
        <div id="logList" class="table-responsive">
            <table class="table table-striped table-hover table-condensed" 
                id="agency_logs_table" style="width:100%;">
                <thead>
                    <tr>
                        <th><?=lang('Date');?></th>
                        <th><?=lang('Done By');?></th>
                        <th><?=lang('Done To');?></th>
                        <th><?=lang('Action');?></th>
                        <th><?=lang('Details');?></th>
                        <th><?=lang('Links');?></th>
                    </tr>
                </thead>
            </table>
        </div>
        <!--end of agency logs table }}}2 -->
    </div>
    <div class="panel-footer"></div>
</div>
<!-- panel for agency logs table }}}1 -->

<!-- JS code {{{1 -->
<script type="text/javascript">
$(document).ready(function(){

    <?php $agent_status = $this->session->userdata('agent_status'); ?>
    <?php if($agent_status == 'suspended') { ?>;
    set_suspended_operations();
    <?php } ?>

$('.bookmark-this').click(_pubutils.addBookmark);

    // DataTable settings {{{2
    var dataTable = $('#agency_logs_table').DataTable({
        autoWidth: false,
        searching: false,
        dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
        "responsive": {
            details: {
                    type: 'column'
                }
        },
        buttons: [
            {
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ]
            }
        ],
        columnDefs: [
            { className: 'text-right', targets: [ 4 ] },
            { sortable: false, targets: [ 5 ] },
        ],
        "order": [ 0, 'desc' ],
        processing: true,
        serverSide: true,
        ajax: function (data, callback, settings) {
            data.extra_search = $('#search-form').serializeArray();
            $.post(base_url + "api/agency_logs", data, function(data) {
                callback(data);
            },'json');
        },
    }); // DataTable settings }}}2
    $('#search-form').submit( function(e) {
        e.preventDefault();
        dataTable.ajax.reload();
        set_agent_operations();
    });
    $('#search-form input[type="text"]').keypress(function (e) {
        if (e.which == 13) {
            $('#search-form').trigger('submit');
        }
    });
});
</script>
<!-- JS code }}}1 -->

<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agency_logs.php
