<?php
/**
 *   filename:   rolling_comm_setting.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-03
 *   @brief:     view for agent creating
 */
?>

<!-- form rolling_comm {{{1 -->    
<div class="content-container">
    <form method="POST" id="rolling_comm_setting_form" 
        action="<?=site_url('agency/process_rolling_comm_setting/'.$agent_id)?>" accept-charset="utf-8">
        <div class="panel panel-primary ">
            <!-- panel heading of rolling_comm {{{2 -->    
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="glyphicon glyphicon-list-alt"></i> 
                    <?=lang('Rolling Comm Setting');?> 
                </h4>
                <div class="clearfix"></div>
            </div> <!-- panel heading of rolling_comm }}}2 -->    

            <!-- panel body of rolling_comm  {{{2 -->    
            <div class="panel panel-body" id="rolling_comm_panel_body">
                <?php if (null) { ?>
                <!-- fieldset rolling_comm_setting {{{3 -->
                <div class="col-md-12">
                    <label for="rolling_comm_setting">
                        <?=lang('Rolling Comm Basis');?>
                    </label>
                    <fieldset>
                        <!-- Select Rolling Comm Basis (required) {{{4 -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <?=lang('Rolling Comm Basis');?>
                                    </div>
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
                                </div>
                            </div>
                            <span class="errors"><?php echo form_error('rolling_comm_basis'); ?></span>
                            <span id="error-rolling_comm_basis" class="errors"></span>
                        </div> <!-- Select Rolling Comm Basis (required) }}}4 -->
                        <!-- Select game type (display only when 'Total Bets' is selected) {{{4 -->
                        <div class="col-md-6" id="total_bets_except">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <?=lang('Except Game Type');?>
                                    </div>
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
                                </div>
                            </div>
                            <span class="errors"><?php echo form_error('except_game_type'); ?></span>
                            <span id="error-except_game_type" class="errors"></span>
                        </div> <!-- Select Rolling Comm Basis (required) }}}4 -->
                        <br>
                    </fieldset>
                </div> <!-- fieldset rolling_comm_setting }}}3 -->
                <?php } ?>
                <!-- fieldset rolling_comm_setting {{{3 -->
                <div class="col-md-12">
                    <label for="rolling_comm_setting">
                        <?=lang('Sub Agent Rolling Comm Setting');?>
                    </label>
                    <fieldset>
                        <!-- input number sub_agent_rolling_comm (required) {{{4 -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <?=lang('Sub Agent Rolling Comm');?>
                                    </div>
                                    <input type="text" class="form-control" 
                                    id="sub_agent_rolling_comm" name="sub_agent_rolling_comm" 
                                    value="<?=set_value('sub_agent_rolling_comm',$conditions['sub_agent_rolling_comm'])?>"
                                    title="<?=lang('Input a number between 0~3')?>"/>
                                    <div class="input-group-addon">%</div>
                                </div>
                            </div>
                            <span class="errors"><?php echo form_error('sub_agent_rolling_comm'); ?></span>
                            <span id="error-sub_agent_rolling_comm" class="errors"></span>
                        </div> <!-- input number sub_agent_rolling_comm (required) }}}4 -->
                        <br>
                    </fieldset>
                </div> <!-- fieldset rolling_comm_setting }}}3 -->
                <!-- fieldset rolling_comm_setting {{{3 -->
                <div class="col-md-12">
                    <label for="rolling_comm_setting">
                        <?=lang('Player Rolling Comm Setting');?>
                    </label>
                    <fieldset>
                        <!-- input number Player Rolling Comm (required) {{{4 -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <?=lang('Player Rolling Comm');?>
                                    </div>
                                    <input type="text" class="form-control" 
                                    id="player_rolling_comm" name="player_rolling_comm" 
                                    value="<?=set_value('player_rolling_comm', $conditions['player_rolling_comm'])?>"
                                    title="<?=lang('Input a number between 0~3')?>" />
                                    <div class="input-group-addon">%</div>
                                </div>
                            </div>
                            <span class="errors"><?php echo form_error('player_rolling_comm'); ?></span>
                            <span id="error-player_rolling_comm" class="errors"></span>
                        </div> <!-- input number Rolling Comm (required) }}}4 -->
                        <br>
                    </fieldset>
                </div> <!-- fieldset rolling_comm_setting }}}3 -->
                <!-- fieldset games_rolling_comm_setting {{{3 -->
                <div class="col-md-12">
                    <label for="rolling_comm_setting">
                        <?=lang('Game Rolling Comm Setting') . ' (' . lang('default game rolling comm') . ' = ' .'0.00)';?>
                    </label>
                    <div class="form-group" style="margin-left:5px;margin-right:5px;">
                        <input type="hidden" name="selected_game_tree" value="">
                        <fieldset style="padding:20px">
                            <div id="gameTree" class="col-md-12"> </div>
                        </fieldset>
                    </div>
                </div> <!-- fieldset games_rolling_comm_setting }}}3 -->
                <!-- button row {{{3 -->
                <div class="row">
                    <div class="col-md-5 col-lg-5" style="padding: 10px;">
                    </div>
                    <div class="col-md-6 col-lg-6" style="padding: 10px;">
                        <?php $reset_url=site_url('agency/rolling_comm_setting/'.$agent_id);?>
                        <input type="button" id="cancel" class="btn btn-default btn-sm" value="<?=lang('lang.reset');?>" 
                        onclick="window.location.href='<?php echo $reset_url; ?>'">
                        <input type="submit" id="submit" class="btn btn-sm btn-primary" value="<?=lang('Save');?>" />
                    </div>
                </div>
                <!-- button row }}}3 -->
            </div> <!-- panel body of rolling_comm  }}}2 -->    
        </div>
    </form> <!-- end of form rolling_comm }}}1 -->    
</div>

<script>
$(document).ready(function(){
    $('#gameTree').jstree({
        'core' : {
            'data' : {
                "url" : "<?php echo site_url('/api/get_game_tree_by_agent/' . $agent_id); ?>",
                    "dataType" : "json" // needed only if you do not supply JSON headers
            }
        },
            "checkbox":{
                "tie_selection": false,
            },
            "plugins":[
                "search","checkbox","input_number"
            ]
    });

});
</script>

<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of rolling_comm_setting.php
