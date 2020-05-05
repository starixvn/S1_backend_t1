<div class="row">
	<div class="col-md-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-cog"></i> Change Your Contact Information </h4>
				<!-- <button class="btn btn-info btn-xs pull-right" id="change_contact_toggle"><span class="glyphicon-chevron-up glyphicon" id="button_span_contact_up"></span></button> -->
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="change_contact_panel_body">
				<form method="POST" action="<?= BASEURL . 'affiliate/verifyChangeContact'?>" accept-charset="utf-8">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-12 col-md-offset-0">
								<label for="phone">Phone Number: </label>
							</div>

							<div class="col-md-8 col-md-offset-0">
								<input type="phone" name="phone" id="phone" class="form-control" value="<?= set_value('phone') ?>">
								<label style="color: red; font-size: 12px;"><?php echo form_error('phone'); ?></label>
							</div>
						</div>

						<div class="col-md-12">
							<div class="col-md-12 col-md-offset-0">
								<label for="mobile">Mobile Number: </label>
							</div>

							<div class="col-md-8 col-md-offset-0">
								<input type="mobile" name="mobile" id="mobile" class="form-control" value="<?= set_value('mobile') ?>">
								<label style="color: red; font-size: 12px;"><?php echo form_error('mobile'); ?></label>
							</div>
						</div>

						<div class="col-md-12">
							<div class="col-md-4 col-md-offset-0">
								<input type="submit" name="submit" id="submit" class="btn btn-primary" value="Change Numbers">
							</div>
						</div>
					</div>
				</form>
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-cog"></i> Change Your Email </h4>
				<!-- <button class="btn btn-info btn-xs pull-right" id="change_email_toggle"><span class="glyphicon-chevron-up glyphicon" id="button_span_email_up"></span></button> -->
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="change_email_panel_body">
				<form method="POST" action="<?= BASEURL . 'affiliate/verifyChangeEmail'?>" accept-charset="utf-8">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-12 col-md-offset-0">
								<p class="help-block"><i>Your current Email Address is <b><?= $affiliate['email']?></b></i></p>
							</div>
						</div>

						<div class="col-md-12">
							<div class="col-md-12 col-md-offset-0">
								<label for="email">Email Address: </label>
							</div>

							<div class="col-md-8 col-md-offset-0">
								<input type="email" name="email" id="email" class="form-control" value="<?= set_value('email') ?>">
								<label style="color: red; font-size: 12px;"><?php echo form_error('email'); ?></label>
							</div>
						</div>

						<div class="col-md-12">
							<div class="col-md-4 col-md-offset-0">
								<input type="submit" name="submit" id="submit" class="btn btn-primary" value="Change Email">
							</div>
						</div>
					</div>
				</form>
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-cog"></i> Change Your IM </h4>
				<!-- <button class="btn btn-info btn-xs pull-right" id="change_im_toggle"><span class="glyphicon-chevron-up glyphicon" id="button_span_im_up"></span></button> -->
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="change_im_panel_body">
				<form method="POST" action="<?= BASEURL . 'affiliate/verifyChangeIM'?>" accept-charset="utf-8">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-12 col-md-offset-0">
								<?php if($affiliate['im'] != null) { ?>
									<p class="help-block"><i>Your current IM is <b><?= $affiliate['im'] . ' in ' . $affiliate['imType'] ?></b></i></p>
								<?php } else { ?>
									<p class="help-block"><i>Your haven't set an IM</i></p>
								<?php } ?>
							</div>
						</div>

						<div class="col-md-12">
							<div class="col-md-6 col-md-offset-0">
								<label for="im">IM: </label>
							</div>

							<div class="col-md-6 col-md-offset-0">
								<label for="imtype">IM Type: </label>
							</div>

							<div class="col-md-5 col-md-offset-0">
								<input type="im" name="im" id="im" class="form-control" value="<?= set_value('im') ?>">
								<label style="color: red; font-size: 12px;"><?php echo form_error('im'); ?></label>
							</div>

							<div class="col-md-5 col-md-offset-1">
								<input type="imtype" name="imtype" id="imtype" class="form-control" value="<?= set_value('imtype') ?>">
								<label style="color: red; font-size: 12px;"><?php echo form_error('imtype'); ?></label>
							</div>
						</div>

						<div class="col-md-12">
							<div class="col-md-4 col-md-offset-0">
								<input type="submit" name="submit" id="submit" class="btn btn-primary" value="Change IM">
							</div>
						</div>
					</div>
				</form>
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
</div>