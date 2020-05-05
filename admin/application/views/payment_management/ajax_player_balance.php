<table class="table table-striped table-hover" id="myTable">
	<thead>
		<tr>
			<th><?= lang('lang.player') . ' ' . lang('pay.name'); ?></th>
			<th><?= lang('pay.username'); ?></th>
			<th><?= lang('pay.playerlev'); ?></th>
			<th><?= lang('pay.mainwalltbal'); ?></th>								
			<?php foreach ($games as $game) { ?>
				<th><?= $game['game']?> <?= lang('pay.walltbal'); ?></th>
			<?php } ?>
			<!-- <th>Cashback Bonus Balance</th> -->
			<th><?= lang('pay.totalbal'); ?></th>
			<th><?= lang('pay.curr'); ?></th>
			<th><?= lang('lang.action'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
			if(!empty($playerDetails)) {
				
				foreach($playerDetails as $playerDetails) {
					$totalBalanceAmount = 0;
					$subwallet = $this->payment_manager->getAllPlayerAccountByPlayerId($playerDetails['playerId']);
					$totalBalanceAmount += $playerDetails['mainwalletBalanceAmount'];
		?>			
						<tr>
							<td><?= $playerDetails['firstname'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : ucwords($playerDetails['firstname'].' '.$playerDetails['lastname']) ?></td>
							<td><?= $playerDetails['username'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $playerDetails['username'] ?></td>
							<td><?= $playerDetails['groupName'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $playerDetails['groupName'].' '.$playerDetails['vipLevel'] ?></td>
							<td><?= $playerDetails['mainwalletBalanceAmount'] == '' ? 0 : $playerDetails['mainwalletBalanceAmount'] ?></td>												
							<?php 
								foreach ($subwallet as $key => $subwallet) { 
									$totalBalanceAmount += $subwallet['totalBalanceAmount'];
								?>
								<td><?= $subwallet['totalBalanceAmount'] == '' ? 0 : $subwallet['totalBalanceAmount'] ?></td>
							<?php } ?>
							<!-- <td><?= $playerDetails['cashbackwalletBalanceAmount'][0]['cashbackwalletBalanceAmount'] ?></td> -->
							<td><?= $totalBalanceAmount ?></td>
							<td><?= $playerDetails['currency'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $playerDetails['currency'] ?></td>

							<!-- <td><?= $depositRequest['currency'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $depositRequest['currency'] ?></td>												 -->
							<td>
								<a href="<?= BASEURL. 'payment_management/viewPlayerBalanceAdjustmentForm/'.$playerDetails['playerId'] ?>" class="btn btn-sm btn-success"><?= lang("pay.adjust"); ?></a>												
							</td>
						</tr>
		<?php
				}
			}
			else{ ?>
				<tr>
					<td colspan="6" style="text-align:center"><?= lang("lang.norec"); ?>
					</td>
				</tr>
		<?php	}
		?>
	</tbody>
</table>

<div class="panel-footer">
	<ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>