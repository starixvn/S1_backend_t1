<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt"><i class="icon-list"></i> <?=lang('cms.generateSites');?>
				</h4>
			</div>

			<div class="panel-body" id="panel_body">
				<?php
$command = "../../admin/shell/generate_sites.sh";
$output = shell_exec($command);
echo "<pre>$output</pre>";
?>
			</div>

			<div class="panel-footer"></div>
		</div>
	</div>
</div>