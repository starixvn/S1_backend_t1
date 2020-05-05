<?php

// initialize variables
$gameData = [];
$totalActivePlayers = 0;
$totalPercentage = 0;

foreach ($game as $key => $game_value) {
	// one array per game
	$gameDetails = [];
	// initialize game variables
	$gameDetails['gameDetails'] = $game_value;
	$gameDetails['gameDetails']['percentage'] = "";
	$gameDetails['gameDetails']['active players'] = "";

	$percentage = null;
	$active_players = null;

	// get main rule
	$main_percent = 0;
	$main_active = 0;
	$main_rule = $this->affiliate_manager->getAffiliateMainRule();
	foreach ($main_rule as $key => $value) {
		if ($value['name'] == 'affiliate_main_percentage') {
			$main_percent = $value['value'];
		} elseif ($value['name'] == 'affiliate_main_active') {
			$main_active = $value['value'];
		}
	}

	// get value
	$default = $this->affiliate_manager->getAffiliateDefaultOptionsByGameId($game_value['gameId']);

	foreach ($default as $key => $value) {
		// set game variables
		if ($value['optionsType'] == "percentage") {
			$percentage = $value['optionsValue'];
			$gameDetails['gameDetails']['percentage'] = $percentage;

			$totalPercentage += $percentage;
		} else if ($value['optionsType'] == "active players") {
			$active_players = $value['optionsValue'];
			$gameDetails['gameDetails']['active players'] = $active_players;

			$totalActivePlayers += $active_players;
		}
	}

	// add game array to gameData
	$gameData[] = $gameDetails;
}

?>


<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <!-- <a href="#main" style="color: white;" id="hide_main" class="btn btn-info btn-sm">
                    <i class="glyphicon glyphicon-chevron-up" id="hide_main_up"></i></a> -->
                    <i class="glyphicon glyphicon-cog"></i> <?=lang('aff.ts01');?>
                </h4>
            </div>

            <div class="panel-body" id="main_panel_body">
            	<form class="form-horizontal" method="POST" action="<?=BASEURL . 'affiliate_management/setAsDefault'?>">

        			<br>

            		<div class="form-group">
            			<div class="col-md-8 col-md-offset-4">
			            	<div class="col-md-3 col-lg-3" style="margin-left: -7px;">
			            		<label for="percentage"><?=lang('aff.ts05');?></label>
			            		<input type="text" name="main_percent" id="" class="form-control input-sm percentage" value="<?=$main_percent;?>" readonly/>
			            	</div>
			            	<div class="col-md-3 col-lg-3">
			            		<label for="active_players"><?=lang('aff.ts06');?></label>
			            		<input type="text" name="main_active" id="" class="form-control input-sm active_players" value="<?=$main_active;?>" readonly/>
			            	</div>
		            	</div>
	            	</div>

            		<div class="form-group row">
            			<div class="col-md-8 col-md-offset-2">
            				<?php foreach ($gameData as $game_value) {?>
			            	<div class="col-md-3 col-lg-3">
			            		<label for="percentage"><?=$game_value['gameDetails']['game'] . " " . lang('aff.ts02');?></label>
			            		<input type="text" name="<?='percentage-' . $game_value['gameDetails']['gameId']?>" id="<?='percentage-' . $game_value['gameDetails']['gameId']?>" class="form-control input-sm percentage" value="<?=$game_value['gameDetails']['percentage'];?>" readonly/>
			            		<?php echo form_error('percentage-' . $game_value['gameDetails']['gameId'], '<span class="help-block" style="color:#ff6666;">', '</span>');?>
			            	</div>
			            	<div class="col-md-3 col-lg-3">
			            		<label for="active_players"><?=$game_value['gameDetails']['game'] . " " . lang('aff.ts03');?></label>
			            		<input type="text" name="<?='active_players-' . $game_value['gameDetails']['gameId']?>" id="<?='active_players-' . $game_value['gameDetails']['gameId']?>" class="form-control input-sm active_players" value="<?=$game_value['gameDetails']['active players'];?>" readonly/>
			            		<?php echo form_error('active_players-' . $game_value['gameDetails']['gameId'], '<span class="help-block" style="color:#ff6666;">', '</span>');?>
			            	</div>
			            	<?php }
?>
		            	</div>
	            	</div>

	            	<center>
        				<input type="button" class="btn btn-info btn-sm custom-btn-size" id="btn_edit" value="<?=lang('lang.edit');?>" onclick="editTermsDefault();"/>
        				<input type="submit" class="btn btn-info btn-sm custom-btn-size" id="btn_submit" value="<?=lang('aff.ts04');?>" style="display:none;margin-top:0;"/>
        				<input type="button" class="btn btn-info btn-sm custom-btn-size" id="btn_cancel" value="<?=lang('aff.ts04');?>" style="display:none;margin-top:0;"/>
        			</center>

					<br><br>
        			<div class="well container">
        				<h3><?=lang('lang.info');?>:</h3>
        				<p><b><?=lang('aff.ai44');?></b> <?=lang('aff.ai91');?></p>
        			</div>
	            </form>
            </div>
            <div class="panel-footer"></div>
        </div>
    </div>
</div>