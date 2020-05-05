<th><?=lang('Action')?></th>
<th><?=lang('ID')?></th>
<th><?=lang('Player')?></th>
<th><?=lang('Transfer')?></th>
<th><?=lang('Operator')?></th>
<th><?=lang('Amount')?></th>
<th><?=lang('Status')?></th>
<th><?=lang('Created At')?></th>
<th><?=lang('Updated At')?></th>
<th><?=lang('External ID')?></th>
<th><?=lang('API ID')?></th>
<th><?=lang('Reason')?></th>
<th><?=lang('Query Status')?></th>
<th><?=lang('Fix Flag')?></th>
<th><?=lang('File')?></th>
<th><?=lang('Resp ID')?></th>

<script type="application/javascript">
	var _sort_col_index_for_transfer_list = 7;

	function autoFixTransfer(id){
		if(!confirm("<?=lang('Are you sure')?>?")){
			return;
		}

		var notify=_pubutils.notifyLoading("<?=lang('Loading');?>...");

		//call
		$.post('/payment_management/auto_fix_transfer/'+id, function(data){

			_pubutils.safelog(data);

			var err="";

			if(data){
				if(data['success']){

					var msg="<?=lang('Successful');?>";
					if(data['message']){
						msg=data['message'];
					}

					_pubutils.notifySuccess(msg);

				}else{
					err=data['error_message'];
					_pubutils.notifyErr(err);
				}
			}else{
				err="<?=lang('Failed');?>";
				_pubutils.notifyErr(err);
			}

		}).always(function(){
			_pubutils.closeNotify(notify);
		});
	}

	function queryStatus(id){
		var notify=_pubutils.notifyLoading("<?=lang('Loading');?>...");

		//call
		$.post('/payment_management/query_transfer_status/'+id, function(data){
			_pubutils.safelog(data);

			var err="";

			if(data) {
				if(data['success']) {
					var msg=null;
					if(data['status_message']) {
						msg=data['status_message'];
					} else {
						msg="<?=lang('Unknown Status');?>";
					}

					_pubutils.notifySuccess(msg);
				} else {
					err=data['error_message'];
					_pubutils.notifyErr(err);
				}
			} else {
				err="<?=lang('Query Status Failed');?>";
				_pubutils.notifyErr(err);
			}
		}).fail(function(){
			err="<?=lang('Failed');?>";
			_pubutils.notifyErr(err);
		}).always(function(){
			_pubutils.closeNotify(notify);
		});
	}
</script>
