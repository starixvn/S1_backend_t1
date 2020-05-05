<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-diamond"></i> <?=lang('player.frl.setup.title')?>
            <div class="pull-right">
                <a href="<?=BASEURL . 'player_management/addEditFriendReferralLevel' . $vipSettingId?>" class="btn btn-default btn-sm">
                    <span class="glyphicon glyphicon-plus"></span> <?=lang('player.sd09')?>
                </a>
            </div>
            <div class="clearfix"></div>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12">
                <div id="tag_table">
                    <table class="table table-striped table-hover" id="my_table" style="margin: 0px 0 0 0; width: 100%;">
                        <thead>
                        <tr>
                            <th><?=lang('player.frl01')?></th>
                            <th><?=lang('player.frl02')?></th>
                            <th><?=lang('player.frl03')?></th>
                            <th><?=lang('player.frl04')?></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data as $row) { ?>
                        <tr>
                            <td><?=$row['min_betting']?></td>
                            <td><?=$row['max_betting']?></td>
                            <td><?=$row['min_volid_player']?></td>
                            <td><?=$row['max_volid_player']?></td>
                            <td><?php 
                                $games = json_decode($row['selected_game_tree'], true);
                                foreach($games as $game_row) {
                                    $percentage = '';
                                    if ($game_row['percentage'])
                                        $percentage = '%';
                                    echo $game_row['text'] . ' ( ' . $game_row['number'] . $percentage . ' )' . '<br>';
                                }
                                ?></td>
                            <td>
                                <div class="actionVipGroup">
                                    <a href="<?=BASEURL . 'player_management/addEditFriendReferralLevel/' . $row['id']?>">
                                        <button class="btn btn-sm btn-info"><?=lang('player.frl.edit')?></button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>  
        </div>
    </div>
    <div class="panel-footer"></div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#my_table').DataTable({
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "dom": "<'panel-body' <'pull-right'f><'pull-right progress-container'>l>t<'panel-body'<'pull-right'p>i>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ]
                }
            ],
            "order": [ 0, 'asc' ]
        });
    });
</script>