<style type="text/css">
	.col-md-3 {
		margin: 0;
		padding: 0 10px;
		float: left;
		height: 70px;
	}

	.col-md-3 label {
		margin: 0;
		padding: 0;
		float: left;
		height: auto;
	}

	.col-md-3 span {
		margin: 0;
		padding: 0;
		float: left;
		height: auto;
		color: red;
	}

	.col-md-3 input#markasfinal {
		margin: 25px 0 0 0;
	}
</style>

<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt"><i class="icon-wallet"></i> <?= lang('lang.edit') . " " . lang('aff.sb7') . ":"; ?> <i><?= $earnings['username'];?></i>
					<a href="<?= BASEURL . 'affiliate_management/viewAffiliateMonthlyEarnings'?>" class="btn btn-default btn-sm pull-right" id="view_affiliate"><span class="glyphicon glyphicon-remove"></span></a>
					<span class="clearfix"></span>
				</h4>
			</div>

			<div class="panel-body" id="affiliate_panel_body">
				<form action="<?= BASEURL . 'affiliate_management/verifyEditAffiliateMonthlyEarnings' ?>" method="POST">
					<div class="col-md-12">
						<input type="hidden" name="affiliateId" id="affiliateId" value="<?= $earnings['affiliateId']; ?>" />
						<input type="hidden" name="affiliateMonthlyEarningsId" id="affiliateMonthlyEarningsId" value="<?= $earnings['affiliateMonthlyEarningsId']; ?>" class="form-control" />

						<div class="col-md-3"> 
							<label for="active_players"><?= lang('aff.ai44'); ?></label>
							<input type="text" name="active_players" id="active_players" value="<?= $earnings['active_players']; ?>" class="form-control" readonly />
						</div>

						<div class="col-md-3"> 
							<label for="opening_balance"><?= lang('aff.ai45'); ?></label>
							<input type="text" name="opening_balance" id="opening_balance" value="<?= $earnings['opening_balance']; ?>" class="form-control" readonly />
						</div>

						<div class="col-md-3"> 
							<label for="earnings"><?= lang('aff.ai46'); ?></label>
							<input type="text" name="earnings" id="earnings" value="<?= $earnings['earnings']; ?>" class="form-control" readonly />
						</div>

						<div class="col-md-3"> 
							<label for="approved"><?= lang('aff.ai47'); ?></label>
							<input type="text" name="approved" id="approved" value="<?= (set_value('approved') == null) ? $earnings['approved']:set_value('approved'); ?>" onchange="computeClosingBalance();" class="form-control number_only" />
							<span class="input-sm" id="approved_error"><?= form_error('approved'); ?></span>
						</div>

						<div class="col-md-3"> 
							<label for="closing_balance"><?= lang('aff.ai48'); ?></label>
							<input type="text" name="closing_balance" id="closing_balance" value="<?= (set_value('closing_balance') == null) ? $earnings['closing_balance']:set_value('closing_balance'); ?>" class="form-control" readonly/>
						</div>

						<div class="col-md-3"> 
							<label for="notes"><?= lang('aff.apay11'); ?></label>
							<input type="text" name="notes" id="notes" value="<?= set_value('notes'); ?>" class="form-control" />
							<span class="input-sm"><?= form_error('notes'); ?></span>
						</div>

						<div class="col-md-3"> 
							<input type="checkbox" name="markasfinal" id="markasfinal" value="1" /> <?= lang('aff.ai88'); ?> <i class="icon-question" data-toggle="tooltip" data-placement="right" title="<?= lang('aff.ai89'); ?>"></i>
							<span class="input-sm"><?= form_error('markasfinal'); ?></span>
						</div>
					</div>

					<div class="col-md-12">
						<div class="col-md-4 col-md-offset-5">
							<input type="submit" class="btn btn-primary btn-sm" value="<?= lang('lang.edit'); ?>"/>
							<input type="reset" class="btn btn-primary btn-sm" value="<?= lang('lang.reset'); ?>"/>
						</div>
					</div>
				</form>
			</div>
			<div class="panel-footer"></div>
		</div>
	</div>
</div>
