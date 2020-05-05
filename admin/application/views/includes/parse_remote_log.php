
<style type="text/css">

.panel_main{
	margin: auto;
}


@media (min-width: 1200px) {
	.panel_main{
		width: 1100px;
	}
}
</style>

<div class="panel panel-primary panel_main">

	<div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-cogs"></i> &nbsp;<?php echo lang('Remote Log File'); ?> : <?=$logfile?>
		<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		</h4>
	</div>

	<div id="main_panel" class="panel-collapse collapse in ">

		<div class="panel-body">
			<div class="row">
				<div class="col-md-12">
					<?=lang('Task ID').': '.$token?>
					<br>
					<div id="download-link">
						<?=lang('Download Link') . ": "?><a href="<?=site_url('remote_logs/'.$logfile)?>" target="_blank"> <?=$logfile?></a>
					</div>
					
				</div>
			</div>

			<br>

			<?php if (isset($filesize_limit)): ?>
			<div id="filesize_limit">
				<p class="bg-warning"><?=lang('remote_log_file_limit_message')?></p>
			</div>	
			<?php else: ?>

			<div class="row queue_result" style="display: none;">
				<div class="col-md-12">
					<pre id="result_panel" style=" overflow-x: scroll; max-height: 800px"><code class="json" id="queue_original_result"><?=$file_content?></code></pre>
				</div>
			</div>
			<?php endif; ?>


		</div>

	</div>

</div>

<script type="text/javascript">

	$(function(){
		$('pre code').each(function(i, block) {
		    hljs.highlightBlock(block);
		});

		$(".queue_result").show();
	});


</script>
