<style type="text/css">
.account_row{
    margin: 4px;
}
.readonly_account_header{
    padding: 4px 4px;
}
</style>

<form autocomplete="off">
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading readonly_account_header">
                <h3 class="panel-title">
                    <span class="fa-stack">
                      <i class="fa fa-pencil fa-stack-1x"></i>
                      <i class="fa fa-ban fa-stack-2x fa-rotate-90"></i>
                    </span> <?=lang('Readonly Account');?>
                </h3>
            </div>
            <div class="panel-body">
                <div class="row account_row">
                    <div class="col-md-1 text-right">
                        <label class="control-label"><?=lang('Enabled Account')?></label>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label"><?=lang('Readonly Username');?></label>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label"><?=lang('Password');?></label>
                    </div>
                </div>
                <?php
                    for($i=0;$i<$maxAgencyReadonlyAccount;$i++){
                        $readonlyAccount=$emptyReadonlyAccount;
                        $exists=isset($readonlyAccountList[$i]);
                        if($exists){
                            $readonlyAccount=$readonlyAccountList[$i];
                        }
                        $existsUsername=!empty($readonlyAccount['username']);
                ?>
                <div class="row account_row">
                    <div class="col-md-1 text-right">
                        <input type="checkbox" id="enable_account_<?=$i?>" <?=$readonlyAccount['enabled'] ? "checked" : "" ?>>
                    </div>
                    <div class="col-md-4">
                        <input type="text" autocomplete="off" id="readonly_username_<?=$i?>" class="form-control input-sm"
                        placeholder='<?=lang('Enter Username');?>'
                        value="<?=$readonlyAccount['username']; ?>"/>
                    </div>
                    <div class="col-md-4">
                        <?php if($existsUsername){ //only allow reset password?>
                        <a href="javascript:void(0)" class="btn btn-info btn-sm btn_reset_password" data-indexofaccount='<?=$i?>'><?=lang('Reset Password');?></a>
                        <?php }else{?>
                        <input type="password" autocomplete="new-password" id="password_<?=$i?>" class="form-control input-sm"
                        placeholder='<?=lang('Password');?>' />
                        <?php }?>
                    </div>
                </div>
                <?php }?>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-md-6 col-lg-6" style="padding: 10px;">
                        <a href="javascript:void(0)" id="btn_save" class="btn btn-primary btn-sm"><?=lang('Save');?></a>
                        <a href="<?=$returnUrl?>" class="btn btn-danger btn-sm"><?=lang('Cancel');?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<div class="modal fade in" id="new_password_dialog" tabindex="-1" role="dialog" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?=lang('NOTICE')?></h4>
            </div>
            <div class="modal-body">
                <p id="reset_message"></p>
                <p><?=lang('Username')?>: <span id="username_content" class="bg-info"></span></p>
                <p><?=lang('Please copy new password')?>: <span id="new_password_content" class="bg-primary"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('Close')?></button>
            </div>
        </div>
    </div>
</div> <!--  modal for level name setting }}}4 -->

<script type="text/javascript">
    function makeAccountParams(){
        var result={};
        var maxAgencyReadonlyAccount=<?=$maxAgencyReadonlyAccount?>;
        for(var i=0;i<maxAgencyReadonlyAccount;i++){

            var password=$("#password_"+i).val();
            if(password===undefined){
                password=null;
            }

            result[i]={
                "enabled": $("#enable_account_"+i).is(":checked"),
                "username": $("#readonly_username_"+i).val(),
                "password": password
            };
        }
        // utils.safelog(result);
        return JSON.stringify(result);
    }
    var saveAccountsUrl="<?=$saveAccountsUrl?>";
    var resetPasswordUrl="<?=$resetPasswordUrl?>";
    var saveFailedMessage="<?=lang('Save failed')?>";
    var resetFailedMessage="<?=lang('Reset failed')?>";
    $(function() {
        $("#btn_save").click(function(e){
            e.preventDefault();
            var btnSaveObj=$(this);
            btnSaveObj.attr('disabled', 'disabled').addClass('disabled');
            //save all
            var jsonParams=makeAccountParams();

            // console.log(jsonParams);

            $.ajax({
                'url': saveAccountsUrl,
                'cache': false,
                'dataType': 'json',
                'method': 'POST',
                'data': jsonParams,
                'xhrFields': {
                    'withCredentials': true
                },
                'success': function(data){
                    // console.log(data);
                    if(data && data['success']){
                        alert(data['message']);
                        //refresh page
                        window.location.reload(true);
                    }else{
                        alert(data['message']);
                        btnSaveObj.removeAttr('disabled').removeClass('disabled');
                    }
                },
                'error': function(xhr, textStatus){
                    alert(saveFailedMessage);
                    btnSaveObj.removeAttr('disabled').removeClass('disabled');
                }
            });

        });

        $(".btn_reset_password").click(function(e){
            e.preventDefault();
            var btnResetObj=$(this);
            btnResetObj.attr('disabled', 'disabled').addClass('disabled');

            //call reset password
            $.ajax({
                'url': resetPasswordUrl+'/'+$(this).data('indexofaccount'),
                'cache': false,
                'dataType': 'json',
                'method': 'POST',
                'xhrFields': {
                    'withCredentials': true
                },
                'success': function(data){
                    // console.log(data);
                    if(data && data['success']){
                        // alert(data['message']);
                        $('#reset_message').html(data['message']);
                        $('#new_password_content').html(data['new_password']);
                        $('#username_content').html(data['username']);
                        $('#new_password_dialog').modal('show');
                        $('#new_password_dialog').on('hidden.bs.modal', function(){
                            window.location.reload(true);
                        });
                        //refresh page
                        // window.location.reload(true);
                    }else{
                        alert(data['message']);
                        btnResetObj.removeAttr('disabled').removeClass('disabled');
                    }
                },
                'error': function(xhr, textStatus){
                    alert(resetFailedMessage);
                    btnResetObj.removeAttr('disabled').removeClass('disabled');
                }
            });
        });
    });
</script>
