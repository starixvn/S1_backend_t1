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

			<li>
				<a href="#playerDetail" onclick="viewPlayer(<?= $playerId?>, 'playerDetail');">Player Detail</a>
			</li>

			<li>
				<a href="#accountDetail" onclick="viewPlayer(<?= $playerId?>, 'accountDetail');">Account Detail</a>
			</li>

			<li class="active">
				<a href="#systemDetail" onclick="viewPlayer(<?= $playerId?>, 'systemDetail');">System Detail</a>
			</li>

			<li>
				<a href="#notes" onclick="viewPlayer(<?= $playerId?>, 'notes');">Notes</a>
			</li>
		</ul>

		<div class="content">
			<br/>

			<div class="row">
				<div class="col-md-4">
					<label for="main_wallet_balance">Registered IP: </label>
				</div>

				<div class="col-md-6 col-md-offset-1">
					<input type="text" name="main_wallet_balance" id="main_wallet_balance" class="form-control" value="<?= $player['registerIp']?>" readonly>
						<?php echo form_error('main_wallet_balance', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>

			<div class="row">
				<div class="col-md-4">
					<label for="referral_code">Referral Code: </label>
				</div>

				<div class="col-md-6 col-md-offset-1">
					<input type="text" name="referral_code" id="referral_code" class="form-control" value="<?= $player['invitationCode']?>" readonly>
						<?php echo form_error('referral_code', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-4">
				<label for="main_wallet_balance">Reffered By: </label>
			</div>

			<div class="col-md-6 col-md-offset-1">
				<input type="text" name="main_wallet_balance" id="main_wallet_balance" class="form-control" value="<?= $friend_referral['inviter'] ? $friend_referral['inviter'] : 'No record found' ?>" readonly>
					<?php echo form_error('main_wallet_balance', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
			</div>
		</div>
	</div>

	<div class="panel-footer">

	</div>
</div>