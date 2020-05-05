<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> Chat History </h4>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="chat_history_panel_body">
				<div class="btn-group">
					<button class="btn btn-info">Sort By</button>
					<button class="btn btn-info dropdown-toggle" style="height: 34px;" data-toggle="dropdown">
						<span class="caret"></span>
					</button>

					<ul class="dropdown-menu">
						<li onclick="sortChatHistoryList('sender')">sender</li>
						<li onclick="sortChatHistoryList('recepient')">receiver</li>
					</ul>
				</div>

				<form class="navbar-search pull-right">
					<input type="text" class="search-query" placeholder="Search" name="search" id="search">
					<input type="button" class="btn" value="Go" onclick="searchChatHistoryList();">
				</form>

				<div id="chatHistoryList">
					<table class="table table-striped table-hover" style="margin: 30px 0 0 0;" id="myTable">
						<thead>
							<tr>
								<th>Sender</th>
								<th>Receiver</th>
								<th>Action</th>
							</tr>
						</thead>

						<tbody>
							<?php if(!empty($chat_history)) { ?>
								<?php foreach($chat_history as $chat_history) { ?>
									<tr>
										<td><?= $chat_history['sender']?></td>
										<td><?= $chat_history['recepient']?></td>
										<td>
											<a href="#" data-toggle="tooltip" class="details" onclick="viewChatHistoryDetails('<?= $chat_history['session']?>');"><span class="glyphicon glyphicon-zoom-in"></span></a>

											<?php if($this->permissions->checkPermissions('delete_chat_history')) { ?>
												<a href="<?= BASEURL . 'player_management/deleteChatHistory/' . $chat_history['session']?>" data-toggle="tooltip" class="delete"><span class="glyphicon glyphicon-trash"></span></a>
											<?php } ?>
										</td>
									</tr>
								<?php } ?>
							<?php } else { ?>
			                        <tr>
			                            <td colspan="3" style="text-align:center"><span class="help-block">No Records Found</span></td>
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

	<div class="col-md-7" id="player_details" style="display:none;">

	</div>
</div>