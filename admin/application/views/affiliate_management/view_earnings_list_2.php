<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <?=lang("lang.search")?>
            <a href="#collapseMonthlyEarnings" class="close" data-toggle="collapse">&times;</a>
        </h4>
    </div>
    <div id="collapseMonthlyEarnings" class="panel-collapse collapse in">
        <div class="panel-body">
            <form class="row" id='search-form' action="<?=site_url('affiliate_management/viewAffiliateEarnings/')?>" method="get">
                <div class="form-group col-md-2">
                    <?php if ($this->utils->isEnabledFeature('switch_to_affiliate_daily_earnings')): ?>
                    <label class="control-label"><?=lang('Date')?></label>
                    <?php echo form_input('date', $conditions['date'], 'class="form-control input-sm dateInput"'); ?>
                    <?php else: ?>
                    <label class="control-label"><?=lang('Year Month')?></label>
                    <?php echo form_dropdown('year_month', $year_month_list, $conditions['year_month'], 'class="form-control input-sm"'); ?>
                    <?php endif ?>
                </div>

                <div class="form-group col-md-2">
                    <label class="control-label"><?=lang('Affiliate Username')?></label>
                    <input type="text" name="affiliate_username" id="affiliate_username" class="form-control input-sm" value="<?=$conditions['affiliate_username']?>">
                </div>

                <div class="form-group col-md-2">
                    <label class="control-label"><?=lang('Parent Affiliate')?></label>
                    <?php echo form_dropdown('parent_affiliate', $affiliates_list, $conditions['parent_affiliate'], 'class="form-control input-sm"'); ?>
                </div>

                <div class="form-group col-md-2">
                    <label class="control-label"><?=lang('Status')?></label>
                    <?php echo form_dropdown('paid_flag', $flag_list, $conditions['paid_flag'], 'class="form-control input-sm"'); ?>
                </div>

                <div class="form-group col-md-2" style="padding-top:25px; text-align:left">
                    <input type="submit" value="<?=lang('aff.al21')?>" id="search_main"class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-primary'?>">
                </div>

                <div class="form-group col-md-12">
                    <label class="control-label"><?=lang('Affiliate Tag');?> </label>
                    <div class="row">
                        <?php if(isset($tags) && !empty($tags)):?>
                            <?php foreach ($tags as $tag_id => $tag) {?>
                                <div class="col-md-2">
                                    <label>
                                        <input type="checkbox" name="tag_id[]" value="<?=$tag_id?>" <?=in_array($tag_id, $conditions['tag_id']) ? 'checked="checked"' : ''?>>
                                        <?=$tag?>
                                    </label>
                                </div>
                            <?php }?>
                        <?php endif;?>
                    </div>
                </div>
            </form>

            <?php if ($this->config->item('show_calculate_button') && ! $this->utils->isEnabledFeature('switch_to_affiliate_daily_earnings')) {?>
                <div class="form-group">
                    <button type="button" class="btn btn-lg btn-warning calculate">
                        <i class="fa fa-exclamation-circle"></i> <?=lang('lang.calculatenow');?>
                    </button>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title"><?=lang('Earnings Report')?></h4>
	</div>
	<div class="panel-body" id="affiliate_panel_body">
        <?php if (!$this->utils->getConfig('hide_affiliate_commission_formula')): ?>
            <button type="button" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-havelockblue' : 'btn-link'?>" onclick="showFormula()"><?=lang('view.aff.comm.formula')?></button><br>
        <?php endif ?>
        <?php if ($this->utils->isEnabledFeature('display_earning_reports_schedule')) { ?>
            <button type="button" class="btn btn-link btn-xs"><?=$cron_sched?></button>
        <?php } ?>
        <br><br>
        <div class="table-responsive">
			<table class="table table-striped table-bordered" id="earningsTable">
				<thead>
                    <th><input type="checkbox" class="user-success" title="" checkedall = '0' id="select_all_users" data-original-title="Select All on current page"></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Action')?></th>
 					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Year Month')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Affiliate Username')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('aff.aj05');?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Active Players')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Total Players')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Gross Revenue')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Platform Fee')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Bonus Fee')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Cashback Fee')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Transaction Fee')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Admin Fee')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Total Fee')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Net Revenue')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Commission Rate')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Commission Amount')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Commission From Sub-affiliates')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Total Commission')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Manual Adjustment')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Status')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Paid By')?></th>
				</thead>
				<tbody></tbody>
				<tfoot></tfoot>
			</table>
		</div>
	</div>
	<div class="panel-footer">
        <a class="btn" id="btn-action-transfer">
            <i class="fa fa-paper-plane-o"></i> <i id="btn-action-label"></i>
        </a>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="formula-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?=lang('Affiliate Commission Formula')?></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('Close');?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade in" id="mainModal" tabindex="-1" role="dialog" aria-labelledby="mainModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mainModalLabel"></h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
        <input name='json_search' type="hidden">
    </form>
    <form id="_export_csv_form" class="hidden" method="POST" target="_blank">
        <input name='json_search' id = "json_csv_search" type="hidden">
    </form>
<?php }?>

<script type="text/javascript">
    var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";
    $(document).ready(function() {
		var table = $('#earningsTable').DataTable( {
            searching: false,
            processing: true,
            serverSide: true,
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post('/api/aff_earnings', data, function(data) {
                    callback(data);
                    var totalCheckboxes = $('input:checkbox').length;
                    if(totalCheckboxes == 0) {
                        $('#btn-action-transfer').attr({disabled: 'true'});
                        selectionValidate(true);
                    } else {
                        $('#btn-action-transfer').removeAttr("disabled");
                        selectionValidate(false);
                    }

                }, 'json');
            },
            columnDefs: [
                { sortable: false, targets: [ 0 ] },
                { className: 'text-right', targets: [ 2,3,4,5,6,7,8,9,10,11,12,13,14,15,16 ] },
            ],
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: "<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>"
                }
                <?php if ($export_report_permission) {?>
                ,{
                    text: '<?php echo lang("lang.export_excel"); ?>',
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                    action: function ( e, dt, node, config ) {
                        var d = {'extra_search': $('#search-form').serializeArray(), 'export_format': 'csv', 'export_type': export_type,
                            'draw':1, 'length':-1, 'start':0};
                        <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                                    $("#_export_excel_queue_form").attr('action', site_url('/export_data/affiliate_earnings'));
                                    $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                    $("#_export_excel_queue_form").submit();
                        <?php }else{?>

                        $.post(site_url('/export_data/affiliate_earnings'), d, function(data){

                            //create iframe and set link
                            if(data && data.success){
                                $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                            }else{
                                alert('export failed');
                            }
                        }).fail(function(){
                            alert('export failed');
                        });

                        <?php }?>
                    }
                }
                <?php } ?>
            ],
        } );

        table.on('page.dt', function() {
            $('#select_all_users')[0].checked = false;
            selectionValidate(true);
        });

        $('.calculate').on('click', function(){
            var url = "<?php echo site_url('/cli/command/calculateMonthlyEarnings_2'); ?>/" + $('select[name="year_month"]').val();
            window.location.href = url;
        });
    });

    function payOne(ctrl){
        if(confirm('<?php echo lang("sys.sure"); ?>')){
            var earningid = $(ctrl).data('earningid');
            window.location.href='<?php echo site_url('/affiliate_management/transfer_one'); ?>' + '/' + earningid;
        }
    }

    $('#select_all_users').on('change',function(){
        $('.batch-selected-cb').prop('checked', $(this).prop('checked'));
        selectionValidate( ! $(this).prop('checked'));
    });

    function paySelected(){
        var i = 0;
        var earningids = [];
        $('.batch-selected-cb:checked').each(function(){
          earningids[i++] = $(this).val();
        });
        if(confirm('<?php echo lang("sys.sure"); ?>')){
            $.post('/affiliate_management/transfer_selected',{ earningids:earningids }, function(data) {
                window.location.href=data;
            });
        }
    }

    function transferAll() {
        var isContinue = confirm("<?= lang('Are you sure you want to continue?') ?>");
        if (isContinue) {
            window.location.href = "<?=site_url('affiliate_management/transfer_all/' . (isset($conditions['year_month']) ? $conditions['year_month'] : $conditions['date']));?>";
        }
    }

    function selectionValidate(trigger) {
        var count = $("[type='checkbox']:checked").length;
        $("#btn-action-transfer").removeClass("btn-success btn-danger");
        $("#btn-action-label").text("");
        $("#btn-action-transfer").removeAttr("href onClick");
        if(trigger) {
            var addClass = "btn-danger";
            var label = "<?=lang('Transfer all to wallet');?>";
            $('#btn-action-transfer').attr({href: 'javascript:void(0)'});
        } else if(count > 0) {
            var addClass = "btn-success";
            var label = "<?=lang('Transfer Selected to wallet');?>";
            $('#btn-action-transfer').attr({href: 'javascript:void(0)' , onClick: 'paySelected();'});
        } else {
            var addClass = "btn-danger";
            var label = "<?=lang('Transfer all to wallet');?>";
            $('#btn-action-transfer').attr({href: 'javascript:void(0)' , onClick: 'transferAll();'});
        }

        $("#btn-action-transfer").addClass(addClass);
        $("#btn-action-label").text(label);
    }

    function showFormula() {
        $('#formula-modal').modal('show').find('.modal-body').load('/affiliate_management/affiliate_formula');
    }

    function modal(load, title) {
        var target = $('#mainModal .modal-body');
        $('#mainModalLabel').html(title);
        target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(load);
        $('#mainModal').modal('show');
        return false;
    }
</script>