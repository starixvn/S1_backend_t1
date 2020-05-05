<table class="table table-striped table-hover tablepress table-condensed" id="myTable">
	<thead>
		<tr>
			<th class="tableHeaderFont"><?=lang('system.word38');?></th>
			<th class="tableHeaderFont"><?=lang('pay.realname');?></th>
			<th class="tableHeaderFont"><?=lang('pay.playerlev');?></th>
			<th class="tableHeaderFont"><?=lang('pay.amt');?></th>
			<th class="tableHeaderFont"><?=lang('pay.curr');?></th>
			<th class="tableHeaderFont"><?=lang('pay.depositTo');?></th>
			<!-- <th>Deposit Method</th> -->
			<th class="tableHeaderFont"><?=lang('pay.deptype');?></th>
			<th class="tableHeaderFont"><?=lang('pay.acctname');?></th>
			<th class="tableHeaderFont"><?=lang('pay.acctnumber');?></th>
			<!-- <th><?=lang('pay.branchname');?></th> -->
			<th class="tableHeaderFont"><?=lang('pay.reqtime');?></th>

			<th class="tableHeaderFont"><?=lang('pay.promoname');?></th>
			<th class="tableHeaderFont"><?=lang('pay.promobonus');?></th>
			<th class="tableHeaderFont"><?=lang('pay.deposit') . ' ' . lang('pay.ip');?></th>
			<th class="tableHeaderFont"><?=lang('pay.deposit') . ' ' . lang('pay.locatn');?></th>

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
$atts_popup = array(
	'width' => '1030',
	'height' => '600',
	'scrollbars' => 'yes',
	'status' => 'yes',
	'resizable' => 'no',
	'screenx' => '0',
	'screeny' => '0');

if (!empty($depositRequest)) {
	foreach ($depositRequest as $depositRequest) {
		?>
					<tr>
						<td><?=$depositRequest['playerName'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : ucwords($depositRequest['playerName'])?></td>
						<td><?=$depositRequest['firstName'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : ucwords($depositRequest['firstName']) . ' ' . ucwords($depositRequest['firstName'])?></td>
						<td><?=$depositRequest['groupName'] . ' ' . $depositRequest['vipLevel']?></td>
						<td><?=$depositRequest['amount'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $depositRequest['amount']?></td>
						<td><?=$depositRequest['currency'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $depositRequest['currency']?></td>
						<td><?=$depositRequest['depositedToBankName'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $depositRequest['depositedToBankName']?></td>
						<!-- <td><?=$depositRequest['paymentMethodName'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['paymentMethodName']?></td> -->
						<td><?=$depositRequest['localBankType'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $depositRequest['localBankType']?></td>
						<td><?=$depositRequest['bankAccountFullName'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $depositRequest['bankAccountFullName']?></td>
						<td><?=$depositRequest['bankAccountNumber'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $depositRequest['bankAccountNumber']?></td>
						<!-- <td><?=$depositRequest['branch'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $depositRequest['branch']?></td> -->
						<td><?=$depositRequest['dwDateTime'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : mdate('%M %d, %Y %H:%i:%s', strtotime($depositRequest['dwDateTime']))?></td>
						<!-- <td><?=$depositRequest['promoName'] == '' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.bonus") . '<i/>' : $depositRequest['promoName']?></td>											 -->
						<!-- <td><?=$depositRequest['depositSlipName'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : 'Open'?></td> -->

						<td><?=$depositRequest['promoName'] == '' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.promo") . '<i/>' : $depositRequest['promoName']?></td>
						<td><?=$depositRequest['bonusAmount'] == '' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.bonus") . '<i/>' : $depositRequest['bonusAmount']?></td>
						<td><?=$depositRequest['dwIp'] == '' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.bonus") . '<i/>' : $depositRequest['dwIp']?></td>
						<td><?=$depositRequest['dwLocation'] == ',' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $depositRequest['dwLocation']?></td>
						<?php if ($depositRequest['dwStatus'] == 'approved' || $depositRequest['dwStatus'] == 'declined') {?>
									<td><?=$depositRequest['processedByAdmin'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : ucwords($depositRequest['processedByAdmin'])?></td>
									<td><?=$depositRequest['processDatetime'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : mdate('%M %d, %Y %H:%i:%s', strtotime($depositRequest['processDatetime']))?></td>
						<?php	}
		?>
						<td><?php if ($depositRequest['depositSlipName'] == '.') {?>
								<i class="help-block"><?=lang("lang.no") . " " . lang("pay.depslip");?><i/>
							<?php } else {?>
							<span class="btn-sm btn-default depositSlipBtn" onclick="PaymentManagementProcess.setDepositSlipValue('<?=IMAGEPATH_DEPOSITSLIP . $depositRequest['depositSlipName']?>')" data-toggle="modal" data-target="#depositSlipModal">
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
							<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getDepositApprovedLocalBank(<?=$depositRequest['walletAccountId']?>,<?=$depositRequest['dwMethod']?>)" data-toggle="modal" data-target="#approvedDetailsModal">
								<?=lang("lang.details");?>
							</span>

						<?php } elseif ($depositRequest['dwStatus'] == 'declined') {?>
							<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getDepositDeclined(<?=$depositRequest['walletAccountId']?>,<?=$depositRequest['dwMethod']?>)" data-toggle="modal" data-target="#declinedDetailsModal">
								<?=lang("lang.details");?>
							</span>

						<?php } else {?>
							<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getDepositRequest(<?=$depositRequest['walletAccountId']?>,<?=$depositRequest['dwMethod']?>)" data-toggle="modal" data-target="#requestDetailsModal">
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