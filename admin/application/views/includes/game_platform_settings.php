<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><font style="color:red;">*</font> <?=lang('Commission Setting');?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <label class="control-label"><?=lang('Deposit Commission')?></label>
                <div class="input-group">
                    <input type="number" class="form-control" name="deposit_comm" id="deposit_comm" value="<?=set_value('deposit_comm', isset($agent) && isset($agent['deposit_comm']) ? number_format($agent['deposit_comm'],2) : 0);?>" step="any" min="0" required="required" <?php if (isset($view_only) && $view_only) echo 'disabled="disabled"' ?>/>
                    <div class="input-group-addon">%</div>
                </div>
                <span class="errors"><?php echo form_error('deposit_comm'); ?></span>
                <span id="error-deposit_comm" class="errors"></span>
            </div>
        </div>
        <br/>
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th><?=lang('Enabled');?></th>
                    <th><?=lang('Game Platform');?></th>
                    <th><?=lang('Game Type');?></th>
                    <?php if (!isset($controller_name) || $controller_name == 'agency_management'): # OGP-3607 ?>
                    <th><?=lang('Game Platform Fee');?></th>
                    <?php endif ?>
                    <?php if (!isset($is_player) || empty($is_player)) { ?>
                    <th><?=lang('Rev Share');?></th>
                    <?php } ?>
                    <th><?=lang('Rolling Comm Basis');?></th>
                    <th><?=lang('Rolling Comm Income');?></th>
                    <!-- <th><?=lang('Rolling Comm Out');?></th> -->
                    <th><?=lang('Bet Threshold');?></th>
                    <th><?=lang('Min Rolling Comm');?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($game_platform_list as $game_platform): ?>
                    <?php foreach ($game_platform['game_types'] as $index => $game_type): ?>
                        <tr>
                            <td>
                                <input type="hidden" name="game_types[<?=$game_type['id']?>][game_platform_id]" value="<?=$game_platform['id']?>"/>
                                <?php if ($index == 0): ?>
                                    <?php

                                        $game_platform_input = array(
                                            'id' => 'game-platform-'.$game_platform['id'],
                                            'name' => 'game_platforms['.$game_platform['id'].'][enabled]',
                                            'class' => 'game-platform',
                                            'data-game-platform-id' => $game_platform['id'],
                                            'value' => 1,
                                        );

                                        if (isset($view_only) && $view_only) {
                                            $game_platform_input['disabled'] = 'disabled';
                                        }

                                        if (isset($conditions['game_platforms'][$game_platform['id']])) {
                                            $game_platform_input['checked'] = 'checked';
                                        }

                                        echo form_checkbox($game_platform_input);

                                    ?>
                                <?php endif ?>
                            </td>
                            <td>
                                <?php if ($index == 0): ?>
                                    <label for="game-platform-<?=$game_platform['id']?>"><?=$game_platform['name']?></label>
                                <?php endif ?>
                            </td>
                            <td>
                                <?=lang($game_type['name'])?>
                            </td>
                            <?php if (!isset($controller_name) || $controller_name == 'agency_management'): # OGP-3607 ?>
                            <td>
                                <?php

                                    $id_platform_fee = "game_types-". $game_type['id'] ."-platform_fee";
                                    $name_platform_fee = "game_types[". $game_type['id'] ."][platform_fee]";

                                    $platform_fee_input = array(
                                        'type' => 'number',
                                        'id' => $id_platform_fee,
                                        'name' => $name_platform_fee,
                                        'class' => 'form-control input-sm platform-field-' . $game_platform['id'] . ' game-type-field-' . $game_type['id'],
                                        'min' => 0,
                                        'step' => 'any',
                                    );

                                    if (isset($view_only) && $view_only) {
                                        $platform_fee_input['disabled'] = 'disabled';
                                    }

                                    if (isset($conditions['game_types'][$game_type['id']]['platform_fee'])) {
                                        $platform_fee_input['value'] = number_format($conditions['game_types'][$game_type['id']]['platform_fee'], 2);
                                    } else {
                                        $platform_fee_input['value'] = '0.00';
                                    }

                                    echo form_input($platform_fee_input);

                                ?>
                                <span id="error-<?=$id_platform_fee?>" class="errors"><?php echo form_error($name_platform_fee); ?></span>
                            </td>
                            <?php endif ?>

                            <?php if (!isset($is_player) || empty($is_player)) { ?>
                            <td>
                                <?php

                                    $id_rev_share = "game_types-". $game_type['id'] ."-rev_share";
                                    $name_rev_share = "game_types[". $game_type['id'] ."][rev_share]";

                                    $rev_share_input = array(
                                        'type' => 'number',
                                        'id' => $id_rev_share,
                                        'name' => $name_rev_share,
                                        'class' => 'rev_share form-control input-sm platform-field-' . $game_platform['id'] . ' game-type-field-' . $game_type['id'],
                                        'min' => 0,
                                        'step' => 'any',
                                    );

                                    if (isset($view_only) && $view_only) {
                                        $rev_share_input['disabled'] = 'disabled';
                                    }

                                    if (isset($conditions['game_types'][$game_type['id']]['rev_share'])) {
                                        $rev_share_input['value'] = number_format($conditions['game_types'][$game_type['id']]['rev_share'], 2);
                                    } else if(isset($agent_level) && $agent_level == 0) {
                                        $rev_share_input['value'] = '100.00';
                                    } else {
                                        $rev_share_input['value'] = '0.00';
                                        #$rev_share_input['disabled'] = 'disabled';
                                    }

                                    if(isset($agent_level) && $this->utils->isEnabledFeature('set_rev_share_to_readonly_for_level_0_agent') && $agent_level == 0) {
                                        $rev_share_input['readonly'] = 'readonly';
                                    }

                                    if (((isset($parent_id) && $parent_id) || (isset($conditions['agent_level']) && $conditions['agent_level'] != 0)) && isset($conditions['game_types'][$game_type['id']]['max_rev_share'])) {
                                        $rev_share_input['max'] = $conditions['game_types'][$game_type['id']]['max_rev_share'];
                                    }

                                    echo form_input($rev_share_input);

                                ?>
                                <span id="error-<?=$id_rev_share?>" class="errors"><?php echo form_error($name_rev_share); ?></span>
                            </td>
                            <?php } ?>

                            <td>
                                <?php if ((isset($view_only) && $view_only) || (isset($parent_id) && $parent_id) || (isset($conditions['agent_level']) && $conditions['agent_level'] != 0)): ?>

                                    <?php

                                        $rolling_comm_basis_input = array(
                                            'type' => 'hidden',
                                            'class' => 'platform-field-' . $game_platform['id'],
                                            'name' => 'game_types['.$game_type['id'].'][rolling_comm_basis]',
                                        );

                                        if ( ! isset($conditions['game_platforms'][$game_platform['id']])) {
                                            $rolling_comm_basis_input['disabled'] = 'disabled';
                                        }

                                        if (isset($conditions['game_types'][$game_type['id']]['rolling_comm_basis'])) {
                                            $rolling_comm_basis_input['value'] = $conditions['game_types'][$game_type['id']]['rolling_comm_basis'];
                                        } else {
                                            $rolling_comm_basis_input['value'] = 'total_bets_except_tie_bets';
                                        }

                                        echo form_input($rolling_comm_basis_input);

                                        $rolling_comm_basis_input['type'] = 'text';
                                        $rolling_comm_basis_input['class'] = 'form-control input-sm';
                                        $rolling_comm_basis_input['value'] = lang($rolling_comm_basis_input['value']);
                                        $rolling_comm_basis_input['readonly'] = 'readonly';
                                        unset($rolling_comm_basis_input['name']);
                                        echo form_input($rolling_comm_basis_input);

                                    ?>

                                <?php else: ?>
                                    <select name="game_types[<?=$game_type['id']?>][rolling_comm_basis]" class="form-control input-sm platform-field-<?=$game_platform['id']?>" <?=isset($view_only) && $view_only ? 'disabled="disabled"' : ''?>>
                                        <option value="total_bets_except_tie_bets"
                                        <?=(isset($conditions['game_types'][$game_type['id']]['rolling_comm_basis']) && $conditions['game_types'][$game_type['id']]['rolling_comm_basis'] == 'total_bets_except_tie_bets') ? 'selected':''?> >
                                        <?=lang('Bets Except Tie Bets');?>
                                        </option>
                                        <option id="basis_total_bets" value="total_bets"
                                        <?=(isset($conditions['game_types'][$game_type['id']]['rolling_comm_basis']) && $conditions['game_types'][$game_type['id']]['rolling_comm_basis'] == 'total_bets') ? 'selected':''?> >
                                        <?=lang('Total Bet');?>
                                        </option>
                                        <option id="basis_total_bets" value="winning_bets"
                                        <?=(isset($conditions['game_types'][$game_type['id']]['rolling_comm_basis']) && $conditions['game_types'][$game_type['id']]['rolling_comm_basis'] == 'winning_bets') ? 'selected':''?> >
                                        <?=lang('Winning Bets');?>
                                        </option>
                                        <option value="total_lost_bets"
                                        <?=(isset($conditions['game_types'][$game_type['id']]['rolling_comm_basis']) && $conditions['game_types'][$game_type['id']]['rolling_comm_basis'] == 'total_lost_bets') ? 'selected':''?> >
                                        <?=lang('Lost Bets');?>
                                        </option>
                                        <option value="total_lost_amount"
                                        <?=(isset($conditions['game_types'][$game_type['id']]['rolling_comm_basis']) && $conditions['game_types'][$game_type['id']]['rolling_comm_basis'] == 'total_lost_amount') ? 'selected':''?> >
                                        <?=lang('Lost Amount');?>
                                        </option>
                                    </select>
                                <?php endif ?>
                            </td>
                            <td>
                                <?php

                                    $id_rolling_comm = "game_types-". $game_type['id'] ."-rolling_comm";
                                    $name_rolling_comm = "game_types[". $game_type['id'] ."][rolling_comm]";

                                    $rolling_comm_input = array(
                                        'type' => 'number',
                                        'id' => $id_rolling_comm,
                                        'name' => $name_rolling_comm,
                                        'class' => 'rolling_comm form-control input-sm platform-field-' . $game_platform['id'] . ' game-type-field-' . $game_type['id'],
                                        'min' => 0,
                                        'step' => 'any',
                                    );

                                    if (isset($view_only) && $view_only) {
                                        $rolling_comm_input['disabled'] = 'disabled';
                                    }

                                    if (isset($conditions['game_types'][$game_type['id']]['rolling_comm'])) {
                                        $rolling_comm_input['value'] = number_format($conditions['game_types'][$game_type['id']]['rolling_comm'], 2);
                                    } else {
                                        $rolling_comm_input['value'] = '0.00';
                                        #$rolling_comm_input['disabled'] = 'disabled';
                                    }

                                    if (((isset($parent_id) && $parent_id) || (isset($conditions['agent_level']) && $conditions['agent_level'] != 0)) && isset($conditions['game_types'][$game_type['id']]['max_rolling_comm'])) {
                                        $rolling_comm_input['max'] = $conditions['game_types'][$game_type['id']]['max_rolling_comm'];
                                    }

                                    echo form_input($rolling_comm_input);

                                ?>
                                <span id="error-<?=$id_rolling_comm?>" class="errors"><?php echo form_error($name_rolling_comm); ?></span>
                            </td>
                            <td>
                                <?php

                                    $id_bet_threshold = "game_types-". $game_type['id'] ."-bet_threshold";
                                    $name_bet_threshold = "game_types[". $game_type['id'] ."][bet_threshold]";

                                    $bet_threshold_input = array(
                                        'type' => 'number',
                                        'id' => $id_bet_threshold,
                                        'name' => $name_bet_threshold,
                                        'class' => 'form-control input-sm platform-field-' . $game_platform['id'] . ' game-type-field-' . $game_type['id'],
                                        'min' => 0,
                                        'step' => 'any',
                                    );

                                    if (isset($view_only) && $view_only) {
                                        $bet_threshold_input['disabled'] = 'disabled';
                                    }

                                    if (isset($conditions['game_types'][$game_type['id']]['bet_threshold'])) {
                                        $bet_threshold_input['value'] = number_format($conditions['game_types'][$game_type['id']]['bet_threshold'], 0);
                                    } else {
                                        $bet_threshold_input['value'] = '0';
                                        #$bet_threshold_input['disabled'] = 'disabled';
                                    }

                                    if (((isset($parent_id) && $parent_id) || (isset($conditions['agent_level']) && $conditions['agent_level'] != 0)) && isset($conditions['game_types'][$game_type['id']]['max_bet_threshold'])) {
                                        $bet_threshold_input['max'] = $conditions['game_types'][$game_type['id']]['max_bet_threshold'];
                                    }

                                    echo form_input($bet_threshold_input);

                                ?>
                                <span id="error-<?=$id_bet_threshold?>" class="errors"><?php echo form_error($name_bet_threshold); ?></span>
                            </td>
                            <td>
                                <?php

                                    $id_min_rolling_comm = "game_types-". $game_type['id'] ."-min_rolling_comm";
                                    $name_min_rolling_comm = "game_types[". $game_type['id'] ."][min_rolling_comm]";

                                    $min_rolling_comm_input = array(
                                        'type' => 'number',
                                        'id' => $id_min_rolling_comm,
                                        'name' => $name_min_rolling_comm,
                                        'class' => 'form-control input-sm platform-field-' . $game_platform['id'] . ' game-type-field-' . $game_type['id'],
                                        'min' => 0,
                                        'step' => 'any',
                                    );

                                    if (isset($view_only) && $view_only) {
                                        $min_rolling_comm_input['disabled'] = 'disabled';
                                    }

                                    if (isset($conditions['game_types'][$game_type['id']]['min_rolling_comm'])) {
                                        $min_rolling_comm_input['value'] = number_format($conditions['game_types'][$game_type['id']]['min_rolling_comm'], 0);
                                    } else {
                                        $min_rolling_comm_input['value'] = '0.00';
                                    }

                                    echo form_input($min_rolling_comm_input);

                                ?>
                                <span id="error-<?=$id_min_rolling_comm?>" class="errors"><?php echo form_error($name_min_rolling_comm); ?></span>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<script>
$(document).ready(function(){
    var els = document.getElementsByClassName('game-platform');
    //console.log(els);
    //console.log(els.length);
    for (var i = 0; i < els.length; i++) {
        var el = els[i];
        if(! el.checked) {
            var game_platform_id = el.dataset.gamePlatformId;
            $('.platform-field-' + game_platform_id).prop('disabled', ! this.checked).trigger('change');
        }
    }

    $('.game-platform').change(function() {
        var game_platform_id = $(this).data('gamePlatformId');
        $('.platform-field-' + game_platform_id).prop('disabled', ! this.checked).trigger('change');
    });
});
</script>
