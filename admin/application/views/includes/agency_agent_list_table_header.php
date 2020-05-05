<?php
/**
 *   filename:   agency_agent_list_table_header.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-08-06
 *   @brief:     table header for agent list. used in both BO and agency UI
 */
?>
<th style="padding:10px">
	<?php if ( ! $this->utils->isEnabledFeature('agency_hide_sub_agent_list_action')): ?>
		<input type="checkbox" id="check_all_agents" class="agent-oper" onclick="checkAll(this.id)"/>
	<?php endif ?>
</th>
<th><?=lang('Agent Username');?></th>
<th><?=lang('Created On');?></th>
<th><?=lang('Credit Limit');?></th>
<th><?=lang('Available Credit');?></th>
<th><?=lang('Status');?></th>
<?php if($this->utils->getConfig('show_agency_rev_share_etc')){ ?>
<th><?=lang('Rev Share');?></th>
<th><?=lang('Rolling Comm');?></th>
<th><?=lang('Rolling Comm Basis');?></th>
<?php } ?>
<th><?=lang('Agent Level');?></th>
<!-- <th><?=lang('Agent Level Name');?></th> -->
<th><?=lang('Parent Agent');?></th>
<th><?=lang('Default Player VIP');?></th>
<th><?=lang('Settlement Period');?></th>
<th><?=lang('Action');?></th>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agency_agent_list_table_header.php
