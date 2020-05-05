<style>
	.notes-textarea {
		resize: none;
		height: 150px !important;
		margin-bottom: 10px;
	}
</style>
<div class="row">
	<div class="col-md-offset-4 col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="fa fa-plus"></i> <?=lang('Batch Deposit')?>
				</h3>
			</div>
			<div class="panel-body">
				<?php if ($errors = validation_errors()): ?>
				<div class="alert alert-danger"><?=$errors?></div>
				<?php endif?>
				<form enctype="multipart/form-data" action="<?=site_url('payment_management/postBatchDeposit')?>" id="form" method="post" autocomplete="off">
					<div class="col-md-12">
						<span style="font-size:12px; text-align: left"><?=sprintf(lang('hint.batch_deposit'), $this->utils->getSystemUrl('admin', 'batch_deposit_upload_sample.csv'))?></span>
					</div>
					<div class="form-group required col-md-12">
						<label for="username" class="control-label"><?=lang('aff.as19')?></label>
						<input type="file" name="usernames" required="required" onchange="return setURL(this.value)" />
						<!-- <input name="username" id="username" class="form-control" type="text" value="<?=set_value('username')?>" required="required"/> -->
					</div>
					<!-- <div class="form-group required">
						<label for="amount" class="control-label"><?=lang('pay.mainwallt')?></label>
						<input name="amount" id="amount" class="form-control" type="number" value="<?=set_value('amount')?>" required="required" min="1" step="any"/>
						<span class="help-block"><?=lang('lang.memberDepLimit')?>: <b id="min_amount"></b> - <b id="max_amount"></b></span>
					</div> -->
					<div class="form-group required col-md-12">
						<label for="account" class="control-label"><?=lang('con.plm35')?></label>
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
								<option value="<?=$account_item->id?>" <?=set_select('account', $account_item->id)?>><?=$account_item->payment_account_name?></option>
							<?php endforeach?>
						</select>
					</div>

					<div class="form-group required col-md-12">
						<label for="date" class="control-label"><?=lang('mess.06')?></label>
						<input name="date" id="date" class="form-control dateInput" type="text" value="<?=set_value('date', $this->utils->getNowForMysql())?>" data-time="true" required="required"/>
					</div>
					<div class="form-group required col-md-6">
						<label for="internal_note" class="control-label"><?=lang('Internal Note')?></label>
						<textarea name="internal_note" id="internal_note" class="form-control notes-textarea" maxlength="500" required="required"><?=set_value('internal_note')?></textarea>
					</div>
					<div class="form-group col-md-6">
						<label for="external_note" class="control-label"><?=lang('External Note')?></label>
						<textarea name="external_note" id="external_note" class="form-control notes-textarea" maxlength="500"><?=set_value('external_note')?></textarea>
					</div>
					<div class="text-right col-md-12">
						<span class="help-block"><b id="submitBtnMsg"></b></span>
					</div>
					<div class="col-md-12 text-right" style="text-align: right;">
						<div class="form-group required text-right" style="padding-top: 15px;padding-right: 30px; display: inline-block;">
							<label class="control-label"><?=lang('lang.status')?></label>
							<label class="radio-inline"><input type="radio" name="status" value="<?php echo Sale_order::STATUS_PROCESSING; ?>" <?php echo $this->utils->getConfig('default_manually_deposit_status') == 'pending' ? 'checked="checked"' : ''; ?> required="required"> <?php echo lang('sale_orders.status.3'); ?></label>
							<?php if ($this->permissions->checkPermissions('set_settled_on_new_deposit')) {?>
							<label class="radio-inline"><input type="radio" name="status" value="<?php echo Sale_order::STATUS_SETTLED; ?>" <?php echo $this->utils->getConfig('default_manually_deposit_status') == 'settled' ? 'checked="checked"' : ''; ?> required="required"> <?php echo lang('sale_orders.status.5'); ?></label>
							<?php }?>
						</div>
						<div class="text-right" style="display: inline-block;">
							<button type="submit" form="form" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?> btn_submit"><i class="fa fa-plus"></i> <?=lang('lang.submit')?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo $this->utils->thirdpartyUrl('jquery-validate/jquery.validate.min.js') ?>"></script>
<?php include __DIR__ . "/../includes/jquery_validate_lang.php"?>
<script type="text/javascript">
	$( function() {
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
			rules: {
				username: {
					remote: {
						url: '/api/playerUsernameExist',
						type: 'post',
						data: {
							username: function() {
								return $('#username').val();
							},
						}
					}
				},
				// date: {
				// 	date: true,
				// }
			},
	        messages: {
	            username: {
	                remote: jQuery.validator.format("<?=lang('aff.as19')?> <b>{0}</b> does not exist.")
	            }
	        },
	        onkeyup: false,
		});

		$( '#username,#amount' ).change( function() {
			var username = username = { username: $('#username').val() };
			$.post('/api/playerDepositLimit', username, function(response) {
				if (response.minDeposit && response.maxDeposit) {
					depositamount = Number($('#amount').val());

					$('#min_amount').text(response.minDeposit);
					$('#max_amount').text(response.maxDeposit);
					if(depositamount < Number(response.minDeposit)){
						$('.btn_submit').prop('disabled', true);
						$('#submitBtnMsg').text("<?php echo lang('Minimum Deposit Amount Reached') ?>");
					}else if(depositamount > Number(response.maxDeposit)){
						$('.btn_submit').prop('disabled', true);
						$('#submitBtnMsg').text("<?php echo lang('Maximum Deposit Amount Reached') ?>");
					}
					else{
						$('.btn_submit').prop('disabled', false);
						$('#submitBtnMsg').text("");
					}
				} else {
					if($(this).prop('id') == 'username'){
						$('#min_amount').text('0.00');
						$('#max_amount').text('0.00');
					}
				}
			}, 'json');
		}).trigger('change');
	});

	function setURL(value) {

	    var val = value;
	    var res = value.split('.').pop();

	    // var oFile = document.getElementById("file").files[0];

     //    if( oFile.size > 51200 ){
     //    	$('#file').val('');
	    //  	return alert('<?=lang('notify.invalid.filesize')?>');
     //    }

	    if( res != 'csv' ){
	     	$('input[name="usernames"]').val('');
	     	return alert('<?=lang('notify.invalid.file')?>');
	    }

	}

</script>