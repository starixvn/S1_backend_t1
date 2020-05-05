<?php
/**
 *   filename:   settlement.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-02
 *   @brief:     view settlement information for agency sub-system
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
                <!-- search on date {{{3 -->
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
                </div>
                <!-- search on date }}}3 -->
                <!-- input row {{{3 -->
                <div class="row">
                    <div class="col-md-4 col-lg-4">
                        <label class="control-label"><?=lang('Agent Username');?></label>
                        <input type="text" name="agent_name" class="form-control input-sm"
                        placeholder=' <?=lang('Enter Agent Username');?>'
                        value="<?php echo $conditions['agent_name']; ?>"/>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <label class="control-label"><?=lang('Parent Agent Username');?></label>
                        <input type="text" name="parent_name" class="form-control input-sm"
                        placeholder=' <?=lang('Enter Parent Agent Username');?>'
                        value="<?php echo $conditions['parent_name']; ?>"/>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <label class="control-label"><?=lang('Status');?></label>
                        <select name="status" id="status" class="form-control input-sm">
                            <option value="" <?=empty($conditions['status']) ? 'selected' : ''?>>
                            --  <?=lang('None');?> --
                            </option>
                            <option value="current" <?=($conditions['status'] == "current") ? 'selected' : ''?> >
                            <?=lang('Current');?>
                            </option>
                            <option value="settled" <?=($conditions['status'] == "settled") ? 'selected' : ''?> >
                            <?=lang('Settled');?>
                            </option>
                            <option value="unsettled" <?=($conditions['status'] == "unsettled") ? 'selected' : ''?> >
                            <?=lang('Unsettled');?>
                            </option>
                            <option value="frozen" <?=($conditions['status'] == "frozen") ? 'selected' : ''?> >
                            <?=lang('Frozen');?>
                            </option>
                        </select>
                    </div>
                </div> <!-- input row }}}3 -->
                <!-- button row {{{3 -->
                <div class="row">
                    <div class="col-md-4 col-lg-4 pull-right" style="padding-top: 20px;">
                        <input type="button" value="<?=lang('lang.reset');?>"
                        class="btn btn-default btn-sm"
                        onclick="window.location.href='<?php echo site_url('agency_management/settlement'); ?>'">

                        <input class="btn btn-sm btn-primary" type="submit"
                        value="<?=lang('lang.search');?>" />
                    </div>
                    <div class="col-md-4" style="padding-top: 20px">
                        <input type="button" value="<?=lang('Export in Excel')?>" class="btn btn-success btn-sm export_excel">
                    </div>
                </div> <!-- button row }}}3 -->
                <!--  modal for send settlement invoice {{{4 -->
                <div class="modal fade in" id="send_invoice_modal"
                    tabindex="-1" role="dialog" aria-labelledby="label_send_invoice_modal">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="label_send_invoice_modal"></h4>
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer"></div>
                        </div>
                    </div>
                </div> <!--  modal for level name setting }}}4 -->
            </div>
            <!-- panel body }}}2 -->
        </div>
    </div>
</form> <!-- end of search form }}}1 -->

<!-- panel for settlement table {{{1 -->
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-bullhorn"></i>
            <?=lang('Settlement List');?>
        </h4>
    </div>
    <div class="panel-body">
        <!-- settlement table {{{2 -->
        <div id="logList" class="table-responsive">
            <table class="table table-striped table-hover table-condensed"
                id="settlement_table" style="width:100%;">
                <thead>
                    <tr>
                        <?php include __DIR__.'/../includes/agency_settlement_table_header.php'; ?>
                        <th><?=lang('Action');?></th>
                    </tr>
                </thead>
            </table>
        </div>
        <!--end of settlement table }}}2 -->
    </div>
    <div class="panel-footer"></div>
</div>
<!-- panel for settlement table }}}1 -->

<!-- JS code {{{1 -->
<script type="text/javascript">
$(document).ready(function(){

    $('.bookmark-this').click(_pubutils.addBookmark);

    $('#search-form input[type="text"]').keypress(function (e) {
        if (e.which == 13) {
            $('#search-form').trigger('submit');
        }
    });

    // DataTable settings {{{2
    var dataTable = $('#settlement_table').DataTable({
        autoWidth: false,
        searching: false,
        dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
        /*
        "responsive": {
            details: {
                    type: 'column'
                }
        },
         */
        buttons: [
            {
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ]
            }
        ],
        columnDefs: [
            //{ className: 'text-right', targets: [ 20 ] },
<?php if($this->utils->getConfig('show_agency_rev_share_etc')){?>
            { sortable: false, targets: [ 20 ] },
            { visible: false, targets: [ 6,7,8,13,14,15 ] },
<?php }else{?>
            { sortable: false, targets: [ 15 ] },
            //{ visible: false, targets: [ 6,7,8,10,11,12 ] },
<?php }?>
        ],
        "order": [ 0, 'asc' ],
        processing: true,
        serverSide: true,
        ajax: function (data, callback, settings) {
            data.extra_search = $('#search-form').serializeArray();
            $.post(base_url + "api/agency_settlement", data, function(data) {
                callback(data);
            },'json');
        },
    }); // DataTable settings }}}2

    $('#search-form').submit( function(e) {
        e.preventDefault();
        dataTable.ajax.reload();
    });

        $('.export_excel').click(function(){

        	// utils.safelog(dataTable.columns());

            var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
            var export_url = '<?php echo site_url('export_data/agency_settlement_list') ?>';
        	// utils.safelog(d);
        	//$.post(site_url('/export_data/agency_settlement_list'), d, function(data){
        	$.post(export_url, d, function(data){
        		//utils.safelog(data);

        		//create iframe and set link
        		if(data && data.success){
        			$('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
        		}else{
        			alert('export failed');
        		}
        	});
        });

});
</script>
<!-- JS code }}}1 -->

<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of settlement.php
