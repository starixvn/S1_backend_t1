<div id="container">
	<div>

		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt pull-left">
					<i class="icon-settings"></i> <?= lang('system.word1'); ?>
				</h4>
				<button class="btn btn-default btn-sm pull-right panel-button" id="email_toggle"><span class="glyphicon glyphicon-chevron-up" id="button_span_email_up"></span></button>
				<div class="clearfix"></div>
			</div>
			<div class="panel-body main_panel_body" id="email_panel_body" >
				<div class="row">
				<div class="col-md-4">
					<form class="form-horizontal" method="post" action="<?= BASEURL . 'user_management/postSettingEmail/' . $user['userId']?>" id="my_form" autocomplete="off" role="form">
						<h4 class="help-block"><?= lang('system.word5'); ?></h4>
						<hr class="hr_between_table">
						<p class="help-block"><i><?= lang('system.word6'); ?><b><?= $user['email']?></b></i></p>
						<div class="form-group">
							<div class="col-md-11">
								<label for="email" class="control-label"><?= lang('system.word7'); ?> </label>
								<input type="email" name="email" class="form-control input-sm">
								<?php echo form_error('email', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							</div>
						</div>
						<button type="submit" class="btn btn-info btn-sm"><?= lang('lang.save'); ?></button>
					</form>
					<br/>
				</div>

				<div class="col-md-4 vertical_line">
					<form class="form-horizontal" method="post" action="<?= BASEURL . 'user_management/postSettingPassword/' . $user['userId']?>" id="my_form" autocomplete="off" role="form">
						<h4 class="help-block"><?= lang('system.word8'); ?></h4>
						<hr class="hr_between_table">
						<div class="form-group">
							<div class="col-md-11">
								<label for="opassword" class="control-label"><?= lang('system.word9'); ?> </label>
								<input type="password" name="opassword" id="opassword" class="form-control">
								<?php echo form_error('npassword', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							</div>
							<div class="col-md-11">
								<label for="npassword" class="control-label"><?= lang('system.word10'); ?> </label>
								<input type="password" name="npassword" id="npassword" class="form-control">
								<?php echo form_error('npassword', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							</div>
							<div class="col-md-11">
								<label for="ncpassword" class="control-label"><?= lang('system.word11'); ?> </label>
								<input type="password" name="ncpassword" id="ncpassword" class="form-control">
								<?php echo form_error('ncpassword', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							</div>
							<div class="col-md-11">
								<input type="checkbox" id="showVisible" name="showVisible" onclick="showSettPassword(this.id);"> <?= lang('sys.rp11'); ?> <br/> <br/>
								<input type="submit" value="<?= lang('lang.save'); ?>" class="btn btn-info btn-sm">
							</div>
						</div>
					</form>
				</div>

				<div class="col-md-4">
					<form class="form-horizontal" method="post" action="<?= BASEURL . 'user_management/postSettingSafetyQuestion/' . $user['userId']?>" id="my_form" autocomplete="off" role="form">
						<h4 class="help-block"><?= lang('system.word12'); ?></h4>
						<hr class="hr_between_table">
						<div class="form-group">
							<div class="col-md-11">
								<div class="well well-sm">
									<label for="checkCustomize" class="control-label"><?= lang('system.word13'); ?> </label>
									<span><input type="checkbox" id="checkCustomize" name="checkCustomize" value="checkCustomize"></span>
								</div>
							</div>
							<div class="col-md-11">
								<div id="safety_question_field">
									<label for="safetyQuestion" class="control-label"><?= lang('system.word14'); ?> </label>
									<select class="form-control" name="safetyQuestion">
										<option value="">--<?= lang('system.word14'); ?>--</option>
										<option value="What is your mother's maiden name?"><?= lang('system.word15'); ?></option>
										<option value="What is your pet's name?"><?= lang('system.word16'); ?></option>
										<option value="Who is your favorite superhero?"><?= lang('system.word17'); ?></option>
									</select> <?php echo form_error('safetyQuestion', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
								</div>
							</div>
							<div class="col-md-11">
								<div id="csafety_question_field" style="display: none;">
									<label for="csafetyQuestion" class="control-label"><?= lang('system.word18'); ?></label>
									<input type="text" name="csafetyQuestion" class="form-control">
										<?php echo form_error('csafetyQuestion', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
								</div>
							</div>
							<div class="col-md-11">
								<label for="answer" class="control-label"><?= lang('system.word19'); ?> </label>
								<input type="text" name="answer" id="answer" class="form-control">
								<?php echo form_error('answer', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							</div>
						</div>
						<input type="submit" value="<?= lang('lang.save'); ?>" class="btn btn-info btn-sm">
					</form>
				</div>
				</div>
				<?php if($this->utils->isEnabledFeature('enable_otp_on_adminusers')){ ?>
				<div class="row">
					<div class="col-md-4">
						<a href="<?=site_url('/user_management/otp_settings')?>" class="btn btn-sm btn-info"><?=lang('2FA Settings')?></a>
					</div>
				</div>
				<?php }?>
			</div>
		</div>

	</div>
</div>