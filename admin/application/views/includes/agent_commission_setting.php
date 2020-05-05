<!--fieldset commission-setting {{{3 -->
<div class="col-md-12">
    <label for="commission-setting">
        <font style="color:red;">*</font> 
        <?=lang('Commission Setting');?>
    </label>
    <fieldset>
        <br>
        <!-- input number rev_share (required) {{{4 -->
        <div class="col-md-6">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon">
                        <?=lang('Rev Share');?>
                    </div>
                    <input type="text" class="form-control" 
                    id="rev_share" name="rev_share" 
                    value="<?=set_value('rev_share',$conditions['rev_share'])?>"
                    title="<?=lang('Input a number between 0~100')?>"/>
                    <div class="input-group-addon">%</div>
                </div>
            </div>
            <span class="errors"><?php echo form_error('rev_share'); ?></span>
            <span id="error-rev_share" class="errors"></span>
        </div> <!-- input number rev_share (required) }}}4 -->
        <!-- input number Rolling Comm (required) {{{4 -->
        <div class="col-md-6 hidden">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon">
                        <?=lang('Rolling Comm');?>
                    </div>
                    <?php if ($this->config->item('hide_commission_settings')): ?>
                        <input type="hidden" id="rolling_comm" name="rolling_comm" value="<?=set_value('rolling_comm', @$conditions['rolling_comm'] ? : '0.00')?>">
                    <?php else: ?>
                        <input type="text" class="form-control" 
                        id="rolling_comm" name="rolling_comm" 
                        value="<?=set_value('rolling_comm', $conditions['rolling_comm'])?>"
                        title="<?=lang('Input a number between 0~3')?>" />
                        <div class="input-group-addon">%</div>
                    <?php endif ?>
                        
                </div>
            </div>
            <span class="errors"><?php echo form_error('rolling_comm'); ?></span>
            <span id="error-rolling_comm" class="errors"></span>
        </div> <!-- input number Rolling Comm (required) }}}4 -->
        <!-- Select Rolling Comm Basis (required) {{{4 -->
        <div class="col-md-6 hidden">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon">
                        <?=lang('Rolling Comm Basis');?>
                    </div>
                    <?php if ($this->config->item('hide_commission_settings')): ?>
                        <input type="hidden" id="rolling_comm_basis" name="rolling_comm_basis" value="<?=set_value('rolling_comm_basis', @$conditions['rolling_comm_basis'] ? : 'total_bets')?>">
                    <?php else: ?>
                        <select name="rolling_comm_basis" id="rolling_comm_basis" 
                            class="form-control input-sm" 
                            title="<?=lang('Select Rolling Comm Basis')?>"
                            onclick="setDisplayTotalBetsExcept()">
                            <option value="" <?=empty($conditions['rolling_comm_basis'])? 'selected':''?>>
                            --  <?=lang('None');?> --
                            </option>
                            <option id="basis_total_bets" value="total_bets" 
                            <?=($conditions['rolling_comm_basis'] == 'total_bets')?'selected':''?> >
                            <?=lang('Total Bet');?>
                            </option>
                            <option value="total_lost_bets" 
                            <?=($conditions['rolling_comm_basis'] == 'total_lost_bets')?'selected':''?> >
                            <?=lang('Lost Bets');?>
                            </option>
                            <option value="total_bets_except_tie_bets" 
                            <?=($conditions['rolling_comm_basis'] == 'total_bets_except_tie_bets')?'selected':''?> >
                            <?=lang('Bets Except Tie Bets');?>
                            </option>
                        </select>
                    <?php endif ?>
                </div>
            </div>
            <span class="errors"><?php echo form_error('rolling_comm_basis'); ?></span>
            <span id="error-rolling_comm_basis" class="errors"></span>
        </div> <!-- Select Rolling Comm Basis (required) }}}4 -->
        <!-- Select game type (display only when 'Total Bets' is selected) {{{4 -->
        <div class="col-md-6 hidden" id="total_bets_except">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon">
                        <?=lang('Except Game Type');?>
                    </div>
                    <?php if ($this->config->item('hide_commission_settings')): ?>
                        <input type="hidden" id="except_game_type" name="except_game_type" value="<?=set_value('except_game_type', @$conditions['total_bets_except'] ? : '')?>">
                    <?php else: ?>
                        <select name="except_game_type" id="except_game_type" 
                            class="form-control input-sm" 
                            title="<?=lang('Select a game type')?>">
                            <option value="" <?=($conditions['total_bets_except'] == '')?'selected':''?> >
                            --  <?=lang('None');?> --
                            </option>
                            <?php for($i = 0; $i < count($game_types); $i++) { ?>
                            <option value="<?=$game_types[$i]['game_type']?>" 
                            <?=($conditions['total_bets_except'] == $game_types[$i]['game_type'])?'selected':''?> >
                            <?=lang($game_types[$i]['game_type_lang']);?>
                            </option>
                            <?php } ?>
                        </select>
                    <?php endif ?>
                </div>
            </div>
            <span class="errors"><?php echo form_error('except_game_type'); ?></span>
            <span id="error-except_game_type" class="errors"></span>
        </div> <!-- Select Rolling Comm Basis (required) }}}4 -->
    </fieldset>
</div> <!-- fieldset commission-setting }}}3