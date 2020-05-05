<script type="text/javascript">
    <?php include $this->utils->getIncludeView('base_pubutils_js.php');?>

    $(function(){
        _pubutils.showResponseResult=function(id){
            var url="<?php echo site_url('/system_management/response_result_detail');?>?id="+id;

            $.getJSON(url, function(data){
                if(data['success']){
                    _pubutils.renderResponseResult(data['response_result']);
                }else{
                    alert(data['message']);
                }

            }).fail(function(){
                alert('<?php echo lang("Sorry, load response result failed");?>');
            });

        };

        _pubutils.renderResponseResult=function(rlt){
            var content='';

            content+='<div><p><?php echo lang('Original Response'); ?></p></div>';

            content+='<div><pre><code>'+rlt['original_content']+'</code></pre></div>';

            content+='<a href="<?=site_url("/system_management/download_response_result")?>/'+rlt['resp_file_id']+'" target="_blank"> <?=lang('Download Raw Log')?> </a>';

            content+='</div>';

            BootstrapDialog.show({
                title: '<?php echo lang("Response Result");?>',
                size: BootstrapDialog.SIZE_WIDE,
                message: content,
                buttons: [{
                    label: '<?php echo lang("Close");?>',
                    action: function(dialogItself){
                        dialogItself.close();
                    }
                }]
            });
        };
    });
</script>
