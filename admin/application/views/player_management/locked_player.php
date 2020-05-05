<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title pull-left"><i class="icon-lock"></i> <?=lang('player.lp01');?> :<b>&nbsp;<?=$player['username']?></b></h4>
        <a href="#close" class="btn btn-default btn-sm pull-right" id="chat_history" onclick="closeDetails()"><span class="glyphicon glyphicon-remove"></span></a>
        <div class="clearfix"></div>
    </div>
    <div class="panel panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12">
                <?php if ($player['playerStatus'] == 0) {?>
                    <form class="form-horizontal" action="<?=BASEURL . 'player_management/lockPlayer/' . $player_id . '/' . $player['playerStatus'] . '/' . $page?>" method="post" role="form">
                    <div class="panel panel-info">
                            <div class="panel-heading">
                        <div class="form-group">
                            <div class="col-md-4">
                                <label for="locked_period" class="control-label"><?=lang('player.lp03');?>:</label>
                                <select class="form-control input-sm" name="locked_period" onchange="specifyLocked(this);">
                                    <option value=""><?=lang('lang.select');?></option>
                                    <option value="0"><?=lang('player.lp04');?></option>
                                    <option value="1"><?=lang('player.lp05');?></option>
                                    <option value="2"><?=lang('player.lp06');?></option>
                                    <option value="3"><?=lang('player.lp07');?></option>
                                    <option value="specify"><?=lang('player.lp08');?></option>
                                </select><?php echo form_error('locked_period', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                            </div>
                            <div id="hide_date">
                                <div class="col-md-4">
                                    <label for="start_date_locked" class="control-label"><?=lang('player.lp09');?>:</label>
                                    <input type="date" name="start_date_locked" id="start_date_locked" class="form-control input-sm" disabled="disabled">
                                    <?php echo form_error('start_date_locked', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                                <div class="col-md-4">
                                    <label for="start_date_locked" class="control-label"><?=lang('player.lp10');?>:</label>
                                    <input type="date" name="end_date_locked" id="end_date_locked" class="form-control input-sm" disabled="disabled">
                                    <?php echo form_error('end_date_locked', '<span class="help-block" style="color:#ff6666;">', '</span>');?> <span class="help-block" style="color:#ff6666;" id="mdate"></span>
                                </div>
                            </div>
                        </div>


                    </div>
                        </div>
                        <div style="text-align:center;">
                            <input type="submit" class="btn btn-info btn-sm" value="<?=lang('lang.submit');?>">
                            <input type="reset" class="btn btn-default btn-sm" value="<?=lang('lang.reset');?>">
                        </div>
                    </form>
                <?php } else {?>
                    <form action="<?=BASEURL . 'player_management/lockPlayer/' . $player_id . '/' . $player['playerStatus'] . '/' . $page?>" method="post" role="form">
                        <div class="row">
                            <div class="col-md-12">
                                <center><h4><label for="unlocked"><?=lang('player.lp11');?> <i style="color:#66cc66;"><?=lang('player.lp12');?></i> <?=lang('player.lp13');?>? </label></h4></center>
                            </div>
                        </div>

                        <br/>

                        <div style="text-align:center;">
                            <input type="submit" class="btn btn-info" value="<?=lang('lang.yes');?>">
                            <a href="#list" class="btn btn-default" id="chat_history" onclick="closeDetails()"><?=lang('lang.no');?></a>
                        </div>
                    </form>
                <?php }
?>
            </div>
        </div>
    </div>
</div>