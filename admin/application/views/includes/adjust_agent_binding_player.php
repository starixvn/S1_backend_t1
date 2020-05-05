<form id="bind-player-form" method="POST" action="<?=site_url($controller_name . '/resetBindingPlayer/' . $agent['agent_id'])?>" autocomplete="off">
    <div class="panel panel-primary">
        <div class="panel panel-body" id="player_panel_body">
            <input type="hidden" name="agent_name" value=<?=$agent['agent_name']?>>
            <div class="row">
                <?php if (count($players) > 0): ?>
                <div class="col-md-12">
                    <div class="input-group">
                        <label for="old_name">
                            <?=lang('Old Binding Player');?>
                        </label>
                        <input class="form-control" type="text" id="old_name" name="old_name" value="<?=$player?>" readonly>
                    </div>
                    <div class="input-group">
                        <label for="binding_player">
                            <?=lang('New Binding Player');?>
                        </label>
                        <!--
                        <input class="form-control" type="text" id="new_name" name="new_name" value="<?=$player?>">
                        -->
                        <?php echo form_dropdown('binding_player', is_array($players) ? $players: array(), $agent['binding_player_id'], ' class="form-control" id="binding_player" required="required"') ?>
                    </div>
                    <!--
                    <div class="input-group">
                        <label for="password"><font style="color:red;">*</font> <?=lang('reg.05');?></label>
                        <input type="password" name="password" id="password" class="form-control input-sm" data-toggle="tooltip" title="<?=lang('reg.a06');?>" required="required">
                        <span class="errors"><?php echo form_error('password'); ?></span>
                        <span id="error-password" class="errors"></span>
                    </div>
                    -->

                    <div class="col-md-11">
                        <center>
                            <span style="color:red;"><?=form_error('hidden_adjust_agent');?></span>
                        </center>
                    </div>
                </div>
                <?php else: ?>
                    <center>
                    <?php echo lang("agency.no.player") ?>
                    <center>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <center>
        <?php if (count($players) > 0): ?>
        <input type="hidden" name="hidden_adjust_agent"  class="form-control">
        <input type="submit" class="btn btn-primary submit_btn btn-sm" value="<?=lang('lang.reset');?>">
        <?php endif; ?>
        <a href="<?=site_url($controller_name . '/agent_information/' . $agent['agent_id'])?>" 
            class="btn btn-sm btn-warning btn-md" id="reset_password">
            <?=lang('lang.cancel');?>
        </a>
    </center>
</form>
