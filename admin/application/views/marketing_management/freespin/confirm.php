<form class="form-horizontal" id="static-site-upload-form" action="<?=site_url('cms_management/saveStaticSites')?>" method="POST" role="form" enctype="multipart/form-data">
	<input type="hidden" name="name" value="<?=$name?>">
	<input type="hidden" name="max_players" value="<?=$max_players?>">
	<input type="hidden" name="promo_code" value="<?=$promo_code?>">
	<!-- <input type="hidden" name="games" value="<?=$games?>"> -->
	<input type="hidden" name="player_ids" value="<?=$player_ids?>">
	<input type="hidden" name="limit_per_player" value="<?=$limit_per_player?>">
	<input type="hidden" name="relative_duration" value="<?=$relative_duration?>">
	<input type="hidden" name="start_date" value="<?=$start_date?>">
	<input type="hidden" name="end_date" value="<?=$end_date?>">
    <div class="row">
        <div class="table-responsive">
            <div class="">
                <div class="col-md-12">
                    <h6><label for="promoName"><?=lang('Package name');?>: <strong><?=$name?></strong></label></h6>
                </div>
            </div>
            <div class="">
                <div class="col-md-12">
                    <h6><label for="promoName"><?=lang('Limit Per Player');?>: <strong><?=$limit_per_player?></label></h6>
                </div>
            </div>
            <div class="">
                <div class="col-md-12">
                    <h6><label for="promoName"><?=lang('Start Date');?>: <strong><?=(!empty($start_date))? $start_date: '---'?></strong></label></h6>
                </div>
            </div>
            <div class="">
                <div class="col-md-12">
                    <h6><label for="promoName"><?=lang('End Date');?>: <strong><?=(!empty($end_date))? $end_date: '---'?></strong></label></h6>
                </div>
            </div>
            <div class="">
                <div class="col-md-12">
                    <h6><label for="promoName"><?=lang('Relative Duration');?>: <strong><?=$relative_duration?> <?=lang('days from package activation')?></strong></label></h6>
                </div>
            </div>
            <div class="">
                <div class="col-md-12">
                    <h6><label for="promoName"><?=lang('Player Limit');?>: <strong><?=$max_players?></strong></label></h6>
                </div>
            </div>
            <div class="">
                <div class="col-md-12">
                    <h6><label for="promoName"><?=lang('Promo Code');?>: <strong><?=(!empty($promo_code))? $promo_code: '---'?></strong></label></h6>
                </div>
            </div>


            <?php
                if( ! empty( $games ) ){
            ?>
                    <div class="game_cnt">
            <?php
                        foreach ($games as $key => $value) {
                            $attributes = json_decode($value->attributes);
            ?>
                            <div class="">
                                <div class="col-md-12">
                                    <h6>
                                        <label for="promoName">
                                            <?=lang('Game')?>: <strong><?=lang($value->game_name)?></strong>
                                        </label>
                                    </h6>
                                </div>
                            </div>

                            <div class="">
                                <div class="col-md-12">
                                    <h6>
                                        <label for="promoName">
                                            <?=lang('Lines')?>: <input type="text" name="lines" readonly="" class="form-control" value="Maximum">
                                            <input type="hidden" name="lines[<?=$value->game_code?>]" id="lines_<?=$value->game_code?>" value="<?=max($attributes->lines)?>">
                                            <input type="hidden" name="" id="stored_lines_<?=$value->game_code?>" value="<?=implode(',', $attributes->lines)?>">
                                        </label>
                                    </h6>
                                </div>
                            </div>

                            <div class="">
                                <div class="col-md-12">
                                    <h6>
                                        <label for="promoName"><?=lang('Line Bet')?>: 
                                            <select name="line_bet[<?=$value->game_code?>]" class="form-control" id="linebet_<?=$value->game_code?>" data-game_code="<?=$value->game_code?>">
                                                <option value="minimum" selected="">Minimum</option>
                                                <option value="maximum">Maximum</option>
                                            </select>
                                            <input type="hidden" name="line_bet[<?=$value->game_code?>]" id="line_bet_<?=$value->game_code?>" value="<?=min($attributes->line_bet)?>">
                                            <input type="hidden" name="" id="stored_line_bet_<?=$value->game_code?>" value="<?=implode(',', $attributes->line_bet)?>">
                                        </label>
                                    </h6>
                                </div>
                            </div>

                            <div class="">
                                <div class="col-md-12">
                                    <h6>
                                        <label for="promoName"><?=lang('Coin / Currency')?>: 
                                        <input type="text" name="coin_value_<?=$value->game_code?>" id="coin_value_<?=$value->game_code?>">/
                                        <select id="currency_<?=$value->game_code?>" style="width: 80px;">
                                            <option value="CNY">CNY</option>
                                            <option value="RMB">RMB</option>
                                            <option value="USD">USD</option>
                                        </select>
                                        <a href="javascript:void(0)" id="addCoin_<?=$value->game_code?>" data-gameCode="<?=$value->game_code?>">
                                            Add
                                        </a>
                                        </label>
                                    </h6>
                                    <div class="coin_values_<?=$value->game_code?>"></div>
                                </div>
                            </div>
            <?php
                        }
            ?>
                    </div>
            <?php
                }
            ?>

        </div>
    </div>
</form>