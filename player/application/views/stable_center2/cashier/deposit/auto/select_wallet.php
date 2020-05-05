<?php if($this->utils->isEnabledFeature('use_self_pick_subwallets')): ?>
<div class="input_name_2 setup-deposit-wallet">
    <div class="input_name_text"><?=lang('Select game  to transfer money to')?></div>
    <div class="select_form">
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
        <input type="hidden" name="sub_wallet_id" class="field" value=""/>
    </div>
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
<?php endif; ?>