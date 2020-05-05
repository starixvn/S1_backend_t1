
<div class="row" id="user-container">

<div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading custom-ph">
                <h3 class="panel-title custom-pt" >
                    <i class="icon-list"></i>
                    <?=lang('New Games');?>
                </h3>

            </div>

            <form id="new_games" method="post" action="<?=site_url('/game_description/processNewGames')?>">
                <div class="panel-body" id="list_panel_body">
                    <div class="row">
                        <div class="form-group  col-md-6" >
                            <input type="hidden" name="selected_game_tree" value="">
                            <fieldset style="padding:20px">
                                <legend><h4><?=lang('Current new games');?></h4></legend>
                                <div id="gameTree" class="col-md-12">
                                </div>
                                <br>
                                <div class="row col-md-6">
                                    <input type="submit" class="btn btn-success save" id="viewed_selected_new_games" value="<?=lang('Change flag of selected games to viewed')?>">
                                    <input type="hidden" name="viewed_selected_new_games">
                                </div>
                                <div class="row col-md-6">
                                    <input type="submit" class="btn btn-success save" id="viewed_all_new_games" value="<?=lang('Change flag of all games to viewed')?>">
                                    <input type="hidden" name="viewed_all_new_games">
                                </div>
                            </fieldset>
                        </div>
                        <div class="form-group  col-md-6" style="margin-top: 20px;">
                            <fieldset style="padding:20px">
                                <div class="row col-md-12">
                                    <label><?=lang('Add to cashback')?></label>
                                    <input type="checkbox" name="add_to_cashback">
                                </div>
                                <div class="row col-md-12">
                                    <label><?=lang('Add to promotion')?></label>
                                    <input type="checkbox" name="add_to_promotion">
                                </div>

                                <br>
                                <div class="row col-md-12">
                                        <input type="submit" class="btn btn-success save" id="save_only" value="<?=lang("Save")?>">
                                        <input type="submit" class="btn btn-success save" id="save_and_unread" value="<?=lang("Save and change flag to viewed")?>">
                                        <input type="hidden" name="save_and_unread">
                                </div>
                            </fieldset>
                        </div>
                    </div>

                </div>
                <div class="panel-footer"></div>
            </form>
        </div>
    </div>


</div>


<script>
    $('#save_and_unread').on('click',function(){
        $('input[name=save_and_unread]').val(1);
    });

    $('#viewed_selected_new_games').on('click',function(){
        $('input[name=viewed_selected_new_games]').val(1);
    });

    $('#viewed_all_new_games').on('click',function(){
        $('input[name=viewed_all_new_games]').val(1);
    });

    $('#gameTree').jstree({
      'core' : {
        'data' : {
          "url" : "<?php echo site_url('/api/get_game_tree_by_flag_new_game'); ?>",
          "dataType" : "json" // needed only if you do not supply JSON headers
        }
      },
      "input_number":{
          "form_sel": '#new_games'
      },
      "checkbox":{
        "tie_selection": false,
      },
      "plugins":[
        "search","checkbox",
      ]
    });

    $(".save").click(function(){
        checkGameIfSelected();
    });

    function checkGameIfSelected(){
      var selected_game=$('#gameTree').jstree('get_checked');
      if(selected_game.length>0){
        $('#new_games input[name=selected_game_tree]').val(selected_game.join());
        $('#gameTree').jstree('generate_number_fields');
      }
    }
</script>