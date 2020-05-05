<div class="row">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left"><strong><?=lang('player.ap01');?></strong></h4>
                <a href="<?=BASEURL . 'player_management/viewAllPlayer'?>" class="btn btn-sm btn-default pull-right"><span class="glyphicon glyphicon-remove"></span></a>
                <div class="clearfix"></div>
            </div>

            <div class="panel panel-body" id="signupinfo_panel_body">
                <form class="form-horizontal" action="<?=BASEURL . 'player_management/actionType'?>" method="post" role="form">

                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped" id="myTable">
                                <thead>
                                    <tr>
                                        <th><?=lang('player.01');?></th>
                                        <th><?=lang('player.07');?></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($players as $row) {?>
                                        <tr>
                                            <td><?=$row['username']?></td>
                                            <td><?=$row['level']?></td>
                                        </tr>
                                    <?php }
?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr/>
                    <div class="col-md-6">

                        <div class="col-md-5 pull-right">
                            <select name="action_type" id="action_type" class="form-control input-sm" onchange="showDivs(this);">
<!-- OG-1002 merge "freeze" and "lock" member to one "block" per platform
                                <?php if ($this->permissions->checkPermissions('lock_player')) {?>
                                    <option value="locked"><?=lang('player.ap02');?></option>
                                <?php }
?>
                                <?php if ($this->permissions->checkPermissions('block_player')) {?>
                                    <option value="blocked"><?=lang('tool.pm07') . '/' . lang('sys.ip05');?></option>
                                <?php }
?>
-->
                                <?php if ($this->permissions->checkPermissions('tag_player')) {?>
                                    <option value="tag"><?=lang('player.ap04');?></option>
                                <?php }
?>
                                <?php if ($this->permissions->checkPermissions('edit_player_vip_level')) {?>
                                    <option value="level"><?=lang('tool.pm01');?></option>
                                <?php }
?>
                            </select>
                            <?php echo form_error('action_type', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                            <input type="hidden" name="players" value="<?=$player_ids?>">
                        </div>
                        <div class="clearfix"></div>

                        <label><?=lang('player.ap05');?></label>

                        <div class="well" style="overflow: auto">

<!-- OG-1002 merge "freeze" and "lock" member to one "block" per platform
                            <div id="block_lock">
                                <div style="display:none;"  id="game">
                                    <div class="form-group" style="margin-bottom:0;">
                                        <h6><label for="block_period" class="col-md-3 control-label"><?=lang('player.ap11');?>:</label></h6>
                                        <div class="col-md-8">
                                            <div class="checkbox">
                                                <?php foreach ($games as $row) {?>
                                                    <div class="col-md-3">
                                                        <label for="game"><input type="checkbox" name="game[]" id="<?=$row['gameId']?>" value="<?=$row['gameId']?>" /> <?=$row['game']?></label>
                                                    </div>
                                                <?php }
?>
                                            </div><?php echo form_error('game', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <h6><label for="block_period" class="col-md-3 control-label"><?=lang('player.ap06');?>:</label></h6>
                                    <div class="col-md-8">
                                        <select class="form-control input-sm" name="period" onchange="specifyDate(this);">
                                            <option value=""><?=lang('lang.select');?></option>
                                            <option value="always"><?=lang('tool.pm08');?></option>
                                            <option value="frozen"><?=lang('player.ap08');?></option>
                                            <option value="unblock"><?=lang('tool.pm09') . '/' . lang('sys.ip06');?></option>
                                        </select><?php echo form_error('period', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                    </div>
                                </div>
                                <div id="hide_date">
                                    <div class="form-group" style="margin-bottom:0;">
                                        <h6><label for="start_date" class="col-md-3 control-label"><?=lang('player.ap09');?>:</label></h6>
                                        <div class="col-md-8">
                                            <input type="date" name="start_date" id="start_date" class="form-control input-sm" disabled="disabled">
                                            <?php echo form_error('start_date', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin-bottom:0;">
                                        <h6><label for="end_date" class="col-md-3 control-label"><?=lang('player.ap10');?>:</label></h6>
                                        <div class="col-md-8">
                                            <input type="date" name="end_date" id="end_date" class="form-control input-sm" disabled="disabled">
                                            <?php echo form_error('end_date', '<span class="help-block" style="color:#ff6666;">', '</span>');?> <span class="help-block" style="color:#ff6666;" id="mdate"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
-->

                            <div id="tag">
                                <div class="form-group" style="margin-bottom:0;">
                                    <h6><label for="tag" class="col-md-3 control-label"><?=lang('player.ap12');?>:</label></h6>
                                    <div class="col-md-8">
                                        <select id="tags" name="tags" class="form-control input-sm" onchange="showDescription(this)">
                                            <option value="">-<?=lang('lang.select');?>-</option>
                                            <?php foreach ($tags as $tag) {?>
                                                <option value="<?=$tag['tagId']?>"><?=$tag['tagName']?></option>
                                            <?php }
?>
                                            <?php if ($page == 'blacklist') {?>
                                                <!-- <option value="Others">Others</option> -->
                                            <?php }
?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" style="display:none;margin-bottom:0;" id="description">
                                    <h6><label for="tag" class="col-md-3 control-label"><?=lang('pay.description');?>:</label></h6>
                                    <div class="col-md-8">
                                        <div class="well" style="overflow:auto;background:#fff;">
                                            <center><div id="tagDescription"></div></center>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="level" style="display: none;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <h6><label for="level" class="col-md-3 control-label"><?=lang('tool.pm01');?>:</label></h6>
                                    <div class="col-md-8">
                                        <select id="level" name="level" class="form-control input-sm" onchange="">
                                            <option value="">-<?=lang('lang.select');?>-</option>
                                            <?php foreach ($level as $level) {?>
                                                <option value="<?=$level['vipsettingcashbackruleId']?>"><?=$level['groupName'] . ' ' . $level['vipLevel']?></option>
                                            <?php }
?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div style="text-align:center;">
                            <input type="submit" class="btn btn-info btn-sm" value="<?=lang('lang.submit');?>">
                            <input type="reset" class="btn btn-default btn-sm" value="<?=lang('lang.reset');?>">
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7" id="player_details" style="display: none;"></div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#myTable').DataTable();
    });
</script>