<?php if($system_feature_use_self_pick_subwallets): ?>
<div class="row deposit-process-mode-<?=in_array($deposit_process_mode, array('2','3')) ? '2' : $deposit_process_mode ?> setup-deposit-wallet">
    <div class="form-group has-feedback">
        <p class="step"><span class="step-icon"><?=$deposit_step++?></span><label class="control-label"><?=lang('Select game  to transfer money to')?></label></p>
        <div class="input-group col col-xs-12 col-sm-12 col-md-4">
            <div class="dropdown setup-deposit-wallet-dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="deposit_target_wallet_toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span><?=lang('Select game  to transfer money to')?></span>
                    <span class="caret"></span>
                </button>

                <ul class="dropdown-menu" aria-labelledby="deposit_target_wallet_toggle">
                    <li role="presentation" value=""><a href="javascript: void(0);" role="menuitem"><?=lang('Main Wallet')?></a></li>
                    <?php foreach($pick_subwallets as $wallet_id => $wallet_name){ ?>
                    <li role="presentation" value="<?=$wallet_id?>"><a href="javascript: void(0);" role="menuitem"><?=$wallet_name?></a></li>
                    <?php } ?>
                </ul>
            </div>
            <input type="hidden" id="deposit_target_wallet" name="deposit_target_wallet" class="field" value=""/>

            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
        </div>
        <div class="help-block with-errors"></div>
    </div>

    <hr/>
</div>
<script>
$(function(){
    $(".setup-deposit-wallet-dropdown .dropdown-menu li").on("click", function(){
        if($(this).attr('disabled')){
            return false;
        }

        var str = $(this).text();
        var val = $(this).attr('value');

        $(this).parent().parent().parent().find(".dropdown-toggle span:first").html(str);
        $(this).parent().parent().parent().find(".field").val(val);
    });
});
</script>
<?php endif ?>