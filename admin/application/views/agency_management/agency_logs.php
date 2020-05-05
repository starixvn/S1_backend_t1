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
                        class="btn btn-xs <?=$panelOpenOrNot?> <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?>">
                    </a>
                </span>
                <a href="javascript:void(0);" class="bookmark-this btn btn-xs pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?>"
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
                <!-- input row {{{3 -->
                <div class="row">
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label" for="log_date_input"><?=lang('report.sum02')?></label>
                        <div class="input-group">
                            <input id="log_date_input" class="form-control input-sm dateInput"
                            data-start="#date_from" data-end="#date_to" data-time="true"/>
                            <input type="hidden" id="date_from" name="date_from" />
                            <input type="hidden" id="date_to" name="date_to" />
                            <span class="input-group-addon input-sm">
                                <input type="checkbox" name="search_on_date" id="search_on_date" value="1"
                                <?php if ($conditions['search_on_date']) : ?> checked="1" <?php endif; ?> />
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label"><?=lang('Agent Username');?></label>
                        <input type="text" name="agent_name" class="form-control input-sm"
                        placeholder=' <?=lang('Enter Agent Username');?>'
                        value="<?php echo $conditions['agent_name']; ?>"/>
                    </div>
                    <div class="col-md-3 col-lg-3">
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
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label"><?=lang('Action');?></label>

                        <select name="agent_action" id="agent_action" class="form-control input-sm">
                            <option value="">-- <?=lang('None');?> --</option>
                            <?php foreach($agency_actions as $action){ ?>
                                <option value="<?=$action['name']?>" <?=(set_value("agent_action") == $action['name']) ? "selected" : ""?> >
                                    <?=lang($action['name'])?>
                                </option>
                            <?php }?>
                        </select>


                    </div>
                </div> <!-- input row }}}3 -->
                <!-- button row {{{3 -->
                <!-- <div class="row"> -->
                    <div class="pull-right hr_between_table">
                        <input type="button" value="<?=lang('lang.reset');?>"
                        class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>"
                        onclick="window.location.href='<?php echo site_url('agency_management/agency_logs'); ?>'">

                        <button type="submit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>"><?=lang('lang.search')?></button>
                    </div>
                <!-- </div> button row }}}3 -->
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
            <?=lang('Agency Logs');?>
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
                        <th><?=lang('Links');?></th>
                        <th><?=lang('Details');?></th>
                    </tr>
                </thead>
            </table>
        </div>
        <!--end of agency logs table }}}2 -->
    </div>
    <div class="panel-footer"></div>
</div>
<!-- panel for agency logs table }}}1 -->
<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
    <input name='json_search' type="hidden">
    </form>
<?php }?>
<!-- JS code {{{1 -->
<script type="text/javascript">
$(document).ready(function(){

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
                            postfixButtons: [ 'colvisRestore' ],
                            className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
                        },
                        <?php

                        if( $this->permissions->checkPermissions('export_agency_logs') ){ ?>
                            {
                                text: "<?php echo lang('CSV Export'); ?>",
                                className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                                action: function ( e, dt, node, config ) {
                                    var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                                    <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                                        d.export_format = 'csv';
                                        d.export_type = export_type;
                                        $("#_export_excel_queue_form").attr('action', site_url('/export_data/agency_logs'));
                                        $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                        $("#_export_excel_queue_form").submit();

                                    <?php } else { ?>
                                    $.post(site_url('/export_data/agency_logs'), d, function(data){
                                        if(data && data.success){
                                            $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                                        }else{
                                            alert('export failed');
                                        }
                                    });
                                    <?php }?>
                                }
                            }
                    <?php } ?>
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
                            if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                                dataTable.buttons().disable();
                            }
                            else {
                                dataTable.buttons().enable();
                            }
                        },'json');
                    },
    }); // DataTable settings }}}2
    $('#search-form').submit( function(e) {
        e.preventDefault();
        dataTable.ajax.reload();
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
