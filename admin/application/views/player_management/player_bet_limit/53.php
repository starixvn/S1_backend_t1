<div class="panel panel-primary">

	<div class="panel-heading">
		<h4 class="panel-title"><i class="glyphicon glyphicon-exclamation-sign"></i> <?=$platform_name.' '.lang('Bet Limits')?></h4>
	</div>

	<div class="panel-body">
		<form action="/player_management/updatePlayerBetLimit/<?=$game_platform_id?>/<?=$player_id?>" method="POST">

			<fieldset>
				<legend><?=lang('Baccarat')?></legend>
				<div class="row">
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Banker')?></label>
						<input type="number" name="baccaratBetLimit.bankerMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="baccaratBetLimit.bankerMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Banker Pair')?></label>
						<input type="number" name="baccaratBetLimit.bankerPairMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="baccaratBetLimit.bankerPairMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Player')?></label>
						<input type="number" name="baccaratBetLimit.playerMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="baccaratBetLimit.playerMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Player Pair')?></label>
						<input type="number" name="baccaratBetLimit.playerPairMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="baccaratBetLimit.playerPairMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Tie')?></label>
						<input type="number" name="baccaratBetLimit.tieMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="baccaratBetLimit.tieMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend><?=lang('Dragon Tiger')?></legend>
				<div class="row">
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Dragon')?></label>
						<input type="number" name="dragonTigerBetLimit.dragonMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="dragonTigerBetLimit.dragonMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Tiger')?></label>
						<input type="number" name="dragonTigerBetLimit.tigerMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="dragonTigerBetLimit.tigerMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Tie')?></label>
						<input type="number" name="dragonTigerBetLimit.tieMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="dragonTigerBetLimit.tieMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend><?=lang('Sicbo')?></legend>
				<div class="row">
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Odd')?></label>
						<input type="number" name="sicboBetLimit.oddMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.oddMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Even')?></label>
						<input type="number" name="sicboBetLimit.evenMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.evenMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Big')?></label>
						<input type="number" name="sicboBetLimit.bigMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.bigMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Small')?></label>
						<input type="number" name="sicboBetLimit.smallMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.smallMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Pair')?></label>
						<input type="number" name="sicboBetLimit.pairMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.pairMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Triple')?></label>
						<input type="number" name="sicboBetLimit.tripleMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.tripleMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('AllTriple')?></label>
						<input type="number" name="sicboBetLimit.allTripleMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.allTripleMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Four')?></label>
						<input type="number" name="sicboBetLimit.fourMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.fourMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Five')?></label>
						<input type="number" name="sicboBetLimit.fiveMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.fiveMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Six')?></label>
						<input type="number" name="sicboBetLimit.sixMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.sixMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Seven')?></label>
						<input type="number" name="sicboBetLimit.sevenMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.sevenMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Eight')?></label>
						<input type="number" name="sicboBetLimit.eightMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.eightMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Nine')?></label>
						<input type="number" name="sicboBetLimit.nineMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.nineMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Ten')?></label>
						<input type="number" name="sicboBetLimit.tenMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.tenMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Eleven')?></label>
						<input type="number" name="sicboBetLimit.elevenMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.elevenMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Twelve')?></label>
						<input type="number" name="sicboBetLimit.twelveMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.twelveMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Thirteen')?></label>
						<input type="number" name="sicboBetLimit.thirteenMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.thirteenMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Fourteen')?></label>
						<input type="number" name="sicboBetLimit.fourteenMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.fourteenMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Fifteen')?></label>
						<input type="number" name="sicboBetLimit.fifteenMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.fifteenMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Sixteen')?></label>
						<input type="number" name="sicboBetLimit.sixteenMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.sixteenMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Seventeen')?></label>
						<input type="number" name="sicboBetLimit.seventeenMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.seventeenMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Single Dice')?></label>
						<input type="number" name="sicboBetLimit.singleDiceMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.singleDiceMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Combination')?></label>
						<input type="number" name="sicboBetLimit.combinationMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.combinationMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('More Left')?></label>
						<input type="number" name="sicboBetLimit.moreLeftMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.moreLeftMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('More Right')?></label>
						<input type="number" name="sicboBetLimit.moreRightMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="sicboBetLimit.moreRightMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
				</div>
			</fieldset>

			<fieldset style="margin-bottom: 15px;">
				<legend><?=lang('Roulette Wheel')?></legend>
				<div class="row">
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Odd')?></label>
						<input type="number" name="rouletteWheelBetLimit.oddMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="rouletteWheelBetLimit.oddMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Even')?></label>
						<input type="number" name="rouletteWheelBetLimit.evenMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="rouletteWheelBetLimit.evenMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Big')?></label>
						<input type="number" name="rouletteWheelBetLimit.bigMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="rouletteWheelBetLimit.bigMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Small')?></label>
						<input type="number" name="rouletteWheelBetLimit.smallMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="rouletteWheelBetLimit.smallMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Red')?></label>
						<input type="number" name="rouletteWheelBetLimit.redMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="rouletteWheelBetLimit.redMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Black')?></label>
						<input type="number" name="rouletteWheelBetLimit.blackMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="rouletteWheelBetLimit.blackMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Dozen')?></label>
						<input type="number" name="rouletteWheelBetLimit.dozenMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="rouletteWheelBetLimit.dozenMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Column')?></label>
						<input type="number" name="rouletteWheelBetLimit.columnMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="rouletteWheelBetLimit.columnMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Line')?></label>
						<input type="number" name="rouletteWheelBetLimit.lineMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="rouletteWheelBetLimit.lineMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Basket')?></label>
						<input type="number" name="rouletteWheelBetLimit.basketMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="rouletteWheelBetLimit.basketMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Corner')?></label>
						<input type="number" name="rouletteWheelBetLimit.cornerMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="rouletteWheelBetLimit.cornerMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Trio')?></label>
						<input type="number" name="rouletteWheelBetLimit.trioMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="rouletteWheelBetLimit.trioMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Street')?></label>
						<input type="number" name="rouletteWheelBetLimit.streetMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="rouletteWheelBetLimit.streetMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Split')?></label>
						<input type="number" name="rouletteWheelBetLimit.splitMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="rouletteWheelBetLimit.splitMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
					<div class="form-group form-group-sm col-md-2">
						<label class="control-label"><?=lang('Straight')?></label>
						<input type="number" name="rouletteWheelBetLimit.straightMin" class="form-control" placeholder="Min" min="0" step="any"/>
						<input type="number" name="rouletteWheelBetLimit.straightMax" class="form-control" placeholder="Max" min="0" step="any"/>
					</div>
				</div>
			</fieldset>

			<button type="submit" class="btn btn-primary pull-right">Update</button>

		</form>
	</div>

	<div class="panel-footer"></div>
</div>