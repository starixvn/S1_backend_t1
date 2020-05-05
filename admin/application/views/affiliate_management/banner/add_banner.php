<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-picture"></i> Banner Settings </h4>
				<a href="<?= BASEURL . 'affiliate_management/bannerSettings'?>" class="btn btn-primary btn-sm pull-right" id="banner_settings"><span class="glyphicon glyphicon-remove"></span></a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="banner_panel_body">
				<label>Add New Banner</label>
				<hr/>

				<form method="POST" action="<?= BASEURL . 'affiliate_management/verifyAddBanner'?>" accept-charset="utf-8" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-1 col-md-offset-0">
							<label for="banner_name">Banner Name: </label>
						</div>

						<div class="col-md-6 col-md-offset-0">
							<input type="text" name="banner_name" id="banner_name" class="form-control" value="<?= set_value('banner_name'); ?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('banner_name'); ?></label>
						</div>
					</div>

					<br/>

					<div class="row">
						<div class="col-md-1 col-md-offset-0">
							<label for="category">Category: </label>
						</div>

						<div class="col-md-6 col-md-offset-0">
							<select name="category" id="category" class="form-control" onchange="checkCategory(this.value);"> 
								<option value="">Select Category</option>
								<option <?= (set_value('category') == "Full Banner (468x60)") ? 'selected':'' ?> value="Full Banner (468x60)">Full Banner (468x60)</option>
								<option <?= (set_value('category') == "Half Banner (234x60)") ? 'selected':'' ?> value="Half Banner (234x60)">Half Banner (234x60)</option>
								<option <?= (set_value('category') == "Leaderboard (728x90)") ? 'selected':'' ?> value="Leaderboard (728x90)">Leaderboard (728x90)</option>
								<option <?= (set_value('category') == "Square Pop-up (250x250)") ? 'selected':'' ?> value="Square Pop-up (250x250)">Square Pop-up (250x250)</option>
								<option <?= (set_value('category') == "Vertical Rectangle (240x400)") ? 'selected':'' ?> value="Vertical Rectangle (240x400)">Vertical Rectangle (240x400)</option>
								<option <?= (set_value('category') == "Others") ? 'selected':'' ?> value="Others">Others</option>
							</select>
							<label style="color: red; font-size: 12px;"><?php echo form_error('category'); ?></label>
						</div>
					</div>

					<br/>

					<div class="row">
						<div class="col-md-1 col-md-offset-0">
							<label>Size <i>(pixels)</i>: </label>
						</div>

						<div class="col-md-1">
							<input type="text" name="width" id="width" class="form-control" <?= (set_value('category') == 'Others') ? '':'readonly' ?> > 
							<label style="color: red; font-size: 12px;"><?php echo form_error('width'); ?></label>
						</div>

						<div class="col-md-1" style="width: 40px; margin: 10px 0 0 0;">
							<label style="color: black; font-size: 12px;">X</label>
						</div>

						<div class="col-md-1">
							<input type="text" name="height" id="height" class="form-control" <?= (set_value('category') == 'Others') ? '':'readonly' ?> > 
							<label style="color: red; font-size: 12px;"><?php echo form_error('height'); ?></label>
						</div>

					</div>

					<br/>

					<div class="row">
						<div class="col-md-1 col-md-offset-0">
							<label for="banner_url">Banner URL: </label>
						</div>

						<div class="col-md-6 col-md-offset-0">
							<input type="text" name="banner_url" id="banner_url" class="form-control" readonly>
							<label style="color: red; font-size: 12px;"><?php echo form_error('banner_url'); ?></label>
						</div>

						<div class="col-md-4 col-md-offset-0" style="margin: 5px 0 0 0;">
							<input type="file" name="userfile" id="userfile" onchange="setURL(this.value);" value="<?= set_value('userfile'); ?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('userfile'); ?></label>
						</div>
					</div>

					<br/>

					<div class="row">
						<div class="col-md-1 col-md-offset-0">
							<label for="language">Language: </label>
						</div>

						<div class="col-md-6 col-md-offset-0">
							<select name="language" id="language" class="form-control"> 
								<option value="">Select Language</option>
								<option <?= (set_value('language') == "CN") ? 'selected':'' ?> value="CN">Chinese</option>
								<option <?= (set_value('language') == "EN") ? 'selected':'' ?> value="EN">English</option>
							</select>
							<label style="color: red; font-size: 12px;"><?php echo form_error('language'); ?></label>
						</div>
					</div>

					<br/>

					<div class="row">
						<div class="col-md-1 col-md-offset-0">
							<label for="currency">Currency: </label>
						</div>

						<div class="col-md-6 col-md-offset-0">
							<select name="currency" id="currency" class="form-control"> 
								<option value="">Select Currency</option>
								<option <?= (set_value('currency') == "CNY") ? 'selected':'' ?> value="CNY">CNY</option>
								<option <?= (set_value('currency') == "USD") ? 'selected':'' ?> value="USD">USD</option>
							</select>
							<label style="color: red; font-size: 12px;"><?php echo form_error('currency'); ?></label>
						</div>
					</div>

					<br/>

					<div class="row">
						<div class="col-md-2 col-md-offset-0">
							<input type="submit" name="submit" id="submit" class="form-control btn btn-primary" value="Add">
						</div>
					</div>
				</form>
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
</div>