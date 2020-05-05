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
		<?php if($this->utils->getConfig('use_new_sbe_color')){?>
			<span class="pull-right">
				<a data-toggle="collapse" href="#main_panel" class="btn btn-info btn-xs" aria-expanded="true"></a>
			</span>
		<?php }else{?>
			<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		<?php }?>
	</h4>
</div>
<div id="main_panel" class="panel-collapse collapse in ">
	<div class="panel-body">
		<div class="row">
			<div class="col-md-12">
				<?php echo lang('Export Excel Task ID').': '.$result_token; ?>
			</div>
		</div>
		<!-- <div id="loader">processing...</div> -->
		<div style="height:20px">
			<span class="text-danger small" style="font-weight:bold;" id="loader2"  ><i><?php echo lang('Processing please wait. It might take time')?>...</i></span>
		</div>
		<div id="progress-details" class="alert alert-info" >
			<?php echo lang('Written'); ?>: <strong id="written-details">0</strong>   <?php echo lang('Total Rows'); ?>: <strong id="total-details">0</strong>  <?php echo lang('Status'); ?>: <strong  id="processStatus" ></strong>
			<?php if($is_remote_export):?>
			<input  type="button" style="margin-top: -6px;display:none" queue_token="<?php echo  $result_token; ?>" id= "stop-process" class="btn btn-xs btn-danger pull-right" value="<?php echo lang('STOP'); ?>">
			<?php endif; ?>
			</div>
			<div class="progress" id="progress" >
				<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="05" aria-valuemin="1" aria-valuemax="100" >
					<span class="progresslabel"></span>
				</div>
			</div>
			<div class="row download_link" style="display:none;">
				<div class="col-md-12">
					<?php echo lang('Download Link is Ready'); ?>: <a href="" class="download_url"></a>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<input type="button" id='btn_refresh_result' class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" value="<?php echo lang('Refresh Result'); ?>">
		</div>
	</div>
</div>


<script type="text/javascript">

var timeoutRefresh=<?php echo $this->utils->getConfig('timeout_refresh_queue');?>;
var refreshUrl="<?php echo site_url('export_data/check_queue/'.$result_token);?>";

function showOriginalResult(data){
$('#loader').hide();
$('#progress-details').show();
var initial = '1';

	if(data['queue_result']){
		var queue_result=data['queue_result'];
		if( queue_result['filename']){
			$('#loader2').hide();
			$('#stop-process').hide();
			$('.progress-bar').css('width', '100%').attr('aria-valuenow', 100);
			$('.progresslabel').html(queue_result.progress+'%');
			$('#written-details').html(queue_result.written);
			$('#total-details').html(queue_result.total_count);
			if(queue_result.progress === undefined){
				$('#progress-details').hide();
			}
			var url="/reports/"+queue_result['filename'];
			$(".download_link .download_url").attr("href", url).text(url);
			$(".download_link").show();
			$(".progress").hide();
		}else{
           if(queue_result.progress === undefined){
           	   $('#loader2').show();
            	queue_result.progress = '1';
            	$('.progress-bar').css('width', parseInt(queue_result.progress)+'%').attr('aria-valuenow', parseInt(queue_result.progress));
            	$('.progresslabel').html("");
           }else{
               $('#loader2').hide();
               $('.progress-bar').css('width', parseInt(queue_result.progress)+'%').attr('aria-valuenow', parseInt(queue_result.progress));
               $('.progresslabel').html(queue_result.progress+'%');
			   $('#written-details').html(queue_result.written);
			   $('#total-details').html(queue_result.total_count);
           }
		}
	}
}

function refreshResult(){

	utils.safelog('refresh result:'+timeoutRefresh);


	$.post(refreshUrl, function(data){
		utils.safelog(data);
		showOriginalResult(data);
		var queue_result=data['queue_result']
		if(data.process_status == <?php echo Queue_result::STATUS_NEW_JOB  ?>){
			if(queue_result.processMsg !== undefined){
				$('#processStatus').html('<span class="text-danger">'+queue_result.processMsg+'</span>');
			}
			$(".progress").show();
			$('#stop-process').show();
		}else if(data.process_status == <?php echo Queue_result::STATUS_STOPPED ?>){
			$('#btn_run_task_again').show();
			$('#processStatus').html('<span class="text-warning"><?php echo lang('Stopped'); ?></span>');
			$(".progress").hide();
			$('#stop-process').show().val("<?php echo lang('Stopped'); ?>!").removeClass("btn-danger").addClass("btn-warning").attr("disabled", "disabled");
		}else{
            $('#processStatus').html('<span class="text-success"><?php echo lang('Done'); ?></span>');
		}
	});

	setTimeout( refreshResult , 1000 * timeoutRefresh);

}
var stopQueueUrl ="<?php echo site_url('export_data/stop_queue/'.$result_token);?>";

$(function(){
	$('#processStatus').html('<span class="text-danger" ><?=lang('Processing')?>...</span>');
	refreshResult();

	<?php if($is_remote_export):?>
	$('#stop-process').click(function(){
		var token = $(this).attr('queue_token');
		if(confirm("<?=lang('Are you sure you want to stop this export?')?> \n" + token)){
			$('#loader2').show();

			$(this).attr("disabled", "disabled");
			$.post(stopQueueUrl, function(data){
				utils.safelog(data);
				refreshResult();
			});
		}

	});
    <?php endif; ?>

    $('#btn_refresh_result').click(function(){
		refreshResult();
	});
	$('#btn_run_task_again').click(function(){
		if(confirm("<?=lang('Are you sure run same task again?')?>")){
			window.location.href="<?=site_url('/system_management/run_task_again/'.$result_token)?>";
		}
	});
});
</script>
