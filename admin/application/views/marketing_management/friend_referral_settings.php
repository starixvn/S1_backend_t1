
<div class="row">
	<div class="col-md-8">
		<div class="panel panel-primary">

			<div class="panel-heading">
				<h4 class="panel-title">
					<i class="fa fa-user-plus"></i> <?=lang('mark.friendrefset')?>
				</h4>
			</div>

			<div class="panel-body">
				<form class="form-horizontal" id="saveFriendReferralSettings" method="post" autocomplete="off">
					<table class="table">
						<thead>
							<tr>
								<th colspan="2"><?=lang('con.referrerBonus')?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td nowrap="nowrap"><?=lang('mark.bonusamt')?> <span class="text-danger">*</span></td>
								<td align="right" class="form-inline">
									<input type="text" class="form-control text-right number_only" name="bonus_amount" id="bonus_amount" required="required" value="<?=$this->utils->safeGetArray($friend_referral_settings, 'bonusAmount', '0.00')?>">
								</td>
							</tr>

							<?php if($this->utils->isEnabledFeature('enable_friend_referral_cashback')){?>
								<tr>
									<td nowrap="nowrap"><?=lang('Cashback Rate')?> (%) <span class="text-danger">*</span></td>
									<td align="right" class="form-inline">
										<input type="text" class="form-control text-right number_only" name="cashback_rate" id="cashback_rate" required="required" value="<?=$this->utils->safeGetArray($friend_referral_settings, 'cashback_rate', '0.00')?>">
									</td>
								</tr>
							<?php }?>

						</tbody>
					</table>
					<table class="table">
						<thead>
							<tr>
								<th colspan="2"><?=lang('con.referredReq')?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td nowrap="nowrap"><?=lang('con.minBet')?> <span class="text-danger">*</span></td>
								<td align="right" class="form-inline">
									<input type="text" class="form-control text-right number_only" name="bet" id="bet" required="required" value="<?=$this->utils->safeGetArray($friend_referral_settings, 'ruleInBet', '0.00')?>">
								</td>
							</tr>
							<tr>
								<td nowrap="nowrap" style="border-top: none;"><?=lang('con.minDep')?> <span class="text-danger">*</span></td>
								<td align="right" class="form-inline" style="border-top: none;">
									<input type="text" class="form-control text-right number_only" name="deposit" id="deposit" required="required" value="<?=$this->utils->safeGetArray($friend_referral_settings, 'ruleInDeposit', '0.00')?>">
								</td>
							</tr>
						</tbody>
					</table>
					<table class="table">
						<thead>
							<tr>
								<th colspan="2"><?=lang('con.referrerReq')?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td nowrap="nowrap"><?=lang('con.minBetWithdraw')?></td>
								<td align="right" class="form-inline">
									<input type="text" class="form-control text-right number_only" name="withdraw_condition" id="withdraw_condition" required="required" value="<?=$this->utils->safeGetArray($friend_referral_settings, 'withdrawalCondition', '0.00')?>">
									<div class="help-block"><?=lang('con.sinceRef')?></div>
								</td>
							</tr>
						</tbody>
					</table>
					<?php if ($this->utils->isEnabledFeature('bind_promorules_to_friend_referral')) {
	?>
					<table class="table">
						<thead>
							<tr>
								<th colspan="2"><?=lang('Bind Promo CMS')?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td nowrap="nowrap"><?=lang('Select Promo')?></td>
								<td align="right">
                                    <select class="form-control" name="promo_cms_id" id="promoCmsId" style="width:205px" >
                                        <option value="">-----<?php echo lang('N/A'); ?>-----</option>
                                        <?php
if (!empty($promoCms)) {
		foreach ($promoCms as $v): ?>
                                                <option value="<?php echo $v['promoCmsSettingId']; ?>" <?php echo $v['promoCmsSettingId'] == $this->utils->safeGetArray($friend_referral_settings, 'promo_id') ? "selected" : "" ?>><?php echo $v['promoName'] ?></option>
                                            <?php endforeach;
	}?>
                                    </select>
								</td>
							</tr>
						</tbody>
					</table>
					<?php }?>
				</form>
			</div>

			<div class="panel-footer text-right">
				<button type="submit" form="saveFriendReferralSettings" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary' ?>"><?=lang('lang.save')?></button>
			</div>

		</div>
	</div>
</div>

<script>
$(document).ready(function () {

//submenu
        $('#collapseSubmenu').addClass('in');
        $('#friendReferralSettings').addClass('active');
});
</script>