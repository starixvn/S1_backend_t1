<div id="container">
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-plus-sign"></i> Add New Player: </h4>
					<div class="clearfix"></div>
				</div>
				<div class="panel panel-body" id="add_player_panel_body">
					<form method="post" action="<?= BASEURL . 'player_management/postAddPlayer'?>" id="my_form" autocomplete="off" class="form-inline">
						<div class="row">
							<div class="col-md-2 col-md-offset-1">
								<label for="username">Username: </label>
							</div>

							<div class="col-md-4 col-md-offset-1">
								<input type="text" name="username" id="username" class="form-control" value="<?php echo set_value('username'); ?>"> 
									<?php echo form_error('username', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
							</div>
						</div>
						
						<br/>

						<div class="row">
							<div class="col-md-2 col-md-offset-1">
								<label for="realname">Realname: </label>
							</div>

							<div class="col-md-4 col-md-offset-1">
								<input type="text" name="realname" id="realname" class="form-control" value="<?php echo set_value('realname'); ?>"> 
									<?php echo form_error('realname', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
							</div>
						</div>
						
						<br/>

						<div class="row">
							<div class="col-md-2 col-md-offset-1">
								<label for="email">Email: </label>
							</div>

							<div class="col-md-4 col-md-offset-1">
								<input type="email" name="email" id="email" class="form-control" value="<?php echo set_value('email'); ?>"> 
									<?php echo form_error('email', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
							</div>
						</div>

						<hr/><hr/>
						
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

						<div class="row">
							<div class="col-md-6 col-md-offset-1">
								<label for="randomPassword">Use random password: </label>
							</div>

							<div class="col-md-0 col-md-offset-0">
								<input type="checkbox" id="randomPassword" name="randomPassword" value="randomPassword" <?= $checked ?>>
							</div>
						</div>

						<br/>
						
						<div id="passwordField" <?= $passwordField ?>>
							<div class="row">
								<div class="col-md-2 col-md-offset-1">
									<label for="password">Password: </label>
								</div>

								<div class="col-md-4 col-md-offset-1">
									<input type="password" name="password" id="password" class="form-control"> 
										<?php echo form_error('password', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-1">
									<label for="cpassword">Confirm Password: </label>
								</div>

								<div class="col-md-4 col-md-offset-1">
									<input type="password" name="cpassword" id="cpassword" class="form-control"> 
										<?php echo form_error('cpassword', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <span class="help-block" id="lcpassword"></span><br/>
								</div>
							</div>
						</div>

						<div id="hiddenField" <?= $hiddenField ?>>
							<div class="row">
								<div class="col-md-2 col-md-offset-1">
									<label for="nrandomPassword">Password: </label>
								</div>

								<div class="col-md-4 col-md-offset-1">
									<input type="text" id="hiddenPassword" name="hiddenPassword" value="<?= $hiddenPassword ?>" readonly class="form-control">
										<i>This password is cannot be <b>edited</b>...</i>
								</div>
							</div>
						</div>

						<hr/><hr/>

						<div class="row">
							<div class="col-md-2 col-md-offset-1">
								<label for="gender">Gender: </label>
							</div>

							<div class="col-md-4 col-md-offset-1">
								<?php
										$male = "";
										if(isset($_POST['gender']) && $_POST['gender'] == 'Male') {
											$male  = 'checked';
										}

										$female = "";
										if(isset($_POST['gender']) && $_POST['gender'] == 'Female') {
											$female  = 'checked';
										}
								?>

								<input type="radio" name="gender" value="Male" <?= $male ?>> Male
								<input type="radio" name="gender" value="Female" <?= $female ?>> Female
									<?php echo form_error('gender', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
							</div>
						</div>
						
						<br/>

						<div class="row">
							<div class="col-md-2 col-md-offset-1">
								<label for="birthday">Birthday: </label>
							</div>

							<div class="col-md-4 col-md-offset-1">
								<input type="date" name="birthday" id="birthday" class="form-control" value="<?php echo set_value('birthday'); ?>"> 
									<?php echo form_error('birthday', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
							</div>
						</div>
						
						<br/>

						<div class="row">
							<div class="col-md-2 col-md-offset-1">
								<label for="phone">Phone: </label>
							</div>

							<div class="col-md-4 col-md-offset-1">
								<input type="text" name="phone" id="phone" class="form-control" value="<?php echo set_value('phone'); ?>" onkeypress="return isNumberKey(event);"> 
									<?php echo form_error('phone', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
							</div>
						</div>
						
						<br/>

						<div class="row">
							<div class="col-md-2 col-md-offset-1">
								<label for="mobile_phone">Mobile Phone: </label>
							</div>

							<div class="col-md-4 col-md-offset-1">
								<input type="text" name="mobile_phone" id="mobile_phone" class="form-control" value="<?php echo set_value('mobile_phone'); ?>" onkeypress="return isNumberKey(event);"> 
									<?php echo form_error('mobile_phone', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
							</div>
						</div>

						<br/>

						<div class="row">
							<div class="col-md-1 col-md-offset-3">
								<input type="submit" value="Submit" class="btn btn-success">
							</div>

							<div class="col-md-5 col-md-offset-2">
								<input type="button" value="Cancel" class="btn btn-default" onclick="history.back();" />
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>


<!--div class="row">
	<div class="col-md-2 col-md-offset-1">
		<label for="tag">Tags: </label>
	</div>

	<div class="col-md-4 col-md-offset-1">
		<select name="tags" id="tags" class="form-control">
			<option value="">-Select-</option>
			<option value="Bet Against fraud">Bet Against fraud</option>
			<option value="The Carry Trade">The Carry Trade</option>
			<option value="Others">Others</option>
		</select>
			<?php //echo form_error('mobile_phone', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
	</div>
</div>

<br/>

<div class="row" style="display: none;" id="specify">
	<div class="col-md-2 col-md-offset-1">
		<label for="specify">Specify: </label>
	</div>

	<div class="col-md-4 col-md-offset-1">
		<input type="text" class="form-control" name="specified_tag">
			<?php //echo form_error('mobile_phone', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
	</div>
</div>

<br/>

<div class="row">
	<div class="col-md-2 col-md-offset-1">
		<label for="tag_description">Description: </label>
	</div>

	<div class="col-md-4 col-md-offset-1">
		<textarea class="form-control" name="tag_description"></textarea>
			<?php //echo form_error('tag_description', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
	</div>
</div-->