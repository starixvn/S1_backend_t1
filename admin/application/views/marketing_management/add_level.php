<div class="panel panel-primary">
	<div class="panel-heading">
		<span style="line-height:24px">Create Level</span>
		<a href="<?= BASEURL . 'marketing_management/level_list' ?>" class="btn btn-default btn-sm pull-right" title="Close"><i class="glyphicon glyphicon-remove"></i></a>
	</div>
	<div class="panel-body">
		<form action="<?= BASEURL . 'marketing_management/add_level'?>" method="post">
			
			<div class="form-group">
				<label class="control-label input-sm">Level Name:</label>
				<input type="text" class="form-control input-sm" name="levelName" placeholder="Required" value="<?php echo set_value('levelName') ?>"/>
			</div>

			<div class="form-group">
				<label class="control-label input-sm">Level Group:</label>
				<input type="text" class="form-control input-sm" name="levelGroup" placeholder="Optional" value="<?php echo set_value('levelGroup') ?>"/>
			</div>

			<button type="submit" class="btn btn-primary btn-block">Submit</button>
		</form>
	</div>
</div>