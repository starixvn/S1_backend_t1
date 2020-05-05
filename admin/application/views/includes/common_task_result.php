
	<div class="row download_link" style="display:none;">
		<div class="col-md-12">
			<?php echo lang('Download Link is Ready'); ?>: <a href="" class="download_url"></a>
		</div>
	</div>

	<div class="row queue_result" style="display:none;">
		<div class="col-md-12">
			<pre id="result_panel"><code class="json" id="queue_original_result"></code></pre>
		</div>
	</div>

<script type="text/javascript">
function showOriginalResult(data){

	if(data['queue_result']){
		var queue_result=data['queue_result'];
		if( queue_result['filename']){
		    var url="/reports/"+queue_result['filename'];
			$(".download_link .download_url").attr("href", url).text(url);
			$(".download_link").show();
		}else{
			$("#queue_original_result").html(data['queue_original_result']);
			$(".queue_result").show();
			$('pre code').each(function(i, block) {
			    hljs.highlightBlock(block);
			});
		}
	}

}
</script>
