<style>
	.push-down { margin-top: 15px; }
</style>
<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseMonthlyEarnings" class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
            <?php include __DIR__ . "/../includes/report_tools.php" ?>
        </h4>
    </div>

    <div id="collapseMonthlyEarnings" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
			<form method="get" id='search-form' action="<?=site_url('affiliate_management/viewAffiliateMonthlyEarnings/');?>">
				<div class="form-group">
					<div class="col-md-2">
						<label class="control-label"><?=lang('Year month');?></label>
						<?php echo form_dropdown('by_year_month', $year_month_list, $conditions['by_year_month'], 'class="form-control input-sm"'); ?>
					</div>
					<div class="col-md-2">
						<label for="by_username" class="control-label"><?=lang('aff.al10');?></label>
						<input type="text" name="by_username" id="by_username" class="form-control input-sm" value="<?=$conditions['by_username'];?>">
						<?php echo form_error('by_username', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-2">
						<label for="parent" class="control-label"><?=lang('lang.parentAffiliate');?></label>
						<?php echo form_dropdown('by_parent_id', $affiliates_list, $conditions['by_parent_id'], 'class="form-control input-sm"'); ?>

					</div>
					<div class="col-md-2">
						<label class="control-label"><?=lang('lang.status');?></label>
						<?php echo form_dropdown('by_flag', $flag_list, $conditions['by_flag'], 'class="form-control input-sm"'); ?>

					</div>
					<div class="col-md-3" style="padding-top:23px;text-align:left">
						<div class="form-group">
							<input type="button" value="<?=lang('aff.al22');?>" onclick="resetForm()" class="btn btn-default btn-sm">
							<input type="submit" value="<?=lang('aff.al21');?>" id="search_main"class="btn btn-primary btn-sm">

						</div>
					</div>

					<?php if ($this->config->item('show_calculate_button')) {?>
					<div class="col-md-2" style="text-align:left">
						<div class="form-group">
							<button type="button" class="btn btn-lg btn-warning calculate">
								<i class="fa fa-exclamation-circle"></i> <?=lang('lang.calculatenow');?>
							</button>
						</div>
					</div>
					<?php }
?>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt"><i class="icon-wallet"></i> <?=lang('Earnings Report');?>
					<span class="clearfix"></span>
				</h4>
			</div>

			<div class="panel-body" id="affiliate_panel_body">
                <div class="table-responsive"  >
					<table class="table table-striped table-bordered" id="earningsTable" style="width:100%;">
						<thead>
							<th><?=lang('lang.action');?></th>
							<th><?=lang('system.word38');?></th>
							<th><?=lang('system.word39');?></th>
							<th><?=lang('lang.yearmonth');?></th>
							<th><?=lang('aff.sb8');?></th>
							<th><?=lang('aff.ts08');?></th>
							<th><?=lang('aff.as24');?></th>
                            <th><?=lang('earnings.gross');?></th>
							<th><?=lang('Platform Fee');?></th>
							<th><?=lang('earnings.fee');?></th>
							<th><?=lang('earnings.net');?></th>
							<th><?=lang('aff.ts02');?></th>
							<th><?=lang('Amount');?></th>
                            <th><?=lang('Wallet');?></th>
							<th><?=lang('lang.status');?></th>
							<th><?=lang('aff.ai49');?></th>
						</thead>
						<tbody>

						</tbody>
						<tfoot>

						</tfoot>

					</table>
				</div>
			</div>
			<div class="panel-footer">
				<a href="<?=site_url('affiliate_management/transfer_all/' . $conditions['by_year_month']);?>" class="btn btn-danger">
					<i class="fa fa-paper-plane-o"></i> <?=lang('Transfer all to wallet');?>
				</a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    function resetForm(){
        $('#search-form')[0].reset();
        $('#by_username').val('');
    }
    $(document).ready(function() {
		$('#earningsTable').DataTable( {
            searching: false,
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            // "responsive": {
            //     details: {
            //         type: 'column'
            //     }
            // },
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ]
                }
                <?php if ($export_report_permission) {?>
                ,{
                    text: '<?php echo lang("lang.export_excel"); ?>',
                    className:'btn btn-sm btn-primary',
                    action: function ( e, dt, node, config ) {
                        var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                        // utils.safelog(d);
                        $.post(site_url('/export_data/affiliate_earnings'), d, function(data){
                            // utils.safelog(data);

                            //create iframe and set link
                            if(data && data.success){
                                $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                            }else{
                                alert('export failed');
                            }
                        }).fail(function(){
                            alert('export failed');
                        });
                    }
                }
                <?php }
?>
            ],
            columnDefs: [
                { sortable: false, targets: [ 5 ] },
                { visible: false, targets: [ 14 ] },
                { className: 'text-right', targets: [ 4,5,6,7,8,9,10,11,12 ] },
            ],
            "order": [ 1, 'asc' ],
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/affiliate_earnings", data, function(data) {
                    callback(data);
                    // $('#total_amount').text(data.summary[0].total_amount);
                },'json');
            }
        } );

        // Filters
        // $('#yearmonth').on('change', function(){
        //     var filter = $(this).val();
        //     $('#earningsTable').DataTable().search(filter).draw();
        // });

        // $('.pay').click(function(e){

        //     // console.log(this);

        // 	var earningid=$(this).data('earningid');

        //     // console.log(earningid);

        // 	window.location.href='<?php echo site_url('/affiliate_management/transfer_one'); ?>'+"/"+earningid;

        // 	e.preventDefault();

        // });

        // $('.adjust').on('click', function(){
        // 	var tr 			= $(this).closest('tr');
        // 	var id 			= tr.find('.id').attr('val');
        // 	var yearmonth	= tr.find('.yearmonth').attr('val');
        // 	var username 	= tr.find('.username').attr('username');
        // 	var balance 	= tr.find('.balance').html();
        // 	var note 		= tr.find('.note').html();

        // 	$('#payment').addClass('hide');
        // 	$('#adjustment').removeClass('hide');

        // 	$('#adjustment input[name="affiliateId"').val(id);
        // 	$('#adjustment input[name="year_month"').val(yearmonth);
        // 	$('#adjustment input[name="username"').val(username);
        // 	$('#adjustment input[name="balance"').val(balance);
        // 	$('#adjustment input[name="note"').val(note);
        // });
        // $('.cancel').on('click', function(){
        // 	$('#payment').addClass('hide');
        // 	$('#adjustment').addClass('hide');
        // });

        $('.calculate').on('click', function(){
        	var url = "<?php echo site_url('/affiliate_management/calculateMonthlyEarnings'); ?>/" + $('#yearmonth').val();
        	window.location.href = url;
        });

        $('.btn_pay_all').click(function(){
        	//pay all now
        	window.location.href='<?php echo site_url('/affiliate_management/transfer_all'); ?>'+"/"+$('#yearmonth').val();
        });

    } );

function payOne(ctrl){

    // console.log(this);

    if(confirm('<?php echo lang("sys.sure"); ?>')){

        var earningid=$(ctrl).data('earningid');

        // console.log(earningid);

        window.location.href='<?php echo site_url('/affiliate_management/transfer_one'); ?>'+"/"+earningid;
    }

    // e.preventDefault();

}
</script>
