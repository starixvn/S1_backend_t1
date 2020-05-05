<?php
if(isset($is_edit) && $is_edit) {
    $verify_url = site_url('/agency/verify_edit_player/' . $player_id);
} else {
    $verify_url = site_url('/agency/verify_add_players');
}
?>
<div class="content-container">
    <div class="panel panel-primary">

        <div class="panel-heading">
            <h4><i class="icon-user-plus"></i> <?=$panel_heading?></h4>
        </div>

        <div class="panel-body" id="player_panel_body">
            <form action="<?=$verify_url?>" class="form-horizontal"
                id="verifyAddAccountProcess" method="POST" autocomplete="off">

                <!-- basic info {{{1 -->
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><font style="color:red;">*</font> <?=lang('Basic Info');?></h3>
                        </div>
                        <div class="panel-body">

                            <input type="hidden" name="agent_id" id="agent_id" value="<?=$agent_id?>">
                            <input type="hidden" name="registered_by" id="registered_by" value="<?=$registered_by?>">
                            <input type="hidden" name="typeOfPlayer" id="typeOfPlayer" value="real">

                            <div class="form-group form-group-sm">
                                <i class="col-md-offset-2 col-md-8 text-danger"><?=lang('reg.02')?></i>
                            </div>

                            <?php if(isset($is_edit) && $is_edit) { ?>
                                <div class="form-group form-group-sm">
                                    <label for="agent_name" class="col-md-2 control-label">
                                        <?=lang('Parent Agent Username')?>
                                    </label>
                                    <div class="col-md-3">
                                        <input type="text" name="agent_name" id="agent_name" class="form-control"
                                        value="<?=isset($parent_agent_name)? $parent_agent_name:'';?>" readonly />
                                    </div>
                                </div>
                            <?php } else { ?>
                            <input type="hidden" name="type_code" id="type_code" value="<?=$type_code?>">
                            <fieldset style="border-width:1px">
                                <div class="form-group form-group-sm">
                                    <label for="batch_add_players" class="col-md-2 control-label"></label>
                                    <div class="col-md-3">
                                        <input type="checkbox" id="batch_add_players" name="batch_add_players"
                                        onclick="enableCountOrNot()" value="1">
                                        <?=lang('Batch Add Players');?>
                                        <span class="help-block text-info batch_help_info">
                                            <?=lang('A number will be appended to the given name')?>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label for="count" class="col-md-2 control-label">
                                        <?=lang('Count')?>
                                        <span class="text-danger batch_help_info">*</span>
                                    </label>
                                    <div class="col-md-3">
                                        <input type="number" name="count" id="count" class="form-control" readonly
                                        min="1" required="required" onkeypress="return isNumberKey(event)">
                                        <span class="help-block text-warning batch_help_info">
                                            <?=lang('player.mp16')?>
                                        </span>
                                        <span class="errors"><?php echo form_error('count'); ?></span>
                                        <span id="error-count" class="errors"></span>
                                    </div>
                                    <label for="agent_name" class="col-md-2 control-label">
                                        <?=lang('Parent Agent Username')?>
                                    </label>
                                    <div class="col-md-3">
                                        <input type="text" name="agent_name" id="agent_name" class="form-control"
                                        value="<?=isset($parent_agent_name)? $parent_agent_name:'';?>" readonly />
                                    </div>
                                </div>
                            </fieldset>
                            <?php } ?>

                            <div class="form-group form-group-sm">
                                <label for="name" class="col-md-2 control-label">
                                    <?=lang('player.mp02')?>
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-3">
                                    <input type="text" name="username" id="username" class="form-control"
                                    <?php echo (isset($is_edit) && $is_edit)?'value="'.$player_details['username']. '" readonly="readonly"':''?>
                                    minlength="<?=$this->CI->utils->getConfig('default_min_size_username')?>"
                                    maxlength="<?=$this->CI->utils->getConfig('default_max_size_username')?>"
                                    required="required" >
                                </div>
                                <span class="errors"><?php echo form_error('name'); ?></span>
                                <span id="error-name" class="errors"></span>
                                <?php if ($this->utils->isEnabledFeature('enable_reset_player_password_in_agency') || (!isset($is_edit) || !$is_edit) ){ ?>
                                <label for="password" class="col-md-2 control-label">
                                    <?=lang('player.mp07')?> <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-3">
                                    <input type="password" name="password" id="password" class="form-control"
                                    <?php echo (isset($is_edit) && $is_edit)?'value="'.$password. '"':''?>
                                    minLength="<?=$this->CI->utils->getConfig('default_min_size_password')?>"
                                    maxLength="<?=$this->CI->utils->getConfig('default_max_size_password')?>"
                                    required="required">
                                </div>
                                <span class="errors"><?php echo form_error('password'); ?></span>
                                <span id="error-password" class="errors"></span>
                                <?php } ?>
                            </div>

                            <div class="form-group form-group-sm">
                                <?php if (isset($is_edit) && $is_edit) { ?>
                                <label for="base_credit" class="col-md-2 control-label">
                                    <?=lang('Base Credit')?> <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-3">
                                    <input type="number" name="base_credit" id="base_credit" class="form-control"
                                    value="<?=$base_credit?>" required="required">
                                </div>
                                <span class="errors"><?php echo form_error('base_credit'); ?></span>
                                <span id="error-base_credit" class="errors"></span>
                                <?php } else { ?>
                                <label for="bet_limit_template_id" class="col-md-2 control-label">
                                    <?=lang('Bet Limit Template')?>
                                </label>
                                <div class="col-md-3">
                                    <select name="bet_limit_template_id" id="bet_limit_template_id" class="form-control">
                                        <option value=""><?=lang('Default Bet Limit')?></option>
                                        <?php foreach ($bet_limit_templates as $bet_limit_template): ?>
                                        <option value="<?=$bet_limit_template['id']?>"
                                        <?=$bet_limit_template['default_template'] == 1 ? 'selected' : ''?>>
                                        <?=$bet_limit_template['template_name']?>
                                        </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <?php } ?>
                                <label for="language" class="col-md-2 control-label">
                                    <?=lang('system.word3')?> <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-3">
                                    <select name="language" id="language" class="form-control" required="required">
                                        <option value=""><?=lang('lang.select')?></option>
                                        <option value="English"
                                        <?=(isset($is_edit) && $is_edit && $player_account_info['language'] == 'English')? 'selected':''?>>
                                        English
                                        </option>
                                        <option value="Chinese"
                                        <?=(isset($is_edit) && $is_edit && $player_account_info['language'] == 'Chinese')? 'selected':''?>>
                                        Chinese
                                        </option>
                                        <option value="Korean"
                                        <?=(isset($is_edit) && $is_edit && $player_account_info['language'] == 'Korean')? 'selected':''?>>
                                        Korean
                                        </option>
                                        <option value="Thai"
                                        <?=(isset($is_edit) && $is_edit && $player_account_info['language'] == 'Thai')? 'selected':''?>>
                                        Thai
                                        </option>
                                        <option value="Indonesian"
                                        <?=(isset($is_edit) && $is_edit && $player_account_info['language'] == 'Indonesian')? 'selected':''?>>
                                        Indonesian
                                        </option>
                                        <option value="Vietnamese"
                                        <?=(isset($is_edit) && $is_edit && $player_account_info['language'] == 'Vietnamese')? 'selected':''?>>
                                        Vietnamese
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group form-group-sm">
                                <label for="description" class="col-md-2 control-label"><?=lang('player.mp04')?></label>
                                <div class="col-md-8">
                                    <textarea name="description" id="description" class="form-control" rows="5"></textarea>
                                </div>
                            </div>

                            <div class="form-group form-group-sm">
                                <span class="col-md-offset-3 col-md-8 text-danger" id="error"></span>
                            </div>
                        </div>
                    </div>
                </div> <!-- basic info }}}1 -->

                <!-- AGENCY GAME PLATFORM SETTINGS -->
                <?php if ($this->utils->isEnabledFeature('rolling_comm_for_player_on_agency') && $this->utils->isEnabledRollingCommByAgentInSession()): ?>
                <div class="col-md-12">
                    <?php if ($this->utils->isEnabledFeature('agent_tier_comm_pattern')) { ?>
                    <?=$this->load->view('includes/game_platform_settings_tier_comm', $game_platform_settings, TRUE)?>
                    <?php } else { ?>
                    <?=$this->load->view('includes/game_platform_settings', $game_platform_settings, TRUE)?>
                    <?php } ?>
                </div>
                <?php endif ?>
                <!-- END AGENCY GAME PLATFORM SETTINGS -->

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
<script>
$(document).ready(function(){
    var ajax_url = "<?=site_url('agency/add_player_validation_ajax')?>";
    var labels = '<?=json_encode($labels)?>';
    var fields = '<?=json_encode($fields)?>';
    add_player_form_validation(ajax_url, fields, labels);
});
</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of ajax_add_account_process.php
