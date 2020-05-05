<table class="table table-striped table-hover tablepress table-condensed" id="myTable">
	<thead>
		<tr>
			<th class="tableHeaderFont"><?= lang('system.word38'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.realname'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.playerlev'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.amt'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.sn'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.pin'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.curr'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.reqtime'); ?></th>
			
			<th class="tableHeaderFont"><?= lang('pay.promoname'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.promobonus'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.deposit') . ' ' . lang('pay.ip'); ?></th>
			<th class="tableHeaderFont"><?= lang('pay.deposit') . ' ' . lang('pay.locatn'); ?></th>
			
			<?php if($this->session->userdata('dwStatus') == 'approved' || $this->session->userdata('dwStatus') == 'declined'){ ?>
				<th class="tableHeaderFont"><?= lang('pay.procssby'); ?></th>
				<th class="tableHeaderFont"><?= lang('pay.procsson'); ?></th>
			<?php	} ?>
			<th class="tableHeaderFont"><?= lang('lang.action'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php //var_dump($depositRequest);
            $atts_popup = array(
                              'width'      => '1030',
                              'height'     => '600',
                              'scrollbars' => 'yes',
                              'status'     => 'yes',
                              'resizable'  => 'no',
                              'screenx'    => '0',
                              'screeny'    => '0');

			if(!empty($depositRequest)) {
				foreach($depositRequest as $depositRequest) {
		?>											
					<tr>
						<td><?= $depositRequest['playerName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : ucwords($depositRequest['playerName']) ?></td>																								
						<td><?= $depositRequest['firstName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : ucwords($depositRequest['firstName']).' '.ucwords($depositRequest['firstName']) ?></td>											
						<td><?= $depositRequest['groupName'].' '.$depositRequest['vipLevel'] ?></td>
						<td><?= $depositRequest['amount'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $depositRequest['amount'] ?></td>
						<td><?= $depositRequest['cashcard_sn'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $depositRequest['cashcard_sn'] ?></td>
						<td><?= $depositRequest['cashcard_pin'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $depositRequest['cashcard_pin'] ?></td>
						<td><?= $depositRequest['currency'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $depositRequest['currency'] ?></td>
						<td><?= $depositRequest['dwDateTime'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : mdate('%M %d, %Y %H:%i:%s',strtotime($depositRequest['dwDateTime'])) ?></td>
						<td><?= $depositRequest['promoName'] == '' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.promo") .'<i/>' : $depositRequest['promoName'] ?></td>
						<td><?= $depositRequest['bonusAmount'] == '' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.bonus") .'<i/>' : $depositRequest['bonusAmount'] ?></td>
						<td><?= $depositRequest['dwIp'] == '' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.bonus") .'<i/>' : $depositRequest['dwIp'] ?></td>
						<td><?= $depositRequest['dwLocation'] == ',' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $depositRequest['dwLocation'] ?></td>
						<?php if($depositRequest['dwStatus'] == 'approved' || $depositRequest['dwStatus'] == 'declined'){ ?>															
									<td><?= $depositRequest['processedByAdmin'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : ucwords($depositRequest['processedByAdmin']) ?></td>
									<td><?= $depositRequest['processDatetime'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : mdate('%M %d, %Y %H:%i:%s',strtotime($depositRequest['processDatetime'])) ?></td>
						<?php	} ?>
						<td>
						<?php if($depositRequest['dwStatus'] == 'approved'){ ?>
							<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getDepositApprovedLocalBank(<?= $depositRequest['walletAccountId'] ?>,<?= $depositRequest['dwMethod'] ?>)" data-toggle="modal" data-target="#approvedDetailsModal">
								<?= lang("lang.details"); ?>
							</span>

						<?php }elseif($depositRequest['dwStatus'] == 'declined'){ ?>										
							<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getDepositDeclined(<?= $depositRequest['walletAccountId'] ?>,<?= $depositRequest['dwMethod'] ?>)" data-toggle="modal" data-target="#declinedDetailsModal">
								<?= lang("lang.details"); ?>
							</span>

						<?php }else{ ?>
							<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getDepositRequest(<?= $depositRequest['walletAccountId'] ?>,<?= $depositRequest['dwMethod'] ?>)" data-toggle="modal" data-target="#requestDetailsModal">
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
						<td colspan="11" style="text-align:center"><?= lang("lang.norec"); ?>
						</td>
					</tr>
		<?php	}
		?>
	</tbody>
</table>

<div class="panel-footer">
	<ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>