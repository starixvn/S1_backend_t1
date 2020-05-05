<ul class="sidebar-nav" id="sidebar">
    <?php
    $permissions = $this->permissions->getPermissions();
    $active= isset($active) ? $active : '';
    if ($permissions != null) {
        foreach ($permissions as $value) {
            switch ($value) {
                case 'super_summary_report': ?>
                    <li>
                        <a class="list-group-item <?php echo $active=='summary_report' ? 'active' : '';?>" id="summary_report" style="border: 0px;margin-bottom:0.1px;"
                             href="<?php echo site_url('super_report_management/summary_report'); ?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Summary Report');?>">
                            <i class="icon-pie-chart <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                            <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('Summary Report');?></span>
                        </a>
                    </li>
                    <?php break;
                case 'super_player_report': ?>
                    <li>
                        <a class="list-group-item <?php echo $active=='player_report' ? 'active' : '';?>" id="player_report" style="border: 0px;margin-bottom:0.1px;"
                           href="<?php echo site_url('super_report_management/player_report'); ?>" data-toggle="tooltip" data-placement="right" title="<?=lang('report.s09');?>">
                            <i class="icon-users <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                            <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('report.s09');?></span>
                        </a>
                    </li>
                    <?php break;
                case 'super_game_report': ?>
                    <li>
                        <a class="list-group-item <?php echo $active=='game_report' ? 'active' : '';?>" id="game_report" style="border: 0px;margin-bottom:0.1px;"
                           href="<?php echo site_url('super_report_management/games_report'); ?>" data-toggle="tooltip" data-placement="right" title="<?=lang('report.s07');?>">
                            <i class="icon-dice <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                            <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('report.s07');?></span>
                        </a>
                    </li>
                    <?php break;
                case 'super_payment_report': ?>
                    <li>
                        <a class="list-group-item <?php echo $active=='payment_report' ? 'active' : '';?>" id="payment_report" style="border: 0px;margin-bottom:0.1px;"
                           href="<?php echo site_url('super_report_management/payment_report'); ?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Payment Report');?>">
                            <i class="icon-credit-card <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                            <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('Payment Report');?></span>
                        </a>
                    </li>
                    <?php break;
                case 'super_promotion_report': ?>
                    <li>
                        <a class="list-group-item <?php echo $active=='promotion_report' ? 'active' : '';?>" id="promotion_report" style="border: 0px;margin-bottom:0.1px;"
                           href="<?php echo site_url('super_report_management/promotion_report'); ?>" data-toggle="tooltip" data-placement="right" title="<?=lang('report.s02');?>">
                            <i class="fa fa-table <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                            <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('report.s02');?></span>
                        </a>
                    </li>
                    <?php break;
                case 'super_cashback_report': ?>
                    <li>
                        <a class="list-group-item <?php echo $active=='cashback_report' ? 'active' : '';?>" id="cashback_report" style="border: 0px;margin-bottom:0.1px;"
                           href="<?php echo site_url('super_report_management/cashback_report'); ?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Cashback Report');?>">
                            <i class="icon-bullhorn  <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                            <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('Cashback Report');?></span>
                        </a>
                    </li>
                    <?php break;

                default:
                    break;
            }
        }
    }
    ?>

</ul>
<ul id="sidebar_menu" class="sidebar-nav">
    <li class="sidebar-brand">
        <a id="menu-toggle" href="#" onclick="Template.changeSidebarStatus();">
            <span id="main_icon" class="icon-arrow-<?=($this->session->userdata('sidebar_status') == 'active') ? 'left' : 'right';?> pull-right"></span></span>
        </a>
    </li>
</ul>
<script type="text/javascript">
    $( document ).ready(function() {
        $('#main_icon').on('click',function(){
            if($("#wrapper").hasClass("active")){
                $.each($('.sidebar-nav li a i'),function( index, value ){
                    $(value).addClass('pull-right');
                });
            }else{
                $.each($('.sidebar-nav li a i'),function( index, value ){
                    $(value).removeClass('pull-right');
                });
            }
        });
    });
</script>
