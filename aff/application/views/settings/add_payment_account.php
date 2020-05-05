<div class="container">
	<br/><br/>

	<!-- Payment Information -->
	<div class="row">
		<div class="panel panel-primary">
			<div class="nav-head panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-cog"></i> <?= lang('pay.newacc'); ?> </h4>
				<a href="<?= BASEURL . 'affiliate/modifyAccount'?>" class="btn btn-info btn-xs pull-right"><span class="glyphicon glyphicon-remove "></span></a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="info_panel_body">
				<form action="<?= BASEURL . 'affiliate/verifyaddNewAccount' ?>" method="POST">
					<div class="row">
						<div class="col-md-6 col-md-offset-0">
							<label for="bank_name"><?= lang('pay.bankname'); ?>: </label>
						</div>

						<div class="col-md-6 col-md-offset-0">
							<label for="account_name"><?= lang('pay.accname'); ?>: </label>
						</div>
						<div class="col-md-5 col-md-offset-0">
							<select name="bank_name" id="bank_name" class="form-control">
								<option value="">-- <?=lang('cashier.73');?> --</option>
								<?php foreach ($banks as $row): ?>
									<?php if ($row['enabled_withdrawal']): ?>
										<option value="<?=$row['bankTypeId']?>" <?php echo set_select('bank_name',  $row['bankTypeId']); ?>><?=lang($row['bankName'])?></option>
									<?php endif?>
								<?php endforeach?>
							</select>
							<label style="color: red; font-size: 12px;"><?php echo form_error('bank_name'); ?></label>
						</div>

						<div class="col-md-5 col-md-offset-1">
							<input type="text" name="account_name" id="account_name" class="form-control"value="<?=implode(' ', [$affiliate['firstname'], $affiliate['lastname']])?>" readonly="readonly">
							<label style="color: red; font-size: 12px;" id="msg_account_name"><?php echo form_error('account_name'); ?></label>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 col-md-offset-0">
							<label for="account_info"><?= lang('pay.accinfo'); ?>: </label>
						</div>

						<div class="col-md-6 col-md-offset-0">
							<label for="account_number"><?= lang('pay.accnum'); ?>: </label>
						</div>

						<div class="col-md-5 col-md-offset-0">
							<input type="text" name="account_info" id="account_info" class="form-control" value="<?= set_value('account_info'); ?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('account_info'); ?></label>
						</div>

						<div class="col-md-5 col-md-offset-1">
							<input type="text" name="account_number" id="account_number" class="form-control" value="<?= set_value('account_number'); ?>">
							<label style="color: red; font-size: 12px;" id="msg_account_number"><?php echo form_error('account_number'); ?></label>
						</div>
					</div>

					<div class="row">
						<div class="col-md-2 col-md-offset-5">
							<input type="submit" name="submit" id="submit" class="btn-hov btn btn-info" value="<?= lang('lang.save'); ?>">
							<a href="<?= BASEURL . 'affiliate/modifyAccount' ?>" class="btn btn-default"><?= lang('lang.cancel'); ?></a>
						</div>
					</div>
				</form>
			</div>

			<div class="panel-footer">

			</div>
			<div class="col-md-6 col-md-offset-0">
				<label style="color: red; font-size: 12px;"><?= lang('affiliate_bank_reminder'); ?></label>
			</div>
		</div>
	</div>
	<!-- End of Payment Information -->
</div>
<script type="text/javascript">
	$(document).ready(function() {
	    $("#account_number").keydown(function (e) {
	        // Allow: backspace, delete, tab, escape, enter and .
	        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
	             // Allow: Ctrl+A, Command+A
	            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
	             // Allow: home, end, left, right, down, up
	            (e.keyCode >= 35 && e.keyCode <= 40)) {
	                 // let it happen, don't do anything
	                 return;
	        }
	        // Ensure that it is a number and stop the keypress
	        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
	        	$('#msg_account_number').text("<?=sprintf(lang('formvalidation.is_numeric2'),lang('aff.ai25'))?>");
	            e.preventDefault();
	        } else {
	        	$('#msg_account_number').text("");
	        }

	    });

	    $("#account_name").keydown(function (e) {
	        $('#msg_account_name').text("");
			if (e.shiftKey || e.ctrlKey || e.altKey) {
				e.preventDefault();
				$('#msg_account_name').text("<?=sprintf(lang('formvalidation.alpha'),lang('aff.ai90'))?>");
			} else {
				var key = e.keyCode;
				if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90))) {
					e.preventDefault();
					$('#msg_account_name').text("<?=sprintf(lang('formvalidation.alpha'),lang('aff.ai90'))?>");
				}
			}
	        
	    });
	});
</script>