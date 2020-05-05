<div class="container">
    <div class="panel panel-primary">

        <div class="panel-heading">
            <h4><i class="icon-user-plus"></i> <?=lang('Edit Players')?></h4>
        </div>

        <div class="panel-body" id="player_panel_body">
            <form action="<?=BASEURL.'agency/verify_edit_player/'. $player_id ?>" class="form-horizontal" 
                id="verifyAddAccountProcess" method="POST" autocomplete="off" onsubmit="verifyAddAccountProcess()">

                <input type="hidden" name="agent_id" id="agent_id" value="<?=$agent_id?>">

                <div class="form-group form-group-sm">
                    <label for="name" class="col-md-3 control-label">
                        <?=lang('player.mp02')?> 
                    </label>
                    <div class="col-md-8">
                        <input type="text" name="name" id="name" class="form-control" required="required" 
                        value="<?=$player_details['username']?>" readonly>
                    </div>
                    <span class="errors"><?php echo form_error('name'); ?></span>
                    <span id="error-name" class="errors"></span>
                </div>

                <div class="form-group form-group-sm">
                    <label for="password" class="col-md-3 control-label"><?=lang('player.mp07')?> <span class="text-danger">*</span></label>
                    <div class="col-md-8">
                        <input type="password" name="password" id="password" class="form-control" 
                        value="<?=$password?>" minLength="6" maxLength="20" required="required">
                    </div>
                    <span class="errors"><?php echo form_error('password'); ?></span>
                    <span id="error-password" class="errors"></span>
                </div>
                <div class="form-group form-group-sm">
                    <label for="rolling_comm" class="col-md-3 control-label">
                        <?=lang('Rolling Comm')?> 
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-8">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="rolling_comm" name="rolling_comm" 
                            value="<?=set_value('rolling_comm', $player_details['rolling_comm'])?>" title="<?=lang('Input a number between 0~3')?>" />
                            <div class="input-group-addon">%</div>
                        </div>
                        <span class="errors"><?php echo form_error('rolling_comm'); ?></span>
                        <span id="error-rolling_comm" class="errors"></span>
                    </div>
                </div>
              <div class="form-group form-group-sm">
                    <label for="base_credit" class="col-md-3 control-label"><?=lang('Base Credit')?> <span class="text-danger">*</span></label>
                    <div class="col-md-8">
                        <input type="number" name="base_credit" id="base_credit" class="form-control"
                        value="<?=$base_credit?>" required="required">
                    </div>
                    <span class="errors"><?php echo form_error('base_credit'); ?></span>
                    <span id="error-base_credit" class="errors"></span>
                </div>
                <div class="form-group form-group-sm">
                    <label for="agent_name" class="col-md-3 control-label"><?=lang('Parent Agent Username')?></label>
                    <div class="col-md-8">
                        <input type="text" name="agent_name" id="agent_name" class="form-control" 
                        value="<?=isset($parent_agent_name)? $parent_agent_name:'';?>" readonly />
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="language" class="col-md-3 control-label"><?=lang('system.word3')?> <span class="text-danger">*</span></label>
                    <div class="col-md-8">
                        <select name="language" id="language" class="form-control" required="required">
                            <option value=""><?=lang('lang.select')?></option>
                            <option value="English" <?=($player_account_info['language'] == 'English')? 'selected':''?>>English</option>
                            <option value="Chinese" <?=($player_account_info['language'] == 'Chinese')? 'selected':''?>>Chinese</option>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="description" class="col-md-3 control-label"><?=lang('player.mp04')?></label>
                    <div class="col-md-8">
                        <textarea name="description" id="description" class="form-control" rows="5"></textarea>
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <span class="col-md-offset-3 col-md-8 text-danger" id="error"></span>
                </div>

                <div class="form-group form-group-sm">
                    <div class="col-md-offset-6 col-md-8">
                        <button type="submit" class="btn btn-primary"><?=lang('lang.save')?></button>
                    </div>
                </div>

            </form>
        </div>
        <div class="panel-footer"></div>
    </div>
</div>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of ajax_add_account_process.php
