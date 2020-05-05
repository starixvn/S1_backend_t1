<table class="table table-striped table-hover tablepress table-condensed" id="myTable">
	<thead>
		<tr>
			<th class="tableHeaderFont"><?=lang('system.word38');?></th>
			<th class="tableHeaderFont"><?=lang('system.word39');?></th>
			<th class="tableHeaderFont"><?=lang('pay.playerlev');?></th>
			<th class="tableHeaderFont"><?=lang('pay.amt');?></th>
			<th class="tableHeaderFont"><?=lang('pay.curr');?></th>
			<th class="tableHeaderFont"><?=lang('pay.depmethod');?></th>
			<th class="tableHeaderFont"><?=lang('pay.reqtime');?></th>
			<th class="tableHeaderFont"><?=lang('pay.transrefnumber');?></th>
			<th class="tableHeaderFont"><?=lang('pay.acctname');?></th>
			<th class="tableHeaderFont"><?=lang('pay.thirdpartacct');?></th>
			<!-- <th>Merchant Account</th> -->
			<th class="tableHeaderFont"><?=lang('pay.promoname');?></th>
			<th class="tableHeaderFont"><?=lang('pay.promobonus');?></th>
			<th class="tableHeaderFont"><?=lang('pay.deposit') . ' ' . lang('pay.ip');?></th>
			<th class="tableHeaderFont"><?=lang('pay.deposit') . ' ' . lang('pay.locatn');?></th>
			<!-- <th>Bonus Amount</th> -->
			<?php if ($this->session->userdata('dwStatus') == 'approved' || $this->session->userdata('dwStatus') == 'declined') {?>
				<th><?=lang('pay.procssby');?></th>
				<th><?=lang('pay.procsson');?></th>
			<?php	}
?>
			<th class="tableHeaderFont"><?=lang('pay.depslip');?></th>
			<th class="tableHeaderFont"><?=lang('lang.action');?></th>
		</tr>
	</thead>

	<tbody>
		<?php //var_dump($depositRequest);
if (!empty($depositRequest)) {
	foreach ($depositRequest as $depositRequest) {
		?>
					<tr>
						<td><?=$depositRequest['username'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['username']?></td>
						<td><?=$depositRequest['firstName'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : ucwords($depositRequest['firstName'] . ' ' . $depositRequest['lastName'])?></td>
						<td><?=$depositRequest['groupName'] . ' ' . $depositRequest['vipLevel']?></td>
						<td><?=$depositRequest['amount'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['amount']?></td>
						<td><?=$depositRequest['currency'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['currency']?></td>
						<td><?=$depositRequest['depositTo'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['depositTo']?></td>
						<td><?=$depositRequest['dwDateTime'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : mdate('%M %d, %Y %H:%i:%s', strtotime($depositRequest['dwDateTime']))?></td>
						<td><?=$depositRequest['transacRefCode'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['transacRefCode']?></td>
						<td><?=$depositRequest['depositorName'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['depositorName']?></td>
						<td><?=$depositRequest['depositorAccount'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['depositorAccount']?></td>
						<td><?=$depositRequest['promoName'] == '' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.promo") . '<i/>' : $depositRequest['promoName']?></td>
						<td><?=$depositRequest['bonusAmount'] == '' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.bonus") . '<i/>' : $depositRequest['bonusAmount']?></td>
						<td><?=$depositRequest['dwIp'] == '' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.bonus") . '<i/>' : $depositRequest['dwIp']?></td>
						<td><?=$depositRequest['dwLocation'] == ',' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.record") . '<i/>' : $depositRequest['dwLocation']?></td>
						<?php if ($depositRequest['dwStatus'] == 'approved' || $depositRequest['dwStatus'] == 'declined') {?>
									<td><?=$depositRequest['processedByAdmin'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : ucwords($depositRequest['processedByAdmin'])?></td>
									<td><?=$depositRequest['processDatetime'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : mdate('%M %d, %Y %H:%i:%s', strtotime($depositRequest['processDatetime']))?></td>
						<?php	}
		?>
						<td><?php if ($depositRequest['depositSlipName'] == '.') {?>
								<i class="help-block"><?=lang("lang.no") . " " . lang("pay.depslip");?><i/>
							<?php } else {?>
							<span class="btn-sm btn-default depositSlipBtn"  data-toggle="modal" data-target="#depositSlipModal">
								Open
							</span>
							<?php }
		?>
							<!-- start depositSlipModal-->
							<div class="row">
								<div class="modal fade" id="depositSlipModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content modal-content-three">
											<div class="modal-header">
												<a class='notificationRefreshList' href="<?=BASEURL . 'payment_management/refreshList/depositApproved'?>">
													<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?=lang("lang.close");?></span></button>
												</a>
												<h4 class="modal-title" id="myModalLabel"><i class="glyphicon glyphicon-list-alt"></i>&nbsp;<?=lang("pay.depslip");?></h4>
											</div>

											<div class="modal-body">

												<img id="banner_name" class="depositSlipImage" src="<?=IMAGEPATH_DEPOSITSLIP . $depositRequest['depositSlipName']?>" >
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- end depositSlipModal-->
						</td>
						<td>
						<?php if ($depositRequest['dwStatus'] == 'approved') {?>
							<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getManualThirdPartyDepositApproved(<?=$depositRequest['walletAccountId']?>)" data-toggle="modal" data-target="#approvedDetailsModal">
								<?=lang("lang.details");?>
							</span>

						<?php } elseif ($depositRequest['dwStatus'] == 'declined') {?>
							<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getManualThirdPartyDepositDeclined(<?=$depositRequest['walletAccountId']?>)" data-toggle="modal" data-target="#declinedDetailsModal">
								<?=lang("lang.details");?>
							</span>

						<?php } else {?>
							<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getManualThirdPartyDepositRequest(<?=$depositRequest['walletAccountId']?>,<?=$depositRequest['dwMethod']?>)" data-toggle="modal" data-target="#requestDetailsModal">
								<?=lang("lang.details");?>
							</span>
						<?php }
		?>
						</td>
					</tr>
		<?php
}
} else {?>
					<tr>
						<td colspan="18" style="text-align:center"><?=lang("lang.norec");?>
						</td>
					</tr>
		<?php	}
?>
	</tbody>
</table>

<div class="panel-footer">
	<ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links();?> </ul>
</div>