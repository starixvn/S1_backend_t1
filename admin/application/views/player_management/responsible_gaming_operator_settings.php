<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt pull-left">
            <i class="glyphicon glyphicon-cog"></i> <?=lang('Responsible Gaming Setting');?>
        </h4>
        
        <div class="clearfix"></div>
    </div>
    <div class="panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12">
                    <form class="form-horizontal" action="<?=BASEURL . 'player_management/postResponsibleGamingSetting'?>" method="post" role="form">
                        <div class="col-md-12 well" style="overflow: auto;">
                            <div class="col-md-6">
                                <?php echo lang('Number of days will the system approve self exclusion request') ?>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="self_exclusion_txt" value="<?php echo (int)$respGameData['self_exclusion_approval_day_cnt'] ?>" min="1" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <?php echo lang('day/s') ?>
                            </div>
                        </div>
                        <div class="col-md-12 well" style="overflow: auto;">
                            <div class="col-md-6">
                                <?php echo lang('Number of days will the system add cooling off after temporary self exclusion expired') ?>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="self_exclusion_cooling_off_txt" value="<?php echo (int)$respGameData['self_exclusion_cooling_off_day_cnt'] ?>" min="1" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <?php echo lang('day/s') ?>
                            </div>
                        </div>
                        <div class="col-md-12 well" style="overflow: auto;">
                            <div class="col-md-6">
                                <?php echo lang('Number of days will the system approve cool off request') ?>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="cool_off_txt" value="<?php echo (int)$respGameData['cool_off_approval_day_cnt'] ?>" min="1" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <?php echo lang('day/s') ?>
                            </div>
                        </div>
                        <div class="col-md-12 well" style="overflow: auto;">
                            <div class="col-md-6">
                                <?php echo lang('Number of days will the system approve deposit limit request') ?>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="deposit_limit_txt" value="<?php echo (int)$respGameData['deposit_limit_approval_day_cnt'] ?>" min="1" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <?php echo lang('day/s') ?>
                            </div>
                        </div>
                        <div class="col-md-12 well" style="overflow: auto;">
                            <div class="col-md-6">
                                <?php echo lang('Number of days will the system approve loss limit request') ?>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="loss_limit_txt" value="<?php echo (int)$respGameData['loss_limit_approval_day_cnt'] ?>" min="1" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <?php echo lang('day/s') ?>
                            </div>
                        </div>
                        <div class="col-md-12 well" style="overflow: auto;">
                            <div class="col-md-6">
                                <?php echo lang('Number of days will the system reactivates the player after block in the game and website') ?>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="reactivation_txt" value="<?php echo (int)$respGameData['player_reactication_day_cnt'] ?>" min="1" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <?php echo lang('day/s') ?>
                            </div>
                        </div>
                        <div class="col-md-12 well" style="overflow: auto;">
                            <div class="col-md-6">
                                <?php echo lang('Automatic reopen the account after Temp Self Exclusion') ?>
                            </div>
                            <div class="col-md-2">
                                <input type="radio" name="reopen_temp_self_exclusion_account_txt" value="1" <?php echo (int)($respGameData['automatic_reopen_temp_self_exclusion_account']) ? 'checked' : ''?>> <?=lang('lang.yes');?>
                            </div>
                            <div class="col-md-2">
                                <input type="radio" name="reopen_temp_self_exclusion_account_txt" value="0" <?php echo (int)(!$respGameData['automatic_reopen_temp_self_exclusion_account']) ? 'checked' : ''?>> <?=lang('lang.no');?>
                            </div>
                        </div>
                        <div class="col-md-12 well" style="overflow: auto;">
                            <div class="col-md-6">
                                <?php echo lang('Disable and hide wagering limits in SBE and player center') ?>
                            </div>
                            <div class="col-md-2">
                                <input type="radio" name="disable_and_hide_wagering_limits_txt" value="1" <?php echo (int)($respGameData['disable_and_hide_wagering_limits']) ? 'checked' : ''?>> <?=lang('lang.yes');?>
                            </div>
                            <div class="col-md-2">
                                <input type="radio" name="disable_and_hide_wagering_limits_txt" value="0" <?php echo (int)(!$respGameData['disable_and_hide_wagering_limits']) ? 'checked' : ''?>> <?=lang('lang.no');?>
                            </div>
                        </div>
                        <input type="submit" name="submit" value="<?php echo lang('Save');?>" class="btn btn-md pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-primary' ?>">
                    </form>
            </div>
        </div>
    </div>
</div>