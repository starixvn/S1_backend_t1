<!--main-->
<form class="form-horizontal" action="<?=site_url('player_management/searchAllPlayer')?>" method="post" role="form" name="myForm">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary
              " style="margin-bottom:10px;">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="icon-search" id="hide_main_up"></i> <?=lang('lang.search');?>
                        <span class="pull-right">
                            <span class="panel" style="border-color:#DDDDDD;padding-bottom:2px;">
                                &nbsp;
                                <!-- <input type="checkbox" id="checkall" value="checkall" onclick="checkAllPlayerInfo(this.value)" checked/> <span style="font-size:14px;"><?=lang('player.ui02');?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
                                <label>
                                    <input type="checkbox" id="playerinfo_checkbox" value="player" onclick="return false" checked/> <span style="font-size:14px;"><?=lang('lang.playerinfo');?></span>
                                </label>
                                <label>
                                    <input type="checkbox" id="deposit" value="deposit" onclick="checkSearchPlayerInfo(this.value);"/> <span style="font-size:14px;"><?=lang('player.74');?></span>
                                </label>
                                <label>
                                    <input type="checkbox" id="gspa" value="gspa" onclick="checkSearchPlayerInfo(this.value);"/> <span style="font-size:14px;"><?=lang('lang.gameinfo');?></span>
                                </label>
                            </span>&nbsp;
                            <a href="#main" style="color: gray;" id="hide_main" class="btn btn-default btn-sm">
                                <i class="glyphicon glyphicon-chevron-up" id="hide_main_up"></i>
                            </a>
                        </span>
                    </h4>
                </div>
                <span class="clearfix"></span>
                <div class="panel panel-body main_panel_body" id="main_panel_body">
                    <fieldset>
                        <legend><?=lang('lang.playerinfo');?></legend>
                        <div class="form-group">
                            <div class="col-md-3 col-lg-2">
                                <label for="username" class="control-label"><?=lang('player.01');?>:</label>
                                <input type="text" name="username" id="username" class="form-control input-sm" value="<?=$this->session->userdata('session_username')?>">
                                <input type="hidden" name="user_number" id="user_number" class="form-control input-sm">
                            </div>
                            <div class="col-md-3 col-lg-2">
                                <label for="username" class="control-label"><?=lang('player.71');?>:</label>
                                <select name="search_by" id="search_by" class="form-control input-sm">
                                    <option value="1" <?=$this->session->userdata('session_searchby') == '1' ? 'selected' : ''?>><?=lang('player.02');?></option>
                                    <option value="2" <?=$this->session->userdata('session_searchby') == '2' ? 'selected' : ''?>><?=lang('player.03');?></option>
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-4">
                                <input type="checkbox" name="search_reg_date" id="search_reg_date" value="1" checked="checked">
                                <label class="control-label"><?=lang('player.38');?>: </label>

                                <input id="reportrange" class="form-control input-sm dateInput search_reg_date_date_picker" data-start="#start_date" data-end="#end_date" />
                                <input type="hidden" name="start_date" id="start_date" value="<?=$this->session->userdata('start_date')?>" />
                                <input type="hidden" name="end_date" id="end_date" value="<?=$this->session->userdata('end_date ')?>"/>
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <label for="player_level" class="control-label"><?=lang('player.07');?>: </label>
                                <select name="player_level" id="player_level" class="form-control input-sm">
                                    <option value=""><?=lang('player.08');?></option>
                                    <?php foreach ($allLevels as $key => $value) {?>
                                        <option value="<?=$value['vipsettingcashbackruleId']?>" <?=$this->session->userdata('session_playerLevel') == $value['vipsettingcashbackruleId'] ? 'selected' : ''?> ><?=$value['groupName'] . ' ' . $value['vipLevel']?></option>
                                    <?php }
?>
                                </select>
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <label for="registration_website" class="control-label"><?=lang('player.09');?>: </label>
                                <input type="text" name="registration_website" id="registration_website" class="form-control input-sm" value="<?=$this->session->userdata('session_registrationWebsite')?>">
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <label for="ip_address" class="control-label"><?=lang('player.10');?>: </label>
                                <input type="text" name="ip_address" id="ip_address" class="form-control input-sm" value="<?=$this->session->userdata('session_registrationIP')?>">
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <label for="registered_by" class="control-label"><?=lang('player.67');?>:</label>
                                <select name="registered_by" id="registered_by" class="form-control input-sm">
                                    <option value="" <?=$this->session->userdata('session_registered_by') == Null ? 'selected' : ''?> ><?=lang('lang.select');?></option>
                                    <option value="website" <?=$this->session->userdata('session_registered_by') == "website" ? 'selected' : ''?> ><?=lang('player.68');?></option>
                                    <option value="mass_account" <?=$this->session->userdata('session_registered_by') == "mass_account" ? 'selected' : ''?>><?=lang('player.69');?></option>
                                </select>
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <label for="friend_referral_code" class="control-label"><?=lang('player.18');?>: </label>
                                <input type="text" name="friend_referral_code" id="friend_referral_code" class="form-control input-sm" value="<?=$this->session->userdata('session_referral_id')?>">
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <label for="account_type" class="control-label"><?=lang('player.ui09');?>:</label>
                                <select name="account_type" id="account_type" class="form-control input-sm">
                                    <option value="" <?=$this->session->userdata('session_account_type') == Null ? 'selected' : ''?>><?=lang('lang.select');?></option>
                                    <option value="real" <?=$this->session->userdata('session_account_type') == "real" ? 'selected' : ''?>><?=lang('player.ui12');?></option>
                                    <option value="demo" <?=$this->session->userdata('session_account_type') == "demo" ? 'selected' : ''?>><?=lang('player.ui13');?></option>
                                </select>
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <label for="first_name" class="control-label"><?=lang('player.04');?>: </label>
                                <input type="text" name="first_name" id="first_name" class="form-control input-sm" value="<?=$this->session->userdata('session_firstName')?>">
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <label for="last_name" class="control-label"><?=lang('player.05');?>: </label>
                                <input type="text" name="last_name" id="last_name" class="form-control input-sm" value="<?=$this->session->userdata('session_lastName')?>">
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <label for="email" class="control-label"><?=lang('player.06');?>:</label>
                                <input type="email" class="form-control input-sm" name="email" id="email" value="<?=$this->session->userdata('session_email')?>">
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <label for="im_account" class="control-label"><?=lang('player.22');?>: </label>
                                <input type="text" name="im_account" id="im_account" class="form-control input-sm" value="<?=$this->session->userdata('session_imAccount')?>">
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <label for="city" class="control-label"><?=lang('player.19');?>: </label>
                                <input type="text" name="city" id="city" class="form-control input-sm" value="<?=$this->session->userdata('session_city')?>">
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <label for="country" class="control-label"><?=lang('player.20');?>: </label>
                                <select name="country" id="country" class="form-control input-sm">
                                    <option value="" <?=$this->session->userdata('session_country') == Null ? 'selected' : ''?>><?=lang('player.21');?></option>
                                    <?php foreach (unserialize(COUNTRY_LIST) as $key) {?>
                                            <option value="<?=$key?>" <?=$this->session->userdata('session_country') == $key ? 'selected' : ''?>><?=lang('country.' . $key)?></option>
                                    <?php }
?>
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset id="deposit_search" style="display:none;">
                        <legend><?=lang('player.74');?></legend>
                        <div class="form-group">
                            <div class="col-md-3 col-lg-3">
                                <label for="first_deposited" class="control-label"> <?=lang('pay.deptype');?></label>
                                <div style="border:1px solid #E8E8E8;border-radius:5px;padding:0 10px 4px 10px;">
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" onclick="checkDepositCheckBox()" name="deposit_order" id="first_deposited" value="1"> <?=lang('player.75');?>
                                    </label>
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" onclick="checkDepositCheckBox()" name="deposit_order" id="second_deposited" value="2"> <?=lang('player.76');?>
                                    </label>
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" onclick="checkDepositCheckBox()" name="deposit_order" id="never_deposited" value="3"> <?=lang('player.35');?>
                                    </label>
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" onclick="checkDepositCheckBox()" name="deposit_order" id="deposited" value="4"> <?=lang('pay.deposited');?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 col-lg-4">
                                <label for="deposit_date_from" class="control-label"><?=lang('cashier.deposit_datetime');?>: </label>
                                <input id="reportrange" class="form-control input-sm dateInput" data-start="#deposit_date_from" data-end="#deposit_date_to"/>
                                <input type="hidden" name="deposit_date_from" id="deposit_date_from">
                                <input type="hidden" name="deposit_date_to" id="deposit_date_to">
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <label for="first_deposited" class="control-label"> <?=lang('report.p20');?></label>
                                <div style="border:1px solid #E8E8E8;border-radius:5px;padding:0 10px 4px 10px;">
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" name="deposit_amount_type" id="deposit_amount_type3" value="3" checked="checked">
                                        <?=lang('cms.anyAmt');?>
                                    </label>
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" name="deposit_amount_type" id="deposit_amount_type1" value="1" >
                                        <?=lang('mark.deposit');?> <span data-toggle="tooltip" data-placement="top" title="<?=lang('player.87');?>">&#60;</span> <?=lang('pay.amt');?>
                                    </label>
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" name="deposit_amount_type" id="deposit_amount_type2" value="2" >
                                        <?=lang('mark.deposit');?> <span data-toggle="tooltip" data-placement="top" title="<?=lang('player.88');?>">&#62;</span> <?=lang('pay.amt');?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <label for="deposit_date_to" class="control-label"><?=lang('system.word32');?>: </label>
                                <input type="text" name="deposit_amount" disabled id="deposit_amount" class="form-control input-sm number_only" value="">
                            </div>
                        </div>
                    </fieldset>

                    <fieldset id="gspa_search" style="display:none;">
                        <legend><?=lang('lang.gameinfo');?></legend>

                        <div class="form-group">
                            <div class="col-md-2 col-lg-2">
                                <label for="tagged" class="control-label"><?=lang('player.26');?>: </label>
                                <select name="tagged" id="tagged" class="form-control input-sm">
                                    <option value=""><?=lang('lang.select');?></option>
                                    <?php if (!empty($tags)) {
	?>
                                    	<?php foreach ($tags as $tag) {?>
                                        <option value="<?=$tag['tagId']?>" ><?=$tag['tagName']?></option>
                                        <?php }
	?>
                                    <?php }
?>
                                </select>
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <label for="status" class="control-label"><?=lang('role.25');?>: </label>
                                <select name="status" id="status" class="form-control input-sm">
                                    <option value=""><?=lang('player.13');?></option>
                                    <option value="1" ><?=lang('tool.pm08');?></option>
                                    <option value="0" ><?=lang('tool.pm09');?></option>
                                </select>
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <label for="blocked_gaming_networks" class="control-label"><?=lang('player.33');?>:</label>
                                <select class="form-control input-sm" name="blocked_gaming_networks" id="blocked_gaming_networks">
                                    <option value=""><?=lang('player.34');?></option>
                                    <?php foreach ($game_platforms as $row) {?>
                                        <option value="<?=$row['id']?>" ><?=$row['system_code']?></option>
                                    <?php }
?>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <label for="promo" class="control-label"><?=lang('player.36');?>:</label>
                                <select name="promo" id="promo" class="form-control input-sm">
                                    <option value=""><?=lang('player.37');?></option>
                                    <?php foreach ($promo as $key => $value) {?>
                                        <option value="<?=$value['promorulesId']?>" ><?=$value['promoName']?></option>
                                    <?php }
?>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <label for="affiliate" class="control-label"><?=lang('Under Affiliate');?>: </label>
                                <select name="affiliate" id="affiliate" class="form-control input-sm">
                                    <option value=""><?=lang('player.25');?></option>
                                    <?php foreach ($affiliates as $key => $value) {?>
                                        <option value="<?=$value['affiliateId']?>" ><?=$value['username']?></option>
                                    <?php }
?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="first_deposited" class="control-label"> <?=lang('player.uw06');?></label>
                                <div style="border:1px solid #E8E8E8;border-radius:5px;padding:0 10px 4px 10px;">
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" onclick="checkWalletCheckBox()" name="wallet_order" id="main_wallet" value="0"> <?=lang('pay.mainwallt');?>
                                    </label>
                                    <?php foreach ($game_platforms as $row) {?>
                                        <!-- <option value="<?=$row['id']?>" ><?=$row['system_code']?></option> -->
                                        <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                            <input type="radio" onclick="checkWalletCheckBox()" name="wallet_order" id="<?=strtolower($row['system_code'])?>_wallet" value="<?=$row['id']?>"> <?=$row['system_code'] . ' ' . lang('player.uw06');?>
                                        </label>
                                    <?php }
?>
                                </div>
                            </div>
                            <div class="col-md-5 col-lg-2">
                                <label for="wallet_amount" class="control-label"><?=lang('system.word32');?>:</label>
                                <div style="border:1px solid #E8E8E8;border-radius:5px;padding:0 10px 4px 10px;">
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" name="wallet_amount_type" disabled id="wallet_amount_type1" value="1" >
                                        <span data-toggle="tooltip" data-placement="top" title="<?=lang('player.87');?>">&#60;</span> <?=lang('pay.amt');?>
                                    </label>
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" name="wallet_amount_type" disabled id="wallet_amount_type2" value="2" >
                                        <span data-toggle="tooltip" data-placement="top" title="<?=lang('player.88');?>">&#62;</span> <?=lang('pay.amt');?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-2">
                                <label for="wallet_amount" class="control-label"><?=lang('player.ui21');?>:</label>
                                <input type="text" name="wallet_amount" disabled id="wallet_amount" style="width:100%;" class="form-control input-sm number_only" value="">
                            </div>
                        </div>
                    </fieldset>
                    <center style="margin-top:10px;">
                        <input type="reset" value="<?=lang('lang.reset');?>" class="btn btn-default btn-sm" onclick="window.location='/player_management/viewAllPlayer';">
                        <input type="submit" value="<?=lang('lang.search');?>" id="search_main"class="btn btn-info btn-sm">
                    </center>
                </div>
            </div>
        </div>
    </div>
    <!--end of main-->
</form>

<div class="row">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading custom-ph">
                <h4 class="panel-title custom-pt">
                    <i class="icon-users"></i> <?=lang('player.44');?>
                    <a href="#modal_column" role="button" data-toggle="modal" class="btn btn-sm btn-default pull-right">
                        <span class="glyphicon glyphicon-list"></span> <?=lang('sys.vu57');?>
                    </a>
                    <span class="clearfix"></span>
                </h4>
            </div>

            <div class="panel-body" id="player_panel_body">
                <form action="<?=site_url('player_management/selectedPlayers')?>" method="post" role="form">

                    <table class="table table-striped table-hover table-condensed" style="margin: 0px 0 0 0; width: 100%;" id="myTable">
                        <!-- <hr class="hr_between_table"/> -->
                        <?php if ($this->permissions->checkPermissions('lock_player') || $this->permissions->checkPermissions('block_player')
	|| $this->permissions->checkPermissions('tag_player') || $this->permissions->checkPermissions('edit_player_vip_level')) {?>
                            <input type="submit" class="btn btn-danger btn-xs btn-action" value="<?=lang('player.45')?>">
                        <?php }
?>
                        <thead id="thead">
                            <tr>
                                <th></th>
                                <th style="padding: 8px"><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                                <th><?=lang("lang.action");?></th>
                                <th><?=lang('player.01');?></th>
                                <?=$this->session->userdata('name') == "checked" || !$this->session->userdata('name') ? '<th id="visible">' . lang("sys.vu19") . '</th>' : ''?>
                                <?=$this->session->userdata('level') == "checked" || !$this->session->userdata('level') ? '<th>' . lang("player.39") . '</th>' : ''?>
                                <?=$this->session->userdata('email') == "checked" || !$this->session->userdata('email') ? '<th id="visible">' . lang("player.06") . '</th>' : ''?>
                                <?=$this->session->userdata('country') == "checked" || !$this->session->userdata('country') ? '<th id="visible">' . lang("player.20") . '</th>' : ''?>
                                <?=$this->session->userdata('tag') == "checked" || !$this->session->userdata('tag') ? '<th id="visible">' . lang("player.41") . '</th>' : ''?>
                                <?=$this->session->userdata('last_login_time') == "checked" || !$this->session->userdata('last_login_time') ? '<th id="visible">' . lang("player.42") . '</th>' : ''?>
                                <?=$this->session->userdata('registered_on') == "checked" || !$this->session->userdata('registered_on') ? '<th>' . lang("player.43") . '</th>' : ''?>
                                <?=$this->session->userdata('registered_by') == "checked" || !$this->session->userdata('registered_by') ? '<th id="visible">' . lang("player.67") . '</th>' : ''?>
                                <?=$this->session->userdata('status_col') == "checked" || !$this->session->userdata('status_col') ? '<th id="visible">' . lang("lang.status") . '</th>' : ''?>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
if (!empty($players)) {
	foreach ($players as $players) {
		$name = $players['lastName'] . " " . $players['firstName'];
		if ($players['status'] == 1 || $players['status'] == 2) {?>
                                    <tr class="danger">
                                    <?php } else {?>
                                    <tr>
                                    <?php }
		?>
                                        <td></td>
                                        <td  style="padding: 8px"><input type="checkbox" class="checkWhite" id="<?=$players['playerId']?>" name="players[]" value="<?=$players['playerId']?>" onclick="uncheckAll(this.id)"></td>
                                        <td>
                                        <!-- <a href="#showDetails" data-toggle="tooltip" class="details" onclick="viewPlayer(<?=$players['playerId']?>, 'overview');"><span class="glyphicon glyphicon-zoom-in"></span></a>-->
                                        <?php if ($this->permissions->checkPermissions('edit_player_vip_level')) {?>
                                            <a href="#showPlayerLevel" data-toggle="tooltip" title="<?=lang('tool.pm01');?>" class="playerlevel" onclick="viewPlayer(<?=$players['playerId']?>, 'adjustplayerlevel');"><span class="glyphicon glyphicon-edit"></span></a>
                                        <?php }
		?>
                                        <?php if ($this->permissions->checkPermissions('tag_player')) {?>
                                            <a href="#tags" data-toggle="tooltip" title="<?=lang('tool.pm04');?>" class="tags" onclick="viewPlayerWithCurrentPage(<?=$players['playerId']?>, 'playerTag', 'playerlist');"><span class="glyphicon glyphicon-tag"></span></a>
                                        <?php }
		?>

                                        <!-- OG-1002 merge "freeze" and "lock" member to one "block" per platform
                                        <?php if ($this->permissions->checkPermissions('lock_player')) {?>
                                            <a href="#lockPlayer" data-toggle="tooltip" title="<?=lang('tool.pm02');?>" class="lock" onclick="viewPlayerWithCurrentPage(<?=$players['playerId']?>, 'lockedPlayer', 'playerlist');"><span class="glyphicon glyphicon-lock"></span></a>
                                        <?php }
		?>
                                        <?php if ($this->permissions->checkPermissions('block_player')) {?>
                                            <a href="#blockPlayer" data-toggle="tooltip" title="<?=lang('tool.pm03');?>" class="block" onclick="viewPlayerWithCurrentPage(<?=$players['playerId']?>, 'blockPlayerInGame', 'playerlist');"><span class="glyphicon glyphicon-ban-circle"></span></a>
                                        <?php }
		?>
                                        -->
                                        </td>
                                        <td><a href="<?=site_url('player_management/userInformation/' . $players['playerId'])?>"><?=$players['username']?></a></td>

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
                                            <td id="visible"><?=$players['lastLoginTime'] == '0000-00-00 00:00:00' || $players['lastLoginTime'] == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $players['lastLoginTime']?></td>
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
                                    </tr>
                                <?php }
	?>
                            <?php }
?>
                        </tbody>
                    </table>
                </form>
                <div class="col-md-6" draggable="true" id="player_details" style="position: absolute;left: 0;top: 0; width: 800px;">
                </div>
            </div>

            <div class="panel-footer"></div>
        </div>
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
        var sortCol=9;
        $('#myTable').DataTable({
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "columnDefs": [ {
                targets:   [0,1],
                orderable: false,
            } ],
            "order": [ sortCol, 'desc' ],
            "dom": '<"top"fl>rt<"bottom"ip>',
            "fnDrawCallback": function(oSettings) {
                $('.btn-action').prependTo($('.top'));
            }
        });

        if ($('#reportrange:not([disabled])').length) {
            // set initial date_range_picker config
            var dateRangeConfig = {
                format: 'YYYY-MM-DD',
                separator: ' - ',
                showShortcuts: true,
                shortcuts:
                {
                    'prev-days': [1,3,5,7],
                    'prev' : ['week','month','year']
                },
                getValue: function()
                {
                    return $('#dateRangeData').html();
                },
                setValue: function(s,s1,s2)
                {
                    d1 = moment(s1).format('YYYY-MM-DD');
                    d2 = moment(s2).format('YYYY-MM-DD');
                    $('#start_date').val(d1);
                    $('#end_date').val(d2);
                },
            };

            // get current language
            var curLang = "<?php echo isset($currentLang) ? $currentLang : ''; ?>";
            if(curLang == "") curLang = $('#lang_select').val();
            if(curLang == "") curLang = 'cn';
            // SWITCH LANGUAGE
            if(curLang == 1) {
                dateRangeConfig.language = 'en';
            } else if(curLang == 2) {
                dateRangeConfig.language = 'cn';
            }

            var reportrange = $('#reportrange').dateRangePicker(dateRangeConfig);

            reportrange.bind('datepicker-change',function(event, obj) {
                var date1 = moment(obj.date1);
                var date2 = moment(obj.date2);
                $('#dateRangeData').html(date1.format('YYYY-MM-DD') + ' - ' + date2.format('YYYY-MM-DD'));
                $('#dateRangeValue').val(date1.format('YYYY-MM-DD') + ' - ' + date2.format('YYYY-MM-DD'));
                $('#dateRangeValueStart').val(date1.format('YYYY-MM-DD'));
                $('#dateRangeValueEnd').val(date2.format('YYYY-MM-DD'));

                // set specific input by id
                $('#start_date').val(date1.format('YYYY-MM-DD'));
                $('#end_date').val(date2.format('YYYY-MM-DD'));
            });
        }

        if ($('#reportrangeDeposit:not([disabled])').length) {
            // set initial date_range_picker config
            var dateRangeConfig = {
                format: 'YYYY-MM-DD',
                separator: ' - ',
                showShortcuts: true,
                shortcuts:
                {
                    'prev-days': [1,3,5,7],
                    'prev' : ['week','month','year']
                },
                getValue: function()
                {
                    return $('#dateRangeDataDeposit').html();
                },
                setValue: function(s,s1,s2)
                {
                    d1 = moment(s1).format('YYYY-MM-DD');
                    d2 = moment(s2).format('YYYY-MM-DD');
                    $('#deposit_date_from').val(d1);
                    $('#deposit_date_to').val(d2);
                },
            };

            // get current language
            var curLang = "<?php echo isset($currentLang) ? $currentLang : ''; ?>";
            if(curLang == "") curLang = $('#lang_select').val();
            if(curLang == "") curLang = 'cn';
            // SWITCH LANGUAGE
            if(curLang == 1) {
                dateRangeConfig.language = 'en';
            } else if(curLang == 2) {
                dateRangeConfig.language = 'cn';
            }

            var reportrange = $('#reportrangeDeposit').dateRangePicker(dateRangeConfig);

            reportrange.bind('datepicker-change',function(event, obj) {
                var date1 = moment(obj.date1);
                var date2 = moment(obj.date2);

                // set specific input by id
                $('#deposit_date_from').val(date1.format('YYYY-MM-DD'));
                $('#deposit_date_to').val(date2.format('YYYY-MM-DD'));
            });
        }

        // DATE RANGE PICKER END ---------------------------------------------------------------------------


    });

    $("#search_reg_date").click(function() {
        if(this.checked) {
          $('.search_reg_date_date_picker').prop('disabled',false);
        }else{
          $('.search_reg_date_date_picker').prop('disabled',true);
        }
    });

    $("#deposit").click(function() {
        if(this.checked) {
          $('#first_deposited').prop('checked',true);
        }else{
          $('#first_deposited').prop('checked',false);
        }
    });

    function drag_start(event) {
            var style = window.getComputedStyle(event.target, null);
            event.dataTransfer.setData("text/plain",
            (parseInt(style.getPropertyValue("left"),10) - event.clientX) + ',' + (parseInt(style.getPropertyValue("top"),10) - event.clientY));
    }

    function drag_over(event) {
        event.preventDefault();
        return false;
    }

    function drop(event) {
        var offset = event.dataTransfer.getData("text/plain").split(',');
        var dm = document.getElementById('player_details');
        dm.style.left = (event.clientX + parseInt(offset[0],10)) + 'px';
        dm.style.top = (event.clientY + parseInt(offset[1],10)) + 'px';
        event.preventDefault();
        return false;
    }

    var dm = document.getElementById('player_details');
        dm.addEventListener('dragstart',drag_start,false);

    document.body.addEventListener('dragover',drag_over,false);
    document.body.addEventListener('drop',drop,false);
</script>