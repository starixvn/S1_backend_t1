<div class="panel panel-primary">
	<div class="panel-heading">
		<span style="line-height:24px">Create Game</span>
		<a href="<?= BASEURL . 'marketing_management/game_list' ?>" class="btn btn-default btn-sm pull-right" title="Close"><i class="glyphicon glyphicon-remove"></i></a>
	</div>
	<div class="panel-body">
		<form action="<?= BASEURL . 'marketing_management/add_game'?>" method="post">

			<div class="form-group">
				<label class="control-label input-sm">Game Name:</label>
				<input type="text" class="form-control input-sm" name="game" placeholder="Required" value="<?php echo set_value('game') ?>"/>
			</div>

			<button type="submit" class="btn btn-primary btn-block">Submit</button>
		</form>
	</div>
</div>