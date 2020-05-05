<style type="text/css">
  .nav-tabs li a {font-size:13px;}
</style>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> Other Details </h4>
		<a href="#close" class="btn btn-primary btn-sm pull-right" id="chat_history" onclick="closeDetails()"><span class="glyphicon glyphicon-remove"></span></a>
		<div class="clearfix"></div>
	</div>

	<div class="panel-body" id="details_panel_body">
		<ul class="nav nav-tabs">
			<li>
				<a href="#overview" onclick="viewPlayer(<?= $playerId?>, 'overview');">Overview</a>
			</li>

			<li class="active">
				<a href="#playerDetail" onclick="viewPlayer(<?= $playerId?>, 'playerDetail');">Player Detail</a>
			</li>

			<li>
				<a href="#accountDetail" onclick="viewPlayer(<?= $playerId?>, 'accountDetail');">Account Detail</a>
			</li>

			<li>
				<a href="#systemDetail" onclick="viewPlayer(<?= $playerId?>, 'systemDetail');">System Detail</a>
			</li>

			<li>
				<a href="#notes" onclick="viewPlayer(<?= $playerId?>, 'notes');">Notes</a>
			</li>
		</ul>

		<div class="content">
			<br/>
			<div class="row">
				<div class="col-md-2 col-md-offset-1">
					<label for="username">Username: </label>
				</div>

				<div class="col-md-7 col-md-offset-1">
					<input type="text" name="username" id="username" class="form-control" value="<?= $player['username']?>" readonly>
						<?php echo form_error('username', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3 col-md-offset-1">
					<label for="first_name">First Name: </label>
				</div>

				<div class="col-md-6 col-md-offset-1">
					<input type="text" name="first_name" id="first_name" class="form-control" value="<?= $player['firstName']?>" readonly>
						<?php echo form_error('first_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3 col-md-offset-1">
					<label for="last_name">Last Name: </label>
				</div>

				<div class="col-md-6 col-md-offset-1">
					<input type="text" name="last_name" id="last_name" class="form-control" value="<?= $player['lastName']?>" readonly>
						<?php echo form_error('last_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2 col-md-offset-1">
					<label for="gender">Gender: </label>
				</div>

				<div class="col-md-7 col-md-offset-1">
					<input type="text" name="gender" id="gender" class="form-control" value="<?= $player['gender']?>" readonly>
						<?php echo form_error('gender', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>


			<div class="row">
				<div class="col-md-2 col-md-offset-1">
					<label for="birthdate">Birthdate: </label>
				</div>

				<div class="col-md-7 col-md-offset-1">
					<input type="date" name="birthdate" id="birthdate" class="form-control" value="<?= $player['birthdate']?>" readonly>
						<?php echo form_error('birthdate', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>


			<div class="row">
				<div class="col-md-4 col-md-offset-1">
					<label for="contact_number">Contact Number: </label>
				</div>

				<div class="col-md-5 col-md-offset-1">
					<input type="text" name="contact_number" id="contact_number" class="form-control" value="<?= $player['contactNumber']?>" readonly>
						<?php echo form_error('contact_number', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2 col-md-offset-1">
					<label for="email">Email: </label>
				</div>

				<div class="col-md-7 col-md-offset-1">
					<input type="text" name="email" id="email" class="form-control" value="<?= $player['email']?>" readonly>
						<?php echo form_error('email', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>
		</div>
	</div>

	<div class="panel-footer">

	</div>
</div>