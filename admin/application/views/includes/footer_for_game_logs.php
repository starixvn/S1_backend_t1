<?php
    $colspan_value = 9;
    $summary_colspan_value = 28;
    if ($this->utils->isEnabledFeature('aff_show_real_name_on_reports')) {
        $colspan_value++;
        $summary_colspan_value++;
    }

    if (!$this->utils->isEnabledFeature('close_aff_and_agent')) {
        $colspan_value++;
        $summary_colspan_value++;
    }

    if ($this->utils->isEnabledFeature('show_agent_name_on_game_logs')) {
        $colspan_value++;
        $summary_colspan_value++;
    }

    $footer_cols1 = 9;
    $footer_cols2 = 25;
    if( $this->utils->isEnabledFeature('show_sports_game_columns_in_game_logs') ){
        $footer_cols1+=4;
        $footer_cols2+=4;
    }
    if( $this->utils->isEnabledFeature('show_bet_time_column') ){
        $footer_cols1++;
        $footer_cols2++;
    }
    if( $this->utils->isEnabledFeature('enabled_show_rake') ){
        $footer_cols1++;
        $footer_cols2++;
    }

?>
<tr>
    <th style="text-align: right" colspan="<?=$colspan_value?>"><?=lang('Total')?></th>
    <th style="text-align: right"><span class="real-bet-total">0.00</span></th>
    <th style="text-align: right"><span class="bet-total">0.00</span></th>
    <th style="text-align: right"><span class="result-total">0.00</span></th>
    <th style="text-align: right"><span class="bet-result-total">0.00</span></th>
    <th style="text-align: right"><span class="win-total">0.00</span></th>
    <th style="text-align: right"><span class="loss-total">0.00</span></th>
    <th colspan="<?=$footer_cols1?>"></th>
</tr>
<tr>
    <th colspan="<?=$summary_colspan_value?>" style="text-align: right;">
		<?=lang('cms.totalBetAmount');?>: <span class="bet-total">0.00</span><br>
		<?=lang('cms.totalResultAmount');?>: <span class="result-total">0.00</span><br>
    	<?=lang('Total Bet + Result Amount');?>: <span class="bet-result-total">0.00</span><br>
		<?=lang('Total Win');?>: <span class="win-total">0.00</span><br>
		<?=lang('Total Loss');?>: <span class="loss-total">0.00</span><br>
		<?=lang('Average Bet');?>: <span class="ave-bet-total">0.00</span><br>
		<?=lang('Total Number of Bets');?>: <span class="bet-count-total">0</span>
	</th>
</tr>
<tr>
	<td colspan="<?=$footer_cols2?>" id="platform-summary" style="text-align: right;"></td>
</tr>
