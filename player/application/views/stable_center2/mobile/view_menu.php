<?php
$live_person_button_id = $this->config->item('live_person_button_id');
$col_user_name = 9;
$col_user_msg = 3;
$col_live_person = 0;
$col_currency_list = 0;
if(empty($live_person_button_id) && empty($currency_select_html)){
    $col_user_name = 9;
    $col_user_msg = 3;
}elseif(!empty($live_person_button_id) && empty($currency_select_html)){
    $col_user_name = 6;
    $col_live_person = 3;
}elseif(empty($live_person_button_id) && !empty($currency_select_html)){
    $col_user_name = 5;
    $col_currency_list=4;
}else{
    $col_user_name = 4;
    $col_live_person = 2;
    $col_currency_list=3;
}
?>
<div class="actmain col col-md-12">
    <?php if($this->utils->isEnabledFeature('eanble_display_mobile_user_icon')) : ?>

        <div class="actuesr_icon">
            <?php if(isset($profilePicture)) : ?>
                <div class="actuesr_icon_customer">
                    <img id="playerCenterPlayerProfilePic" src="<?=$profilePicture?>" alt="<?php echo $this->authentication->getUsername(); ?>">
                </div>
            <?php else : ?>
                <div class="actuesr_icon_defult">
                    <img src="/<?=$this->utils->getPlayerCenterTemplate(FALSE)?>/images/svg/user_icon.svg">
                </div>
            <?php endif; ?>
        </div>
        <div class="act_usname">
            <ul>
                <li><?=lang('Hello')?>: <?=$player['username']?> <?=$currency_select_html?></li>
            </ul>
        </div>
        <div class="act_msg">1
            <a id="usermessage" class="threepage" href="<?=$this->utils->getPlayerMessageUrl()?>">
                <img src="<?=$this->utils->getAnyCmsUrl('/includes/images/message_icon_white.svg')?>">
                <a><?=lang('cs.messages');?></a>z
            </a>
        </div>
    <?php else : ?>
        <div class="username row">
            <div class="col col-sm-<?=$col_user_name?> col-xs-<?=$col_user_name?> user_name">
                <p><?=lang('Hello')?>, <?=$player['username']?></p>
                <?php if($this->utils->isEnabledFeature('mobile_show_vip_referralcode')): ?>
                    <p><?=lang($player['groupName'])?>-<?=lang($player['vipLevelName'])?></p>
                <?php endif; ?>
                <?php if($this->utils->isEnabledFeature('display_total_bet_amount_in_overview')): ?>
                    <p>
                        <?=lang('Betting today');?>: <?=$player_today_total_betting_amount?>
                    </p>
                <?php endif; ?>
            </div>
            <?php if(!empty($currency_select_html)){ ?>
            <div class="col col-sm-<?=$col_currency_list?> col-xs-<?=$col_currency_list?> currency_list">
                <?=$currency_select_html?>
            </div>
            <?php }?>

            <?php if(!empty($live_person_button_id)) : ?>
                <div id="<?=lang($live_person_button_id)?>" class="chat-button" style="display: none;"></div>
                <div class="col col-sm-2 col-xs-2 chat_btn" style="cursor:pointer;">
                    <div id="userchat" class="threepage" style="cursor:pointer;">
                        <a href="javascript:void(0)" onclick="<?=$this->utils->getLiveChatOnClick();?>">
                            <img src="<?=$this->utils->getAnyCmsUrl('/includes/images/chat_icon_white.svg')?>">
                            <span class="labelmsg"><?=lang('cu.16');?></span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="col col-sm-<?=$col_user_msg?> col-xs-<?=$col_user_msg?> user_msg" style="cursor:pointer;">
                <div id="usermessage" class="threepage">
                    <a href="<?=$this->utils->getPlayerMessageUrl()?>">
                        <img src="<?=$this->utils->getAnyCmsUrl('/includes/images/message_icon_white.svg')?>">
                        <span class="labelmsg"><?=lang('cs.messages');?></span>
                        <span class="new-msg-tip"><?=$this->utils->unreadMessages($player['playerId'])?></span>
                    </a>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <div class="row wallet main-wallet">
        <div class="col-sm-8 col-xs-8">
            <p class="text"><?=lang("Main Wallet Total")?><span class="money playerTotalBalance"><?=$this->CI->utils->displayCurrency($big_wallet_simple['main_wallet']['balance'])?></span></p>
        </div>
        <div class="col-sm-4 col-xs-4">
            <a href="javascript:void(0);" class="btn refreshBalanceButton"><?=lang('lang.refreshbalance')?></a>
        </div>
    </div>
    <?php //if(!$this->utils->isEnabledFeature('always_auto_transfer_if_only_one_game')){ //hide game wallet ?>
    <div class="row wallet game-wallet">
        <div class="col-sm-7 col-xs-7">
            <p class="text"><?=lang("Game Wallet Total")?><span class="money playerTotalBalance"><?=$this->CI->utils->displayCurrency($big_wallet_simple['game_total'])?></span></p>
        </div>
        <div class="col-sm-5 col-xs-5">
            <a href="javascript: void(0);" class="btn transferAllToMainBtn"><?=lang("Transfer Back All")?></a>
        </div>
    </div>
    <?php //} ?>

</div>

<?php if($this->utils->isEnabledFeature('show_game_lobby_in_player_center')){ ?>

    <?php include "includes/game_lobby.php" ?>

<?php } ?>

<?php include __DIR__ . "/includes/mobile_main_menu.php" ?>

<div class="blank"></div>
<!--bottom-->
<?php if($this->utils->isEnabledFeature('enable_mobile_copyright_footer')): ?>
    <?=$this->load->view($this->utils->getPlayerCenterTemplate(FALSE) . '/mobile/includes/template_footer');?>
<?php endif; ?>
<div class="modal fade" id="progress-modal" role='dialog'>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <img class="loading" src="/<?=$this->utils->getPlayerCenterTemplate(FALSE)?>/images/loading.gif">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php if($this->utils->isEnabledFeature('enable_registered_show_success_popup') && !$is_registered_popup_success_done) : ?>
    <div class="modal fade " id="registered-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <?php if ($this->operatorglobalsettings->getSettingJson('registered_success_popup') == 1) : ?>
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="modal-title text-center">
                        <div class="title">
                            <h4><?=lang('Congratulations! You have successfully registered');?></h4>
                        </div>
                        <?php if($this->utils->isEnabledFeature('ole777_on_first_popup_after_register')){ ?>
                            <div class="redirect-link">
                                <span><a href="<?= $this->utils->getSystemUrl('m'); ?>"><?= lang('Go Home'); ?></a></span>
                            </div>
                            <div class="deposit-info">
                                <span><a href="/player_center2/deposit"><?= lang('Deposit immediately'); ?></a></span>
                            </div>
                        <?php }else{?>
                            <div class="deposit-info">
                                <span><?= lang('Account'); ?>：<?= $this->authentication->getUsername(); ?></span>
                                <span><?= lang('Account balance'); ?>：<?= $this->utils->displayCurrency($total_no_frozen); ?></span>
                                <span><a href="/player_center2/deposit"><?= lang('Deposit immediately'); ?></a></span>
                            </div>
                            <div class="redirect-link">
                                <span><a href="<?= $this->utils->getSystemUrl('m'); ?>"><?= lang('Go to the game hall'); ?></a></span>
                            </div>
                        <?php }?>
                        <?php if($this->utils->isEnabledFeature('switch_to_player_center_promo_on_first_popup_after_register')){ ?>
                            <div class="player-center-promo">
                                <span><a href="/player_center2/promotion"><?= lang('View Promotions'); ?></a></span>
                            </div>
                        <?php }else{?>
                            <div class="promo-list">
                                <?php $promoList = $this->utils->getPlayerPromo('firstLogin', $player['playerId']); ?>
                                <?php foreach ($promoList as $_list) : ?>
                                    <span> <?= $_list['promoDescription']; ?> </span>
                                    <span><a href="<?=$this->utils->getSystemUrl('player')?>/player_center2/promotion"><?= lang('View it now'); ?></a></span><br/>
                                <?php endforeach; ?>
                            </div>
                        <?php }?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($this->operatorglobalsettings->getSettingJson('registered_success_popup') == 2) : ?>
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="modal-title text-center">
                        <a href="javascript:void(0)" onclick=" $('#registered-modal').modal('hide'); ">
                            <img src="<?= $this->utils->getSystemUrl('www') . "/" . $this->utils->getConfig('registered_image_poup_path') ?>">
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script type="text/javascript" src="/common/js/player_center/registered-autoplay-procedure.js?v=<?=PRODUCTION_VERSION;?>"></script>

<script type="text/javascript">
    RegisteredAutoPlayProcedure.is_registered_popup_success_done = <?php echo($is_registered_popup_success_done); ?>;
    RegisteredAutoPlayProcedure.enable_registered_show_popup = <?= ($this->utils->isEnabledFeature('enable_registered_show_success_popup')) ? 1 : 0 ?>;
    RegisteredAutoPlayProcedure.execute();

    $(function(){
        var modal = $('#progress-modal').modal({
            'show': false
        });

        $('#logoutxx').on('click', function(e){
            e.preventDefault();

            modal.modal('show');

            window.location = base_url + 'player_center/player_center_logout';
        });

        _export_sbe_t1t.on('updated.t1t.player_wallet', function(e, wallet_info){
            $('.main-wallet .playerTotalBalance').html(_export_sbe_t1t.utils.displayCurrency(wallet_info['main_wallet']['balance']));
            $('.game-wallet .playerTotalBalance').html(_export_sbe_t1t.utils.displayCurrency(wallet_info['game_total']));

            $('.refreshBalanceButton').removeClass('disabled').prop('disabled', false);
            $('.transferAllToMainBtn').removeClass('disabled').prop('disabled', false);
        });

        $('.refreshBalanceButton').on('click', function(){
            $('.refreshBalanceButton').addClass('disabled').prop('disabled', true);
            $('.transferAllToMainBtn').addClass('disabled').prop('disabled', true);

            _export_sbe_t1t.player_wallet.refreshPlayerBalance();
        });

        $('.transferAllToMainBtn').on('click', function(){
            $('.refreshBalanceButton').addClass('disabled').prop('disabled', true);
            $('.transferAllToMainBtn').addClass('disabled').prop('disabled', true);

            Loader.show();

            _export_sbe_t1t.player_wallet.transferAllBalance(_export_sbe_t1t.variables.main_wallet_id, function(){
                Loader.hide();

                $('.refreshBalanceButton').removeClass('disabled').prop('disabled', false);
                $('.transferAllToMainBtn').removeClass('disabled').prop('disabled', false);
            });
        });
    });
</script>

