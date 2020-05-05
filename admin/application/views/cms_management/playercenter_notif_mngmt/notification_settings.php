<style type="text/css">
	/* table {table-layout:fixed;} */
    table td {word-wrap:break-word;}
</style>
<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt pull-left">
					<i class="icon-newspaper"></i> <?=lang('Cashier Notification Manager');?>
				</h4>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body">
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<ul class="nav nav-tabs">
						  <li class="active">
						  	<a data-toggle="tab" href="#fundTransfer">
						  		<?php echo lang("Fund Transfer") ?>
						  	</a>
						  </li>
<!--						  <li>-->
<!--						  	<a data-toggle="tab" href="#cashbackClaim">-->
<!--						  		--><?php //echo lang("Cashback Claim") ?><!--		-->
<!--						  	</a>-->
<!--						  </li>-->
						  <li>
						  	<a data-toggle="tab" href="#customerSupportUrl">
						  		<?php echo lang("Customer Support Url") ?>
						  	</a>
						  </li>
						</ul>

						<div class="tab-content">
						  <div id="fundTransfer" class="tab-pane fade in active">
						    <?php $this->load->view("cms_management/playercenter_notif_mngmt/fund_transfer_settings"); ?>
						  </div>
<!--						  <div id="cashbackClaim" class="tab-pane fade">-->
<!--						    --><?php //$this->load->view("cms_management/playercenter_notif_mngmt/cashback_notif_settings"); ?>
<!--						  </div>-->
						  <div id="customerSupportUrl" class="tab-pane fade">
						    <?php $this->load->view("cms_management/playercenter_notif_mngmt/customer_support_url_settings"); ?>
						  </div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>