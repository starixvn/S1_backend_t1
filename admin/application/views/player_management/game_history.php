<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> Game History </h4>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="player_panel_body">
				<div class="btn-group">
					<button class="btn btn-info">Sort By</button>
					<button class="btn btn-info dropdown-toggle" style="height: 34px;" data-toggle="dropdown">
						<span class="caret"></span>
					</button>

					<ul class="dropdown-menu">
						<li onclick="sortGameHistory('p.username')">Username</li>
						<li onclick="sortGameHistory('pgh.gameId')">Game</li>
					</ul>
				</div>

				<form class="navbar-search pull-right">
					<input type="text" class="search-query" placeholder="Search" name="search" id="search">
					<input type="button" class="btn" value="Go" onclick="searchGameHistory();">
				</form>

				<div id="gameHistoryList">
					<table class="table table-striped table-hover" style="margin: 30px 0 0 0;" id="myTable">
						<thead>
							<tr>
								<th>Player Name</th>
								<th>Game</th>
								<th>Game Begin</th>
								<th>Game End</th>
								<th>Total Win</th>
								<th>Total Loss</th>
								<th>Action</th>
							</tr>
						</thead>

						<tbody>
							<?php if(!empty($players)) { ?>
								<?php foreach($players as $players) { ?>
									<tr>
										<td><?= $players['username']?></td>
										<td><?= $players['game']?></td>
										<td><?= $players['gameBegin']?></td>
										<td><?= $players['gameEnd']?></td>
										<td><?= $players['totalWin']?></td>
										<td><?= $players['totalLoss']?></td>
										<td><a href="#" data-toggle="tooltip" class="details" onclick="viewGameDetails(<?= $players['gameHistoryId']?>);"><span class="glyphicon glyphicon-zoom-in"></span></a></td>
									</tr>
								<?php } ?>
							<?php } else { ?>
			                        <tr>
			                            <td colspan="7" style="text-align:center"><span class="help-block">No Records Found</span></td>
			                        </tr>
							<?php } ?>
						</tbody>
					</table>

					<br/><br/>

					<div class="col-md-12 col-offset-0">
					    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
				    </div>
				</div>
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>

	<div class="col-md-12" id="player_details" style="display: none;">

	</div>
</div>