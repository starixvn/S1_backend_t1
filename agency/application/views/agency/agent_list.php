<?php
/**
 *   filename:   view_agent_list.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-02
 *   @brief:     view for agent list in agency sub-system
 */

// set display according to configurations
$panelOpenOrNot = $this->config->item('default_open_search_panel') ? '' : 'collapsed';
$panelDisplayMode = $this->config->item('default_open_search_panel') ? '' : 'collapse in';

if (isSet($_GET['parent_id'])) {
	$parent_id = $_GET['parent_id'];
    $all_downlines = '';
} else {
    $all_downlines = 'checked';
}
$activate_url = site_url('agency/activate_agent_array');
$freeze_url = site_url('agency/freeze_agent_array');
$suspend_url = site_url('agency/suspend_agent_array');
$agent_level = $this->session->userdata('agent_level');
if (isset($_GET['search_on_date'])) {
	$search_on_date = $_GET['search_on_date'];
} else {
	$search_on_date = false;
}
?>
<div class="content-container">
<!-- search form {{{1 -->
    <form class="form-horizontal" id="search-form">
        <input type="hidden" name="parent_id" value="<?php echo $parent_id?>"/>
        <div class="panel panel-primary">
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
                </h4>
            </div>
            <!-- panel heading }}}2 -->

            <div id="collapseAgentList" class="panel-collapse <?=$panelDisplayMode?>">
                <!-- panel body {{{2 -->
                <div class="panel-body">
                    <!-- search on date {{{3 -->
                    <div class="row">
                        <div class="col-md-offset-1 col-md-5">
                            <label class="control-label"><?=lang('Agent Username');?></label>
                            <input type="text" name="agent_name" class="form-control input-sm"
                            placeholder=' <?=lang('Enter Agent Username');?>' />
                        </div>
                        <div class="col-md-5">
                            <label class="control-label"><?=lang('report.sum02')?></label>
                            <div class="input-group">
                                <input class="form-control dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
                                <input type="hidden" id="date_from" name="date_from"/>
                                <input type="hidden" id="date_to" name="date_to"/>
                                <span class="input-group-addon input-sm">
                                    <input type="checkbox" name="search_on_date" id="search_on_date" value="1"
                                    <?php if ($search_on_date) {echo 'checked';} ?>/>
                                </span>
                            </div>
                        </div>
                        <!-- checkbox-all-downlines {{{3
                        <div class="col-md-6 col-lg-6 ">
                            <div class="form-group">
                                <label>
                                </label>
                            </div>
                            <input type="checkbox" name="include_all_downlines" value="true" <?=$all_downlines?>/>
                            <?=lang('Include All Downline Agents')?>
                        </div> }}}3 -->
                    </div>
                    <!-- button row {{{3 -->
                    <div class="row">
                        <div class="col-md-offset-1 col-md-5" style="margin-top: 10px;">
                            <input type="button" value="<?=lang('lang.reset');?>"
                            class="btn btn-default btn-sm"
                            onclick="window.location.href='<?php echo site_url('agency/sub_agents_list'); ?>'">
                            <input class="btn btn-sm btn-primary" type="submit" value="<?=lang('lang.search');?>" />
                        </div>
                        <div class="col-md-5" style="margin-top: 10px;">
                            <input type="button" value="<?=lang('Add Agent');?>"
                            class="btn btn-success btn-sm agent-oper"
                            onclick="window.location.href='<?php echo site_url('agency/create_sub_agent/'.$parent_id); ?>'" />
                            <input type="button" value="<?=lang('Batch Add Agent');?>"
                            class="btn btn-success btn-sm agent-oper"
                            onclick="window.location.href='<?php echo site_url('agency/batch_create_sub_agent/'.$parent_id); ?>'" />
                        </div>
                        <!--
                    <div class="col-md-3 col-lg-3" style="padding: 10px;">
                        <input type="button" value="<?=lang('Show Hierarchical Tree');?>"
                        class="btn btn-success btn-sm agent-oper"
                        onclick="window.location.href='<?php echo site_url('agency/agent_hierarchy/'.$parent_id); ?>'" />
                    </div> -->
                    </div> <!-- button row }}}3 -->
                </div>
                <!-- panel body }}}2 -->
            </div>
        </div>
    </form> <!-- end of search form }}}1 -->

    <!-- panel for agent table {{{1 -->
    <div class="panel panel-primary">
        <div class="panel-heading custom-ph">
            <h4 class="panel-title custom-pt">
                <i class="icon-bullhorn"></i>
                <?=lang('Agent List');?>
            </h4>
        </div>
        <div class="panel-body">
            <!-- agent table {{{2 -->
            <div id="logList" class="table-responsive">
                <!-- class="table table-striped table-hover table-condensed" -->
                <table class="table table-striped table-hover table-bordered"
                    id="agent_list_table" style="width:100%;">
                    <!-- Buttons for selected agents {{{3 -->
                    <?php if ( ! $this->utils->isEnabledFeature('agency_hide_sub_agent_list_action')): ?>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <input type='button' id="btn-activate" value='<?php echo lang('Activate Selected'); ?>'
                                    class='btn btn-info btn-sm agent-oper' onclick="activate_selected_agents('<?=$activate_url?>')">
                                    <input type='button' id="btn-suspend" value='<?php echo lang('Suspend Selected'); ?>'
                                    class='btn btn-warning btn-sm agent-oper' onclick="suspend_selected_agents('<?=$suspend_url?>')">
                                    <input type='button' id="btn-freeze" value='<?php echo lang('Freeze Selected'); ?>'
                                    class='btn btn-danger btn-sm agent-oper' onclick="freeze_selected_agents('<?=$freeze_url?>')">
                                </span>
                            </div><!-- /input-group -->
                        </div>
                        <br><br>
                    <?php endif ?>
                    <!-- Buttons for selected agents }}}3 -->
                    <thead>
                        <tr>
                            <?php include __DIR__.'/../includes/agency_agent_list_table_header.php'; ?>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <?php if($this->utils->getConfig('show_agency_rev_share_etc')){ ?>
                            <th colspan="13" style="text-align:left"></th>
                            <?php }else{ ?>
                            <th colspan="10" style="text-align:left"></th>
                            <?php }?>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!--end of agent table }}}2 -->
            <!--  modal for adding players for the agent {{{4 -->
            <div class="modal fade in" id="add_players_modal"
                tabindex="-1" role="dialog" aria-labelledby="label_add_players_modal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="label_add_players_modal"></h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer"></div>
                    </div>
                </div>
            </div> <!--  modal for level name setting }}}4 -->
        </div>
        <div class="panel-footer"></div>
    </div>
    <!-- panel for agent table }}}1 -->
</div>

<!-- JS code {{{1 -->
<script type="text/javascript">
$(document).ready(function(){
        <?php if (isSet($_GET['parent_id'])) { ?>
            $('#date_from').val('');
            $('#date_to').val('');
        <?php } ?>

    <?php $agent_status = $this->session->userdata('agent_status'); ?>
    <?php if($agent_status == 'suspended') { ?>;
    set_suspended_operations();
    <?php } ?>

    $('#search-form input[type="text"]').keypress(function (e) {
        if (e.which == 13) {
            $('#search-form').trigger('submit');
        }
    });

    // DataTable settings {{{2
    var dataTable = $('#agent_list_table').DataTable({
        lengthMenu: [50, 100, 250, 500, 1000],
        //scrollX: true,
        //scrollCollapse: true,
        autoWidth: false,
        searching: false,
        dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",

        "responsive": {
            details: {
                type: 'column'
            }
        },
        buttons: [
        {
            text: "<?=lang('Column visibility')?>",
            extend: 'colvis',
            postfixButtons: [ 'colvisRestore' ]
        }
        ],
        columnDefs: [
            { className: 'text-left', targets: [ 4 ] },
            { sortable: false, targets: [ 0 ] }
        ],

        "order": [ 1, 'asc' ],
        processing: true,
        serverSide: true,
        ajax: function (data, callback, settings) {
            data.extra_search = $('#search-form').serializeArray();
            $.post(base_url + "api/agent_list", data, function(data) {
                callback(data);
                set_agent_operations();
            },'json')
            .fail( function (jqxhr, status_text)  {
                if ( jqxhr.status >= 300 && jqxhr.status < 500 ) {
                    if (confirm('<?= lang('session.timeout') ?>')) {
                        window.location.href = '/';
                    }
                }
                else {
                    alert(status_text);
                }
            });
        }
    }); // DataTable settings }}}2

    $('#search-form').submit( function(e) {
        e.preventDefault();
        dataTable.ajax.reload();
    });

});


function credit_transactions(agent_username) {
    var url = '/agency/credit_transactions?agent_username=' + agent_username;
    var win = window.open(url, '_blank');
    win.focus();
}
</script>
<!-- JS code }}}1 -->

<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of view_agent_list.php
