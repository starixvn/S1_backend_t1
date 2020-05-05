<div class="panel panel-primary panel_main">
    <div class="panel-heading">
        <h4 class="panel-title"><?php echo lang('2FA Settings'); ?>
        <a href="#otp_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
        </h4>
    </div>

    <div id="otp_panel" class="panel-collapse collapse in ">

    <div class="panel-body">
        <div>
            <?php if(empty($user['otp_secret'])){ ?>
            <?=lang('2FA is disabled now')?>
            <a href="javascript:void(0)" class="btn btn-sm btn-primary btn_enable_otp"><?=lang('Enable 2FA')?></a>
            <div class="otp_init" style="display: none">
                <div class="row">
                    <div class="col-md-6">
                        <div class="image_qrcode"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?=lang('2FA Secret Text')?>: <span class="secret"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?=lang('Input Code To Validate')?>: <input type="text" class="init_code">
                        <a href="javascript:void()" class="btn btn-sm btn-primary btn_validate_code"><?=lang('Validate Code and Enable 2FA')?></a>
                    </div>
                </div>
            </div>
            <?php }else{?>
            <div><?=lang('2FA is enabled now')?></div>
            <?=lang('Input Code To Validate')?>: <input type="text" class="otp_code">
            <a href="javascript:void(0)" class="btn btn-sm btn-primary btn_disable_otp"><?=lang('Disable 2FA')?></a>
            <?php }?>
        </div>
    </div>

    </div>

</div>

<script src="<?=$this->utils->thirdpartyUrl('jquery-qrcode/jquery.qrcode.min.js')?>"></script>
<script type="text/javascript">
$(document).ready(function(){
    $(".btn_validate_code").click(function(){
        if(confirm("<?=lang('Do you want to validate and enable 2FA')?>?")){
            var url="<?=site_url('/user_management/validate_and_enable_otp')?>";
            var secret=$('.otp_init .secret').data('initsecret');
            var code=$(".init_code").val();
            $.ajax(url,{
                cache: false,
                dataType: 'json',
                method: 'POST',
                data: {secret: secret, code: code},
                success: function(data){
                    // console.log(data);
                    alert(data['message']);
                    if(data['success']){
                        window.location.reload();
                    }
                },
                error: function(){
                    alert("<?=lang('Init 2FA failed')?>");
                }
            });
        }
        return false;
    });

    $(".btn_enable_otp").click(function(){
        if(confirm("<?=lang('Do you want to enable 2FA')?>?")){
            //load secret info
            var url="<?=site_url('/user_management/init_otp_secret')?>";
            $.ajax(url,{
                cache: false,
                dataType: 'json',
                method: 'POST',
                success: function(data){
                    if(data && data['success']){
                        //url
                        $('.otp_init .image_qrcode').empty();
                        $('.otp_init .image_qrcode').qrcode(data['result']['url']);
                        //text
                        $('.otp_init .secret').html(data['result']['text']).data('initsecret', data['result']['secret']).show();
                        $('.otp_init').show();
                    }
                },
                error: function(){
                    alert("<?=lang('Enable 2FA failed')?>");
                }
            });
        }
        return false;
    });

    $(".btn_disable_otp").click(function(){
        if(confirm("<?=lang('Do you want to disable 2FA')?>?")){
            var url="<?=site_url('/user_management/disable_otp')?>";
            var code=$(".otp_code").val();
            $.ajax(url,{
                cache: false,
                dataType: 'json',
                method: 'POST',
                data: {code: code},
                success: function(data){
                    alert(data['message']);
                    if(data['success']){
                        window.location.reload();
                    }
                },
                error: function(){
                    alert("<?=lang('Disable 2FA failed')?>");
                }
            });
        }
        return false;
    });

});

</script>
