<form method="POST" action="<?=site_url('player_management/playerResetParentAgent/' . $player['playerId'])?>" autocomplete="off">
    <div class="panel panel-primary">
        <div class="panel panel-body" id="player_panel_body">
            <div class="row">
                <div class="col-md-12">
                    <div class="input-group">
                        <label for="old_name">
                            <?=lang('Old Agent Name');?>
                        </label>
                        <input class="form-control" type="text" id="old_name"
                        name="old_name" value="<?=$agent?>" readonly>
                    </div>
                    <div class="input-group">
                        <label for="agent_id">
                            <?=lang('New Agent Name');?>
                        </label>
                        <?php echo form_dropdown('agent_id', is_array($agents) ? $agents : array(), $player['agent_id'], ' class="form-control" id="agent_id" required="required"') ?>
                    </div>
                    <div class="col-md-11">
                        <center>
                            <span style="color:red;"><?=form_error('hidden_adjust_agent');?></span>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <center>
        <input type="hidden" name="hidden_adjust_agent"  class="form-control">
        <input type="submit" class="btn btn-primary submit_btn btn-sm" value="<?=lang('Set');?>">
        <a href="<?=site_url('player_management/userInformation/' . $player['playerId'])?>"
            class="btn btn-sm btn-warning btn-md" id="reset_password">
            <?=lang('lang.cancel');?>
        </a>
    </center>
</form>
<script>
    $(document).ready(function(){
        $('#agent_id').multiselect({
            enableFiltering: true,
            includeSelectAllOption: true,
            selectAllJustVisible: false,
            buttonWidth: '100%',

            buttonText: function(options, select) {
                if (options.length === 0) {
                    return '';
                }
                else {
                    var labels = [];
                    options.each(function() {
                        if ($(this).attr('label') !== undefined) {
                            labels.push($(this).attr('label'));
                        }
                        else {
                            labels.push($(this).html());
                        }
                    });
                    return labels.join(', ') + '';
                }
            }
        });
    })
</script>
