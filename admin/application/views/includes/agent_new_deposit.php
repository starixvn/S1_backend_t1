<div class="row">
	<div class="col-md-offset-4 col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="fa fa-plus"></i> <?=lang('Deposit Agent')?>
				</h3>
			</div>
			<div class="panel-body">
				<?php if ($errors = validation_errors()): ?>
					<div class="alert alert-danger"><?=$errors?></div>
				<?php endif?>
				<form id="form" method="post" autocomplete="off">
					<div class="form-group required">
						<label for="username" class="control-label"><?=lang('Agent Username')?></label>
						<input name="username" id="username" class="form-control" type="text" value="<?php echo $username; ?>" required="required"/>
					</div>
					<div class="form-group required">
						<label for="amount" class="control-label"><?=lang('Amount')?></label>
						<input name="amount" id="amount" class="form-control" type="number" value="<?=set_value('amount')?>" required="required" min="1" step="any"/>
					</div>
					<div class="form-group required">
						<label for="date" class="control-label"><?=lang('mess.06')?></label>
						<input name="date" id="date" class="form-control dateInput" type="text" value="<?=set_value('date', $this->utils->getNowForMysql())?>" data-time="true" required="required"/>
					</div>
					<div class="form-group required">
						<label for="date" class="control-label"><?=lang('sys.gd11')?></label>
						<textarea name="reason" id="reason" class="form-control" rows="5"><?=set_value('reason')?></textarea>
					</div>
				</form>
			</div>
			<div class="panel-footer">
				<div class="text-right">
					<button type="submit" form="form" class="btn btn-primary"><i class="fa fa-plus-circle"></i>
					<?=lang('Deposit Agent')?></button>
                    <?php if (isset($agent_id) && $agent_id > 0) { ?>
                    <a href="/<?=$controller_name?>/agent_information/<?=$agent_id?>#bank_info" class="btn btn-default"><?=lang('lang.cancel');?></a>
                    <?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo $this->utils->thirdpartyUrl('jquery-validate/jquery.validate.min.js') ?>"></script>
<?php include __DIR__."/../includes/jquery_validate_lang.php" ?>
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
				        form.submit();
				    }
			    }
			},
			rules: {
				username: {
					remote: {
						url: '/api/exists_agent_username',
						type: 'post',
						data: {
							username: function() {
								return $('#username').val();
							},
						}
					}
				},
				date: {
					date: true,
				}
			},
	        messages: {
	            username: {
	                remote: jQuery.validator.format("<?=lang('Agent')?> <b>{0}</b> does not exist.")
	            }
	        },
	        onkeyup: false,
		});

	});
</script>
