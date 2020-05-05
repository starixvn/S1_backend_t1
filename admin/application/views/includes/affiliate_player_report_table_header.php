<?php
/**
 *   filename:   affiliate_player_report_table_header.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-08-07
 *   @brief:     table header for affiliate player report. used by view_player_report and invoice_page in both BO and UI.
 */
?>
                            <th><?=lang('#')?></th>
                            <th><?=lang('Player')?></th>
                            <?php if ($this->utils->isEnabledFeature('aff_show_real_name_on_reports')): ?>
                                <th><?=lang('Real Name')?></th>
                            <?php endif ?>
                            <th><?=lang('Affiliate')?></th>
                            <th><?=lang('report.pr03')?></th>
                            <th><?=lang('IP Address')?></th>
                            <?php if ($this->utils->isEnabledFeature('show_cashback_and_bonus_on_aff_player_report')): ?>
                                <th><?=lang('Total Cashback')?></th>
                                <th><?=lang('Total Bonus')?></th>
                            <?php endif ?>
                            <?php if ( ! $this->utils->isEnabledFeature('hide_deposit_and_withdraw_on_aff_player_report')): ?>
                                <th><?=lang('report.pr21')?></th>
                                <th><?=lang('report.pr22')?></th>
                            <?php endif ?>
                            <th><?=lang('Total Deposit - Total Withdrawal')?></th>
                        	<th><?=lang('Total Bet')?></th>
                        	<th><?=lang('Bet Result')?></th>
                            <?php if ( ! $this->utils->isEnabledFeature('hide_total_win_loss_on_aff_player_report')): ?>
	                            <th><?=lang('Total Win')?></th>
	                            <th><?=lang('Total Loss')?></th>
                            <?php endif ?>
                            <th><?=lang('Net Gaming')?></th>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of affiliate_player_report_table_header.php
