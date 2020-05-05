<div class="panel panel-primary" id="game_form">
    <div class="panel-heading">
        <h4 class="panel-title"> <a href="#game_info_update" id="hide_game_info" class="btn btn-primary btn-sm">
                <i class="glyphicon glyphicon-chevron-up" id="hide_game_up"></i></a> &nbsp;
            <strong><?= $system_code ?> <?= lang('Bet Setting') ?></strong>
        </h4>
    </div>
    <div class="panel-body" id="game_panel_body">
        <div class="row">
            <div class="col-md-6" style="padding:20px">
                <div class="form-group">
                    <ul class="list-group">
                        <li class="list-group-item"><?php echo lang('Username'); ?> : <b><?= $username ?></b> </li>
                    </ul>
                </div>
                <form class="form-horizontal" id="append_form">
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="sport"><?= lang('Sport Type') ?></label>
                        <div class="col-sm-10">
                            <select class="form-control js-sport-type" id = "sport" name ="sport" required>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="market"><?= lang('Market Type') ?></label>
                        <div class="col-sm-10">
                            <select class="form-control js-market-type" id="market">
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="min_bet"><?php echo lang('Minimum Bet'); ?> :</label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="min_bet" placeholder="<?php echo lang('Enter minimum bet'); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="max_bet"><?php echo lang('Maximum Bet'); ?> :</label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="max_bet" placeholder="<?php echo lang('Enter maximum bet'); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="max_bet_per_match"><?php echo lang('Max Bet Per Match'); ?> :</label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="max_bet_per_match" placeholder="Enter max bet per match" required>
                        </div>
                    </div>
                    <div class="form-group">        
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary btn_append"><?= lang('Add bet limit to json params') ?></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <ul class="list-group">
                            <li class="list-group-item"><b><?php echo lang('Note: Add bet limit to json or update manually then update bet limit'); ?></b> </li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="col-md-4" style="padding:20px">
                <form class="form-horizontal" id="form_game_update" action="<?php echo base_url()?>player_management/update_sbobet_game_bet_limit/<?=$player_id?>/<?=$game_platform_id?>" method="post">
                    <div class="form-group">
                        <div class="alert alert-info">
                            <strong><?php echo lang('Info'); ?>!</strong> <?php echo lang('Current PLayer Bet Setting Per Sport Type'); ?> 
                        </div>
                    </div>
                    <h4><?php echo lang('Json Params'); ?></h4>
                    <div class="form-group">
                        <pre class="form-control" id="json_display" name="json_display">
                            <?php echo $json ?>
                        </pre>
                        <input type="hidden" id="json_params" name="json_params">
                        <input type="hidden" name="system_code" value="<?=$system_code?>">
                    </div>
                    <div class="form-group">
                        <div class="col-sm-10">
                          <button type="button" class="btn btn-primary btn_update_sport_bet"><?= lang('Update Bet Limit') ?></button>
                        </div>
                    </div>
                </form> 
            </div>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>

<script type="text/javascript">
    // Init ACE editor for JSON
    var json_display = ace.edit("json_display");
    json_display.setTheme("ace/theme/tomorrow");
    json_display.session.setMode("ace/mode/json");
    json_display.setOption("maxLines", 200);
    json_display.setOption("minLines", 2);
    json_display.setValue(JSON.stringify(JSON.parse(json_display.getValue()), null, 4));

    // $(document).on("click",".btn_update",function( event ){
    //     $('.btn_update_sport_bet').click();
    // });


    $(document).on("click",".btn_update_sport_bet",function( event ){
        event.preventDefault();
        var info = json_display.getValue();
        if ( ! isJsonString(info) ) {
            alert('Invalid JSON');
            return false;
        }
        $( "#json_params" ).val(info);
        $( "#form_game_update" ).submit();
    });

    function isJsonString(str) {
        if (str == '') return true;
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    var sports = $.parseJSON('<?php echo $sport_types; ?>');
    var markets = $.parseJSON('<?php echo $market_types; ?>');
    // console.log(sports);
    $(document).ready(function() {
        $('.js-sport-type').select2({data: sports}).val(0).trigger('change'); ;
        $('.js-market-type').select2({data: markets}).val(0).trigger('change'); ;
    });


    $(document).on("keypress","#min_bet, #max_bet, #max_bet_per_match",function(e){
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });

    $("#append_form").submit(function(e){
        e.preventDefault(e);
        var sport = $('.js-sport-type').val();
        var market = $('.js-market-type').val();
        var minimum_bet = $('#min_bet').val();
        var max_bet = $('#max_bet').val();
        var max_bet_per_match = $('#max_bet_per_match').val();

        var bet_input = {};
        var current_json = json_display.getValue();
        var current_json = jQuery.parseJSON( current_json );
        var bet_input = JSON.stringify(bet_input, null,  4);
        var bet_input = jQuery.parseJSON( bet_input );
        var exist = checkExist(sport, market);

        if(minimum_bet > max_bet || minimum_bet > max_bet_per_match){
            var message = '<?php echo lang('Minimum bet must be less than to all fields.'); ?>';
            alert(message);
            return false;
        }
        if(max_bet > max_bet_per_match ){
            var message = '<?php echo lang('Maximum bet must be less than per match.'); ?>';
            alert(message);
            return false;
        }

        
        bet_input["sport_type"] = $.isNumeric(sport) ? parseInt(sport) : sport;
        bet_input["market_type"] = $.isNumeric(market) ? parseInt(market) : market;
        bet_input["min_bet"] = $.isNumeric(minimum_bet) ? parseInt(minimum_bet) : minimum_bet;
        bet_input["max_bet"] = $.isNumeric(max_bet) ? parseInt(max_bet) : max_bet;
        bet_input["max_bet_per_match"] = $.isNumeric(max_bet_per_match) ? parseInt(max_bet_per_match) : max_bet_per_match;

        
        if(exist){
            var message = '<?php echo lang('Sport and market limit already exist. Do you want to override ?'); ?>';
            var conf = confirm(message);
            if (conf == true) {
                current_json.some(function(el, key) {
                    //update current key value
                    if(el.sport_type == sport && el.market_type == market){
                        current_json[key]['min_bet'] = minimum_bet;
                        current_json[key]['max_bet'] = max_bet;
                        current_json[key]['max_bet_per_match'] = max_bet_per_match;
                    }
                }); 
                json_display.setValue(JSON.stringify(current_json, null, 4));
            } else {
                return false;
            }
        } else {
            current_json.push(bet_input);
            json_display.setValue(JSON.stringify(current_json, null, 4));
        }   
    });

    function checkExist($sport_type, $market_type){
        var current_json = json_display.getValue();
        var arr = jQuery.parseJSON( current_json );
        return arr.some(function(el) {
            console.log(el.sport_type);
            return (el.sport_type == $sport_type && el.market_type == $market_type);
        }); 
    }

</script>