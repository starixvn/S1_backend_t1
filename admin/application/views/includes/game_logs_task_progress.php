
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
		<h4 class="panel-title"><i class="fa fa-cogs"></i> &nbsp;<?php echo lang('Task Progress'); ?>
		<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		</h4>
	</div>

	<div id="main_panel" class="panel-collapse collapse in ">

	<div class="panel-body">
		<div class="row">
			<div class="col-md-12">
				<?php echo lang('Task ID').': '.$result_token; ?>
			</div>
		</div>
		<div class="progress" style="height: 30px;">
		  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
		  	<?php echo lang('In progress: "The game logs sync is currently in progress"'); ?>
		    <span class="sr-only"></span>
		  </div>
		</div>

		<div class="row queue_failed" style="display:none;">
			<div class="col-md-12">
				<p class="bg-danger"><?php echo lang('Failed');?></p>
			</div>
		</div>

		<div class="row queue_done" style="display:none;">
			<div class="col-md-12">
				<p class="bg-success"><?php echo lang('Done');?></p>
			</div>
		</div>

		<div class="row download_link" style="display:none;">
			<div class="col-md-12">
				<?php echo lang('Download Link is Ready'); ?>: <a href="" class="download_url"></a>
			</div>
		</div>

		<div class="row queue_result" style="display:none;">
			<div class="col-md-12">
				<pre id="queue_original_result"></pre>
			</div>
		</div>

	</div>

	<div class="panel-footer">
		<input type="button" class="btn btn-primary" value="<?php echo lang('Refresh'); ?>">
	</div>

	</div>

</div>

<script type="text/javascript">

var timeoutRefresh=<?php echo $this->utils->getConfig('timeout_refresh_queue');?>;
var refreshUrl="<?php echo site_url('system_management/check_queue/'.$result_token);?>";

function refreshResult(){

	utils.safelog('refresh result:'+timeoutRefresh);

	$.post(refreshUrl, function(data){
		utils.safelog(data);

		if(data && data['success']){
			if(data['status']==<?=Queue_result::STATUS_DONE?>){
				//link or message
				if(data['queue_result']['filename']){
					var url="<?php echo site_url('/reports'); ?>/"+data['queue_result']['filename'];
					$(".download_link .download_url").attr("href", url).text(url);
					$(".download_link").show();
				}else if(data['queue_result']){
					$("#queue_original_result").html(data['queue_original_result']);
					// $(".queue_result").show();
					// $(".queue_done").show();
					$(".progress").find(".progress-bar").addClass('progress-bar-success');
					$(".progress").find(".progress-bar").text('<?php echo lang('Done: "The game logs sync is done"'); ?>');
				}
			}else if(data['status']==<?=Queue_result::STATUS_ERROR?>){
				$(".progress").hide();
				//failed
				$("#queue_original_result").html(data['queue_original_result']);
				// $(".queue_result").show();
				// $(".queue_failed").show();
				$(".progress").find(".progress-bar").addClass('progress-bar-danger');
				$(".progress").find(".progress-bar").text('<?php echo lang('Failed'); ?>');
			}else{
				if(data['queue_result']){
					$("#queue_original_result").html(data['queue_original_result']);
					// $(".queue_result").show();
				}
				setTimeout( refreshResult , 1000 * timeoutRefresh);
			}
		}else{
			setTimeout( refreshResult , 1000 * timeoutRefresh);
		}

	}).fail(function(){
		setTimeout( refreshResult , 1000 * timeoutRefresh);
	});

}

// setTimeout( refreshResult , 1000 * timeoutRefresh);

$(function(){
	refreshResult();
});

</script>
