
<div class="row" id="user-container">

<div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading custom-ph">
                <h3 class="panel-title custom-pt" >
                    <i class="icon-list"></i>
                    <?=lang('Newly Added Games');?>
                </h3>
            </div>
            <form method="post" action="<?=site_url('/game_description/activate_newly_added_games')?>">
            <div class="panel-body" id="list_panel_body">
                <div class="row">
                    <div class="form-group  col-md-6" >
                        <input type="hidden" name="selected_game_tree" value="">
                        <fieldset style="padding:20px">
                            <legend><h4><?=lang('Current new games');?></h4></legend>
                            <div id="gameTree" class="col-md-12">
                            </div>
                        </fieldset>
                        <br>
                        <div class="row col-md-6">
                            <?php
                                if($new_games_cnt){ ?>
                            <input type="submit" class="btn btn-success save" id="activate_new_games" value="<?=lang('Activate New Games')?>">
                        <?php } ?>
                        </div>
                    </div>
                    <div class="form-group  col-md-6">
                        <fieldset style="padding:20px">
                            <legend><h4><?=lang('Notes:');?></h4></legend>
                            <p>
                                <?php
                                if($new_games_cnt){
                                echo lang('By clicking <strong>Activate New Games</strong> button, these games will be added in your current game list');
                                }else{
                                    echo lang("No More New Games");
                                }?>
                            </p>
                        </fieldset>
                    </div>
                </div>
            </div>
            </form>
            <div class="panel-footer"></div>
        </div>
    </div>
</div>

<script>
    $('#gameTree').jstree({
      'core' : {
        'data' : {
          "url" : "<?php echo site_url('/api/get_game_tree_by_flag_new_game'); ?>",
          "dataType" : "json",
        },
      },
    });
</script>