<?php
/**
 *   filename:   agent_bank_info.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2017-11-03
 *   @brief:     agent bank information and payment history
 */
function isAdmin($c) {
    return $c == 'agency_management';
}
function hasPermissions($self, $c, $p) {
    if ($c == 'agency_management' && !$self->permissions->checkPermissions($p)) {
        return false;
    }
    return true;
}
function hasFeature($self, $f) {
    if (!$self->utils->isEnabledFeature($f)) {
        return false;
    }
    return true;
}
?>
		<!-- Bank Info -->
		<div class="panel panel-primary">

			<div class="panel-heading">
				<h4 class="panel-title">
					<a href="#bank_info" id="hide_affbank_info" class="btn btn-info btn-sm">
						<i class="glyphicon glyphicon-chevron-up" id="hide_affbi_up"></i>
					</a> <?=lang('aff.ai22');?>
                    <?php if (isAdmin($controller_name) && $this->permissions->checkPermissions('edit_agent_bank_account')) {?>
                    <a href="<?=site_url('/' . $controller_name . '/addBankInfo/' . $agent_id)?>" class="btn btn-sm btn-info pull-right"><i class="glyphicon glyphicon-plus"></i> <?=lang('lang.add');?></a>
                    <?php } ?>
                </h4>
			</div>

			<div class="panel-body affbank_panel_body" style="display: none;">
				<div class="row">

					<div class="col-md-3">
                        <div>
							<label class="text-info"><h4 style="margin-bottom: 0; font-weight: bold; margin-top: 0; margin-bottom: 0;"><?php echo lang('Main Wallet'); ?>:</h4></label>
							<span class="text-warning"><strong><?php echo $this->utils->formatCurrencyNoSym(@$agent['wallet_balance']); ?></strong></span>
						</div>

                        <?php if($this->utils->isEnabledFeature('agent_can_use_balance_wallet')) { ?>
                        <div>
							<label class="text-info"><h6 style="margin-bottom: 0; font-weight: bold; margin-top: 0; margin-bottom: 0;"><?php echo lang('Balance Wallet'); ?>:</h6></label>
							<span class="text-warning small"><strong><?php echo $this->utils->formatCurrencyNoSym(@$agent['wallet_hold']); ?></strong></span>
						</div>
                        <?php } ?>

                        <div>
							<label class="text-info"><h6 style="margin-bottom: 0; font-weight: bold; margin-top: 0; margin-bottom: 0;"><?php echo lang('Pending withdraw amount'); ?>:</h6></label>
							<span class="text-warning small"><strong><?php echo $this->utils->formatCurrencyNoSym(@$agent['frozen']); ?></strong></span>
						</div>
					</div>
					<div class="col-md-9">
						<div class="btn-group" role="group" style="margin-bottom: 10px;">
							<?php if (hasPermissions($this, $controller_name, 'agent_balance_transfer')) {?>
							<input type="button" class="btn btn-info btn-xs btn_transfer_bal_from_main" value="<?php echo lang('Main Wallet To Locked Wallet'); ?>">
							<input type="button" class="btn btn-info btn-xs btn_transfer_bal_to_main" value="<?php echo lang('Locked Wallet To Main Wallet'); ?>">
							<input type="button" class="btn btn-primary btn-xs btn_transfer_main_to_binding_player" value="<?php echo lang('Main Wallet To Binding Player'); ?>">
							<input type="button" class="btn btn-primary btn-xs btn_transfer_locked_to_binding_player" value="<?php echo lang('Locked Wallet To Binding Player'); ?>">
							<input type="button" class="btn btn-success btn-xs btn_transfer_main_from_binding_player" value="<?php echo lang('Transfer From Binding Player'); ?>">
							<?php }?>
						</div>

						<div class="btn-group" role="group" style="margin-bottom: 10px;">
							<?php if (isAdmin($controller_name) && hasPermissions($this, $controller_name, 'agent_deposit')) {?>
							<input type="button" class="btn btn-info btn-xs btn_deposit" value="<?php echo lang('Deposit To Main'); ?>">
							<?php }?>
							<?php if (isAdmin($controller_name) && hasPermissions($this, $controller_name, 'agent_withdraw')) {?>
							<input type="button" class="btn btn-info btn-xs btn_withdraw" value="<?php echo lang('Withdraw From Main'); ?>">
							<?php }?>

							<?php if (isAdmin($controller_name) && hasPermissions($this, $controller_name, 'agent_withdraw')) {?>
							<input type="button" class="btn btn-info btn-xs btn_withdraw_bal" value="<?php echo lang('Withdraw From Locked Wallet'); ?>">
							<?php }?>
						</div>

					    <?php if (isAdmin($controller_name)) {?>
						<div class="btn-group" role="group" style="margin-bottom: 10px;">
							<?php if (hasPermissions($this, $controller_name, 'agent_deposit')) {?>
							<a href="/<?=$controller_name?>/agent_manual_add_balance/<?=$agent_id?>" class="btn btn-info btn-xs btn_manual_add_balance"><?php echo lang('Manual Add Balance'); ?></a>
							<?php }?>
							<?php if (hasPermissions($this, $controller_name, 'agent_withdraw')) {?>
							<a href="/<?=$controller_name?>/agent_manual_subtract_balance/<?=$agent_id?>" class="btn btn-info btn-xs btn_manual_subtract_balance"><?php echo lang('Manual Subtract Balance'); ?></a>
							<?php }?>
						</div>
					    <?php }?>
					</div>
				</div>
				<hr/>
				<table class="table" id="bankTable">
					<thead>
						<!-- <th></th> -->
						<th><?=lang('aff.ai23');?></th>
						<th><?=lang('aff.ai90');?></th>
						<th><?=lang('aff.ai24');?></th>
						<th><?=lang('aff.ai25');?></th>
						<th><?=lang('aff.ai26');?></th>
						<th><?=lang('aff.ai27');?></th>
						<th><?=lang('aff.ai28');?></th>
						<th><?=lang('lang.action');?></th>
					</thead>
					<tbody>
						<?php if (!empty($bank)): ?>
							<?php foreach ($bank as $bank_value): ?>
								<tr>
									<!-- <td></td> -->
									<td><?=lang($bank_value['bank_name'])?></td>
									<td><?=$bank_value['account_name']?></td>
									<td><?=$bank_value['branch_address']?></td>
									<td><?=$bank_value['account_number']?></td>
									<td><?=(empty(strtotime($bank_value['created_on']))) ? '' : date("Y-m-d H:i:s", strtotime($bank_value['created_on']))?></td>
									<td><?=(empty(strtotime($bank_value['updated_on']))) ? '' : date("Y-m-d H:i:s", strtotime($bank_value['updated_on']))?></td>
									<td><?=($bank_value['status'] == '0') ? lang('aff.ai29') : lang('aff.ai30')?></td>
									<td>
										<?php if (isAdmin($controller_name)  && hasPermissions($this, $controller_name, 'edit_agent_bank_account')) {?>
											<a href="<?=site_url('/' . $controller_name . '/editPayment/' . $bank_value['agent_payment_id'])?>" data-toggle="tooltip" title="<?=lang('lang.edit');?>"><i class="glyphicon glyphicon-edit"></i></a>

										<?php if ($bank_value['status'] == 0) {?>
											<a href="#" data-toggle="tooltip" title="<?=lang('lang.deactivate');?>" class="inactive" onclick="deactivate_payment('<?=$bank_value['agent_payment_id']?>', '<?=$bank_value['bank_name']?>', '<?=$agent_id?>'); "><i class="glyphicon glyphicon-remove-circle"></i></a>
										<?php } else {?>
											<a href="#" data-toggle="tooltip" title="<?=lang('lang.activate');?>" class="active" onclick="activate_payment('<?=$bank_value['agent_payment_id']?>', '<?=$bank_value['bank_name']?>', '<?=$agent_id?>'); "><i class="glyphicon glyphicon-ok-sign"></i></a>
											<a href="#" data-toggle="tooltip" title="<?=lang('lang.delete');?>" class="active" onclick="delete_payment('<?=$bank_value['agent_payment_id']?>', '<?=$bank_value['bank_name']?>', '<?=$agent_id?>'); "><i class="glyphicon glyphicon-remove"></i></a>
										<?php }?>
										<?php }?>
									</td>
								</tr>
							<?php endforeach ?>
						<?php endif ?>
					</tbody>
				</table>
			</div>
		</div>
		<!-- End of Bank Info -->

		<!-- Payment History -->
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a href="#personal_info" id="hide_affpay_info" class="btn btn-info btn-sm">
						<i class="glyphicon glyphicon-chevron-up" id="hide_affap_up"></i>
					</a> <?=lang('aff.ai54');?>
				</h4>
			</div>
			<div class="panel-body affap_panel_body" style="display: none;">
				<div id="paymentHistory">
					<table class="table table-striped" id="paymentTable" style="width: 100%;">
						<thead>
							<!-- <th></th> -->
							<th><?=lang('Date');?></th>
							<th><?=lang('Amount');?></th>
							<th><?=lang('Before Balance');?></th>
							<th><?=lang('After Balance');?></th>
							<th><?=lang('Notes');?></th>
						</thead>
						<tbody>
							<?php if (!empty($transactions)): ?>
								<?php foreach ($transactions as $payment_value): ?>
									<tr>
										<!-- <td></td> -->
										<td><?php echo $payment_value['created_at']; ?></td>
										<td><?php echo $this->utils->formatCurrencyNoSym($payment_value['amount']); ?></td>
										<td><?php echo $this->utils->formatCurrencyNoSym($payment_value['before_balance']); ?></td>
										<td><?php echo $this->utils->formatCurrencyNoSym($payment_value['after_balance']); ?></td>
										<td><?php echo (empty($payment_value['note'])) ? lang('player.ub12') : $payment_value['note'] ?></td>
									</tr>
								<?php endforeach ?>
							<?php endif ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- End of Payment History -->
<script>
// Filters
$('#bankTable').DataTable( {
	// "responsive": {
	// 	details: {
	// 		type: 'column'
	// 	}
	// },
	// "columnDefs": [ {
	// 	className: 'control',
	// 	orderable: false,
	// 	targets:   0
	// } ],
	"order": [ 1, 'asc' ]
} );

$('#paymentTable').DataTable( {
	// "responsive": {
	// 	details: {
	// 		type: 'column'
	// 	}
	// },
	// "columnDefs": [ {
	// 	className: 'control',
	// 	orderable: false,
	// 	targets:   0
	// } ],
	"order": [ 1, 'desc' ]
} );

$('.btn_deposit').click(function(){
	window.location.href='<?php echo site_url("/" . $controller_name . "/agent_deposit/" . $agent_id); ?>';
});
$('.btn_withdraw').click(function(){
	window.location.href='<?php echo site_url("/" . $controller_name . "/agent_withdraw/" . $agent_id."/main"); ?>';
});
$('.btn_transfer_bal_to_main').click(function(){
	BootstrapDialog.confirm('<?php echo lang('Do you want transfer all balance to main wallet?'); ?>', function(result){
		if(result) {
			window.location.href='<?php echo site_url("/" . $controller_name . "/agent_transfer_bal_to_main/" . $agent_id); ?>';
		}
	});
});
$('.btn_withdraw_bal').click(function(){
	BootstrapDialog.confirm('<?php echo lang('Do you want withdraw locked wallet'); ?>?', function(result){
		if(result) {
			window.location.href='<?php echo site_url("/" . $controller_name . "/agent_withdraw/" . $agent_id."/balance"); ?>';
		}
	});

});
$('.btn_transfer_bal_from_main').click(function(){
	BootstrapDialog.confirm('<?php echo lang('Do you want transfer all main wallet to balance wallet?'); ?>', function(result){
		if(result) {
			window.location.href='<?php echo site_url("/" . $controller_name . "/agent_transfer_bal_from_main/" . $agent_id); ?>';
		}
	});

});
$('.btn_transfer_main_from_binding_player').click(function(){
	BootstrapDialog.confirm('<?php echo lang('Do you want transfer all balance to agent main wallet from main wallet of the binding player?'); ?>', function(result){
		if(result) {
			window.location.href='<?php echo site_url("/" . $controller_name . "/agent_transfer_balance_from_binding_player/" . $agent_id . "/main"); ?>';
		}
	});
});
$('.btn_transfer_main_to_binding_player').click(function(){
	BootstrapDialog.confirm('<?php echo lang('Do you want transfer all balance in main wallet to main wallet of the binding player?'); ?>', function(result){
		if(result) {
			window.location.href='<?php echo site_url("/" . $controller_name . "/agent_transfer_balance_to_binding_player/" . $agent_id . "/main"); ?>';
		}
	});
});
$('.btn_transfer_locked_to_binding_player').click(function(){
	BootstrapDialog.confirm('<?php echo lang('Do you want transfer all balance in locked wallet to main wallet of the binding player?'); ?>', function(result){
		if(result) {
			window.location.href='<?php echo site_url("/" . $controller_name . "/agent_transfer_balance_to_binding_player/" . $agent_id . "/hold"); ?>';
		}
	});
});
    // view_information.php BANK INFO
    $("#hide_affbank_info").click(function() {
        $(".affbank_panel_body").slideToggle();
        $("#hide_affbi_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    // view_information.php AFF TRACK CODE
    $("#hide_affpay_info").click(function() {
        $(".affap_panel_body").slideToggle();
        $("#hide_affap_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agent_bank_info.php
