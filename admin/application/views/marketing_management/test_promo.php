<style type="text/css">
 .ace_editor{
    min-height: 200px;
 }
</style>

<div class="panel panel-primary panel_main">

	<div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-cogs"></i> &nbsp;<?php echo $title; ?>
		<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		</h4>
	</div>

	<div id="main_panel" class="panel-collapse collapse in ">

	<div class="panel-body">

	<div class="panel panel-primary panel_main">
		<div class="panel-heading">
		<h5 class="panel-title"><?php echo lang('Mock Data'); ?></h5>
		</div>
		<div class="panel-body">

			<form class="form-horizontal" role="form" method="post" onsubmit="return validForm();"
				action="<?php echo site_url('/marketing_management/dryrun_promo/'.$cmsPromoId);?>">
			<div class="row">
				<div class="col-md-4">
				<label class="control-label"><?=lang('Username')?></label> :
				<input type="text" class="form-control input-sm" name="player_username" value="<?=$player_username?>">
				</div>
			</div>
			<div class="row">
				<?php foreach ($mock as $key => $value) {?>
				<div class="col-md-3">
				<label class="control-label" for="<?=$key?>"><?=lang($key)?></label>
				<input type="text" name="<?=$key?>" id="<?=$key?>" class="form-control input-sm" value="<?=$value?>">
				</div>
				<?php }?>
			</div>
			<div class="row">
				<div class="col-md-12">
				<label class="control-label"><?=lang('Mock for class')?></label>
                <input type="hidden" name="promo_rule_class_mock" id="promo_rule_class_mock">
				<textarea id="promo_rule_class_mock_editor" class="form-control" rows="10"><?=$promo_rule_class_mock?></textarea>
				</div>
			</div>
			<p style="padding: 10px;">
			<button class="btn btn-primary"><?=lang('Dry Run')?></button>
			</p>
			</form>
		</div>
	</div>

	<p><?=lang('Username')?>: <?=$player_username?></p>

	<?=lang('Runtime Mock')?>
	<pre><?php echo json_encode($runtime_mock, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);?></pre>

	<?=lang('Result')?>
	<pre><?php echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);?></pre>

	<?=lang('Debug Log')?>
	<pre><?php echo $debug_log;?></pre>

	</div>

	</div>

</div>

<script type="text/javascript">
	var classMockEditor = null;
	$(document).ready(function(){
		$('#view_system_settings').addClass('active');
	    classMockEditor = ace.edit("promo_rule_class_mock_editor");
	    classMockEditor.setTheme("ace/theme/tomorrow");
	    classMockEditor.getSession().setMode("ace/mode/javascript");
	});
	// resizeSidebar();

	function validForm(){
		// console.log(classMockEditor.getValue());
    	$("#promo_rule_class_mock").val(classMockEditor.getValue());
    	return true;
	}

</script>