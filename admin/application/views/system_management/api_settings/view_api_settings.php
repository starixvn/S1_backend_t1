<style type="text/css">
	div#nav_content {
		width: 100%; 
		height: auto; 
		float: left; 
		border: 1px solid lightgray;
		border-top: none; 
		padding: 30px 0;
	}


</style>

<div class="row">
	<div id="container" class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title pull-left"><i class="icon-settings"></i> <?= lang('system.word92'); ?></h3>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body" id="panel_body">
				<div class="col-md-12">
					<ul class="nav nav-tabs">
						<?php 
							$cnt = 1;

							foreach ($games as $key => $value) { 
								if($cnt == 1) {
						?>
									<li class="tab <?= !isset($type) ? 'active':($type == $value['gameId']) ? 'active':'' ?>" id="<?= $value['gameId']; ?>"><a href="#" onclick="changeAPISettings(<?= $value['gameId']; ?>);" data-toggle="tab"><?= $value['game']; ?></a></li>
						<?php 
								} else {
						?>
									<li class="tab <?= !isset($type) ? '':($type == $value['gameId']) ? 'active':'' ?>" id="<?= $value['gameId']; ?>"><a href="#" onclick="changeAPISettings(<?= $value['gameId']; ?>);" data-toggle="tab"><?= $value['game']; ?></a></li>
						<?php
								}

								$cnt++;
							} 
						?>
					</ul>

					<div id="nav_content">
						
					</div>
				</div>
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
		changeAPISettings('<?= !isset($type) ? 1:$type ?>');
    });
</script>