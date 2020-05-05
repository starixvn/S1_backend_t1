<th class="hidden-col"><?=lang('ID');?></th>
<th class="test"><?=lang('Bet Date');?></th>
<th class="test"><?=lang('Transaction Date / Payout Date');?></th>
<th class="test"><?=lang('Updated Date');?></th>
<th id="col-player-username" class="test hidden-col"><?=lang('Player Username');?></th>

<?php if ($this->utils->isEnabledFeature('aff_show_real_name_on_reports')): ?>
    <th class="test"><?=lang('Real Name');?></th>
<?php endif ?>

<?php if (!$this->utils->isEnabledFeature('close_aff_and_agent')): ?>
    <th class="test"><?=lang('Affiliate Username');?></th>
<?php endif ?>

<?php if ($this->utils->isEnabledFeature('show_agent_name_on_game_logs')): ?>
    <th class="test"><?=lang('Agent Username');?></th>
<?php endif ?>

<th class="test"><?=lang('Player Level')?></th>
<th><?=lang('cms.gameprovider');?></th>
<th><?=lang('cms.gametype');?></th>
<th><?=lang('cms.gamename');?></th>
<th id="col-real-bet" class="right-text-col"><?=lang('Real Bet');?></th>
<th id="col-available-bet" class="right-text-col"><?=lang('Valid Bet');?></th>
<th id="col-result-amt" class="right-text-col"><?=lang('mark.resultAmount');?></th>
<th class="right-text-col"><?=lang('lang.bet.plus.result');?></th>
<th class="right-text-col"><?=lang('Win Amount'); ?></th>
<th class="right-text-col"><?=lang('Loss Amount'); ?></th>
<th class="right-text-col"><?=lang('mark.afterBalance');?></th>
<th class="right-text-col"><?=lang('pay.transamount');?></th>
<th><?=lang('Round No'); ?></th>
<th><?=lang('player.ut12');?></th>
<th><?=lang('Bet Detail');?></th>

<?php if($this->utils->isEnabledFeature('show_bet_time_column')): ?>
    <th><?=lang('player.ug06');?></th>
<?php endif; ?>

<th id="flag_col" class="hidden-col"><?=lang('player.ut10');?></th>
<th id="game_provider_id_col" class="hidden-col"><?=lang('gameproviderId');?></th>
<th><?=lang('Bet Type');?></th>

<?php if($this->utils->isEnabledFeature('show_sports_game_columns_in_game_logs')): ?>
    <th><?=lang('Match Type');?></th>
    <th><?=lang('Match Details');?></th>
    <th><?=lang('Handicap');?></th>
    <th><?=lang('Odds');?></th>
<?php endif; ?>

<?php if($this->utils->isEnabledFeature('enabled_show_rake')): ?>
    <th><?=lang('Rake');?></th>
<?php endif; ?>

<th><?=lang('Status');?></th>

<?php if ($this->utils->isEnabledFeature('hide_free_spin_on_game_history')): ?>
    <th><?=lang('Code');?></th>
<?php endif; ?>