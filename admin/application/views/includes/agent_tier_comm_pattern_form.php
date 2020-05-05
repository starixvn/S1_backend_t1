<?php
/**
 *   filename:   agent_tier_comm_pattern.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2017-11-11
 *   @brief:     view for creating and displaying agent tier commission patterns
 */
$validate_url = site_url('/' . $controller_name . '/save_tier_comm_pattern');
if (isset($is_edit) && $is_edit){
    $validate_url .= '/'. $is_edit;
}
?>
<div class = "container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title pull-left">
                <i class="fa fa-cog"></i> <?=lang('Agent Tier Commission Patterns');?>
            </h4>
            <div class="clearfix"></div>
        </div><!-- end panel-heading -->

        <div class="panel-body collapse in" id="agency_main_panel_body">
            <form id="form_option_1"  method="POST" action="<?=$validate_url?>">
                <?php if (isset($is_edit) && $is_edit): ?>
                <input type="hidden" name="pattern_id" value="<?=$conditions['pattern_id']?>" />
                <?php endif ?>
                <!-- fieldset commission-setting {{{3 -->
                <div class="col-md-12">
                    <label for="commission-setting">
                        <h3><?=lang('Commission Setting');?></h3>
                    </label>
                    <fieldset>
                        <div class="row">
                        <!-- input pattern_name (required) {{{4 -->
                        <div class="col-md-4">
                            <label for="pattern_name">
                                <font style="color:red;">*</font>
                                <?=lang('Pattern Name');?>
                            </label>

                            <input type="text" name="pattern_name" id="pattern_name" class="form-control "
                            minlength='3' maxlength='30'
                            value="<?=set_value('pattern_name', $conditions['pattern_name']);?>" data-toggle="tooltip"
                            title="<?=lang('Pattern Name');?>">

                            <span class="errors"><?php echo form_error('pattern_name'); ?></span>
                            <span id="error-pattern_name" class="errors"></span>
                        </div> <!-- input pattern_name (required) }}}4 -->
                        <!-- Select cal_method {{{4 -->
                        <div class="col-md-4">
                            <label for="cal_method">
                                <?=lang('Calculation Method');?>
                            </label>
                            <select name="cal_method" id="cal_method" class="form-control"
                                title="<?=lang('Select Calculation Method')?>">
                                <option value="0" <?php echo $conditions['cal_method'] == 0? 'selected':''?>>
                                <?=lang('Highest Attained');?>
                                </option>
                                <option value="1" <?php echo $conditions['cal_method'] == 1? 'selected':''?>>
                                <?=lang('Tier Independent');?>
                                </option>
                            </select>
                        </div> <!-- Select cal_method }}}4 -->
                        <!-- Select Rolling Comm Basis (required) {{{4 -->
                        <div class="col-md-4 ">
                            <label for="rolling_comm_basis">
                                <?=lang('Rolling Comm Basis');?>
                            </label>
                            <select name="rolling_comm_basis" id="rolling_comm_basis"
                                class="form-control" title="<?=lang('Select Rolling Comm Basis')?>" >
                                <option id="basis_total_bets" value="total_bets"
                                <?php echo $conditions['rolling_comm_basis'] == 'total_bets'?'selected':''?>>
                                <?=lang('Total Bet');?>
                                </option>
                                <option value="total_lost_bets"
                                <?php echo $conditions['rolling_comm_basis'] == 'total_lost_bets'?'selected':''?>>
                                <?=lang('Lost Bets');?>
                                </option>
                                <option value="winning_bets"
                                <?php echo $conditions['rolling_comm_basis'] == 'winning_bets'?'selected':''?>>
                                <?=lang('Winning Bets');?>
                                </option>
                                <option value="total_bets_except_tie_bets"
                                <?php echo $conditions['rolling_comm_basis'] == 'total_bets_except_tie_bets'?'selected':''?>>
                                <?=lang('Total Bets Except Tie Bets');?>
                                </option>
                            </select>
                            <span class="errors"><?php echo form_error('rolling_comm_basis'); ?></span>
                            <span id="error-rolling_comm_basis" class="errors"></span>
                        </div> <!-- Select Rolling Comm Basis (required) }}}4 -->
                </div>
                    <br>
                    </fieldset>
                </div> <!-- fieldset commission-setting }}}3 -->
                <!-- fieldset tiers {{{3 -->
                <div class="col-md-12">
                    <label for="commission-setting">
                        <h3><?=lang('Tiers Info');?></h3>
                    </label>
                    <fieldset>
                        <!-- input tier_count (required) {{{4 -->
                        <div class="row">
                        <div class="col-md-6">
                            <font style="color:red;">*</font>
                            <?=lang('Tier Count');?>

                            <input type="number" name="tier_count" id="tier_count" class="form-control "
                            value="<?=set_value('tier_count', $conditions['tier_count']);?>" data-toggle="tooltip"
                            title="<?=lang('number of tiers');?>">
                            <span class="errors"><?php echo form_error('tier_count'); ?></span>
                            <span id="error-tier_count" class="errors"></span>
                        </div> <!-- input tier_count (required) }}}4 -->
                        <div class="col-md-6">
                            <label for="set_tiers_button"> <?=lang(' ');?> </label>
                            <div id="set_tiers_button" class="row">
                                <input type="button" id="set_tiers" value="<?=lang('Set Tiers');?>" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>"
                                onclick="set_tiers_info()" />
                            </div>
                        </div>
                    </div>
                    <br>
                        <div class="col-md-12">
                            <table class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th><?=lang('Tier Index');?></th>
                                        <th><?=lang('Upper Bound');?></th>
                                        <th><?=lang('Rev Share');?></th>
                                        <th><?=lang('Rolling Comm');?></th>
                                    </tr>
                                </thead>
                                <tbody id='set_tiers_tbody'>
                                    <!--
                                    <tr>
                                        <td>
                                            <input type="number" id="tier_index" name="tier_index"
                                            class="form-control input-sm" value="0" readonly="readonly"/>
                                            <span id="error-tier_index" class="errors">
                                                <?php echo form_error("tier_index"); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <input type="number" id="upper_bound" name="upper_bound" step="any"
                                            class="form-control input-sm" value="0.00" />
                                            <span id="error-upper_bound" class="errors">
                                                <?php echo form_error("upper_bound"); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <input type="number" id="rev_share" name="rev_share" step="any"
                                            class="form-control input-sm"
                                            value="<?=set_value('rev_share', $conditions['rev_share'])?>"  />
                                            <span id="error-rev_share" class="errors">
                                                <?php echo form_error("rev_share"); ?>
                                            </span>
                                        </td>
                                    </tr>
-->
                                </tbody>
                            </table>
                        </div>
                    </fieldset>
                </div>
                <!-- fieldset tiers }}}3 -->
                <!-- fieldset Active Players {{{3 -->
                <div class="col-md-12">
                    <label for="commission-setting">
                        <h3><?=lang('Active Players');?></h3>
                    </label>
                    <fieldset>
                        <br>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon"><?php echo lang('Minimum Total Bets');?></div>
                                    <input type="text" class="form-control amount_only" name="min_bets"
                                    maxlength="10" value="<?=set_value('min_bets', $conditions['min_bets'])?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon"><?=lang('Minimum Deposit Per Player(Main Wallet)');?></div>
                                    <input type="text" class="form-control amount_only" name="min_trans"
                                    maxlength="15" value="<?=set_value('min_trans', $conditions['min_trans'])?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon"><?php echo lang('Minimum Active Players'); ?></div>
                                    <input type="text" class="form-control amount_only" maxlength="10"
                                    name="min_active_player_count"
                                    value="<?=set_value('min_active_player_count', $conditions['min_active_player_count'])?>"
                                    required />
                                    <div class="input-group-addon">#</div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <!-- fieldset tiers }}}3 -->
                <div class="col-md-12">
                    <br>
                    <div class="col-md-7">
                        <button type="submit" id="submit_pattern" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>">
                            <i class="fa fa-floppy-o"></i> <?=lang('sys.vu70');?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div><!-- end panel-body -->
    <!--div class="panel-footer"></div-->
</div>
<script>
function set_tiers_info(){
    var upper_bounds = JSON.parse('<?=json_encode(isset($upper_bounds)?$upper_bounds:[])?>');
    var rev_shares = JSON.parse('<?=json_encode(isset($rev_shares)?$rev_shares:[])?>');
    var rolling_comms = JSON.parse('<?=json_encode(isset($rolling_comms)?$rolling_comms:[])?>');
    var is_edit = '<?=(isset($is_edit) && $is_edit)? 1:0?>';
    var old_tier_count = '<?=isset($old_tier_count)?$old_tier_count:1?>';

    if(is_edit == '0') is_edit = false;

    // console.log('is_edit = ' + is_edit);
    // console.log('upper_bounds = ' + upper_bounds);

    var tier_count = $('#tier_count').val();
    var tbody = '';
    var v = 0;
    for (var i = 0; i < tier_count; i++) {
        var tier_index = '<td>'
        tier_index += '<input type="number" id="tier_index_' + i + '" name="tier_index[' + i + ']" class="form-control input-sm" value="' + i + '" readonly="readonly"/>';
        tier_index += '</td>';

        var upper_bound = '<td>';
        v = (is_edit && i < old_tier_count)?upper_bounds[i]:'0.00';
        // console.log('v = ' + v);
        upper_bound += '<input type="number" id="upper_bound_' + i + '" name="upper_bound[' + i + ']" step="any" class="form-control input-sm" value="' + v + '" />';
        upper_bound += '</td>';

        var rev_share = '<td><div class="input-group">';
        v = (is_edit && i < old_tier_count)?rev_shares[i]:'0.00';
        rev_share += '<input type="number" id="rev_share_' + i + '" name="rev_share[' + i + ']" step="any" class="form-control input-sm" value="' + v + '"  />';
        rev_share += '<div class="input-group-addon">%</div>';
        rev_share += '</div></td>';

        var rolling_comm = '<td><div class="input-group">'
        v = (is_edit && i < old_tier_count)?rolling_comms[i]:'0.00';
        rolling_comm += '<input type="number" id="rolling_comm_' + i + '" name="rolling_comm[' + i + ']" step="any" class="form-control input-sm" value="' + v + '"  />';
        rolling_comm += '<div class="input-group-addon">%</div>';
        rolling_comm += '</div></td>';
        tbody += '<tr>' + tier_index + upper_bound + rev_share + rolling_comm + '</tr>';
    }
    $('#set_tiers_tbody').html(tbody);
    return;
}

$(document).ready(function(){
    set_tiers_info();
});
</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agent_tier_comm_pattern.php
