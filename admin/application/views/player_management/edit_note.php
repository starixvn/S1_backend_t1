<style type="text/css">
  .nav-tabs li a {font-size:13px;}
</style>
<div class="panel panel-primary">
	<div class="panel panel-body" id="details_panel_body">
		<ul class="nav nav-tabs">
			<li>
				<a href="#" onclick="viewPlayer(<?= $playerId?>, 'overview');">Overview</a>
			</li>

			<li>
				<a href="#" onclick="viewPlayer(<?= $playerId?>, 'playerDetail');">Player Detail</a>
			</li>

			<li>
				<a href="#" onclick="viewPlayer(<?= $playerId?>, 'accountDetail');">Account Detail</a>
			</li>

			<li>
				<a href="#" onclick="viewPlayer(<?= $playerId?>, 'systemDetail');">System Detail</a>
			</li>

			<li class="active">
				<a href="#" onclick="viewPlayer(<?= $playerId?>, 'notes');">Notes</a>
			</li>
		</ul>

		<div class="content">
			<br/>
			<h4>Edit Notes</h4>
			<form method="post" action="<?= BASEURL . 'player_management/postEditNote/' . $playerId . '/' . $note['noteId']?>" role="form">
				<textarea class="form-control" name="note" style="height: 150px;"><?= $note['notes'] ?></textarea> <br/>
					<?php echo form_error('note', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
				<input type="submit" class="btn btn-default btn-sm" value="Submit">
				<a href="#" onclick="viewPlayer(<?= $playerId?>, 'notes');" class="btn btn-default btn-sm">Cancel</a>
			</form>
		</div>
	</div>

	<div class="panel-footer">

	</div>
</div>