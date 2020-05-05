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

			<li>
				<a href="#systemDetail" onclick="viewPlayer(<?= $playerId?>, 'systemDetail');">System Detail</a>
			</li>

			<li class="active">
				<a href="#notes" onclick="viewPlayer(<?= $playerId?>, 'notes');">Notes</a>
			</li>
		</ul>

		<div class="content">
			<br/>

			<?php if($this->permissions->checkPermissions('add_notes')) { ?>
				<a href="#addNote" onclick="viewPlayer(<?= $playerId?>, 'addNotes');" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-plus-sign"></span> Add Notes</a>
			<?php } ?>

			<br/>
			<?php $ctr = 1; ?>
			<?php if(!empty($player)) {
				foreach ($player as $row) { ?>
				<br/>
				<div class="panel panel-primary">
					<div class="panel-heading">
						<div class="pull-left">
							<h5 class="panel-title">Note <?= $ctr ?></h5>
						</div>
						<div class="pull-right">
							<?php if($this->authentication->getUserId() == $row['userId']) {?>
								<?php if($this->permissions->checkPermissions('edit_notes')) { ?>
									<a href="#editNote" onclick="viewPlayer(<?= $playerId?>, 'editNote/<?= $row['noteId'] ?>');" data-toggle="tooltip" class="edit_note"><span class="glyphicon glyphicon-pencil"></span></a>
								<?php } ?>

								<?php if($this->permissions->checkPermissions('delete_notes')) { ?>
									<a href="<?= BASEURL . 'player_management/deleteNote/' . $playerId . '/' . $row['noteId'] ?>" data-toggle="tooltip" class="delete_note"><span class="glyphicon glyphicon-trash"></span></a>
								<?php } ?>
							<?php } ?>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<span class="help-block"><?= $row['notes'] ?></span>
						<div class="pull-right">
							<span class="help-block"><b>From :</b> <?= $row['username'] ?></span>
							<span class="help-block"><b>Date :</b> <?= $row['updatedOn'] ?></span>
						</div>
					</div>
				</div>
			<?php $ctr++; }
				} ?>
		</div>
	</div>

	<div class="panel-footer">

	</div>
</div>