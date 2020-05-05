<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> <?= lang('player.mp13'); ?> </h4>
		<a href="<?= BASEURL . 'player_management/accountProcess'?>" class="btn btn-default btn-sm pull-right" id="account_process"><span class="glyphicon glyphicon-remove"></span></a>
		<div class="clearfix"></div>
	</div>

	<div class="panel panel-body" id="player_panel_body">
		<form method="POST" action="<?= BASEURL . 'player_management/verifyEditAccountProcessDetails/' . $player['playerId'] . "/" . $player['type'] ?> ">
			<div class="row">
				<div class="col-md-2 col-md-offset-1">
					<label for="username"><?= lang('player.01'); ?>: </label>
				</div>

				<div class="col-md-7 col-md-offset-1">
					<input type="text" name="username" id="username" class="form-control" value="<?= $player['username']?>">
				</div>
			</div>

			<br/>

			<div class="row">
				<div class="col-md-2 col-md-offset-1">
					<label for="password"><?= lang('player.56'); ?>: </label>
				</div>

				<div class="col-md-7 col-md-offset-1">
					<input type="text" name="password" id="password" class="form-control" value="<?= $player['batchPassword']?>">
				</div>
			</div>

			<br/>

			<div class="row">
				<div class="col-md-9 col-md-offset-1">
					<label style="display:none; color:red;" id="error"></label>
				</div>
			</div>

			<br/>

			<div class="row">
				<div class="col-md-7 col-md-offset-5">
					<input type="button" class="btn btn-primary" value="Save" onclick="verifyEditAccountProcessDetails(<?= $player['playerId']?>, '<?= $player['type']?>');">
				</div>
			</div>
		</form>
	</div>

	<div class="panel-footer">

	</div>
</div>