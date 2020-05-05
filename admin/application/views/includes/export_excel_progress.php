<?php
$queue_token;
?>

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
		<h4 class="panel-title"><i class="fa fa-cogs"></i> &nbsp;<?php echo lang('Export Excel Progress'); ?>
		<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		</h4>
	</div>

	<div id="main_panel" class="panel-collapse collapse in ">

	<div class="panel-body">
		<div class="row">
			<div class="col-md-12">
				<?php echo lang('Export Excel Task ID').': '.$result_token; ?>
			</div>
		</div>
		<div class="progress">
		  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
		    <span class="sr-only"></span>
		  </div>
		</div>

		<div class="row download_link" style="display:none;">
			<div class="col-md-12">
				<?php echo lang('Download Link is Ready'); ?>: <a href="" class="download_url"></a>
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
var refreshUrl="<?php echo site_url('export_data/check_queue/'.$result_token);?>";

function refreshResult(){

	utils.safelog('refresh result:'+timeoutRefresh);

	$.post(refreshUrl, function(data){
		utils.safelog(data);

		if(data && data['success']){
			if(data['done']){
				var url="<?php echo site_url('/reports'); ?>/"+data['queue_result']['filename'];
				// alert(url);
				// window.location.href=url;
				$(".progress").hide();
				$(".download_link .download_url").attr("href", url).text(url);
				$(".download_link").show();
			}
		}

	});

	setTimeout( refreshResult , 1000 * timeoutRefresh);

}

// setTimeout( refreshResult , 1000 * timeoutRefresh);

$(function(){
	refreshResult();
});

</script>
