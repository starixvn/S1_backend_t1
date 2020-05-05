<?php
$show_manually_deposit_notify_message = FALSE;
$auto_reload_page = FALSE;

$buttons = [];
if($this->utils->isEnabledFeature('show_last_manually_deposit_order_status') && (!empty($last_manually_sale_order) && !$last_manually_sale_order['is_notify'])){
     // $last_manually_sale_order['status'] = Sale_order::STATUS_PROCESSING;
    if(($last_manually_sale_order['status'] == Sale_order::STATUS_PROCESSING)){
        if(strtotime($last_manually_sale_order['timeout_at']) < time()){
            $last_manually_deposit_notify_message = sprintf(lang('deposit.manually.last_order_status.pending_timeout_msg'), $last_manually_sale_order['secure_id'], $last_manually_sale_order['created_at'], $this->utils->displayCurrency($last_manually_sale_order['amount']), $this->utils->getPlayerHistoryUrl('deposit'), $this->utils->getConfig('manually_deposit_cool_down_minutes'));
            $show_manually_deposit_notify_message = TRUE;
            $allow_manually_deposit = FALSE;

            $buttons[] = '<a href="javascript:void(0)" class="btn btn-primary btn-live-chat" onclick="' . $this->utils->getLiveChatOnClick() . '">' . lang('Contact Customer Service') . '</a>';
            $buttons[] = '<a href="javascript:void(0)" class="btn btn-primary btn-continue-deposit">' . lang('Continue Deposit') . '</a>';
        }else{
            $last_manually_deposit_notify_message = sprintf(lang('deposit.manually.last_order_status.pending_msg'), $last_manually_sale_order['secure_id'], $last_manually_sale_order['created_at'], $this->utils->displayCurrency($last_manually_sale_order['amount']), $this->utils->getPlayerHistoryUrl('deposit'), $this->utils->getConfig('manually_deposit_cool_down_minutes'));
            $show_manually_deposit_notify_message = TRUE;
            $allow_manually_deposit = FALSE;

            $buttons[] = '<a href="javascript:void(0)" class="btn btn-primary btn-live-chat" onclick="' . $this->utils->getLiveChatOnClick() . '">' . lang('Contact Customer Service') . '</a>';

            $auto_reload_page = true;
            if($this->utils->isEnabledFeature('only_allow_one_pending_deposit')){

            }else{
                $buttons[] = '<a href="javascript:void(0)" class="btn btn-primary btn-continue-deposit">' . lang('Continue Deposit') . '</a>';
            }
        }
    }elseif($last_manually_sale_order['status'] == Sale_order::STATUS_DECLINED){
        $last_manually_deposit_notify_message = sprintf(lang('deposit.manually.last_order_status.declined_msg'), $last_manually_sale_order['secure_id'], $last_manually_sale_order['created_at'], $this->utils->displayCurrency($last_manually_sale_order['amount']), $this->utils->getPlayerHistoryUrl('deposit'));
        $show_manually_deposit_notify_message = TRUE;
        $allow_manually_deposit = FALSE;

        $buttons[] = '<a href="javascript:void(0)" class="btn btn-primary btn-live-chat" onclick="' . $this->utils->getLiveChatOnClick() . '">' . lang('Contact Customer Service') . '</a>';
        $buttons[] = '<a href="javascript:void(0)" class="btn btn-primary btn-continue-deposit">' . lang('Continue Deposit') . '</a>';
    }elseif($last_manually_sale_order['status'] == Sale_order::STATUS_SETTLED){
        $last_manually_deposit_notify_message = sprintf(lang('deposit.manually.last_order_status.settled_msg'), $last_manually_sale_order['secure_id'], $last_manually_sale_order['created_at'], $this->utils->displayCurrency($last_manually_sale_order['amount']), $this->utils->getPlayerHistoryUrl('deposit'));
        $show_manually_deposit_notify_message = TRUE;
        $allow_manually_deposit = FALSE;

        $buttons[] = '<a href="'.$this->utils->getSystemUrl('player', '/player_center2/report/index/deposit').'" class="btn btn-primary btn-deposit-list">' . lang('Deposit List') . '</a>';
        $buttons[] = '<a href="javascript:void(0)" class="btn btn-primary btn-transfer">' . lang('Transfer') . '</a>';
    }else{
        $show_manually_deposit_notify_message = FALSE;
        $allow_manually_deposit = TRUE;
    }
}
?>
<?php if($show_manually_deposit_notify_message): ?>
<div id="deposit-manual-notify-message">
    <h4 class="title"><?=lang($last_manually_sale_order['payment_type_name'])?></h4>
    <div class="notify_secure_id">
    <span class="text"><?=lang('Order ID')?>: <?=$last_manually_sale_order['secure_id']?></span>
    </div>
    <div class="notify_message_content">
    <span class="text"><?=$last_manually_deposit_notify_message?></span>
    </div>
    <div class="deposit-manual-notify-message-actions">
        <?php foreach($buttons as $button): ?>
            <?=$button?>
        <?php endforeach ?>
    </div>
</div>
<script type="text/javascript">
$(function(){
    var clearLastDeposit = function() {
        $.ajax({
            "contentType": "application/x-www-form-urlencoded; charset=UTF-8",
            "url": "/api/player_deposit_notify",
            "type": "POST",
            "data": {"sale_order_id": "<?=$last_manually_sale_order['id']?>"},
            "success": function(data){
            },
            "error": function(){
            }
        });
    };

    $('#deposit-manual-notify-message .btn-continue-deposit').on('click', function(){
        $('#deposit-manual-notify-message').remove();
        $('.hide_deposit_form').removeClass('hide_deposit_form');
        return false;
    });

    // If any of these buttons exist (i.e. deposit is not in pending state), only show the last deposit message this once
    if($('#deposit-manual-notify-message .btn-continue-deposit, #deposit-manual-notify-message .btn-transfer, #deposit-manual-notify-message .btn-deposit-list').length > 0) {
        clearLastDeposit();
    }

    $('#deposit-manual-notify-message .btn-transfer').on('click', function() {
        <?php if($this->utils->is_mobile()) : ?>
        window.location.href="<?=$this->utils->getSystemUrl('player', '/player_center/mobile_transfer')?>";
        <?php else : ?>
        $('#quick_transfer').click();
        <?php endif; ?>
    });

    <?php if($auto_reload_page) : # auto reload page to refresh deposit status ?>
    setTimeout(function() {
        location.reload();
    }, 30000);
    <?php endif; ?>

    $([document.documentElement, document.body]).animate({
        scrollTop: $("h4.title").offset().top
    },100);
});
</script>
<?php endif ?>