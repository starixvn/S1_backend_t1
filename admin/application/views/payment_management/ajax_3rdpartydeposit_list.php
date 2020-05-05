<table class="table table-striped table-hover tablepress table-condensed" id="myTable">
	<thead>
		<tr>
			<th class="tableHeaderFont"><?= lang('system.word38'); ?></th>
			<th class="tableHeaderFont"><?= lang('system.word39'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.playerlev'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.amt'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.curr'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.depmethod'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.reqtime'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.merchacct'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.promoname'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.promobonus'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.deposit') . ' ' . lang('pay.ip'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.deposit') . ' ' . lang('pay.locatn'); ?></th>
			<!-- <th>Bonus Amount</th> -->
			<?php if($this->session->userdata('dwStatus') == 'approved' || $this->session->userdata('dwStatus') == 'declined'){ ?>
				<th><?= lang('pay.procssby'); ?></th>
				<th><?= lang('pay.procsson'); ?></th>
			<?php	} ?>
			<th class="tableHeaderFont"><?= lang('lang.action'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php //var_dump($depositRequest);
			if(!empty($depositRequest)) {
				foreach($depositRequest as $depositRequest) {
		?>											
					<tr>																								
						<td><?= $depositRequest['playerName'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?></i>' : ucwords($depositRequest['playerName']) ?></td>		
						<td><?= $depositRequest['firstName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>'  : ucwords($depositRequest['firstName'].' '.$depositRequest['lastName']) ?></td>									
						<td><?= $depositRequest['groupName'].' '.$depositRequest['vipLevel'] ?></td>
						<td><?= $depositRequest['amount'] == '' ? '<i class="help-block">'.lang("lang.norecyet").'</i>' : $depositRequest['amount'] ?></td>
						<td><?= $depositRequest['currency'] == '' ? '<i class="help-block">'.lang("lang.norecyet").'</i>' : $depositRequest['currency'] ?></td>
						<td><?= $depositRequest['paymentMethodName'] == '' ? '<i class="help-block">'.lang("lang.norecyet").'</i>' : $depositRequest['paymentMethodName'] ?></td>
						<td><?= $depositRequest['dwDateTime'] == '' ? '<i class="help-block">'.lang("lang.norecyet").'</i>' : mdate('%M %d, %Y %H:%i:%s',strtotime($depositRequest['dwDateTime'])) ?></td>
						<td><?= $depositRequest['paypalMerchantAccount'] == '' ? '<i class="help-block">'.lang("lang.norecyet").'</i>' : $depositRequest['paypalMerchantAccount'] ?></td>
						<td><?= $depositRequest['promoName'] == '' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.promo"). '</i>' : $depositRequest['promoName'] ?></td>
						<td><?= $depositRequest['bonusAmount'] == '' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.bonus"). '</i>' : $depositRequest['bonusAmount'] ?></td>
						<td><?= $depositRequest['dwIp'] == '' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.bonus") .'<i/>' : $depositRequest['dwIp'] ?></td>
						<td><?= $depositRequest['dwLocation'] == ',' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.record") .'<i/>' : $depositRequest['dwLocation'] ?></td>
						<?php if($depositRequest['dwStatus'] == 'approved' || $depositRequest['dwStatus'] == 'declined'){ ?>															
									<td><?= $depositRequest['processedByAdmin'] == '' ? '<i class="help-block">'.lang("lang.norecyet").'<i/>' : ucwords($depositRequest['processedByAdmin']) ?></td>
									<td><?= $depositRequest['processDatetime'] == '' ? '<i class="help-block">'.lang("lang.norecyet").'<i/>' : mdate('%M %d, %Y %H:%i:%s',strtotime($depositRequest['processDatetime'])) ?></td>
						<?php	} ?>
						
						<td>
						<?php if($depositRequest['dwStatus'] == 'approved'){ ?>
							<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getDepositApproved(<?= $depositRequest['walletAccountId'] ?>,<?= $depositRequest['dwMethod'] ?>)" data-toggle="modal" data-target="#approvedDetailsModal">
								<?= lang("lang.details"); ?>
							</span>

						<?php }elseif($depositRequest['dwStatus'] == 'declined'){ ?>										
							<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getDepositDeclined(<?= $depositRequest['walletAccountId'] ?>,<?= $depositRequest['dwMethod'] ?>)" data-toggle="modal" data-target="#declinedDetailsModal">
								<?= lang("lang.details"); ?>
							</span>

						<?php }else{ ?>
							<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getAutoThirdPartyDepositRequest(<?= $depositRequest['walletAccountId'] ?>,<?= $depositRequest['dwMethod'] ?>)" data-toggle="modal" data-target="#requestDetailsModal">
								<?= lang("lang.details"); ?>
							</span>
						<?php } ?>
						</td>
					</tr>
		<?php 		
				}
			}
			else{ ?>
					<tr>
						<td colspan="14" style="text-align:center"><?= lang("lang.norec"); ?>
						</td>
					</tr>
		<?php	}
		?>
	</tbody>
</table>

<div class="panel-footer">
	<ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>