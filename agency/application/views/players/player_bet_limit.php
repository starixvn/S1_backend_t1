<?php
if(!isset($max_bet_limit)){
	$max_bet_limit=5000;
}
if(!isset($min_bet_limit)){
	$min_bet_limit=0;
}
?>

<style type="text/css">
.tab-content{
	margin-top: 10px;
}
</style>
<div class="content-container">
	<?php if(isset($player_id)){ ?>
	<form id="bet-limit-form" action="/agency/updatePlayerBetLimit/<?=$player_id?>" method="POST">
	<input type="hidden" name="gameId" value="<?=$gameId?>"/>
	<?php }else{?>
	<form id="bet-limit-form" action="/agency/post_bet_limit_template/<?=@$template_id?>" method="POST">
	<?php }?>
	<div class="panel panel-primary">

		<div class="panel-heading">
			<h4 class="panel-title"><i class="fa fa-hand-stop-o"></i> <?=lang('Bet Limits')?></h4>
		</div>

		<div class="panel-body">
			<?php if(!isset($player_id)){ ?>
			<div class="row">
				<div class="col-md-4 form-group">
					<label class="control-label"><?=lang('Template Name')?></label>
					<input type="text" class="form-control input-sm" name="template_name" value="<?= @$template_name; ?>">

					<div class="checkbox">
					    <label>
					      	<input type="checkbox" name="default_template" value="1" <?=@$default_template == 1?'checked':''?>> <?=lang('player.ui55')?>
					    </label>
				  	</div>
					<div class="checkbox">
					    <label>
					      	<input type="checkbox" name="public_to_downline" value="1" <?=@$public_to_downline == 1?'checked':''?>> <?=lang('Public to Downline')?>
					    </label>
				  	</div>
				</div>
			</div>
			<?php }?>
			<div class="row">
				<?php if(isset($player_id)){ ?>
				<div class="col-md-2">
					<ul class="nav nav-pills nav-stacked" >
						<?php foreach ($gameIds as $gameId): ?>
							<li role="presentation" <?php if ($limit['gameId'] == $gameId) echo 'class="active"'?>><a href="/agency/playerBetLimit/<?=$player_id?>/<?=$game_type?>/<?=$gameId?>"><?=$gameId?></a></li>
						<?php endforeach ?>
					</ul>
				</div>
				<?php }?>
				<div class="col-md-10">

				<ul class="nav nav-tabs">
					<li role="presentation" class="active"><a href="#limit_baccarat" aria-controls="limit_baccarat" role="tab" data-toggle="tab"><?=lang('baccarat')?></a></li>
					<li role="presentation" ><a href="#limit_vip_baccarat" aria-controls="limit_vip_baccarat" role="tab" data-toggle="tab"><?=lang('VIP Baccarat')?></a></li>
					<li role="presentation" ><a href="#limit_dragontiger" aria-controls="limit_dragontiger" role="tab" data-toggle="tab"><?=lang('dragonTiger')?></a></li>
					<li role="presentation" ><a href="#limit_sicbo" aria-controls="limit_sicbo" role="tab" data-toggle="tab"><?=lang('sicbo')?></a></li>
					<li role="presentation" ><a href="#limit_roulettewheel" aria-controls="limit_roulettewheel" role="tab" data-toggle="tab"><?=lang('rouletteWheel')?></a></li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="limit_baccarat">
							<div class="form-horizontal">
								<div class="form-group form-group-sm">
									<label class="control-label col-md-2"><?=lang('banker')?></label>
									<div class="col-md-2">
										<input type="number" name="vip_baccaratBetLimit.bankerMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['vip_baccaratBetLimit.bankerMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="vip_baccaratBetLimit.bankerMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['vip_baccaratBetLimit.bankerMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('bankerPair')?></label>
									<div class="col-md-2">
										<input type="number" name="vip_baccaratBetLimit.bankerPairMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['vip_baccaratBetLimit.bankerPairMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="vip_baccaratBetLimit.bankerPairMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['vip_baccaratBetLimit.bankerPairMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('player')?></label>
									<div class="col-md-2">
										<input type="number" name="vip_baccaratBetLimit.playerMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['vip_baccaratBetLimit.playerMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="vip_baccaratBetLimit.playerMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['vip_baccaratBetLimit.playerMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('playerPair')?></label>
									<div class="col-md-2">
										<input type="number" name="vip_baccaratBetLimit.playerPairMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['vip_baccaratBetLimit.playerPairMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="vip_baccaratBetLimit.playerPairMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['vip_baccaratBetLimit.playerPairMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('tie')?></label>
									<div class="col-md-2">
										<input type="number" name="vip_baccaratBetLimit.tieMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['vip_baccaratBetLimit.tieMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="vip_baccaratBetLimit.tieMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['vip_baccaratBetLimit.tieMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
								</div>
							</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="limit_vip_baccarat">
							<div class="form-horizontal">
								<div class="form-group form-group-sm">
									<label class="control-label col-md-2"><?=lang('banker')?></label>
									<div class="col-md-2">
										<input type="number" name="baccaratBetLimit.bankerMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['baccaratBetLimit.bankerMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="baccaratBetLimit.bankerMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['baccaratBetLimit.bankerMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('bankerPair')?></label>
									<div class="col-md-2">
										<input type="number" name="baccaratBetLimit.bankerPairMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['baccaratBetLimit.bankerPairMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="baccaratBetLimit.bankerPairMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['baccaratBetLimit.bankerPairMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('player')?></label>
									<div class="col-md-2">
										<input type="number" name="baccaratBetLimit.playerMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['baccaratBetLimit.playerMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="baccaratBetLimit.playerMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['baccaratBetLimit.playerMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('playerPair')?></label>
									<div class="col-md-2">
										<input type="number" name="baccaratBetLimit.playerPairMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['baccaratBetLimit.playerPairMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="baccaratBetLimit.playerPairMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['baccaratBetLimit.playerPairMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('tie')?></label>
									<div class="col-md-2">
										<input type="number" name="baccaratBetLimit.tieMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['baccaratBetLimit.tieMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="baccaratBetLimit.tieMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['baccaratBetLimit.tieMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
								</div>
							</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="limit_dragontiger">
							<div class="form-horizontal">
								<div class="form-group form-group-sm">
									<label class="control-label col-md-2"><?=lang('dragon')?></label>
									<div class="col-md-2">
										<input type="number" name="dragonTigerBetLimit.dragonMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['dragonTigerBetLimit.dragonMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="dragonTigerBetLimit.dragonMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['dragonTigerBetLimit.dragonMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('tiger')?></label>
									<div class="col-md-2">
										<input type="number" name="dragonTigerBetLimit.tigerMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['dragonTigerBetLimit.tigerMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="dragonTigerBetLimit.tigerMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['dragonTigerBetLimit.tigerMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('tie')?></label>
									<div class="col-md-2">
										<input type="number" name="dragonTigerBetLimit.tieMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['dragonTigerBetLimit.tieMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="dragonTigerBetLimit.tieMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['dragonTigerBetLimit.tieMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
								</div>
							</div>
					</div>

					<div role="tabpanel" class="tab-pane" id="limit_sicbo">
							<div class="form-horizontal">
								<div class="form-group form-group-sm">
									<label class="control-label col-md-2"><?=lang('odd')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.oddMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.oddMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.oddMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.oddMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('even')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.evenMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.evenMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.evenMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.evenMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('big')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.bigMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.bigMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.bigMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.bigMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('small')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.smallMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.smallMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.smallMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.smallMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('pair')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.pairMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.pairMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.pairMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.pairMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('triple')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.tripleMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.tripleMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.tripleMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.tripleMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('all Triple')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.allTripleMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.allTripleMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.allTripleMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.allTripleMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('four')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.fourMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.fourMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.fourMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.fourMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('five')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.fiveMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.fiveMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.fiveMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.fiveMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('six')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.sixMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.sixMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.sixMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.sixMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('seven')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.sevenMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.sevenMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.sevenMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.sevenMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('eight')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.eightMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.eightMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.eightMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.eightMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('nine')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.nineMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.nineMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.nineMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.nineMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('ten')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.tenMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.tenMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.tenMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.tenMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('eleven')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.elevenMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.elevenMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.elevenMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.elevenMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('twelve')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.twelveMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.twelveMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.twelveMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.twelveMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('thirteen')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.thirteenMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.thirteenMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.thirteenMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.thirteenMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('fourteen')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.fourteenMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.fourteenMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.fourteenMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.fourteenMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('fifteen')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.fifteenMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.fifteenMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.fifteenMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.fifteenMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('sixteen')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.sixteenMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.sixteenMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.sixteenMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.sixteenMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('seventeen')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.seventeenMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.seventeenMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.seventeenMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.seventeenMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('singleDice')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.singleDiceMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.singleDiceMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.singleDiceMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.singleDiceMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('combination')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.combinationMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.combinationMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.combinationMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.combinationMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('moreLeft')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.moreLeftMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.moreLeftMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.moreLeftMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.moreLeftMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('moreRight')?></label>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.moreRightMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['sicboBetLimit.moreRightMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="sicboBetLimit.moreRightMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['sicboBetLimit.moreRightMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
								</div>
							</div>
					</div>

					<div role="tabpanel" class="tab-pane" id="limit_roulettewheel">
							<div class="form-horizontal">
								<div class="form-group form-group-sm">
									<label class="control-label col-md-2"><?=lang('odd')?></label>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.oddMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['rouletteWheelBetLimit.oddMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.oddMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['rouletteWheelBetLimit.oddMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('even')?></label>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.evenMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['rouletteWheelBetLimit.evenMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.evenMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['rouletteWheelBetLimit.evenMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('big')?></label>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.bigMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['rouletteWheelBetLimit.bigMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.bigMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['rouletteWheelBetLimit.bigMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('small')?></label>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.smallMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['rouletteWheelBetLimit.smallMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.smallMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['rouletteWheelBetLimit.smallMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('red')?></label>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.redMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['rouletteWheelBetLimit.redMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.redMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['rouletteWheelBetLimit.redMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('black')?></label>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.blackMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['rouletteWheelBetLimit.blackMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.blackMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['rouletteWheelBetLimit.blackMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('dozen')?></label>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.dozenMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['rouletteWheelBetLimit.dozenMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.dozenMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['rouletteWheelBetLimit.dozenMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('column')?></label>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.columnMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['rouletteWheelBetLimit.columnMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.columnMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['rouletteWheelBetLimit.columnMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('line')?></label>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.lineMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['rouletteWheelBetLimit.lineMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.lineMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['rouletteWheelBetLimit.lineMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('basket')?></label>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.basketMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['rouletteWheelBetLimit.basketMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.basketMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['rouletteWheelBetLimit.basketMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('corner')?></label>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.cornerMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['rouletteWheelBetLimit.cornerMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.cornerMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['rouletteWheelBetLimit.cornerMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('trio')?></label>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.trioMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['rouletteWheelBetLimit.trioMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.trioMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['rouletteWheelBetLimit.trioMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('street')?></label>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.streetMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['rouletteWheelBetLimit.streetMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.streetMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['rouletteWheelBetLimit.streetMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('split')?></label>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.splitMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['rouletteWheelBetLimit.splitMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.splitMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['rouletteWheelBetLimit.splitMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<label class="control-label col-md-2"><?=lang('straight')?></label>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.straightMin" class="form-control" placeholder="<?=lang('lang.min')?>" value="<?=@$limit['rouletteWheelBetLimit.straightMin']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
									<div class="col-md-2">
										<input type="number" name="rouletteWheelBetLimit.straightMax" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=@$limit['rouletteWheelBetLimit.straightMax']?>" min="<?php echo $min_bet_limit; ?>" max="<?php echo $max_bet_limit; ?>" step="any"/>
									</div>
								</div>
							</div>
					</div>

					<div class="pull-right" style="margin-top: 15px;">
						<a href="/agency/bet_limit_template_list" class="btn btn-default"><?=lang('button.back')?></a>
						<button type="submit" class="btn btn-primary"><?=lang('Save')?></button>
					</div>

				</div> <!-- class="tab-content" -->
				</div>
			</div>
		</div>

	</div>
	</form>
</div>
<script type="text/javascript">
	$(function() {
		$("input").on("invalid", function(e) {
			var game = $(e.target).parents('.tab-pane').prop('id');
			$('a[href="#' + game + '"').tab('show');
		});
	});
</script>