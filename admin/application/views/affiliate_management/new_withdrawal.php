<div class="row">
	<div class="col-md-offset-4 col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('Affiliate Withdraw')?>
				</h3>
			</div>
			<div class="panel-body">
				<?php if ($errors = validation_errors()): ?>
					<div class="alert alert-danger"><?=$errors?></div>
				<?php endif?>
				<form id="form" method="post" autocomplete="off">
					<div class="form-group required">
						<label for="username" class="control-label"><?=lang('Affiliate Username')?> </label>
						<input name="username" id="username" class="form-control" type="text" value="<?php echo $username; ?>" required="required"/>
					</div>
					<?php if($walletType=='main'){?>
					<div class="form-group required">
						<label for="username" class="control-label"><?=lang('Affiliate Main Wallet')?></label>
						<input id="balance" class="form-control" type="text" value="0.00" readonly="readonly" tabindex="-1" />
					</div>
					<?php }else{ ?>
					<div class="form-group required">
						<label for="username" class="control-label"><?=lang('Affiliate Locked Wallet')?></label>
						<input id="balance" class="form-control" type="text" value="0.00" readonly="readonly" tabindex="-1" />
					</div>
					<?php }?>
					<div class="form-group required">
						<label for="amount" class="control-label"><?=lang('cashier.09')?></label>
						<input name="amount" id="amount" class="form-control" type="text" class="number_only" value="<?=set_value('amount')?>" required="required" min="0.01" step="any"/>
					</div>

					<div class="form-group required">
						<label for="date" class="control-label"><?=lang('mess.06')?></label>
						<input name="date" id="date" class="form-control dateInput" type="text" value="<?=set_value('date', $this->utils->getNowForMysql())?>" data-time="true" required="required"/>
					</div>

					<div class="form-group required">
						<label for="date" class="control-label"><?=lang('sys.gd11')?></label>
						<textarea name="reason" id="reason" class="form-control" rows="5" style="resize: none;"><?=set_value('reason')?></textarea>
					</div>
				</form>
			</div>
			<div class="panel-footer">
				<div class="text-right">
					<button type="submit" form="form" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>"><i class="fa fa-minus"></i> <?=lang('Affiliate Withdraw')?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo $this->utils->thirdpartyUrl('jquery-validate/jquery.validate.min.js') ?>"></script>
<?php include __DIR__."/../includes/jquery_validate_lang.php" ?>
<script type="text/javascript">
	$('#amount').keyup(function () {
    	this.value = this.value.replace(/[^0-9\.]/g,'');
	});

	$( function() {
		var validator = $('#form').validate({
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
				        form.submit();
				    }
			    }
			},
			rules: {
				username: {
					remote: {
						url: '/api/exists_aff_username',
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
	                remote: jQuery.validator.format("<?=lang('Affiliate')?> <b>{0}</b> does not exist.")
	            },
	        },
	        onkeyup: false,
		});

		$( '#username' ).change( function() {

			var data = { username: $(this).val(), walletType : "<?php echo $walletType;?>" };

			$.post('/api/get_aff_wallet_balance', data, function(response) {
				if (response.balance) {
					$('#amount').attr('max',response.balance);
					$('#balance').val(response.balance);
				} else {
					// $('#amount').removeAttr('max');
					$('#balance').val('0.00');
				}
			}, 'json');

		}).trigger('change');

	});
</script>