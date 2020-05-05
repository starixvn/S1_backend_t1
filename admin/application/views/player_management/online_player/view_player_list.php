<style type="text/css">
	#nav_content {
		width: 100%; 
		height: auto; 
		float: left; 
		border: 1px solid lightgray; 
		border-top: none; 
		padding: 30px 0;
	}

</style>
<!--main-->
<div class="row">
	<div class="col-md-12 col-lg-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title" style="font-family:calibri;font-size:1.4em;">
					<i class="icon-user-check" id="hide_main_up"></i> <?= lang('player.sd07'); ?>

					<a href="#main" 
              id="hide_main" class="btn btn-default btn-sm pull-right">
						<i class="glyphicon glyphicon-chevron-up" id="hide_main_up"></i>
					</a>

					<i class="clearfix"></i>
				</h4>
			</div>

			<div class="panel panel-body" id="main_panel_body">
				<div class="col-md-12">
					<ul class="nav nav-tabs">
						<li class="tab active" id="website"><a href="#" onclick="changeOnlineList('0');" data-toggle="tab"><?= lang('player.ol01'); ?></a></li>
						
						<?php 
							foreach ($games as $key => $value) { 
								if($value['game'] == 'AG') break;
						?>
							<li class="tab" id="<?= $value['gameId']; ?>"><a href="#" onclick="changeOnlineList('<?= $value['gameId'] ?>');" data-toggle="tab"><?= $value['game'] ?></a></li>
						<?php } ?>
					</ul>

					<div id="nav_content">
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!--end of main-->

<script type="text/javascript">
	$(document).ready(function(){
		changeOnlineList('0');
	});
</script>
