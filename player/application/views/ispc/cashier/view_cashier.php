<div class="container dashboar-container hidden">
    <?php include $template_path . '/includes/components/news.php';?>
    <div class="member-center">
        <div class="col-md-12 mc-content tab-content nopadding">
            <?php include VIEWPATH . '/stable_center2/includes/dashboard/member_center.php';?>
            <?php include VIEWPATH . '/stable_center2/includes/dashboard/account_information.php';?>
            <?php include VIEWPATH . '/stable_center2/includes/dashboard/shop.php';?>
            <?php include VIEWPATH . '/stable_center2/includes/dashboard/vip_rewards.php';?>
            <?php if ($this->utils->isEnabledFeature('enabled_favorites_and_rencently_played_games')) :?>
            <?php include VIEWPATH . '/stable_center2/includes/dashboard/favorite_games.php';?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!$this->utils->isEnabledFeature('hidden_player_first_login_welcome_popup')) :?>
<!-- VIP Group Modal -->
<?php if(count($vipList) > 0) : ?>
    <div class="modal fade " id="vip-group-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="modal-title">
                        <h4><?php echo $operator_setting->main ?></h4>
                        <p><?php echo $operator_setting->sub ?></p>
                    </div>
                    <div class="row">
                        <?php
                        $x = 1;
                        foreach($vipList as $key => $vip):
                            $imageUrl = empty(trim($vip['image'])) ? $this->utils->imageUrl("vip_cover/default_vip_cover.jpeg") : $this->utils->imageUrl("vip_cover/".$vip['image']);
                            if($key == 4) break;
                            $btn_val = "b".$x;
                            $x++;
                            ?>
                            <div class="col-xs-12 col-sm-6 col-md-3">
                                <a href="">
                                    <h4><?php echo $vip['groupName'] ?></h4>
                                    <img style="height: 100px;width: 400px;" src="<?php echo $imageUrl; ?>" alt=""/>
                                    <div class="tip-bottom">
                                        <?php echo ($vip['groupDescription']); ?>
                                    </div>
                                </a>
                                <a href="javascript:void(0)" class="btn btn_join <?= $vip['is_player_choose_vip'] ? '' : 'disabled'?>"
                                   data-id="<?php echo $vip['vipSettingId']?>"
                                   data-level="<?php echo $vip['level'] ?>"
                                   data-gname="<?php echo $vip['groupName'] ?>"
                                   data-lname="<?php echo $vip['levelName'] ?>"><?= $operator_setting->$btn_val ?: "Join"; ?></a>
                            </div>
                        <?php endforeach?>
                    </div>
                </div>
                <div class="modal-footer text-left">
                    <button type="button" class="btn btn-default submit-btn" data-dismiss="modal"><?= lang('reg.53') ?></button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php endif; ?>

<?php if (!$is_registered_popup_success_done) : ?>
<div class="modal fade " id="registered-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="modal-title text-center">
                    <div class="title">
                        <h4><?= lang('Congratulations! You have successfully registered'); ?></h4>
                    </div>
                    <div class="deposit-info">
                        <span><?= lang('Account'); ?>：<?= $this->authentication->getUsername(); ?></span>
                        <span><?= lang('Account balance'); ?>：<?= $this->utils->displayCurrency($total_no_frozen); ?></span>
                        <span><a href="<?= $this->utils->getSystemUrl('player'); ?>/player_center2/deposit"><?= lang('Deposit immediately'); ?></a></span>
                    </div>
                    <div class="redirect-link">
                        <span><a href="<?= $this->utils->getSystemUrl('www', '/') . "#main-content"; ?>"><?= lang('Go to the game hall'); ?></a></span>
                    </div>
                    <div class="promo-list">
                        <?php $promoList = $this->utils->getPlayerPromo('firstLogin', $player['playerId']); ?>
                        <?php foreach ($promoList as $_list) : ?>
                        <span> <?= $_list['promoDescription']; ?> </span>
                        <span><a href="<?=$this->utils->getSystemUrl('player')?>/player_center2/promotion"><?= lang('View it now'); ?></a></span><br/>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    $(function() {
        setTimeout(function(){
            $('.circle').each(function(index, el) {
                var num = $(this).find('span').text() * 3.6;
                if (num <= 180) {
                    $(this).find('.right').css('transform', "rotate(" + num + "deg)");
                } else {
                    $(this).find('.right').css('transform', "rotate(180deg)");
                    $(this).find('.left').css('transform', "rotate(" + (num - 180) + "deg)");
                };
            });
        }, 1500);
    });

    /* For VIP Rewards */
    $(".privilege-icon a").click(function(e) {
        $(".prm").removeClass("active")
        $(".mc").removeClass("active")
        $(".msg").removeClass("active")
        $(".fm").removeClass("active")
        $(".ai").removeClass("active")
        $(".scy").removeClass("active")
        $(".ah").removeClass("active")
        $(".shp").removeClass("active")
        $(".vipr").removeClass("active")
        $(".reff").removeClass("active")
        $(".favg").removeClass("active")
        $(".rsg").removeClass("active")
        $("."+$(e.target).attr("target")).addClass("active")
    });

    /* For Completion Icon */
    $(".tips a").click(function(e) {
        $(".prm").removeClass("active")
        $(".mc").removeClass("active")
        $(".msg").removeClass("active")
        $(".fm").removeClass("active")
        $(".ai").removeClass("active")
        $(".scy").addClass("active")
        $(".ah").removeClass("active")
        $(".shp").removeClass("active")
        $(".vipr").removeClass("active")
        $(".reff").removeClass("active")
        $(".favg").removeClass("active")
    });

    /* For Fund Management Section */
    $('input[name="withdrawAmount"]').change(function(){
        if ($(this).val())
            {
                $(".depositBtn").removeClass('disabled');
            }else{
               $(".depositBtn").addClass('disabled');
            }
    });

    $(".show-btn").click(
    function(){
       $("#fm-deposit .bank-list").animate( { height:"115px" }, { queue:false, duration:300 });
    },
    function(){
       $("#fm-deposit .bank-list").animate( { height:"325px" }, { queue:false, duration:300 });
       $(".show-btn").hide();
       $(".hide-btn").show();
    });

    $(".hide-btn").click(
    function(){
       $("#fm-deposit .bank-list").animate( { height:"325px" }, { queue:false, duration:300 });
    },
    function(){
       $("#fm-deposit .bank-list").animate( { height:"115px" }, { queue:false, duration:300 });
       $(".hide-btn").hide();
       $(".show-btn").show();
    });
</script>
<?php
    $joinVipUrl = "http://" . $this->utils->getSystemHost('player') . "/player_center/joinVip";
    $updateLoginInfo = "http://" . $this->utils->getSystemHost('player') . "/player_center/updateLoginInfo/";
?>
<script type="text/javascript">
    RegisteredAutoPlayProcedure.is_tutorial_done = <?php echo ($is_tutorial_done ); ?>;
    RegisteredAutoPlayProcedure.playerId = <?php echo $player['playerId']?>;
    RegisteredAutoPlayProcedure.joinVipUrl = <?php echo json_encode($joinVipUrl); ?>;
    RegisteredAutoPlayProcedure.vip_count = <?php echo count($vipList); ?>;
    RegisteredAutoPlayProcedure.is_vip_show_done = <?php echo ($is_vip_show_done); ?>;
    RegisteredAutoPlayProcedure.is_registered_popup_success_done = <?php echo ($is_registered_popup_success_done ); ?>;
    RegisteredAutoPlayProcedure.enable_registered_show_popup = <?= ($this->utils->isEnabledFeature('enable_registered_show_success_popup')) ? 1 : 0 ?>;

    RegisteredAutoPlayProcedure.execute();

</script>
<script type="text/javascript">
    var glob = {
        show_tag_for_unavailable_deposit_accounts: <?= $show_tag_for_unavailable_deposit_accounts ?> ,
        disable_account_transfer_when_balance_check_fails: <?= $disable_account_transfer_when_balance_check_fails ?> ,
        tag_unavailable: ' (<?= lang('Unavailable') ?>)'
    };
</script>