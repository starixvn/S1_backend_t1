<style>
	.push-down { margin-top: 15px; }
</style>
    <div class="panel-heading">
        <h4 class="panel-title">
            <?=lang("lang.search")?>
            <a href="#collapseMonthlyEarnings" class="close" data-toggle="collapse">&times;</a>
        </h4>
    </div>

    <div id="collapseMonthlyEarnings" class="panel-collapse collapse in">
        <div class="panel-body">

            <form class="row" id='search-form' action="<?=site_url('player_management/viewFriendReferralMonthlyEarnings/')?>" method="get">

                <div class="form-group col-md-2">
                    <label class="control-label"><?=lang('Year Month')?></label>
                    <?php echo form_dropdown('year_month', $year_month_list, $conditions['year_month'], 'class="form-control input-sm"'); ?>
                </div>

                <div class="form-group col-md-2">
                    <label class="control-label"><?=lang('Player Username')?></label>
                    <input type="text" name="player_username" id="player_username" class="form-control input-sm" value="<?=$conditions['player_username']?>">
                </div>

                <div class="form-group col-md-2">
                    <label class="control-label"><?=lang('Status')?></label>
                    <?php echo form_dropdown('paid_flag', $flag_list, $conditions['paid_flag'], 'class="form-control input-sm"'); ?>
                </div>

                <div class="form-group col-md-2" style="padding-top:25px; text-align:left">
                    <input type="submit" value="<?=lang('aff.al21')?>" id="search_main"class="btn btn-primary btn-sm">
                </div>

            </form>
        </div>
    </div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title">玩家推薦玩家獎金</h4>
	</div>

	<div class="panel-body" id="player_friend_referrial_panel_body">
        <div class="table-responsive">
			<table class="table table-striped table-bordered" id="earningsTable">
				<thead>
                    <th><!--<input type="checkbox" class="user-success" title="" checkedall = '0' id="select_all_users" data-original-title="Select All on current page">--></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Action')?></th>
 					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Year Month')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Player Username')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Active Players')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Total Players')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('report.g09')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Total Commission')?></th>
					<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Status')?></th>
                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Manual Adjustment')?></th>
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
		$('#earningsTable').DataTable( {
            searching: false,
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post('/api/friend_referrial_monthly_earning', data, function(data) {
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
                // { visible: false, targets: [ 5,6,7,8,9 ] },
                //{ className: 'text-right', targets: [ 2,3,4,5,6,7,8,9,10,11,12,13,14,15,16 ] },
            ],
        } );

        $('.calculate').on('click', function(){
            var url = "<?php echo site_url('/cli/command/calculateFriendReferrialMonthlyEarnings_ibetg'); ?>/" + $('select[name="year_month"]').val();
            window.location.href = url;
        });

        $('.btn_pay_all').click(function(){
            var url = "<?php echo site_url('/player_management/transfer_all'); ?>/" + $('select[name="year_month"]').val();
            window.location.href = url;
        });
    });


    function payOne(ctrl){
        if(confirm('<?php echo lang("sys.sure"); ?>')){
            var earningid = $(ctrl).data('earningid');
            window.location.href='<?php echo site_url('/player_management/transfer_one'); ?>' + '/' + earningid;
        }
    }

    $('#select_all_users').on('change',function(){
        $('.batch-selected-cb').prop('checked', $(this).prop('checked'));
    });


    //this function is added by jhunel 3-29-2017
    function paySelected(){
        var i = 0;
        var earningids = [];
        $('.batch-selected-cb:checked').each(function(){
          earningids[i++] = $(this).val();
        });
        if(confirm('<?php echo lang("sys.sure"); ?>')){
            $.post('/player_management/transfer_selected',{ earningids:earningids }, function(data) {
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
            $('#btn-action-transfer').attr('href','<?=site_url('player_management/transfer_all/' . $conditions['year_month']);?>');
        }

        $("#btn-action-transfer").addClass(addClass);
        $("#btn-action-label").text(label);
    }
    //eof added of jhunel

    function modal(load, title) {
        var target = $('#mainModal .modal-body');
        $('#mainModalLabel').html(title);
        target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(load);
        $('#mainModal').modal('show');

    }

</script>