<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> Chat History Details </h4>
		<a href="#chat_details" onclick="closeDetails()" class="btn btn-primary btn-sm pull-right" id="chat_history"><span class="glyphicon glyphicon-remove"></span></a>
		<div class="clearfix"></div>
	</div>

	<div class="panel panel-body" id="chat_history_details_panel_body">
		<table class="table">
			<?php
				$cnt = 0;
				foreach ($chat_details as $row) {
					if($cnt == 0) {
			?>
						<tr class="warning">
							<td><b><?= $row['sender'] . ": "?></b> <?= $row['message'] . "<br/>"; ?></td>
						</tr>
			<?php
						$cnt = 1;
					} else {
			?>
						<tr class="info">
							<td><b><?= $row['sender'] . ": "?></b> <?= $row['message'] . "<br/>"; ?></td>
						</tr>
			<?php
						$cnt = 0;
					}
			}?>
		</table>
	</div>

	<div class="panel-footer">

	</div>
</div>