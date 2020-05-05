<div class="panel panel-primary" id="game_form" style="width: 40%;margin: 0 auto;">
    <div class="panel-heading">
        <h4 class="panel-title"> <a href="#game_info_update" id="hide_game_info" class="btn btn-primary btn-sm">
                <i class="glyphicon glyphicon-chevron-up" id="hide_game_up"></i></a> &nbsp;
            <strong><?= $system_code ?> <?= lang('Bet Setting') ?></strong>
        </h4>
    </div>
    <div class="panel-body" id="game_panel_body">
        <div class="row">
            <div class="col-md-4" style="padding:20px; left: 200px;">
                <div class="form-group">
                    <ul class="list-group">
                        <li class="list-group-item"><?php echo lang('Username'); ?> : <b><?= $username ?></b> </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="padding:20px">
                <form class="form-horizontal" id="append_form">
                    <div class="form-group">
                        <div class="form-group">
                            <label class="control-label col-md-3" style="left: 50px" for="min_bet"><?php echo lang('Minimum Bet'); ?> :</label>
                            <div class="col-sm-6">
                            <input type="text" class="form-control" id="min_bet" placeholder="<?php echo lang('Enter minimum bet'); ?>" required>  
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" style="left: 50px" for="max_bet"><?php echo lang('Maximum Bet'); ?> :</label>
                            <div class="col-sm-6">
                            <input type="text" class="form-control" id="max_bet" placeholder="<?php echo lang('Enter maximum bet'); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col" style="text-align: center">
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

    $(document).on("click", ".btn_update_sport_bet",function(){
        var gameId = "<?php echo $game_platform_id; ?>";
        var playerId = "<?php echo $player_id; ?>";
        var min_bet = $("#min_bet").val();
        var max_bet = $("#max_bet").val();
        var data = JSON.stringify({
            min_bet:min_bet,
            max_bet:max_bet
        });
        // console.log(data);
        // // alert(data);
        var url = "/async/update_player_ogplus_bet_setting";
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: {
                data:data,
                playerId:playerId,
                gameId:gameId
            },
            success: function(data){
                // alert(data);
                if(data.success == true){
                    alert("<?php echo lang('Update success!'); ?>");
                    location.reload();
                } else {
                    alert("<?php echo lang('Try again!'); ?>");
                }
            }
        });
    });

    $(document).on("keypress","#min_bet,#max_bet",function(e){
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            alert('Must input Integer');
            return false;
        }
    });



</script>