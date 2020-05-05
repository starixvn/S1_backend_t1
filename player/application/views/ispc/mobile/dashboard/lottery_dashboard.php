<div class="dashboard">
    <div class="news clearfix">
        <span><i class="glyphicon glyphicon-volume-up" aria-hidden="true"></i></span>
        <iframe class="marquee _public_news" src="javascript: void(0);" scrolling="no" seamless="seamless"></iframe>
    </div>

    <div class="banner-container clearfix">
        <?=$this->CI->load->widget('banner', null, [
            'category' => CMSBANNER_CATEGORY_MOBILE_HOME,
            'indicators' => FALSE
        ]);?>
    </div>

    <div class="fastbet clearfix">
        <iframe src="/player_center2/lottery/fastbet" scrolling="no"></iframe>
    </div>

    <?php if(!$this->utils->isEnabledFeature('hidden_lottery_game_list_on_the_mobile_dashboard')): ?>
    <div class="lottery-game-list-container clearfix">
        <?php include $template_path . '/includes/components/lottery_game_list.php';?>
        <script type="text/javascript">
            $(function(){
                $('.lottery-game-list-container .collapse').collapse();
            });
        </script>
    </div>
    <?php endif ?>

    <?php if(!$this->utils->isEnabledFeature('hidden_myfavorite_widget_on_the_mobile_dashboard')): ?>
    <div class="myfavorite-container clearfix">
        <?=$this->CI->load->widget('myfavorite');?>
    </div>
    <?php endif ?>
</div>

<!--bottom-->
<?php if($this->utils->isEnabledFeature('enable_mobile_copyright_footer')): ?>
    <?=$this->load->view($this->utils->getPlayerCenterTemplate(FALSE) . '/mobile/includes/template_footer');?>
<?php endif; ?>

<script type="text/javascript" src="/common/js/player_center/registered-autoplay-procedure.js?v=<?=PRODUCTION_VERSION;?>"></script>

<script type="text/javascript">
    RegisteredAutoPlayProcedure.is_registered_popup_success_done = <?php echo($is_registered_popup_success_done); ?>;
    RegisteredAutoPlayProcedure.enable_registered_show_popup = <?= ($this->utils->isEnabledFeature('enable_registered_show_success_popup')) ? 1 : 0 ?>;
    RegisteredAutoPlayProcedure.execute();
</script>

