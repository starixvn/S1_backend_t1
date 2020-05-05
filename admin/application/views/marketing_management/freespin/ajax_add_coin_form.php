<style type="text/css">
	.coin_note{
		display: block;
	    font-size: 13px;
	    margin-top: 10px;
	    color: red;
	    font-style: italic;
	}
</style>
<?php
	
    foreach ($games as $key => $value) {
        
        $attributes = json_decode($value->attributes);
        $coins = array();

        foreach ($attributes->coin as $idx => $val) {
            $coins[$val->currency] = $val->coin_values;
        }
?>
        
        <br>
        <div class="row">
            <div class="col-md-12">
                <h6><label for="promoName"><?=lang('Game')?>: </label> <label><strong><?=lang($value->game_name)?></strong></label></h6>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h6><label for="promoName"><?=lang('Lines')?>: </label></h6>
                <input type="text" name="lines" readonly="" class="form-control" value="Maximum">
                <input type="hidden" name="line[]" value="<?=max($attributes->lines)?>"> 
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <h6><label for="promoName"><?=lang('Line Bet')?>: </label></h6>
                <select name="line_bet_<?=substr(substr($value->game_code, -4)
, -4)?>" data-game_code="<?=substr($value->game_code, -4)
?>" class="form-control" id="linebet_<?=substr($value->game_code, -4)
?>">
                    <option value="minimum" selected="">Minimum</option>
                    <option value="maximum">Maximum</option>
                </select>
                <input type="hidden" id="line_bet_<?=substr($value->game_code, -4)
?>" name="line_bet[]" value="<?=min($attributes->line_bet)?>">
                <input type="hidden" id="<?=substr($value->game_code, -4)
?>_line_bet_stored_value" name="<?=substr($value->game_code, -4)
?>_line_bet_stored_value" value="<?=implode(',', $attributes->line_bet)?>">
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <h6><label for="promoName"><?=lang('Coin / Currency')?>: </label></h6>
                <input type="text" name="" id="coin_value_<?=substr($value->game_code, -4)
?>" data-game_code="<?=substr($value->game_code, -4)
?>" class="form-control" style="width: 100px; display: inline;">
                <label>/</label>
                <select id="coins_<?=substr($value->game_code, -4)
?>" data-game_code="<?=substr($value->game_code, -4)
?>" name="" style="width: 100px; display: inline;" class="form-control">
                    <option value="CNY">CNY</option>
                    <option value="RMB">RMB</option>
                    <option value="USD">USD</option>
                </select>
                <span><a href="javascript:void(0)" id="add_coin_<?=substr($value->game_code, -4)
?>" data-game_code="<?=substr($value->game_code, -4)
?>">ADD</a></span>

                <div class="coins_cnt_<?=substr($value->game_code, -4)
?>">
                    
                    
                </div>

                <input type="hidden" name="<?=substr($value->game_code, -4)
?>_CNY_coin_available" value="<?=(isset($coins['CNY'])) ? implode(',', $coins['CNY']) : ''?>" />
                <input type="hidden" name="<?=substr($value->game_code, -4)
?>_RMB_coin_available" value="<?=(isset($coins['RMB'])) ? implode(',', $coins['RMB']) : ''?>" />
                <input type="hidden" name="<?=substr($value->game_code, -4)
?>_USD_coin_available" value="<?=(isset($coins['USD'])) ? implode(',', $coins['USD']) : ''?>" />
                <span class="coin_note"><?=lang('sys.gd11')?>: <?=lang('Only enter between the range of')?> <label class="lbl_coins_<?=substr($value->game_code, -4)
?>"><?=implode(',', $coins['CNY'])?></label></span>
            </div>
        </div>

<?php
    }

?>    
