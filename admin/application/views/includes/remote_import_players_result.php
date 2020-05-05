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
			<?=lang('Player Info')?>
		</div>
		<div class="col-md-8">
			<div id="param_import_player_csv_file"></div>
		</div>
	</div>
	<div class="row queue_result_params">
		<div class="col-md-4">
			<?=lang('Affiliate Info')?>
		</div>
		<div class="col-md-8">
			<div id="param_import_aff_csv_file"></div>
		</div>
	</div>
	<div class="row queue_result_params">
		<div class="col-md-4">
			<?=lang('Affiliate Contact Info')?>
		</div>
		<div class="col-md-8">
			<div id="param_import_aff_contact_csv_file"></div>
		</div>
	</div>
	<div class="row queue_result_params">
		<div class="col-md-4">
			<?=lang('Player Contact Info')?>
		</div>
		<div class="col-md-8">
			<div id="param_import_player_contact_csv_file"></div>
		</div>
	</div>
	<div class="row queue_result_params">
		<div class="col-md-4">
			<?=lang('Player Bank Info')?>
		</div>
		<div class="col-md-8">
			<div id="param_import_player_bank_csv_file"></div>
		</div>
	</div>
	<div class="row queue_result_params">
		<div class="col-md-4">
			<?=lang('Agency Info')?>
		</div>
		<div class="col-md-8">
			<div id="param_import_agency_csv_file"></div>
		</div>
	</div>
	<div class="row queue_result_params">
		<div class="col-md-4">
			<?=lang('Agency Contact Info')?>
		</div>
		<div class="col-md-8">
			<div id="param_import_agency_contact_csv_file"></div>
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

<script type="text/javascript">
function showOriginalResult(data){

	if(data['full_params']){
		$("#param_import_player_csv_file").html(data['full_params']['files']['import_player_csv_file']);
		$("#param_import_aff_csv_file").html(data['full_params']['files']['import_aff_csv_file']);
		$("#param_import_aff_contact_csv_file").html(data['full_params']['files']['import_aff_contact_csv_file']);
		$("#param_import_player_contact_csv_file").html(data['full_params']['files']['import_player_contact_csv_file']);
		$("#param_import_player_bank_csv_file").html(data['full_params']['files']['import_player_bank_csv_file']);
		$("#param_import_agency_csv_file").html(data['full_params']['files']['import_agency_csv_file']);
		$("#param_import_agency_contact_csv_file").html(data['full_params']['files']['import_agency_contact_csv_file']);
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
