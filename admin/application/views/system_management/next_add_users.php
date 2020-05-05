<ol class="breadcrumb">
	<li class="text-muted"><a href="<?= BASEURL . 'user_management/viewAddUser' ?>"><?= lang('system.word27'); ?></a></li>
	<li class="text-primary"><b><?= lang('system.word28'); ?></b></li>
	<li class="text-muted"><?= lang('system.word29'); ?></li>
</ol>

<form method="post" action="<?= BASEURL . 'user_management/postAddUser'?>" id="my_form" autocomplete="off" class="form-horizontal">
	<div class="panel panel-info">
		<div class="panel-heading">
			<center><?= lang('system.word30'); ?>: <b><?= $role['roleName'] ?></b></center>
			<input type="hidden" name="roleId" value="<?= $role['roleId'] ?>">
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading custom-ph">
					<h4 class="panel-title custom-pt"><i class="glyphicon glyphicon-plus-sign"></i> <?= lang('system.word37'); ?> </h4>
				</div>

				<div class="panel-body custom_panel_padding_space" id="email_panel_body">

					<?php
						$checked = '';
						$passwordField = '';
						$hiddenField = '';

						if(isset($_POST['randomPassword']) && $_POST['randomPassword'] == 'randomPassword') {
							$checked = 'checked';
							$passwordField = 'style="display: none;"';
							$hiddenField = '';
						} else {
							$checked = '';
							$passwordField = '';
							$hiddenField = 'style="display: none;"';
						}
					?>

					<div class="form-group">
						<div class="col-md-4 col-lg-2">
							<label for="username" class="control-label"><span style="color: #ea2f10;">*</span> <?= lang('system.word38'); ?>: </label>
							<input type="text" name="username" id="username" class="form-control input-sm" value="<?php echo set_value('username'); ?>" required="required" maxlength="20">
							<?php echo form_error('username', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
						</div>
						<div class="col-md-4 col-lg-3">
							<label for="realname" class="control-label"><?= lang('system.word39'); ?>: </label>
							<input type="text" name="realname" id="realname" class="form-control input-sm" value="<?php echo set_value('realname'); ?>" maxlength="16">
							<?php echo form_error('realname', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
						</div>
						<div class="col-md-4 col-lg-3">
							<label for="email" class="control-label"><?= lang('system.word42'); ?></label>
							<input type="email" name="email" id="email" class="form-control input-sm" value="<?php echo set_value('email'); ?>" >
							<?php echo form_error('email', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
						</div>
						<div class="col-md-6 col-lg-2">
							<label for="department" class="control-label"><span style="color: #ea2f10;">*</span> <?= lang('system.word40'); ?> </label>
							<input type="text" name="department" id="department" class="form-control input-sm" value="<?php echo set_value('department'); ?>" required="required">
							<?php echo form_error('department', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
						</div>
						<div class="col-md-6 col-lg-2">
							<label for="position" class="control-label"><?= lang('system.word41'); ?> </label>
							<input type="text" name="position" id="position" class="form-control input-sm" value="<?php echo set_value('position'); ?>" >
							<?php echo form_error('position', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
						</div>
					</div>
					<hr class="hr_between_table"/>
					<div class="form-group mg_bottom_zero">
						<div id="hiddenField" <?= $hiddenField ?>>
							<div class="col-md-5">
								<label for="nrandomPassword" class="control-label"><?= lang('system.word44'); ?> </label>
								<input type="text" id="hiddenPassword" name="hiddenPassword" value="<?= $hiddenPassword ?>" readonly style="background-color: aa0000;" class="form-control input-sm">
								<i style="color:#676767;font-size:13px;"><?= lang('system.word50'); ?></i>
							</div>
						</div>
						<div id="passwordField" <?= $passwordField ?>>
							<div class="col-md-5">
								<label for="password" class="control-label"><span style="color: #ea2f10;">*</span> <?= lang('system.word44'); ?> </label>
								<input type="password" name="password" id="password" class="form-control input-sm" required="required" maxlength="34">
								<?php echo form_error('password', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							</div>
							<div class="col-md-5">
								<label for="cpassword" class="control-label"><span style="color: #ea2f10;">*</span> <?= lang('system.word45'); ?> </label>
								<input type="password" name="cpassword" id="cpassword" class="form-control input-sm" required="required" maxlength="34">
								<?php echo form_error('cpassword', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <span class="help-block" id="lcpassword"></span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="form-check mg_left_right_space">
      						<input name="randomPassword" class="form-check-input" type="checkbox" id="randomPassword" value="randomPassword" <?= $checked?>>
      						<label class="form-check-label" for="randomPassword"><?= lang('system.word43'); ?></label>
      					</div>
					</div>
					<?php if($this->rolesfunctions->checkRole($role['roleId']) == true) { ?>
						<hr class="hr_between_table"/>
						<div class="form-group">
							<div class="col-md-3 col-md-offset-0">
								<label for="wid_amt" class="control-label"><?= lang('system.word51'); ?>: </label>
							</div>

							<div class="col-md-2">
								<input type="text" style="width:100%" name="wid_amt" id="wid_amt" class="form-control input-sm number_">
							</div>

							<div class="col-md-4">
								<?php echo form_error('wid_amt', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							</div>
						</div>
					<?php } ?>

					<hr class="hr_between_table"/>
					<div class="form-group">
						<div class="col-md-12">
							<label for="note" class="control-label"><?= lang('system.word46'); ?> </label>
							<textarea name="note" style="width:100%" placeholder="<?= lang('system.word47'); ?>" style="max-width: 250px; max-height: 150px; min-height: 100px; min-width: 100px;" class="form-control input-sm"><?php echo set_value('note'); ?></textarea>
							<?php echo form_error('note', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<button type="submit" class="btn btn-info"><i class="fa fa-check"></i> <?= lang('system.word48'); ?></button>
						<input type="button" value="<?= lang('system.word49'); ?>" class="btn btn-default" onclick="history.back();" />
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</form>