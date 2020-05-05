<table class="table table-striped table-hover tablepress table-condensed" id="myTable">
	<thead>
		<tr>
			<th class="tableHeaderFont"><?= lang("pay.username"); ?></th>
			<th class="tableHeaderFont"><?= lang("pay.realname"); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.playerlev'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.withamt'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.reqon'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.mainwallt'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.ptwallt'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.agwallt'); ?></th>
			<!-- <th>Cashback Wallet</th> -->
			<th class="tableHeaderFont"><?= lang('pay.totalbal'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.promoname'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.bonusamt'); ?></th>
			<!-- <th>Currency</th> -->								
			<!-- <th>Withdrawal Method</th> -->	
			<th class="tableHeaderFont"><?= lang('pay.bankname'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.acctname'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.acctnumber'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.acctbranch'); ?></th>
			
			<th class="tableHeaderFont"><?= lang('pay.withip'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.withlocation'); ?></th>
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
							<td><?= $depositRequest['username'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $depositRequest['username'] ?></td>
							<td><?= $depositRequest['firstname'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : ucwords($depositRequest['firstname']).' '.ucwords($depositRequest['lastname']) ?></td>
							<td><?= $depositRequest['groupName'].' '.$depositRequest['vipLevel'] ?></td>
							<td><?= $depositRequest['amount'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $depositRequest['amount'] ?></td>	
							<td><?= $depositRequest['dwDateTime'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : mdate('%M %d, %Y %H:%i:%s',strtotime($depositRequest['dwDateTime'])) ?></td>
							<td><?= $depositRequest['mainwalletBalanceAmount'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $depositRequest['mainwalletBalanceAmount'] ?></td>	
							<td><?= $depositRequest['subwalletBalanceAmount'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $depositRequest['subwalletBalanceAmount'][0]['subwalletBalanceAmount'] ?></td>	
							<td><?= $depositRequest['subwalletBalanceAmountAG'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $depositRequest['subwalletBalanceAmountAG'][0]['subwalletBalanceAmount'] ?></td>	
							<!-- <td><?= $depositRequest['cashbackwalletBalanceAmount'] == '' ? '<i class="help-block">'. lang("lang.norecyet") . '<i/>' : $depositRequest['cashbackwalletBalanceAmount'][0]['cashbackwalletBalanceAmount'] ?></td>	 -->
							<td><?= $depositRequest['mainwalletBalanceAmount'] + $depositRequest['subwalletBalanceAmount'][0]['subwalletBalanceAmount']+$depositRequest['subwalletBalanceAmountAG'][0]['subwalletBalanceAmount'] ?></td>	
							<td><?= $depositRequest['playerPromoActive'] == '' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.promo") .'<i/>' : $depositRequest['playerPromoActive'][0]['promoName'] ?></td>	
							<td><?= $depositRequest['playerPromoActive'] == '' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.bonus") .'<i/>' : $depositRequest['playerPromoActive'][0]['bonusAmount'] ?></td>
							<!-- <td><?= $depositRequest['currency'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $depositRequest['currency'] ?></td> -->												
							<!-- <td><?= $depositRequest['paymentMethodName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $depositRequest['paymentMethodName'] ?></td> -->
							<!-- <input type="hidden" id="promoBonusStatus" value="<?= $depositRequest['promoBonusStatus'] ?>" /> -->
							<td><?= $depositRequest['bankName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $depositRequest['bankName'] ?></td>	
							<td><?= $depositRequest['bankAccountFullName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $depositRequest['bankAccountFullName'] ?></td>	
							<td><?= $depositRequest['bankAccountNumber'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $depositRequest['bankAccountNumber'] ?></td>	
							<td><?= $depositRequest['branch'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $depositRequest['branch'] ?></td>	

							<td><?= $depositRequest['dwIp'] == ',' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.bonus") .'<i/>' : $depositRequest['dwIp'] ?></td>
							<td><?= $depositRequest['dwLocation'] == '' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.bonus") .'<i/>' : $depositRequest['dwLocation'] ?></td>
							<?php if($depositRequest['dwStatus'] == 'approved' || $depositRequest['dwStatus'] == 'declined'){ ?>															
										<td><?= $depositRequest['processedBy'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : ucwords($depositRequest['processedByAdmin']) ?></td>
										<td><?= $depositRequest['processDatetime'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : mdate('%M %d, %Y %H:%i:%s',strtotime($depositRequest['processDatetime'])) ?></td>
							<?php	} ?>
							
							<td>
							<?php if($depositRequest['dwStatus'] == 'approved'){ ?>
								<span class="btn-xs btn-info review-btn" onclick="PaymentManagementProcess.getWithdrawalApproved(<?= $depositRequest['walletAccountId'] ?>)" data-toggle="modal" data-target="#approvedDetailsModal">
									<?= lang("lang.details"); ?>
								</span>

							<?php }elseif($depositRequest['dwStatus'] == 'declined'){ ?>										
								<span class="btn-xs btn-info review-btn" onclick="PaymentManagementProcess.getWithdrawalDeclined(<?= $depositRequest['walletAccountId'] ?>)" data-toggle="modal" data-target="#declinedDetailsModal">
									<?= lang("lang.details"); ?>
								</span>

							<?php }else{ ?>
								<span class="btn-xs btn-info review-btn" onclick="PaymentManagementProcess.getWithdrawalRequest(<?= $depositRequest['walletAccountId'] ?>,<?= $depositRequest['dwMethod'] ?>)" data-toggle="modal" data-target="#requestDetailsModal">
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
					<td colspan="20" style="text-align:center"><?= lang("lang.norec"); ?>
					</td>
				</tr>
		<?php	}
		?>
	</tbody>
</table>

<div class="panel-footer">
	<ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>