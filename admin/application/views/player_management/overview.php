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
			<li class="active">
			<a href="#overview" onclick="viewPlayer(<?= $playerId?>, 'overview');">Overview</a>
			</li>

			<li>
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
					<label for="realname">Username: </label>
				</div>

				<div class="col-md-7 col-md-offset-1">
					<input type="text" name="username" id="username" class="form-control" value="<?= $player['username']?>" readonly>
						<?php echo form_error('username', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2 col-md-offset-1">
					<label for="tag">Tag: </label>
				</div>

				<div class="col-md-7 col-md-offset-1">
					<input type="text" name="tag" id="tag" class="form-control" value="<?= $tag['tagName']?>" placeholder="No tag yet" readonly>
						<?php echo form_error('tag', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>

			<div class="row">
				<div class="col-md-4 col-md-offset-1">
					<label for="total_deposited_amount">Total Deposited Amount: </label>
				</div>

				<div class="col-md-5 col-md-offset-1">
					<input type="text" name="total_deposited_amount" id="total_deposited_amount" class="form-control" placeholder="Not yet deposited" value="<?= $total_deposits['totalDeposit'] ?>" readonly>
						<?php echo form_error('total_deposited_amount', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2 col-md-offset-1">
					<label for="balance">Balance: </label>
				</div>

				<div class="col-md-7 col-md-offset-1">
					<input type="text" name="balance" id="balance" class="form-control" value="<?= $player['totalBalanceAmount'] ?>" placeholder="No balance yet" readonly>
						<?php echo form_error('balance', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3 col-md-offset-1">
					<label for="registered_ip">Registered IP: </label>
				</div>

				<div class="col-md-6 col-md-offset-1">
					<input type="text" name="registered_ip" id="registered_ip" class="form-control" value="<?= $player['registerIp']?>" readonly>
						<?php echo form_error('registered_ip', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3 col-md-offset-1">
					<label for="last_login_on">Last Login On: </label>
				</div>

				<div class="col-md-6 col-md-offset-1">
					<input type="text" name="last_login_on" id="last_login_on" class="form-control" value="<?= $player['lastLoginTime']?>" readonly>
						<?php echo form_error('last_login_on', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>
		</div>
	</div>

	<div class="panel-footer">

	</div>
</div>