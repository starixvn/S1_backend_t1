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

			<li class="active">
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
				<div class="col-md-4">
					<label for="main_wallet_balance">Main Wallet: </label>
				</div>

				<div class="col-md-6 col-md-offset-1">
					<input type="text" name="main_wallet_balance" id="main_wallet_balance" class="form-control" value="Main" readonly>
						<?php echo form_error('main_wallet_balance', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>

			<?php if(!empty($subwallet)) { ?>
				<?php foreach($subwallet as $row) { ?>
					<div class="row">
						<div class="col-md-4">
							<label for="sub_wallet_balance"> <?= strtoupper($row['game']) ?> Sub Wallet: </label>
						</div>

						<div class="col-md-6 col-md-offset-1">
							<input type="text" name="sub_wallet_balance" id="sub_wallet_balance" class="form-control" value="<?= $row['totalBalanceAmount'] ?>" readonly>
								<?php echo form_error('sub_wallet_balance', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
						</div>
					</div>
				<?php } ?>
			<?php } ?>


			<div class="row">
				<div class="col-md-4">
					<label for="total_balance">Total Balance: </label>
				</div>

				<div class="col-md-6 col-md-offset-1">
					<input type="text" name="total_balance" id="total_balance" class="form-control" value="<?= $player['totalBalanceAmount']?>" readonly>
						<?php echo form_error('total_balance', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>

			<div class="row">
				<div class="col-md-5">
					<label for="daily_max_withdrawal">Daily Max-Withdrawal: </label>
				</div>

				<div class="col-md-5 col-md-offset-1">
					<input type="text" name="daily_max_withdrawal" id="daily_max_withdrawal" class="form-control" value="<?= $ranking_settings[0]['dailyMaxWithdrawal']?>" readonly>
						<?php echo form_error('daily_max_withdrawal', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
				</div>
			</div>

		</div>
	</div>

	<div class="panel-footer">

	</div>
</div>