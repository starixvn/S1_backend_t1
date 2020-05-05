<div class="panel panel-primary" id="game_form">
    <div class="panel-heading">
        <h4 class="panel-title"> <a href="#game_info_update" id="hide_game_info" class="btn btn-primary btn-sm">
                <i class="glyphicon glyphicon-chevron-up" id="hide_game_up"></i></a> &nbsp;
            <strong><?=lang('player.ui06')?></strong>
        </h4>
    </div>
    <div class="panel-body" id="game_panel_body">
        <form class="form-horizontal" id="form_game_update" action="<?php echo base_url()?>player_management/updateGameInfo/<?=$playerId?>/<?=$game_platform_id?>" method="post">
            <div class="form-group">
                <div class="col-sm-4">
                    <div class="alert alert-warning">
                        <strong><?=lang('Note')?>!</strong> <?=lang('This is a default params value, not the exact player general bet settings. Input the desired bet setting value to update.')?>
                    </div>
                    <pre class="form-control" id="game_extra" name="game_extra" style="height: 300px;width: 900px;">
                        <?= $json ?>
                    </pre>
                    <input type="hidden" id="info_params" name="info_params">
                    <input type="hidden" name="system_code" value="<?=$system_code?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-10">
                  <button type="button " class="btn btn-primary btn_update"><?=sprintf(lang('gameplatformaccount.title.update'),$system_code)?></button>
                </div>
            </div>
        </form>
        <form class="form-horizontal" id="form_game_additional_update" action="<?php echo base_url()?>player_management/updateAdditionalInfo/<?=$playerId?>/<?=$game_platform_id?>" method="post">
            <div class="form-group">
                <div class="col-sm-4">
                    <pre class="form-control" id="game_additional" name="game_additional" style="height: 300px;width: 900px;"></pre>
                    <input type="hidden" id="additional_params" name="additional_params">
                    <input type="hidden" name="system_code" value="<?=$system_code?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-10">
                  <button type="button " class="btn btn-primary btn_update_addi"><?=sprintf(lang('gameplatformaccount.title.additional'),$system_code)?></button>
                </div>
            </div>
        </form>  
    </div>
    <div class="panel-footer"></div>
</div>

<script type="text/javascript">
    // Init ACE editor for JSON
    var game_extra = ace.edit("game_extra");
    game_extra.setTheme("ace/theme/tomorrow");
    game_extra.session.setMode("ace/mode/json");
    var game_additional = ace.edit("game_additional");
    game_additional.setTheme("ace/theme/tomorrow");
    game_additional.session.setMode("ace/mode/json");

    if($("#game_extra").length != 1){
        game_extra.setValue(JSON.stringify(JSON.parse(game_extra.getValue()), null, 4));
    }
    if($("#game_additional").length != 1){
        game_additional.setValue(JSON.stringify(JSON.parse(game_additional.getValue()), null, 4));
    }


    $(document).on("click",".btn_update",function( event ){
        event.preventDefault();
        var info = game_extra.getValue();
        if ( ! isJsonString(info) ) {
            alert('Invalid JSON');
            return false;
        }
        $( "#info_params" ).val(info);
        $( "#form_game_update" ).submit();
    });
    $(document).on("click",".btn_update_addi",function( event ){
        event.preventDefault();
        var addi = game_additional.getValue();
        if ( ! isJsonString(addi) ) {
            alert('Invalid JSON');
            return false;
        }
        $( "#additional_params" ).val(addi);
        $( "#form_game_additional_update" ).submit();
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
</script>