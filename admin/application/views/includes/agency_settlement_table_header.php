<?php
/**
 *   filename:   agency_settlement_table_header.php
 *   author:     William Zhang
 *   date:       2016-08-01
 *   @brief:     table header in agency settlement page
 */

?>
                            <th><?=lang('Agent Username');?></th>
                            <th><?=lang('Game Platform');?></th>
                            <th><?=lang('Game Type');?></th>
                            <th><?=lang('Status');?></th>
                            <th><?=lang('Settlement Period');?></th>
                            <th><?=lang('Date Range');?></th>
                            <th><?=lang('Rev Share');?></th>
                            <th><?=lang('Bets');?></th>
                             <th><?=lang('Net Gaming');?></th>
 <?php if($this->utils->isEnabledFeature('rolling_comm_for_player_on_agency')){?>
                            <th><?=lang('Rolling Comm Out');?></th>
                            <th><?=lang('Rolling Comm Income');?></th>
                            <th><?=lang('Rolling Comm Rate');?></th>
                            <th><?=lang('Rolling Comm Payment Status');?></th>
<?php }?>
                            <th><?=lang('My Earning');?></th>
                            <th><?=lang('Current Amt Payable');?></th>
                            <th><?=lang('Actual Amt Payable');?></th>
                            <th><?=lang('Balance');?></th>
                            <th><?=lang('Total Amt Payable');?></th>
                            <th><?=lang('Parent Agent Username');?></th>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agency_settlement_table_header.php
