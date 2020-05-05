
<tr>
	<th><?=lang('Sub Total')?></th>
	<th colspan="12" ></th>
	<?php if($this->utils->isEnabledFeature('show_risk_score')): ?>
	<th></th>
    <?php endif?>
	<?php if($this->utils->isEnabledFeature('show_kyc_status')): ?>
    <th></th>
    <?php endif?>
	<?php if( !$this->utils->isEnabledFeature('close_aff_and_agent') && $this->utils->isEnabledFeature('show_search_affiliate')): ?>
	<th></th>
    <?php endif?>
    <?php if( !$this->utils->isEnabledFeature('close_aff_and_agent')): ?>
    <th></th>
    <?php endif?>
	<th><span class="sub-total-deposit-bonus">0.00</span></th>
	<th><span class="sub-total-cashback-bonus">0.00</span></th>
	<th><span class="sub-total-referral-bonus">0.00</span></th>
	<th><span class="sub-total-manual-bonus">0.00</span></th>
	<th><span class="sub-total-subtract-bonus">0.00</span></th>
	<th><span class="sub-total-bonus-add">0.00</span></th>
	<th><span class="sub-total-firstdeposits">0.00</span></th>
	<th><span class="sub-total-second-deposit">0.00</span></th>
	<th><span class="sub-total-deposit-add">0.00</span></th>
	<th><span class="sub-total-deposit-times-add">0.00</span></th>
	<th><span class="sub-total-withdrawal-add">0.00</span></th>
	<th><span class="sub-total-dw-add">0.00</span></th>
	<th><span class="sub-total-bets-add">0.00</span></th>
	<th><span class="sub-total-payout-add">0.00</span></th>
	<th><span class="sub-total-payout-rate">0.00%</span></th>
	<th ></th>
</tr>


<!--TODO NOT POSSIBLE NOW DUE TOTALS NOT TALLYING-->
<tr>
	<th class="text-primary"><?=lang('Total')?></th>
	<th colspan="12" ></th>
	<?php if($this->utils->isEnabledFeature('show_risk_score')): ?>
	<th></th>
    <?php endif?>
	<?php if($this->utils->isEnabledFeature('show_kyc_status')): ?>
    <th></th>
    <?php endif?>
	<?php if( !$this->utils->isEnabledFeature('close_aff_and_agent') && $this->utils->isEnabledFeature('show_search_affiliate')): ?>
	<th></th>
    <?php endif?>
    <?php if( !$this->utils->isEnabledFeature('close_aff_and_agent')): ?>
    <th></th>
    <?php endif?>
	<th class="text-right"><span class="total-cashback-bonus text-primary">0.00</span></th>
	<th class="text-right"><span class="total-deposit-bonus text-primary">0.00</span></th>
	<th class="text-right"><span class="total-referral-bonus text-primary">0.00</span></th>
	<th class="text-right"><span class="total-manual-bonus text-primary">0.00</span></th>
	<th class="text-right"><span class="total-subtract-bonus text-primary">0.00</span></th>
	<th class="text-right"><span class="total-bonus-add text-primary">0.00</span></th>
	<th class="text-right"><span class="total-firstdeposits text-primary">0.00</span></th>
	<th class="text-right"><span class="total-second-deposit text-primary">0.00</span></th>
	<th class="text-right"><span class="total-deposit-add text-primary">0.00</span></th>
	<th class="text-right"><span class="total-deposit-times-add text-primary">0.00</span></th>
	<th class="text-right"><span class="total-withdrawal-add text-primary">0.00</span></th>
	<th class="text-right"><span class="total-dw-add text-primary">0.00</span></th>
	<th class="text-right"><span class="total-bets-add text-primary">0.00</span></th>
	<th class="text-right"><span class="total-payout-add text-primary">0.00</span></th>
	<th class="text-right"><span class="total-payout-rate text-primary">0.00%</span></th>
	<th ></th>
</tr>