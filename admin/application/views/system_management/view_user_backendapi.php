<div id="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="fa fa-key"></i> <?=lang('View Backend API Keys');?> </h4>
        </div>
        <div class="panel panel-body">
            <div class="row">
                <div class="col-md-4 text-right">
                    <p><?=lang('Admin User ID')?> (admin_user_id) :</p>
                </div>
                <div class="col-md-8">
                    <?=$targetUserId?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 text-right">
                    <p><?=lang('Secure Key')?> (secure_key) :</p>
                </div>
                <div class="col-md-8">
                    <?=empty($keys['secure_key']) ? lang('N/A') : $keys['secure_key']?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 text-right">
                    <p><?=lang('Sign Key')?> (sign_key) :</p>
                </div>
                <div class="col-md-8">
                    <?=empty($keys['sign_key']) ? lang('N/A') : $keys['sign_key']?>
                </div>
            </div>
            <?php if($this->utils->isEnabledFeature('enabled_backendapi') && $this->permissions->checkPermissions('regenerate_backendapi_keys')){ ?>
            <div class="row">
                <div class="col-md-3">
                    <p>
                    <a id="regenerate_keys" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>"><?=lang('Regenerate Keys')?></a>
                    </p>
                </div>
            </div>
            <?php }?>
        </div>
    </div>
</div>

<script type="text/javascript">
$("#regenerate_keys").click(function($e){
    $e.preventDefault();
    if(confirm('<?=lang("Do you want to regenerate keys?")?>')){
        $.ajax(
            '<?=site_url("/system_management/regenerate_user_backendapi/".$targetUserId)?>',
            {
                cache: false,
                dataType: 'json',
                success: function(data){
                    if(data){
                        //success
                        if(data['success']){
                            alert('<?=lang("Regenerate keys successfully")?>');
                            window.location.reload();
                        }else{
                            alert(data['error']);
                        }
                    }else{
                        alert('<?=lang("Sorry, internal error")?>');
                        window.location.reload();
                    }
                },
                error: function(){
                    alert('<?=lang("Sorry, internal error")?>');
                    window.location.reload();
                }
            }
        );
    }
});
</script>
