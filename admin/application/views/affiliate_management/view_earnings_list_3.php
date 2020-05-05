<div class="panel panel-primary">

    <div class="panel-heading">
        <h4 class="panel-title">
            <?=lang("lang.search")?>
            <a href="#collapseMonthlyEarnings" class="close" data-toggle="collapse">&times;</a>
        </h4>
    </div>

    <div id="collapseMonthlyEarnings" class="panel-collapse collapse in">
        <div class="panel-body">

            <form class="row" id='search-form' action="<?=site_url('affiliate_management/viewAffiliatePlatformEarnings/')?>" method="get">

                <div class="form-group col-md-4">
                    <?php if ($this->utils->isEnabledFeature('switch_to_affiliate_daily_earnings')): ?>
                    <label class="control-label"><?=lang('Date')?></label>
                    <?php echo form_input('', '', 'class="form-control input-sm dateInput" data-start="#start_date" data-end="#end_date"'); ?>
                    <input type="hidden" name="start_date" id="start_date" value="<?=$conditions['start_date']?>" />
                    <input type="hidden" name="end_date" id="end_date" value="<?=$conditions['end_date']?>" />
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
                    <input type="submit" value="<?=lang('aff.al21')?>" id="search_main"class="btn btn-primary btn-sm">
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

                <div class="form-group col-md-12">
                    <label class="control-label"><?=lang('player.ui29');?> </label>
                    <div class="row">
                        <?php foreach ($game_platform_list as $game_platform_id => $game_platform) {?>
                            <div class="col-md-2">
                                <label>
                                    <input type="checkbox" name="game_platform_id[]" value="<?=$game_platform_id?>" <?=in_array($game_platform_id, $conditions['game_platform_id']) ? 'checked="checked"' : ''?>>
                                    <?=$game_platform?>
                                </label>
                            </div>
                        <?php }?>
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
        <?php if ( ! $this->utils->getConfig('hide_affiliate_commission_formula')): ?>
            <button type="button" class="btn btn-link btn-xs" onclick="showFormula()"><?=lang('view.aff.comm.formula')?></button><br>
        <?php endif ?>

        <?php
            if ($this->utils->isEnabledFeature('display_earning_reports_schedule')) {
        ?>
                <button type="button" class="btn btn-link btn-xs"><?=$cron_sched?></button>
        <?php
            }
        ?>
        <br><br>
        <div class="table-responsive">
			<table class="table table-striped table-bordered" id="earningsTable">
				<thead>
                    <th><input type="checkbox" class="user-success" title="" checkedall = '0' id="select_all_users" data-original-title="Select All on current page"></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Action')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Affiliate Username')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('aff.aj05');?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Game Platform')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Period')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Date')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Game Platform Fee')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Gross Revenue')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Admin Fee')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Bonus Fee')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Cashback Fee')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Transaction Fee')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Total Fee')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Net Revenue')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Commission Rate')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Commission Amount')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Manual Adjustment')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Status')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Paid By')?></th>
				</thead>
				<tbody></tbody>
				<tfoot>
                    <tr id="subtotal">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="game-platform-fee"><?=lang('Game Platform Fee')?></th>
                        <th class="gross-revenue"><?=lang('Gross Revenue')?></th>
                        <th class="admin-fee"><?=lang('Admin Fee')?></th>
                        <th class="bonus-fee"><?=lang('Bonus Fee')?></th>
                        <th class="cashback-fee"><?=lang('Cashback Fee')?></th>
                        <th class="transaction-fee"><?=lang('Transaction Fee')?></th>
                        <th class="total-fee"><?=lang('Total Fee')?></th>
                        <th class="net-revenue"><?=lang('Net Revenue')?></th>
                        <th></th>
                        <th class="commission-amount"><?=lang('Commission Amount')?></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr id="grandtotal">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="game-platform-fee"><?=lang('Game Platform Fee')?></th>
                        <th class="gross-revenue"><?=lang('Gross Revenue')?></th>
                        <th class="admin-fee"><?=lang('Admin Fee')?></th>
                        <th class="bonus-fee"><?=lang('Bonus Fee')?></th>
                        <th class="cashback-fee"><?=lang('Cashback Fee')?></th>
                        <th class="transaction-fee"><?=lang('Transaction Fee')?></th>
                        <th class="total-fee"><?=lang('Total Fee')?></th>
                        <th class="net-revenue"><?=lang('Net Revenue')?></th>
                        <th></th>
                        <th class="commission-amount"><?=lang('Commission Amount')?></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
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
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mainModalLabel"></h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
		var table = $('#earningsTable').DataTable( {
            searching: false,
            processing: true,
            serverSide: true,
            serverSide: true,
            lengthMenu: [ 10, 25, 50, 75, 100, 5000 ],
            order: [[5, 'desc'],[2, 'asc'],[3, 'asc']],
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            ajax: function (data, callback, settings) {

                data.extra_search = $('#search-form').serializeArray();

                $.post('/api/aff_earnings_3', data, function(data) {

                    var game_platform_fee = 0, gross_revenue = 0, admin_fee = 0, bonus_fee = 0, cashback_fee = 0, transaction_fee = 0, total_fee = 0, net_revenue = 0, commission_amount = 0;

                    $.each(data.data, function(i, v){
                        game_platform_fee += Number(v[6].replace(',',''));
                        gross_revenue     += Number(v[7].replace(',',''));
                        admin_fee         += Number(v[8].replace(',',''));
                        bonus_fee         += Number(v[9].replace(',',''));
                        cashback_fee      += Number(v[10].replace(',',''));
                        transaction_fee   += Number(v[11].replace(',',''));
                        total_fee         += Number(v[12].replace(',',''));
                        net_revenue       += Number(v[13].replace(',',''));
                        commission_amount += Number(v[15].replace(',',''));
                    });

                    $('#subtotal .game-platform-fee').text(parseFloat(game_platform_fee).toFixed(2));
                    $('#subtotal .gross-revenue').text(parseFloat(gross_revenue).toFixed(2));
                    $('#subtotal .admin-fee').text(parseFloat(admin_fee).toFixed(2));
                    $('#subtotal .bonus-fee').text(parseFloat(bonus_fee).toFixed(2));
                    $('#subtotal .cashback-fee').text(parseFloat(cashback_fee).toFixed(2));
                    $('#subtotal .transaction-fee').text(parseFloat(transaction_fee).toFixed(2));
                    $('#subtotal .total-fee').text(parseFloat(total_fee).toFixed(2));
                    $('#subtotal .net-revenue').text(parseFloat(net_revenue).toFixed(2));
                    $('#subtotal .commission-amount').text(parseFloat(commission_amount).toFixed(2));

                    $('#grandtotal .game-platform-fee').text(parseFloat(data.summary[0].game_platform_fee).toFixed(2));
                    $('#grandtotal .gross-revenue').text(parseFloat(data.summary[0].gross_revenue).toFixed(2));
                    $('#grandtotal .admin-fee').text(parseFloat(data.summary[0].admin_fee).toFixed(2));
                    $('#grandtotal .bonus-fee').text(parseFloat(data.summary[0].bonus_fee).toFixed(2));
                    $('#grandtotal .cashback-fee').text(parseFloat(data.summary[0].cashback_fee).toFixed(2));
                    $('#grandtotal .transaction-fee').text(parseFloat(data.summary[0].transaction_fee).toFixed(2));
                    $('#grandtotal .total-fee').text(parseFloat(data.summary[0].total_fee).toFixed(2));
                    $('#grandtotal .net-revenue').text(parseFloat(data.summary[0].net_revenue).toFixed(2));
                    $('#grandtotal .commission-amount').text(parseFloat(data.summary[0].commission_amount).toFixed(2));

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
            ],
        } );

        table.on('page.dt', function() {
            $('#select_all_users')[0].checked = false;
            selectionValidate(true);
        });

        $('.calculate').on('click', function(){
            var url = "<?php echo site_url('/cli/command/calculateMonthlyEarnings_3'); ?>/" + $('select[name="year_month"]').val();
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


    //this function is added by jhunel 3-29-2017
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


    function selectionValidate(trigger)
    {
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
            $('#btn-action-transfer').attr('href','<?=site_url('affiliate_management/transfer_all/' . (isset($conditions['year_month']) ? $conditions['year_month'] : $conditions['date']));?>');
        }

        $("#btn-action-transfer").addClass(addClass);
        $("#btn-action-label").text(label);
    }
    //eof added of jhunel

    function showFormula() {
        $('#formula-modal').modal('show').find('.modal-body').load('/affiliate_management/affiliate_formula');
    }

    function modal(load, title) {
        var target = $('#mainModal .modal-body');
        $('#mainModalLabel').html(title);
        target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(load);
        $('#mainModal').modal('show');

    }

</script>