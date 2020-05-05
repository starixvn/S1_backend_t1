<style media="screen" type="text/css">
.full-width {
    display:block;
}
</style>

<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseManageCashbackRequest" class="btn btn-info btn-xs <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapseManageCashbackRequest" class="panel-collapse collapse <?= $this->config->item('default_open_search_panel') ? 'in' : ''?>">
        <form id="search-form" class="form-horizontal" method="get">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-2">
                        <label class="control-label"><?=lang('xpj.cashback.request_datetime')?></label>
                        <input type="text" class="form-control dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
                        <input type="hidden" name="date_from" id="date_from"/>
                        <input type="hidden" name="date_to" id="date_to"/>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label" for="username"><?=lang('pay.username')?></label>
                        <input type="text" name="username" id="username" value="<?=($conditions['username']) ? $conditions['username']:''?>" class="form-control"/>
                    </div>

                    <div class="col-md-2">
                        <label class="control-label" for="status"><?=lang('xpj.cashback.status');?></label>
                        <select class="form-control input-sm" name="status" id="status">
                            <option value=""><?=lang('lang.selectall');?></option>
                            <option value="1">APPROVED</option>
                            <option value="2">DECLINED</option>
                            <option value="3">PENDING</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-lg-2">
                        <label class="control-label" for="request_amount_from"><?=lang('xpj.cashback.request_amount_from')?></label>
                        <input type="number" name="request_amount_from" id="request_amount_from" value="<?=($conditions['request_amount_from']) ? $conditions['request_amount_from']:''?>" class="form-control number_only"/>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="control-label" for="request_amount_to"><?=lang('xpj.cashback.request_amount_to')?></label>
                        <input type="number" name="request_amount_to" id="request_amount_to" value="<?=($conditions['request_amount_to']) ? $conditions['request_amount_to']:''?>" class="form-control number_only"/>
                    </div>

                </div>
            </div>
            <div class="panel-footer text-right">
                <input type="submit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary' ?>" id="btn-submit" value="<?php echo lang('Search'); ?>" >
            </div>
        </form>

    </div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading custom-ph">
		<h4 class="panel-title">
			<?=lang('xpj.cashback')?>
		</h4>
	</div>
	<div class="panel-body">

		<table class="table table-condensed table-bordered table-hover" id="summary-report" style="width: 100%;">
			<thead>
				<tr>
					<th><?=lang('column.id')?></th>
					<th><?=lang('column.player_id')?></th>
					<th><?=lang('column.admin_id')?></th>
					<th><?=lang('pay.username')?></th>
					<th><?=lang('xpj.cashback.request_datetime')?></th>
					<th><?=lang('xpj.cashback.request_amount')?></th>
					<th><?=lang('xpj.cashback.status')?></th>
					<th><?=lang('xpj.cashback.processed_by')?></th>
					<th><?=lang('xpj.cashback.processed_datetime')?></th>
					<th><?=lang('xpj.cashback.notes')?></th>
					<th><?=lang('xpj.cashback.created_at')?></th>
                    <th><?=lang('lang.action');?></th>
				</tr>
			</thead>
		</table>
	</div>
	<div class="panel-footer"></div>
</div>

<div class="modal fade bs-example-modal-md" id="cashbackRequestCancel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel" style="margin: 0 10px;"><?=lang('xpj.cashback.decline_msg');?>: </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form method="post" action="<?=site_url('marketing_management/declineCashbackRequest')?>">
							<?=lang('xpj.cashback.decline_reason');?>
                            <input type="hidden" name="declineCashbackRequestId" id="decline_cashback_request_id" class="form-control">
                            <textarea name="reasonToCancel" class="form-control reason_text" rows="7" required></textarea>
                            <br/>
                            <center>
                                <button class="btn btn-primary" style="width:30%"><?=lang('lang.submit');?></button>
                            </center>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">

    $(document).ready(function(){
        // $('#view_cashback_request_list').addClass('active');
        $('.dateInput').val('');
        $('#date_from').val('');
        $('#date_to').val('');

        var dataTable = $('#summary-report').DataTable( {

            columnDefs: [
                {
                    targets: 1,
                    visible: false,
                },
                {
                    targets: 2,
                    visible: false,
                }
            ],

            processing: true,
            serverSide: true,
			ajax: function (data, callback, settings) {
				data.extra_search = $('#search-form').serializeArray();
				$.post(base_url + "api/cashback_request", data, function(data) {
					callback(data);
				},'json');
			},
        } );

        $('#search-form').submit(function(e) {
        	e.preventDefault();
            dataTable.ajax.reload();
        })
    });

    function approveCashback(cashback_request_id){
        if(confirm("<?php echo lang('confirm.approve'); ?>")){
            window.location.href="<?php echo site_url('marketing_management/approveCashbackRequest') ?>/"+cashback_request_id;
        }
    }

    function viewCashbackDeclineForm(cashback_request_id){
        $('.reason_text').val('');
        $('#decline_cashback_request_id').val(cashback_request_id);
    }

</script>
