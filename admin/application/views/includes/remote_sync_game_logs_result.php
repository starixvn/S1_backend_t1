<style type="text/css">
	.queue_result_params{
		margin: 2px;
		padding: 2px;
	}
	.queue_result{
		margin: 2px;
		padding: 2px;
	}
</style>

	<div class="row queue_result_params">
		<div class="col-md-12 text-info">
			<h3><?=lang('Request')?></h3>
		</div>
	</div>
	<div class="row queue_result_params">
		<div class="col-md-4">
			<?=lang('Start Date')?>
		</div>
		<div class="col-md-8">
			<div id="param_fromDateTimeStr"></div>
		</div>
	</div>
	<div class="row queue_result_params">
		<div class="col-md-4">
			<?=lang('End Date')?>
		</div>
		<div class="col-md-8">
			<div id="param_toDateTimeStr"></div>
		</div>
	</div>
	<div class="row queue_result_params">
		<div class="col-md-4">
			<?=lang('Game Platform')?>
		</div>
		<div class="col-md-8">
			<div id="param_game_api_id"></div>
		</div>
	</div>
	<div class="row queue_result_params">
		<div class="col-md-4">
			<?=lang('Minutes Per Time')?>
		</div>
		<div class="col-md-8">
			<div id="param_timelimit"></div>
		</div>
	</div>
	<div class="row queue_result_params">
		<div class="col-md-4">
			<?=lang('Merge Only')?>
		</div>
		<div class="col-md-8">
			<div id="param_merge_only"></div>
		</div>
	</div>
	<div class="row queue_result_params">
		<div class="col-md-4">
			<?=lang('Original Only')?>
		</div>
		<div class="col-md-8">
			<div id="param_only_original"></div>
		</div>
	</div>
	<div class="row queue_result_params">
		<div class="col-md-4">
			<?=lang('Player')?>
		</div>
		<div class="col-md-8">
			<div id="param_playerName"></div>
		</div>
	</div>
	<div class="row queue_result_params">
		<div class="col-md-4">
			<?=lang('Dry Run')?>
		</div>
		<div class="col-md-8">
			<div id="param_dry_run"></div>
		</div>
	</div>
	<div class="row queue_result">
		<div class="col-md-12 text-info">
			<h3><?=lang('Result')?></h3>
		</div>
	</div>
	<div class="row queue_result">
		<div class="col-md-12">
			<pre><code class="json" id="render_result"></code></pre>
		</div>
	</div>

<!-- 	<div class="row queue_result_header">
		<div class="col-md-12 text-info">
			<h3><?=lang('Logs')?></h3>
		</div>
	</div>
	<div class="row queue_result" style="display:none;">
		<div class="col-md-12">
			<pre id="result_panel"><code class="json" id="queue_original_result"></code></pre>
		</div>
	</div> -->

<script type="text/javascript">
function showOriginalResult(data){

	if(data['full_params']){
		$("#param_fromDateTimeStr").html(data['full_params']['fromDateTimeStr']);
		$("#param_toDateTimeStr").html(data['full_params']['toDateTimeStr']);
		$("#param_game_api_id").html(data['full_params']['game_api_id']);
		$("#param_timelimit").html(data['full_params']['timelimit']);
		$("#param_merge_only").html(data['full_params']['merge_only'] ? 'true' : 'false');
		$("#param_only_original").html(data['full_params']['only_original'] ? 'true' : 'false');
		$("#param_playerName").html(data['full_params']['playerName']);
		$("#param_dry_run").html(data['full_params']['dry_run'] ? 'true' : 'false');
	}
	if(data['queue_result']){
		var result=data['queue_result'];
		$("#render_result").html(JSON.stringify(result, null, 4));
	}

	// $("#queue_original_result").html(data['queue_original_result']);
	// $(".queue_result").show();
	$('pre code').each(function(i, block) {
	    hljs.highlightBlock(block);
	});

}
</script>
