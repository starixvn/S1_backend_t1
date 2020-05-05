<?php
/**
 *   filename:   credit_transactions.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-02
 *   @brief:     view credit transactions for agency sub-system
 */

// set display according to configurations
$panelOpenOrNot = $this->config->item('default_open_search_panel') ? '' : 'collapsed';
$panelDisplayMode = $this->config->item('default_open_search_panel') ? '' : 'in';
if (isset($_GET['search_on_date'])) {
	$search_on_date = $_GET['search_on_date'];
} else {
	$search_on_date = false;
}
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
                <div class="row">
                    <div class="col-md-6">
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
                    <div class="col-md-6 col-lg-6">
                        <label class="control-label"><?=lang('Agent Tag');?></label>
                        <input type="text" name="agent_tag" class="form-control input-sm"
                        placeholder=' <?=lang('Enter Agent Tag');?>'
                        value="<?php echo $conditions['agent_tag']; ?>"/>
                    </div>
                </div>
                <!-- input row {{{3 -->
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <label class="control-label"><?=lang('Agent Username');?></label>
                        <input type="text" name="agent_name" class="form-control input-sm"
                        placeholder=' <?=lang('Enter Agent Username');?>'
                        value="<?php echo $conditions['agent_name']; ?>"/>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label"><?=lang('Minimum Credit Amount');?></label>
                        <input type="number" name="min_credit_amount" class="form-control input-sm" min="0"
                        placeholder=' <?=lang('Enter Minimum Credit Amount');?>'
                        value="<?php echo $conditions['min_credit_amount']; ?>"/>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label"><?=lang('Maximum Credit Amount');?></label>
                        <input type="number" name="max_credit_amount" class="form-control input-sm" min="0"
                        placeholder=' <?=lang('Enter Maximum Credit Amount');?>'
                        value="<?php echo $conditions['max_credit_amount']; ?>"/>
                    </div>
                    <!--
                    <div class="col-md-6 col-lg-6">
                        <label class="control-label"><?=lang('IP');?></label>
                        <input type="text" name="ip_used" id="ip_used" class="form-control input-sm"
                        placeholder=' <?=lang('Enter IP Address for Transactions');?>'
                        value="<?php echo $conditions['ip']; ?>"/>
                    </div> -->
                </div> <!-- input row }}}3 -->
                <!-- button row {{{3 -->
                <div class="row">
                    <div class="col-md-6 col-lg-6 pull-right" style="padding: 10px;">
                        <input type="button" value="<?=lang('lang.reset');?>"
                        class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>"
                        onclick="window.location.href='<?php echo site_url('agency_management/credit_transactions'); ?>'">
                        <input class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" type="submit" value="<?=lang('lang.search');?>" />
                    </div>
                </div> <!-- button row }}}3 -->
            </div>
            <!-- panel body }}}2 -->
        </div>
    </div>
</form> <!-- end of search form }}}1 -->

<!-- panel for Credit Transactions table {{{1 -->
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-bullhorn"></i>
            <?=lang('Credit Transactions');?>
        </h4>
    </div>
    <div class="panel-body">
        <!-- credit transactions table {{{2 -->
        <div id="logList" class="table-responsive">
            <table class="table table-striped table-hover table-condensed"
                id="credit_transactions_table" style="width:100%;">
                <thead>
                    <tr>
                        <th><?=lang('Date');?></th>
                        <th><?=lang('From User');?></th>
                        <th><?=lang('To User');?></th>
                        <th><?=lang('Amount');?></th>
                        <th><?=lang('Before Balance');?></th>
                        <th><?=lang('After Balance');?></th>
                        <th><?=lang('Remarks');?></th>
                        <!-- <th><?=lang('IP Used');?></th> -->
                    </tr>
                </thead>
            </table>
        </div>
        <!--end of credit transactions table }}}2 -->
    </div>
    <div class="panel-footer"></div>
</div>
<!-- panel for credit transactions table }}}1 -->
<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
    <input name='json_search' type="hidden">
    </form>
<?php }?>
<!-- JS code {{{1 -->
<script type="text/javascript">
$(document).ready(function(){
    $('#search-form').submit( function(e) {
        e.preventDefault();
        dataTable.ajax.reload();
    });

    $('.bookmark-this').click(_pubutils.addBookmark);

    $('#search-form input[type="text"]').keypress(function (e) {
        if (e.which == 13) {
            $('#search-form').trigger('submit');
        }
    });

    // DataTable settings {{{2
    var dataTable = $('#credit_transactions_table').DataTable({
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

                    if( $this->permissions->checkPermissions('export_player_lists') ){

                ?>
                        {

                            text: "<?php echo lang('CSV Export'); ?>",
                            className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                            action: function ( e, dt, node, config ) {
                                var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                                // utils.safelog(d);

                                <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                                    d.export_format = 'csv';
                                    d.export_type = export_type;

                                    $("#_export_excel_queue_form").attr('action', site_url('/export_data/credit_transactions'));
                                    $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                    $("#_export_excel_queue_form").submit();
                                <?php }else{?>

                                $.post(site_url('/export_data/credit_transactions'), d, function(data){
                                    // utils.safelog(data);

                                    //create iframe and set link
                                    if(data && data.success){
                                        $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                                    }else{
                                        alert('export failed');
                                    }
                                });
                                <?php }?>
                            }
                        }
                <?php
                    }
                ?>
    ],
        columnDefs: [
        { className: 'text-right', targets: [ 4 ] },
    ],
    "order": [ 0, 'desc' ],
    processing: true,
    serverSide: true,
    ajax: function (data, callback, settings) {
        data.extra_search = $('#search-form').serializeArray();
        $.post(base_url + "api/credit_transactions", data, function(data) {
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

});
</script>
<!-- JS code }}}1 -->

<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of credit_transactions.php
