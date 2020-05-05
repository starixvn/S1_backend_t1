<div class="row">
	<div class="col-md-offset-4 col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="fa fa-plus"></i> <?=lang('New Internal Withdrawal')?>
				</h3>
			</div>
			<div class="panel-body">
				<?php if ($errors = validation_errors()): ?>
					<div class="alert alert-danger"><?=$errors?></div>
				<?php endif?>
				<form id="form" method="post" autocomplete="off">
					<div class="form-group required">
						<label for="account" class="control-label"><?=lang('pay.payment_account_name');?></label>
						<select name="account" id="account" class="form-control" value="<?=set_value('account')?>" required="required">
							<option value=""><?=lang('cashier.74')?></option>
							<?php $previous_account = null?>
							<?php foreach ($account_list as $account_item): ?>
								<?php if ($previous_account != $account_item->payment_type): ?>
									<?php if ($previous_account !== null): ?>
										</optgroup>
									<?php endif?>
									<optgroup label="<?=lang($account_item->payment_type)?>">
								<?php endif?>
								<?php $previous_account = $account_item->payment_type?>
								<option value="<?=$account_item->payment_account_id?>" <?=set_select('account', $account_item->payment_account_id)?>><?=$account_item->payment_account_name?></option>
							<?php endforeach?>
						</select>
					</div>
					<div class="form-group">
						<label for="payment_account_flag_name" class="control-label"><?=lang('pay.payment_account_flag')?></label>
						<input type="text" name="payment_account_flag_name" id="payment_account_flag_name" class="form-control" value="" disabled="disabled">
						<input type="hidden" name="payment_account_flag" id="payment_account_flag">
					</div>
					<div class="form-group">
						<label for="payment_account_number" class="control-label"><?=lang('pay.payment_account_number')?></label>
						<input type="text" name="payment_account_number" id="payment_account_number" class="form-control" value="" disabled="disabled">
					</div>
					<div class="form-group required">
						<label for="amount" class="control-label"><?=lang('Internal Withdrawal Amount')?></label>
						<input name="amount" id="amount" class="form-control" type="number" value="<?=set_value('amount')?>" required="required" min="1" step="any"/>
					</div>
				</form>
			</div>
			<div class="panel-footer">
				<div class="text-right">
					<span class="help-block"><b id="submitBtnMsg"></b></span>
					<button type="submit" form="form" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?> btn_submit"><i class="fa fa-plus"></i> <?=lang('Add New Internal Withdrawal')?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo $this->utils->thirdpartyUrl('jquery-validate/jquery.validate.min.js') ?>"></script>
<?php include __DIR__ . "/../includes/jquery_validate_lang.php"?>
<script type="text/javascript">
	$( function() {
		var account_list = <?=!empty($account_list)?json_encode($account_list):'[]';?>;
		$('select[name="account"]').on('change', function(){
			var payment_account_id = $(':selected', this).val();
			var account_info = account_list.filter(function(row){
				if (row.payment_account_id == payment_account_id) {
					return row;
				}
			});
			$('input[name="payment_account_flag_name"]').val(account_info[0].flag_name);
			$('input[name="payment_account_flag"]').val(account_info[0].flag);
			$('input[name="payment_account_number"]').val(account_info[0].payment_account_number);
			//alert(JSON.stringify(account_info));
		});
		$('#form').validate({
			errorElement: 'span',
			errorClass: 'help-block',
		    highlight: function (element, errorClass, validClass) {
	            $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
		    },
		    unhighlight: function (element, errorClass, validClass) {
	            $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
		    },
			submitHandler: function(form) {
			    if ($(form).valid()) {
			    	if (confirm('<?=lang('sys.sure')?>')) {
			    		$('.btn_submit').prop('disabled', true);
				        form.submit();
				    }
			    }
			},
	        onkeyup: false,
		});
	});
</script>