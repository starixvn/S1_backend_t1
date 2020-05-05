<style type="text/css">
	.control-label {
		font-size: 12px;
	}
	.space-bottom {
		margin-top: 1%;
	}
</style>

<form class="form-horizontal" action="<?=BASEURL . 'player_management/searchAllPlayer'?>" method="post" role="form" name="myForm">
	<!--main-->
	<div class="row">
		<div class="col-md-12 col-lg-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title" style="font-family:calibri;font-size:1.4em;"><i class="icon-search" id="hide_main_up"></i> <?=lang('lang.search');?><a href="#main" 
              id="hide_main" class="btn btn-default btn-sm pull-right"><i class="glyphicon glyphicon-chevron-up" id="hide_main_up"></i></a><i class="clearfix"></i></h4>
				</div>

				<!-- Input Hidden -->
				<input type="hidden" name="user_number" id="user_number" class="form-control input-sm">

				<div class="panel panel-body" id="main_panel_body" style="padding-bottom:0;">

                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?=lang('playerinfo');?></h4>
                        </div>
                        <div class="panel panel-body">
                        	<div class="form-group">
								<div class="col-md-3">
									<label class="control-label"><?=lang('player.01');?>:</label>
									<input type="text" name="username" id="username" class="form-control input-sm" value="<?=set_value('username');?>">
									<?php echo form_error('username', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
								</div>
								<div class="col-md-3">
									<label class="control-label"><?=lang('player.71');?>:</label>
									<select name="search_by" id="search_by" class="form-control input-sm">
										<option value="LIKE"><?=lang('player.72');?></option>
										<option value="AND"><?=lang('player.73');?></option>
									</select>
									<?php echo form_error('search_by', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
								</div>
								<div class="col-md-3">
									<label class="control-label"><?=lang('player.16');?>:</label>
									<input type="date" name="start_date" id="start_date" class="form-control input-sm">
									<?php echo form_error('start_date', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
								</div>
								<div class="col-md-3">
									<label class="control-label"><?=lang('player.49');?>:</label>
									<input type="date" name="end_date" id="end_date" class="form-control input-sm">
									<?php echo form_error('end_date', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
								</div>
								<div class="col-md-3">
									<label class="control-label"><?=lang('player.07');?>:</label>
									<select name="player_level" id="player_level" class="form-control input-sm">
										<option value=""><?=lang('player.08');?></option>
				                        <?php foreach ($allLevels as $key => $value) {?>
				                            <option value="<?=$value['vipsettingcashbackruleId']?>"><?=$value['groupName'] . ' ' . $value['vipLevel']?></option>
				                        <?php }
?>
			                        </select>
									<?php echo form_error('player_level', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
								</div>
								<div class="col-md-3">
									<label class="control-label"><?=lang('player.09');?>:</label>
									<input type="text" name="registration_website" id="registration_website" class="form-control input-sm">
									<?php echo form_error('registration_website', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
								</div>
								<div class="col-md-3">
									<label class="control-label"><?=lang('player.10');?>:</label>
									<input type="text" name="ip_address" id="ip_address" class="form-control input-sm">
									<?php echo form_error('ip_address', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
								</div>
								<div class="col-md-3">
									<label class="control-label"><?=lang('player.67');?>:</label>
									<select name="registered_by" id="registered_by" class="form-control input-sm">
										<option value=""><?=lang('lang.select');?></option>
										<option value="website"><?=lang('player.68');?></option>
										<option value="mass_account"><?=lang('player.69');?></option>
									</select>
								</div>
								<div class="col-md-3">
									<label class="control-label"><?=lang('player.04');?>:</label>
									<input type="text" name="first_name" id="first_name" class="form-control input-sm">
									<?php echo form_error('first_name', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
								</div>
								<div class="col-md-3">
									<label class="control-label"><?=lang('player.05');?>:</label>
									<input type="text" name="last_name" id="last_name" class="form-control input-sm">
									<?php echo form_error('last_name', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
								</div>
								<div class="col-md-3">
									<label class="control-label"><?=lang('player.18');?>:</label>
									<input type="text" name="friend_referral_code" id="friend_referral_code" class="form-control input-sm">
									<?php echo form_error('friend_referral_code', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
								</div>
								<div class="col-md-3">
									<label class="control-label"><?=lang('player.06');?>:</label>
									<input type="email" class="form-control input-sm" name="email" id="email">
									<?php echo form_error('email', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
								</div>
								<div class="col-md-3">
									<label class="control-label"><?=lang('player.22');?>:</label>
									<input type="text" name="im_account" id="im_account" class="form-control input-sm">
									<?php echo form_error('im_account', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
								</div>
								<div class="col-md-3">
									<label class="control-label"><?=lang('player.19');?>:</label>
									<input type="text" name="city" id="city" class="form-control input-sm">
									<?php echo form_error('city', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
								</div>
								<div class="col-md-3">
									<label class="control-label"><?=lang('player.20');?>:</label>
									<select name="country" id="country" class="form-control input-sm" >
		                                <option value=""><?=lang('player.21');?></option>
		                                <?php foreach (unserialize(COUNTRY_LIST) as $key) {?>
		                                        <option value="<?=$key?>"><?=lang('country.' . $key)?></option>
		                                <?php }
?>
		                            </select>
									<?php echo form_error('country', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
								</div>
								<div class="col-md-3">
                                    <label for="account_type" class="control-label"><?=lang('player.ui09');?>:</label>
                                    <select name="account_type" id="account_type" class="form-control input-sm">
                                        <option value="" <?=$this->session->userdata('session_account_type') == '' ? 'selected' : ''?>><?=lang('lang.select');?></option>
                                        <option value="real" <?=$this->session->userdata('session_account_type') == 'real' ? 'selected' : ''?>><?=lang('player.ui12');?></option>
                                        <option value="demo" <?=$this->session->userdata('session_account_type') == 'demo' ? 'selected' : ''?>><?=lang('player.ui13');?></option>
                                    </select>
                                </div>
							</div>
                        </div>
                    </div>

                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?=lang('player.74');?></h4>
                        </div>
                        <div class="panel panel-body">
                        	<div class="form-group">
                        		<div class="col-md-12">
                                    <h6>
                                        <input type="radio" onclick="checkDepositCheckBox()" name="deposit_order" id="first_deposited" value="1">
                                        <label for="first_deposited">&nbsp; <?=lang('player.75');?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                        <input type="radio" onclick="checkDepositCheckBox()" name="deposit_order" id="second_deposited" value="2">
                                        <label for="second_deposited">&nbsp; <?=lang('player.76');?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                        <input type="radio" onclick="checkDepositCheckBox()" name="deposit_order" id="never_deposited" value="3">
                                        <label for="never_deposited">&nbsp; <?=lang('player.35');?></label>
                                    </h6>
                                    <?php echo form_error('first_deposited', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                                <div class="col-md-3">
                                    <label for="deposit_date_from"><?=lang('player.29');?>: </label>
                                    <input type="date" name="deposit_date_from" disabled id="deposit_date_from" class="form-control input-sm">
                                    <?php echo form_error('deposit_date_from', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>

                                <div class="col-md-3">
                                   	<label for="deposit_date_to"><?=lang('player.30');?>: </label>
                                    <input type="date" name="deposit_date_to" disabled id="deposit_date_to" class="form-control input-sm">
                                    <?php echo form_error('deposit_date_to', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                                <div class="col-md-6">
                                    <h6>
	                                    <input type="radio" name="deposit_amount_type" disabled id="deposit_amount_type1" value="1" ><label for="deposit_amount">&nbsp;<?=lang('player.27');?>: </label>&nbsp;&nbsp;&nbsp;
	                                    <input type="radio" name="deposit_amount_type" disabled id="deposit_amount_type2" value="2" ><label for="deposit_amount">&nbsp;<?=lang('player.28');?>: </label>
	                                </h6>
	                                <input type="text" name="deposit_amount" disabled id="deposit_amount" style="width:50%;" class="form-control input-sm number_only" value="">
                                    <?php echo form_error('deposit_amount', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                        	</div>
                        </div>
                    </div>

					<div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?=lang('player.79');?></h4>
                        </div>
                        <div class="panel panel-body">
                        	<div class="form-group">
                        		<div class="col-md-2">
                                    <h6><label for="status"><?=lang('lang.status');?>: </label></h6>
                                    <select name="status" id="status" class="form-control input-sm">
                                        <option value=""><?=lang('player.13');?></option>
                                        <option value="0" <?=$this->session->userdata('session_status') == '0' ? 'selected' : ''?>><?=lang('player.14');?></option>
                                        <option value="1" <?=$this->session->userdata('session_status') == '1' ? 'selected' : ''?>><?=lang('player.15');?></option>
                                    </select>
                                    <?php echo form_error('status', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>

                                <div class="col-md-2">
                                    <h6><label for="tagged"><?=lang('player.26');?>: </label></h6>
                                    <select name="tagged" id="tagged" class="form-control input-sm">
                                        <option value=""><?=lang('lang.select');?></option>
                                        <?php foreach ($tags as $tag) {?>
                                            <option value="<?=$tag['tagId']?>" <?=$this->session->userdata('session_tagId') == $tag['tagId'] ? 'selected' : ''?>><?=$tag['tagName']?></option>
                                        <?php }
?>
                                    </select>
                                    <?php echo form_error('tagged', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>

                                <div class="col-md-2">
                                    <h6><label for="blocked_gaming_networks"><?=lang('player.33');?>:</label></h6>
                                    <select class="form-control input-sm" name="blocked_gaming_networks" id="blocked_gaming_networks">
                                        <option value=""><?=lang('player.34');?></option>
                                        <?php foreach ($games as $row) {?>
                                            <option value="<?=$row['gameId']?>" <?=$this->session->userdata('session_blocked') == $row['gameId'] ? 'selected' : ''?>><?=$row['game']?></option>
                                        <?php }
?>
                                    </select>
                                    <?php echo form_error('blocked_gaming_networks', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>

                                <div class="col-md-3">
                                    <h6><label for="promo"><?=lang('player.36');?>:</label></h6>
                                    <select name="promo" id="promo" class="form-control input-sm">
                                        <option value=""><?=lang('player.37');?></option>
                                        <?php foreach ($promo as $key => $value) {?>
                                            <option value="<?=$value['depositpromoId']?>" <?=$this->session->userdata('session_promo') == $value['depositpromoId'] ? 'selected' : ''?>><?=$value['promoName']?></option>
                                        <?php }
?>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <h6><label for="affiliate"><?=lang('player.24');?>: </label></h6>
                                    <select name="affiliate" id="affiliate" class="form-control input-sm">
                                        <option value=""><?=lang('player.25');?></option>
                                        <?php foreach ($affiliates as $key => $value) {?>
                                            <option value="<?=$value['affiliateId']?>" <?=$this->session->userdata('session_affiliate') == $value['affiliateId'] ? 'selected' : ''?>><?=$value['username']?></option>
                                        <?php }
?>
                                    </select>
                                    <?php echo form_error('affiliate', '<span class="help-block" style="color:#ff6666;">', '</span>');?> <br/>
                                </div>
                                <div class="col-md-4">
                                    <h6>
                                        <input type="radio" onclick="checkWalletCheckBox()" name="wallet_order" id="main_wallet" value="1">
                                        <label for="first_deposited">&nbsp; <?=lang('pay.mainwallt');?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                        <input type="radio" onclick="checkWalletCheckBox()" name="wallet_order" id="pt_wallet" value="2">
                                        <label for="second_deposited">&nbsp; <?=lang('pay.ptwallt');?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                        <input type="radio" onclick="checkWalletCheckBox()" name="wallet_order" id="ag_wallet" value="3">
                                        <label for="never_deposited">&nbsp; <?=lang('pay.agwallt');?></label>
                                    </h6>
                                </div>
                                <div class="col-md-1">
                                    <h6><label for="wallet_amount">&nbsp; <?=lang('player.ui21');?>:</label></h6>
                                </div>
                                <div class="col-md-4 pull-right">
                                    <h6>
	                                    <input type="radio" name="wallet_amount_type" disabled id="wallet_amount_type1" value="1" ><label for="wallet_amount_type1">&nbsp;<?=lang('player.78');?>: </label>&nbsp;&nbsp;&nbsp;
	                                    <input type="radio" name="wallet_amount_type" disabled id="wallet_amount_type2" value="2" ><label for="wallet_amount_type2">&nbsp;<?=lang('player.79');?>: </label>
                                    </h6>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="wallet_amount" disabled id="wallet_amount" style="width:100%;" class="form-control input-sm number_only" value="">
                                    <?php echo form_error('wallet_balance', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>
                        </div>
                    </div>

					<!-- <div class="form-group">
						<div class="col-md-3">
							<label class="control-label"><?=lang('player.17');?>:</label>
							<input type="date" name="birthdate" id="birthdate" class="form-control input-sm">
							<?php echo form_error('birthdate', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
						</div>
						<div class="col-md-3">
							<label class="control-label"><?=lang('player.23');?>:</label>
							<input type="text" name="qq" id="qq" class="form-control input-sm">
							<?php echo form_error('qq', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
						</div>
					</div> -->

<!-- 					<div class="form-group">
						<div class="col-md-12">
		                    <label class="col-md-2 checkbox-inline">
		                        <input type="checkbox" name="first_deposit_date" id="first_deposit_date" value="first_deposit"/> <?=lang('player.75');?>
		                    </label>
		                    <label class="col-md-2 checkbox-inline">
		                        <input type="checkbox" name="second_deposit_date" id="second_deposit_date" value="second_deposit"/> <?=lang('player.76');?>
		                    </label>
		                    <label class="col-md-2 checkbox-inline">
		                        <input type="checkbox" name="never_deposited" id="never_deposited" value="never_deposited"/> <?=lang('player.35');?>
		                    </label>
						</div>
						<div class="col-md-3">
							<label class="control-label"><?=lang('player.77');?>:</label>
							<input type="date" name="from_date" id="from_date" class="form-control input-sm">
							<?php echo form_error('from_date', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
						</div>
						<div class="col-md-3">
							<label class="control-label"><?=lang('player.78');?>:</label>
							<input type="date" name="to_date" id="to_date" class="form-control input-sm">
							<?php echo form_error('to_date', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
						</div>
						<div class="col-md-3">
							<label class="control-label"><?=lang('player.27');?>:</label>
							<input type="text" name="less_deposit_amount" id="less_deposit_amount" class="form-control input-sm">
							<?php echo form_error('less_deposit_amount', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
						</div>
						<div class="col-md-3">
							<label class="control-label"><?=lang('player.28');?>:</label>
							<input type="text" name="greater_deposit_amount" id="greater_deposit_amount" class="form-control input-sm">
							<?php echo form_error('greater_deposit_amount', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
						</div>
					</div> -->

<!-- 					<hr style="margin-top:5px;margin-bottom:0;">
					<h4><?=lang('player.79');?></h4>
					<hr style="margin-top:0;margin-bottom:5px;">

					<div class="form-group">
						<div class="col-md-2">
							<label class="control-label"><?=lang('lang.status');?>:</label>
							<select name="status" id="status" class="form-control input-sm">
								<option value=""><?=lang('player.13');?></option>
								<option value="0"><?=lang('player.14');?></option>
								<option value="1"><?=lang('player.15');?></option>
							</select>
							<?php echo form_error('status', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
						</div>
						<div class="col-md-2">
							<label class="control-label"><?=lang('player.26');?>:</label>
							<select name="tagged" id="tagged" class="form-control input-sm">
								<option value=""><?=lang('lang.select');?></option>
								<?php foreach ($tags as $tag) {?>
									<option value="<?=$tag['tagId']?>"><?=$tag['tagName']?></option>
								<?php }
?>
							</select>
							<?php echo form_error('tagged', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
						</div>
						<div class="col-md-2">
							<label class="control-label"><?=lang('player.33');?>:</label>
							<select class="form-control input-sm" name="blocked_gaming_networks" id="blocked_gaming_networks">
								<option value=""><?=lang('player.34');?></option>
								<?php foreach ($games as $row) {?>
									<option value="<?=$row['gameId']?>"><?=$row['game']?></option>
								<?php }
?>
							</select>
							<?php echo form_error('blocked_gaming_networks', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
						</div>
						<div class="col-md-3">
							<label class="control-label"><?=lang('player.36');?>:</label>
							<select name="promo" id="promo" class="form-control input-sm">
								<option value=""><?=lang('player.37');?></option>
								<?php foreach ($promo as $key => $value) {?>
									<option value="<?=$value['depositpromoId']?>"><?=$value['promoName']?></option>
								<?php }
?>
							</select>
							<?php echo form_error('promo', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
						</div>
						<div class="col-md-3">
							<label class="control-label"><?=lang('player.24');?>:</label>
							<select name="affiliate" id="affiliate" class="form-control input-sm">
								<option value=""><?=lang('player.25');?></option>
								<?php foreach ($affiliates as $key => $value) {?>
									<option value="<?=$value['affiliateId']?>"><?=$value['username']?></option>
								<?php }
?>
							</select>
							<?php echo form_error('affiliate', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
						</div>
					</div> -->

					<hr style="margin-bottom:0;">
					<div style="text-align:center;">
						<input type="reset" value="<?=lang('lang.reset');?>" class="btn btn-default btn-sm">
						<input type="submit" value="<?=lang('lang.search');?>" id="search_main"class="btn btn-info btn-sm">
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--end of main-->
</form>

<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="icon-users"></i> <?=lang('player.44');?> </h4>
				<div class="pull-right">
					<!--a href="#show_search" role="button" class="btn btn-primary" id="show_search"><span class="glyphicon glyphicon-search"></span></a-->
					<a href="#modal_column" role="button" data-toggle="modal" class="btn btn-default" id="edit_column"><span class="glyphicon glyphicon-list"></span></a>
				</div>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="player_panel_body">
				<form action="<?=BASEURL . 'player_management/selectedPlayers'?>" method="post" role="form">
					<div id="playerList" class="table-responsive" style="overflow: auto;">
						<!--center><span class="help-block">Total number of rows: <?=$count_all?> (page <?=$current_page?> of <?=$total_pages?>), Calculated <?=$today?></span></center-->
						<table class="table table-striped table-hover" style="margin: 10px 0 0 0; width: 100%;" id="myTable">
							<? echo'<input type="submit" class="btn btn-danger btn-sm" value="".lang("player.45").""><hr/>' ?>
							<thead>
								<tr>
									<th></th>
									<th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
									<th><?=lang('player.01');?></th>
									<?=$this->session->userdata('name') == "checked" || !$this->session->userdata('name') ? '<th id="visible">' . lang("player.40") . '</th>' : ''?>
									<?=$this->session->userdata('level') == "checked" || !$this->session->userdata('level') ? '<th>' . lang("player.39") . '</th>' : ''?>
									<?=$this->session->userdata('email') == "checked" || !$this->session->userdata('email') ? '<th id="visible">' . lang("player.06") . '</th>' : ''?>
									<?=$this->session->userdata('country') == "checked" || !$this->session->userdata('country') ? '<th id="visible">' . lang("player.20") . '</th>' : ''?>
									<?=$this->session->userdata('tag') == "checked" || !$this->session->userdata('tag') ? '<th id="visible">' . lang("player.41") . '</th>' : ''?>
									<?=$this->session->userdata('last_login_time') == "checked" || !$this->session->userdata('last_login_time') ? '<th id="visible">' . lang("player.42") . '</th>' : ''?>
									<?=$this->session->userdata('registered_on') == "checked" || !$this->session->userdata('registered_on') ? '<th>' . lang("player.43") . '</th>' : ''?>
									<?=$this->session->userdata('registered_by') == "checked" || !$this->session->userdata('registered_by') ? '<th id="visible">' . lang("player.67") . '</th>' : ''?>
									<?=$this->session->userdata('status_col') == "checked" || !$this->session->userdata('status_col') ? '<th id="visible">' . lang("lang.status") . '</th>' : ''?>
									<th><?=lang("lang.action");?></th>
								</tr>
							</thead>

							<tbody>
								<?php if (!empty($players)) {
	foreach ($players as $players) {
		$name = $players['lastName'] . " " . $players['firstName'];
		?>
									<tr <?=($players['status'] == 1 || $players['status'] == 2) ? 'class="danger"' : '';?> >
										<td></td>
										<td style="text-align:center;"><input type="checkbox" class="checkWhite" id="<?=$players['playerId']?>" name="players[]" value="<?=$players['playerId']?>" onclick="uncheckAll(this.id)"></td>
										<td><a href="<?=BASEURL . 'player_management/userInformation/' . $players['playerId']?>"><?=$players['username']?></a></td>

										<?php if ($this->session->userdata('name') == "checked" || !$this->session->userdata('name')) {?>
											<td id="visible"><?=($players['lastName'] == '') && ($players['firstName'] == '') ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $name?></td>
										<?php }
		?>

										<?php if ($this->session->userdata('level') == "checked" || !$this->session->userdata('level')) {?>
											<td><?=$players['groupName'] == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $players['groupName'] . ' ' . $players['vipLevel']?></td>
										<?php }
		?>

										<?php if ($this->session->userdata('email') == "checked" || !$this->session->userdata('email')) {?>
											<td id="visible"><?=$players['email'] == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $players['email']?></td>
										<?php }
		?>

										<?php if ($this->session->userdata('country') == "checked" || !$this->session->userdata('country')) {?>
											<td id="visible"><?=$players['country'] == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $players['country']?></td>
										<?php }
		?>

										<?php if ($this->session->userdata('tag') == "checked" || !$this->session->userdata('tag')) {?>
											<td id="visible"><?=$players['tagName'] == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $players['tagName']?></td>
										<?php }
		?>

										<?php if ($this->session->userdata('last_login_time') == "checked" || !$this->session->userdata('last_login_time')) {?>
											<td id="visible"><?=$players['lastLoginTime'] == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $players['lastLoginTime']?></td>
										<?php }
		?>

										<?php if ($this->session->userdata('registered_on') == "checked" || !$this->session->userdata('registered_on')) {?>
											<td><?=$players['createdOn'] == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $players['createdOn']?></td>
										<?php }
		?>

										<?php if ($this->session->userdata('registered_by') == "checked" || !$this->session->userdata('registered_by')) {?>
											<td id="visible"><?=$players['registered_by'] == 'website' ? lang("player.68") : lang("player.69")?></td>
										<?php }
		?>

										<?php if ($this->session->userdata('status_col') == "checked" || !$this->session->userdata('status_col')) {?>
											<td id="visible"><?=$players['status'] == 0 ? lang("player.14") : lang("player.15")?></td>
										<?php }
		?>
										<td>
											<!-- <a href="#showDetails" data-toggle="tooltip" class="details" onclick="viewPlayer(<?=$players['playerId']?>, 'overview');"><span class="glyphicon glyphicon-zoom-in"></span></a>-->
											<?php if ($this->permissions->checkPermissions('adjust_player_level')) {?>
												<a href="#showPlayerLevel" data-toggle="tooltip" title="<?=lang('tool.pm01');?>" class="playerlevel" onclick="viewPlayer(<?=$players['playerId']?>, 'adjustplayerlevel');"><span class="glyphicon glyphicon-edit"></span></a>
											<?php }
		?>
											<?php if ($this->permissions->checkPermissions('tag_player')) {?>
												<a href="#tags" data-toggle="tooltip" title="<?=lang('tool.pm04');?>" class="tags" onclick="viewPlayerWithCurrentPage(<?=$players['playerId']?>, 'playerTag', 'playerlist');"><span class="glyphicon glyphicon-tag"></span></a>
											<?php }
		?>

											<?php if ($this->permissions->checkPermissions('lock_player')) {?>
												<!-- <a href="<?=BASEURL . 'player_management/lockPlayer/' . $players['playerId'] . '/' . $players['status'] . '/playerlist'?>" data-toggle="tooltip" class="lock"><span class="glyphicon glyphicon-lock"></span></a> -->
												<a href="#lockPlayer" data-toggle="tooltip" title="<?=lang('tool.pm02');?>" class="lock" onclick="viewPlayerWithCurrentPage(<?=$players['playerId']?>, 'lockedPlayer', 'playerlist');"><span class="glyphicon glyphicon-lock"></span></a>
											<?php }
		?>
											<?php if ($this->permissions->checkPermissions('block_player')) {?>
												<!-- <a href="<?=BASEURL . 'player_management/blockPlayer/' . $players['playerId'] . '/' . $players['blocked'] . '/playerlist'?>" data-toggle="tooltip" class="block"><span class="glyphicon glyphicon-ban-circle"></span></a> -->
												<a href="#blockPlayer" data-toggle="tooltip" title="<?=lang('tool.pm03');?>" class="block" onclick="viewPlayerWithCurrentPage(<?=$players['playerId']?>, 'blockPlayerInGame', 'playerlist');"><span class="glyphicon glyphicon-ban-circle"></span></a>
											<?php }
		?>
										</td>
									</tr>
								<?php }
} else {?>
									<!-- <tr>
			                            <td colspan="10" style="text-align:center"><span class="help-block"><?=lang('lang.norec');?></span></td>
			                        </tr> -->
						        <?php }
?>
							</tbody>
						</table>

						<!-- <br/>

						<div class="col-md-12 col-offset-0">
						    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links();?> </ul>
					    </div> -->
					</div>
				</form>
			</div>

		</div>
	</div>

	<div class="col-md-7" id="player_details" style="display: none;">

	</div>
</div>

<!--MODAL for edit column-->
<div id="modal_column" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_column" aria-hidden="true">
<div class="modal-dialog modal-sm">
<div class="modal-content panel-primary">
	<div class="modal-header panel-heading">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="myModalLabel"><?=lang("player.53");?></h3>
	</div>
	<div class="modal-body">
		<div class="row">
			<div class="col-md-12">
				<div class="help-block">
					<?=lang("player.54");?>
				</div>
			</div>
		</div>

		<div class="row">
		<form action="<?=BASEURL . 'player_management/postChangeColumns'?>" method="post" role="form" id="modal_column_form">
			<div class="col-md-6 col-md-offset-1">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="name" value="name" <?=$this->session->userdata('name') == "checked" || !$this->session->userdata('name') ? 'checked' : ''?>> <?=lang("player.40");?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="level" value="level" <?=$this->session->userdata('level') == "checked" || !$this->session->userdata('level') ? 'checked' : ''?>> <?=lang("player.39");?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="email" value="email" <?=$this->session->userdata('email') == "checked" || !$this->session->userdata('email') ? 'checked' : ''?>> <?=lang("player.06");?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="country" value="country" <?=$this->session->userdata('country') == "checked" || !$this->session->userdata('country') ? 'checked' : ''?>> <?=lang("player.20");?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="last_login_time" value="last_login_time" <?=$this->session->userdata('last_login_time') == "checked" || !$this->session->userdata('last_login_time') ? 'checked' : ''?>> <?=lang("player.42");?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="tag" value="tag" <?=$this->session->userdata('tag') == "checked" || !$this->session->userdata('tag') ? 'checked' : ''?>> <?=lang("player.41");?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="status_col" value="status_col" <?=$this->session->userdata('status_col') == "checked" || !$this->session->userdata('status_col') ? 'checked' : ''?>> <?=lang("lang.status");?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="registered_on" value="registered_on" <?=$this->session->userdata('registered_on') == "checked" || !$this->session->userdata('registered_on') ? 'checked' : ''?>> <?=lang("player.43");?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="registered_by" value="registered_by" <?=$this->session->userdata('registered_by') == "checked" || !$this->session->userdata('registered_by') ? 'checked' : ''?>> <?=lang("player.67");?>
					</label>
				</div>
			</div>
		</form>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?=lang("lang.close");?></button>
		<button class="btn btn-primary" id="save_changes" name="save_changes"><?=lang("player.55");?></button>
	</div>
</div>
</div>
<!--end of MODAL for edit column-->

<script type="text/javascript">
	$(document).ready(function(){
		$('#myTable').DataTable({
			"responsive": {
	                details: {
	                    type: 'column'
	                }
	            },
	            "columnDefs": [ {
	                className: 'control',
	                orderable: false,
	                targets:   0
	            } ],
	            "order": [ 1, 'asc' ]
	        });
	});
</script>
