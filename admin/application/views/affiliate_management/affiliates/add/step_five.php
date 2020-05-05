<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> Add Affiliate </h4>
				<a href="<?= BASEURL . 'affiliate_management/viewAffiliates'?>" class="btn btn-primary btn-sm pull-right" id="view_affiliate"><span class="glyphicon glyphicon-remove"></span></a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="affiliate_panel_body">
				<ol class="breadcrumb">
					<!-- <li class="active">Step 1: Sign Up Information</li>
					<li class="active">Step 2: Personal Information</li>
					<li class="active">Step 3: Payment Information</li> -->
					<li class="active"><a href="<?= BASEURL . 'affiliate_management/stepFour/back'?>">Step 3: Affiliate Game Options</a></li>
					<!-- <li class="active"><b>Step 4: Affiliate Game Platform</b></li> -->
					<li class="active">Step 4: Affiliate Payout Options</li>
					<li class="active">Step 5: Account Information</li>
					<li class="active">Step 6: Finish</li>
				</ol>

				<!-- Content Info -->
				<div class="panel panel-info">
					<div class="panel panel-heading">
						<h4 class="panel-title pull-left"> Affiliate Game Platform </h4>
						<div class="clearfix"></div>
					</div>

					<div class="panel-body">
						<?php
							$games = explode(',', $this->session->userdata('aff_game'));
						?>
						<form method="POST" action="<?= BASEURL . 'affiliate_management/stepSix'?>" accept-charset="utf-8">
							<div class="row">
								<?php foreach ($game as $key => $value_game) { ?>
									<div class="col-md-3 col-md-offset-0">
										<input type="checkbox" name="game[]"
											<?php
												foreach ($games as $value) {
													if($value == $value_game['game']) {
														echo "checked";
														break;
													}
												}
											?>
										value="<?= $value_game['game'] ?>">  <?= $value_game['game'] ?>
									</div>
								<?php } ?>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-6 col-md-offset-0">
									<label style="color: red; font-size: 12px;"><?php echo form_error('game'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-5">
									<input type="submit" name="submit" id="submit" class="form-control btn btn-primary" value="Next">
								</div>
							</div>
						</form>
					</div>
				</div>
				<!-- End of Content Info -->
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
</div>