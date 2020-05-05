<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-filter"></i> Game Filter </h4>
		<button class="btn btn-info btn-sm pull-right" data-toggle="collapse" data-target="#panel-collapse-1"><i class="glyphicon glyphicon-chevron-down"></i></button>
		<div class="clearfix"></div>
	</div>
	<div id="panel-collapse-1" class="collapse">
		<div class="panel-body">
			...
		</div>
		<div class="panel-footer"></div>
	</div>
</div>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list"></i> Game List </h4>
		<button class="btn btn-info btn-sm pull-right" data-toggle="collapse" data-target="#panel-collapse-2"><i class="glyphicon glyphicon-chevron-up"></i></button>
		<div class="clearfix"></div>
	</div>
	<div id="panel-collapse-2" class="collapse in">
		<form action="<?= BASEURL . 'marketing_management/delete_selected_games' ?>" method="post">
			<div class="panel-body">
				<div class="row">
					<div class="col-xs-6" align="left">
						<button type="submit" class="btn btn-warning btn-sm" data-container="body" data-toggle="tooltip" title="Delete all selected Games"><i class="glyphicon glyphicon-remove"></i> Delete Selected</button>
						<a href="<?= BASEURL . 'marketing_management/delete_all_games' ?>" class="btn btn-danger btn-sm" data-container="body" data-toggle="tooltip" title="Delete all Games"><i class="glyphicon glyphicon-trash"></i> Delete All</a>
					</div>
					<div class="col-xs-6" align="right">
						<a href="<?= BASEURL . 'marketing_management/add_game' ?>" class="btn btn-primary btn-sm" data-container="body" data-toggle="tooltip" title="Add Game"><i class="glyphicon glyphicon-plus"></i> Add Game</a>
					</div>
				</div>
			</div>
			<table class="table table-condensed">
				<thead>
					<tr>
						<th><input type="checkbox" id="toggle-checkbox-1" data-toggle="checkbox" data-target=".checkbox-1"></th>
						<!--th>Game ID</th-->
						<th>Game Name</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($game_list as $game): ?>
						<tr>
							<td><input type="checkbox" name="selected[]" class="checkbox-1" data-untoggle="checkbox" data-target="#toggle-checkbox-1" value="<?= $game->gameId ?>"></td>

							<td nowrap="nowrap"><?= $game->game ?></td>
							<td nowrap="nowrap">
								<a href="<?= BASEURL . 'marketing_management/delete_game/' . $game->gameId ?>" class="btn btn-default btn-sm"  data-container="body" data-toggle="tooltip" title="Delete Game"><i class="glyphicon glyphicon-trash"></i></a>
							</td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		</form>
		<div class="panel-body" align="right"><?= $pagination ?></div>
		<div class="panel-footer"></div>
	</div>
</div>