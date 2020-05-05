<ul class="sidebar-nav" id="sidebar">
<?php $permissions = $this->permissions->getPermissions()?>
<?php if ($permissions != null) {
	?>
	<?php foreach ($permissions as $value) {
		?>
		<?php switch ($value) {
		case 'deposit_list': ?>
				<li>
					<a class="list-group-item" id="deposit_list" style="border: 0px;line-height: 1.42857143;margin-bottom:0.1px;" href="<?=site_url('/home/nav/deposit_all')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('role.74');?>">
						<i class="fa fa-money <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
						<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> ><?=lang('role.74');?></span>
						<!-- <span class="badge" style="font-size: 12px;margin-right: 10px;position: absolute;right: 0;">1</span> -->
						<!-- OGP-15305 Hide order count of the side menu: SBE_Payment -->
						<!-- <span title="<?=lang('role.74');?>" class="sbn deposit_count_sidebar deposit-count-offline-dropdown badge" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="font-size: 12px;margin-right: 10px;position: absolute;right: 0;"' : 'style="font-size: 8px;margin-right: 1px;position: absolute;right: 0;"';?>>
                            <?php
								$firstDayOfMon = $this->utils->getFirstDateOfCurrentMonth() . '00:00:00';
								$end_today     = date('Y-m-d 23:59:59');

								$total_deposit = (int)$this->sale_order->countSaleOrders(Sale_order::PAYMENT_KIND_DEPOSIT, Sale_order::VIEW_STATUS_REQUEST_ALL,$firstDayOfMon, $end_today, null, null, Sale_order::COLUMN_CREATED_AT, null);

                            	echo $total_deposit;
                            ?>
                        </span> -->
					</a>
				</li>
			<?php break;?>

			<?php case 'payment_withdrawal_list': ?>
				<li>
					<a class="list-group-item" id="view_withdrawal" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('/home/nav/withdrawal')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('pay.04');?>">
						<i class="icon-drawer <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
						<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('pay.04');?></span>
						<!-- OGP-15305 Hide order count of the side menu: SBE_Payment -->
						<!-- <span title="<?=lang('pay.04');?>" class="sbn withdraw-count-dropdown badge" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="font-size: 12px;margin-right: 10px;position: absolute;right: 0;"' : 'style="font-size: 8px;margin-right: 1px;position: absolute;right: 0;"';?>><?=number_format($this->payment_manager->getDWCount('withdrawal', 'request'))?></span> -->
					</a>
				</li>
			<?php break;?>

			<?php case 'transfer_request': ?>
				<li>
					<a class="list-group-item" id="transfer_request" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('payment_management/transfer_request')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Transfer Request');?>">
						<i class="glyphicon glyphicon-transfer <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
						<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('Transfer Request');?></span>
					</a>
				</li>
			<?php break;?>

			<?php case 'report_transactions': ?>
				<li>
					<a class="list-group-item" id="view_transaction" style="border: 0px;margin-bottom:0.1px;" href="<?php echo site_url('payment_management/viewtransactionList'); ?>" data-toggle="tooltip" data-placement="right" title="<?=lang('pay.transactions');?>">
						<i class="fa fa-table <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
						<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('pay.transactions');?></span>
					</a>
				</li>
			<?php break;?>

			<?php case 'new_deposit': ?>
				<li>
					<a class="list-group-item" id="new_deposit" style="border: 0px;margin-bottom:0.1px;" href="<?php echo site_url('payment_management/newDeposit'); ?>" data-toggle="tooltip" data-placement="right" title="<?=lang('lang.newDeposit');?>">
						<i class="fa fa-plus <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
						<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('lang.newDeposit');?></span>
					</a>
				</li>
			<?php break;?>
			<?php case 'new_internal_withdrawal': ?>
				<li>
					<a class="list-group-item" id="new_internal_withdrawal" style="border: 0px;margin-bottom:0.1px;" href="<?php echo site_url('payment_management/newInternalWithdrawal'); ?>" data-toggle="tooltip" data-placement="right" title="<?=lang('New Internal Withdrawal');?>">
						<i class="fa fa-plus <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
						<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('New Internal Withdrawal');?></span>
					</a>
				</li>
			<?php break;?>

			<?php case 'new_withdrawal': ?>
				<li>
					<a class="list-group-item" id="new_withdrawal" style="border: 0px;margin-bottom:0.1px;" href="<?php echo site_url('payment_management/newWithdrawal'); ?>" data-toggle="tooltip" data-placement="right" title="<?=lang('lang.newWithdrawal');?>">
						<i class="fa fa-minus <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
						<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('lang.newWithdrawal');?></span>
					</a>
				</li>
			<?php break;?>

			<?php case 'exception_order_list': ?>
				<li>
					<a class="list-group-item" id="exception_order_list" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('payment_management/exception_order_list')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Exception Orders');?>">
						<i class="fa fa-exclamation-circle <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
						<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('Exception Orders');?></span>
					</a>
				</li>
			<?php break;?>

			<?php case 'lock_deposit_list': ?>
			<li>
				<a class="list-group-item" id="lock_deposit" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('payment_management/lockedDepositList')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Locked Deposit');?>">
					<i class="fa fa-credit-card-alt <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
					<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('Locked Deposit');?></span>
				</a>
			</li>
			<?php break;?>

			<?php case 'lock_withdrawal_list': ?>
			<li>
				<a class="list-group-item" id="lock_withdrawal" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('payment_management/lockedWithdrawalList')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Locked Withdraw');?>">
					<i class="fa fa-shopping-bag <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
					<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> > <?=lang('Locked Withdraw');?></span>
				</a>
			</li>
			<?php break;?>

			<?php case 'batch_deposit': ?>
				<li>
					<a class="list-group-item" id="batch_deposit" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('payment_management/batchDeposit')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Exception Orders');?>">
						<i class="fa fa-users <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
						<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('Batch Deposit');?></span>
					</a>
				</li>
			<?php break;?>

			<?php default: ?>
			<?php break;?>

		<?php }?>
	<?php }
	?>
<?php }
?>
</ul>
<ul id="sidebar_menu" class="sidebar-nav">
	<li class="sidebar-brand">
		<a id="menu-toggle" href="#" onclick="Template.changeSidebarStatus();">
			<span id="main_icon" class="icon-arrow-<?=($this->session->userdata('sidebar_status') == 'active') ? 'left' : 'right';?> pull-right"></span></span>
		</a>
	</li>
</ul>
<script type="text/javascript">
	$( document ).ready(function() {
	    $(document).on("click","#menu-toggle",function(){
	    	if($( "#wrapper" ).hasClass( "active" )){
	    		$('.sbn').attr('style','font-size: 12px;margin-right: 10px;position: absolute;right: 0;');
	    	}else{
	    		$('.sbn').attr('style','font-size: 8px;margin-right: 1px;position: absolute;right: 0;');
	    	}
		});
	});
</script>