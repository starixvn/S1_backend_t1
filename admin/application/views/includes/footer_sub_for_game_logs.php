<?php
    $colspan_value = 9;
    if ($this->utils->isEnabledFeature('aff_show_real_name_on_reports')) {
        $colspan_value++;
    }

    if (!$this->utils->isEnabledFeature('close_aff_and_agent')) {
        $colspan_value++;
    }

    if ($this->utils->isEnabledFeature('show_agent_name_on_game_logs')) {
        $colspan_value++;
    }
    $cols = 9;
    if( $this->utils->isEnabledFeature('show_sports_game_columns_in_game_logs') ){
        $cols+=4;
    }
    if( $this->utils->isEnabledFeature('show_bet_time_column') ){
        $cols++;
    }
    if( $this->utils->isEnabledFeature('enabled_show_rake') ){
        $cols++;
    }
?>
<tr>
    <th style="text-align: right" colspan="<?=$colspan_value?>"><?=lang('Sub Total')?></th>
    <th style="text-align: right"><span class="sub-real-bet-total">0.00</span></th>
    <th style="text-align: right"><span class="sub-bet-total">0.00</span></th>
    <th style="text-align: right"><span class="sub-result-total">0.00</span></th>
    <th style="text-align: right"><span class="sub-bet-result-total">0.00</span></th>
    <th style="text-align: right"><span class="sub-win-total">0.00</span></th>
    <th style="text-align: right"><span class="sub-loss-total">0.00</span></th>
    <th colspan="<?=$cols?>"></th>
</tr>