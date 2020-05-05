<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title"><?=lang('Batch Add/Update Game Type Result')?>
                <?php foreach ($game_platforms['available_game_platform'] as $key => $value): ?>
                    [<?php echo $value['game_platform_name']; ?>]
                <?php endforeach ?>
                </h4>
            </div>
            <div class="panel-body">
                <?php if (!empty($message)): ?>
                    <div class="text-danger">
                        <?=$message?>
                    </div>
                    <hr/>
                <?php else : ?>

                <h4 class="text-info"><strong><?=lang('Total')?>: <span class="pull-right"><?=$failed_to_add_update_game_type_count + $total_count_of_updated_game_type + $total_count_of_inserted_game_type?></span></strong></h4>
                <h4 class="text-success"><strong><?=lang('Success')?>: <span class="pull-right"><?=$total_count_of_updated_game_type + $total_count_of_inserted_game_type?></span></strong></h4>
                <h4 class="text-danger"><strong><?=lang('Failed')?>: <span class="pull-right"><?=$failed_to_add_update_game_type_count?></span></strong></h4>
                <hr/>

                <div><span class="text-info"><?=lang('Games that has been Save')?>: </span>
                    <div href="#total_added_game_types" data-toggle="collapse" style="margin-left: 3%;"><span><?=lang('Added Game Type')?>: </span> <a href="#total_added_game_types" data-toggle="collapse""><?=empty($total_count_of_inserted_game_type)?0:$total_count_of_inserted_game_type?></a>
                        <ul id="total_added_game_types" class="collpase collapse" >
                            <div style="height: 200px; overflow-y: scroll;">
                                <?php if (empty($total_count_of_inserted_game_type)): ?>
                                    <li><?=lang('No added game type')?></li>
                                <?php endif ?>
                                <?php foreach ($list_of_Games_that_has_been_save['inserted_games'] as $key => $game): ?>
                                    <?php //echo "<pre>";print_r($game); ?>
                                    <li><?=lang($game['game_type'])?> [<?=$game['game_platform_id']?>]</li>
                                <?php endforeach ?>
                            </div>
                        </ul>
                    </div>
                    <div href="#total_updated_game_types" data-toggle="collapse" style="margin-left: 3%;"><span><?=lang('Updated Game Type')?>: </span> <a href="#total_updated_game_types" data-toggle="collapse""><?=empty($total_count_of_updated_game_type)?0:$total_count_of_updated_game_type?></a>
                        <ul id="total_updated_game_types" class="collpase collapse">
                            <div style="height: 200px; overflow-y: scroll;">
                                <?php if (empty($total_count_of_updated_game_type)): ?>
                                    <li><?=lang('No updated Game Type')?></li>
                                <?php endif ?>
                                <?php foreach ($updated_game_type as $key => $game): ?>
                                    <li><?=lang($game['game_type'])?> [<?=$game['game_platform_name']?>]</li>
                                <?php endforeach ?>
                            </div>
                        </ul>
                    </div>
                </div>
                <hr/>
                <div><span class="text-danger"><?=lang('Failed Games')?>: </span>
                    <div href="#missing_game_types" data-toggle="collapse" style="margin-left:3%"><span><?=lang('Missing Game Platform Id')?>: </span><a href="#missing_game_types" data-toggle="collapse""><?=empty($failed_to_add_update_game_type_count)?0:$failed_to_add_update_game_type_count?></a>
                        <ul id="missing_game_types" class="collpase collapse">
                            <div style="height: 200px; overflow-y: scroll;">
                                <?php foreach ($failed_to_add_update_game_type as $key => $value): ?>
                                    <li><span class="glyphicons glyphicons-arrow-right"></span> <?=$value?></li>
                                <?php endforeach ?>
                            </div>
                        </ul>
                    </div>
                </div>
                <hr/>
                <?php endif ?>
                <a href="/game_description/viewGameListSettings" class="btn btn-primary btn-block"><?=lang('Return to Game list settings')?></a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("#collapseSubmenuGameDescription").addClass("in");
    $("a#view_game_description").addClass("active");
    $("a#viewGameListSettings").addClass("active");
</script>