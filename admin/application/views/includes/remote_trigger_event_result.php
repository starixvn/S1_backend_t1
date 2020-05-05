
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

    var final_result=data['final_result'];

    $("#queue_original_result").html(data['queue_original_result']);
    $(".queue_result").show();
    $('pre code').each(function(i, block) {
        hljs.highlightBlock(block);
    });

    if(final_result['success'] && final_result['download_filelink']){
        var url=final_result['download_filelink'];
        $(".download_link .download_url").attr("href", url).text(url);
        $(".download_link").show();
    }

}
</script>
