<style type="text/css">
    table.game-history-content {
        text-align: center;
    }
    table.game-history-content th {
        text-align: center;
    }
    table.game-history-content>thead>tr>th {
        border-bottom: 1px solid #000;
    }
    .box-border-bottom {
        border-bottom: 1px #000 solid;
    }
    table.game-history-content tbody tr td.vcenter {
        vertical-align: middle!important;
    }
    table.game-history-content tbody tr td.game-code-list {
        background: #e6f3fa;
        border-bottom: 1px #000 solid;
    }
    .game-b-b {
        border-bottom: 1px #000 solid;
    }
    @media (min-width: 768px) {
        .modal-xl {
            width: 90%;
        }
    }
</style>
<?php 
    // echo "<pre>";
    // print_r($comparedChanges);exit;
 ?>
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title"><?=lang('Batch Add/Update Game list Result')?> (<?=$game_platform_name?>)</h4>
            </div>
            <div class="panel-body">
                <?php if (!empty($message)): ?>
                    <div class="text-danger">
                        <?=$message?>
                    </div>
                    <hr/>
                <?php else : ?>

                <h4 class="text-info"><strong><?=lang('Total')?>: <span class="pull-right"><?=$total_count_of_games?></span></strong></h4>
                <h4 class="text-success"><strong><?=lang('Success')?>: <span class="pull-right"><?=$total_count_of_success_save_games?></span></strong></h4>
                <h4 class="text-danger"><strong><?=lang('Failed')?>: <span class="pull-right"><?=$total_count_of_failed_to_save_games?></span></strong></h4>
                <hr/>

                <div><span class="text-info"><?=lang('Games that has been Save')?>: </span>
                    <div href="#total_added_games" data-toggle="collapse" style="margin-left: 3%;"><span><?=lang('Added Games')?>: </span> <a href="#total_added_games" data-toggle="collapse""><?=empty($total_added_games)?0:$total_added_games?></a>
                        <ul id="total_added_games" class="collpase collapse" >
                            <div style="height: <?=($total_added_games > 0 ? $total_added_games * 20 : 20) . 'px'?>; overflow-y: scroll;">
                                <?php if (empty($total_added_games)): ?>
                                    <li><?=lang('No added Game')?></li>
                                <?php endif ?>
                                <?php if (count($list_of_Games_that_has_been_save['inserted_games']) > 1): ?>
                                    <?php foreach ($list_of_Games_that_has_been_save['inserted_games'] as $key => $game): ?>
                                        <li><?=lang($game['game_name'])?> [<?=$game['game_code']?>]</li>
                                    <?php endforeach ?>
                                <?php else: ?>
                                    <li><?=lang('No added Game')?></li>
                                <?php endif ?>
                            </div>
                        </ul>
                    </div>
                    <div href="#total_updated_games" data-toggle="collapse" style="margin-left: 3%;"><span><?=lang('Updated Games')?>: </span> <a href="#total_updated_games" data-toggle="collapse""><?=empty($total_updated_games)?0:$total_updated_games?></a>
                        <ul id="total_updated_games" class="collpase collapse">
                            <div style="height: <?=($total_updated_games > 0 ? $total_updated_games * 20 : 20) . 'px'?>; overflow-y: scroll;">
                                <?php if (empty($total_updated_games)): ?>
                                    <li><?=lang('No updated Game')?></li>
                                <?php else: ?>
                                    <?php foreach ($list_of_Games_that_has_been_save['updated_games'] as $key => $game): ?>
                                        <li><?=lang($game['game_name'])?> [<?=$game['game_code']?>]</li>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </div>
                        </ul>
                    </div>
                </div>
                <hr/>
                <div><span class="text-danger"><?=lang('Failed Games')?>: </span>
                     <div href="#missing_game_codes" data-toggle="collapse" style="margin-left:3%"><span><?=lang('No Game Codes')?>: </span><a href="#missing_game_codes" data-toggle="collapse""><?=empty($total_count_of_failed_no_game_codes)?0:$total_count_of_failed_no_game_codes?></a>
                        <ul id="missing_game_codes" class="collpase collapse">
                            <div style="height: <?=($total_count_of_failed_no_game_codes > 0 ? $total_count_of_failed_no_game_codes * 20 : 20) . 'px'?>; overflow-y: scroll;">
                                <?php if (empty($total_count_of_failed_no_game_codes)): ?>
                                    <li><?=lang('No missing game code')?></li>
                                <?php else: ?>
                                    <?php foreach ($missing_games['game_codes_for'] as $key): ?>
                                        <li><?=$key?></li>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </div>
                        </ul>
                    </div>
                    <div href="#missing_game_types" data-toggle="collapse" style="margin-left:3%"><span><?=lang('Missing Game Type')?>: </span><a href="#missing_game_types" data-toggle="collapse""><?=empty($count_of_missing_game_type)?0:$count_of_missing_game_type?></a>
                        <ul id="missing_game_types" class="collpase collapse">
                            <div style="height: <?=($count_of_missing_game_type > 0 ? $count_of_missing_game_type * 20 : 20) . 'px'?>; overflow-y: scroll;">
                                <?php if (empty($count_of_missing_game_type)): ?>
                                    <li><?=lang('No missing game type')?></li>
                                <?php endif ?>
                                <?php foreach ($missing_games['game_types'] as $key): ?>
                                    <li><span class="glyphicons glyphicons-arrow-right"></span> <?=$key?></li>
                                <?php endforeach ?>
                            </div>
                        </ul>
                    </div>
                    <div href="#game_dont_have_game_type" data-toggle="collapse" style="margin-left:3%"><span><?=lang('Games don\'t have game type')?>: </span><a href="#game_dont_have_game_type" data-toggle="collapse""><?=empty($total_count_of_games_dont_gave_game_type)?0:$total_count_of_games_dont_gave_game_type?></a>
                        <ul id="game_dont_have_game_type" class="collpase collapse">
                            <div style="height: <?=($total_count_of_games_dont_gave_game_type > 0 ? $total_count_of_games_dont_gave_game_type * 20 : 20) . 'px'?>; overflow-y: scroll;">
                                <?php if (empty($total_count_of_games_dont_gave_game_type)): ?>
                                    <li><?=lang('No missing game')?></li>
                                <?php endif ?>
                                <?php foreach ($missing_games['dont_have_game_type'] as $key): ?>
                                    <li><span class="glyphicons glyphicons-arrow-right"></span> <?=$key?></li>
                                <?php endforeach ?>
                            </div>
                        </ul>
                    </div>
                    <span style="margin-left:3%"><?=lang('Count of unsave games due game type failure')?>: </span><span class="text-danger"><?=$count_of_unsave_games_due_game_type_failure?></span>
                </div>
                <hr/>
                <div><span class="text-info"><?=lang('Available Game types')?>:</span>
                    <ul>
                        <?php if (!empty($available_game_types)): ?>
                            <?php foreach ($available_game_types as $key => $game_type): ?>
                                <li><?=lang($game_type['game_type'])?></li>
                            <?php endforeach ?>
                        <?php endif ?>
                    </ul>
                </div>
                <div>
                    <span class="text-info btn btn-primary" data-toggle="modal" data-target="#myModal"><?=lang('Check Changes')?>:</span>
                </div>
                <hr/>
                    <?php if (!empty($client_update_map)): ?>
                        <div style="margin-left:3%"><span class="text-info"><?=lang('Clients that has been updated')?>:</span>
                        <?php foreach ($client_update_map as $client_url => $value): ?>
                            <?php
                            $client_name = explode('.', $client_url);
                            $client_name = ($client_name[1] == 'staging') ? ucfirst($client_name[2]):ucfirst($client_name[1]);
                            ?>
                        <div style="margin-left: 3%;"><a href="#<?=$client_name?>" data-toggle="collapse""><span><?=$client_name?>: <span style="font-size:10px;"><?=lang('Game Count')?>:<?=$value['Counts']['insert'] + $value['Counts']['update'] + count($value['missing_game_type_id'])?> </span></span></a>
                            <ul id="<?=$client_name?>" class="collpase collapse" >
                                <!-- Added games -->
                                <li><?=lang('Added Games')?>: <a href=".<?=$client_name?>_inserted_games" data-toggle="collapse"><?=($value['Counts']['insert'] > 0) ? $value['Counts']['insert']:0?></a>
                                    <?php if (!empty($value['list_of_games']['inserted_games'])): ?>
                                        <?php foreach ($value['list_of_games']['inserted_games'] as $key => $inserted_game): ?>
                                            <li class="collpase collapse <?=$client_name?>_inserted_games"><?=lang($inserted_game['game_name'])?></li>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </li>
                                <!-- End -->
                                <!-- Updated games -->
                                <li><?=lang('Updated Games')?>:  <a href=".<?=$client_name?>_updated_games" data-toggle="collapse"><?=($value['Counts']['update'] > 0) ? $value['Counts']['update']:0?></a>
                                    <?php foreach ($value['list_of_games']['updated_games'] as $key => $update_game): ?>
                                        <li class="collpase collapse <?=$client_name?>_updated_games"><?=lang($update_game['game_name'])?></li>
                                    <?php endforeach ?>
                                </li>
                                <!-- End -->
                                <!-- Added games -->
                                <li><span class="text-danger"><?=lang('Games don\'t have game type')?>:</span>  <a href=".<?=$client_name?>_game_dont_have_game_type" data-toggle="collapse"><?=(count($value['missing_game_type_id']) > 0) ? count($value['missing_game_type_id']):0?></a>
                                     <?php foreach ($value['missing_game_type_id'] as $key => $game_dont_have_game_type): ?>
                                        <li class="collpase collapse <?=$client_name?>_game_dont_have_game_type"><?=lang($game_dont_have_game_type['game_name'])?><span class="text-danger">[<?=$game_dont_have_game_type['game_type']?>]</span></li>
                                    <?php endforeach ?>
                                </li>

                                <li><span class="text-danger"><?=lang('Missing game type')?>:</span> <a href=".<?=$client_name?>_missing_game_type" data-toggle="collapse"><?=(count($value['missing_game_type']) > 0) ? count($value['missing_game_type']):0?></a>
                                    <?php foreach ($value['missing_game_type'] as $key => $game_type): ?>
                                        <li class="collpase collapse <?=$client_name?>_missing_game_type"><?=$game_type?></li>
                                    <?php endforeach ?>
                                </li>

                            </div>
                        </ul>
                        <?php endforeach ?>
                        <hr/>
                    <?php endif ?>
                <?php endif ?>
                <a href="/game_description/viewGameListSettings" class="btn btn-primary btn-block"><?=lang('Return to Game list settings')?></a>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLongTitle" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="myModalLongTitle"><?=lang('Games Report')?>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </h5>
          </div>
          <div class="modal-body ">
            <div class="table-responsive">
            <table class="table game-history-content" id="myTable">
              <thead>
                <tr>
                    <th><?=lang('Game Code')?></th>
                    <th>Status</th>
                    <th>Game Name</th>
                    <th>Dlc Enabled</th>
                    <th>progressive</th>
                    <th>Flash Enabled</th>
                    <th>Offline Enabled</th>
                    <th>Mobile Enabled</th>
                    <th>note</th>
                    <th>status</th>
                    <th>Flag Show In Site</th>
                    <th>No Cash Back</th>
                    <th>attributes</th>
                    <th>Game Order</th>
                    <th>Html Five Enabled</th>
                    <th>English Name</th>
                    <th>Related Game Desc Id</th>
                    <th>Enabled Freespin</th>
                    <th>Sub Game Provider</th>
                    <th>Enabled On Android</th>
                    <th>Enabled On Ios</th>
                </tr>
              </thead>

              <tbody class="box-border-bottom">
                <?php foreach ($comparedChanges as $key => $details): ?>
                
                <tr>
                    <td  class="vcenter game-code-list"><strong><?=$key?></strong></td>
                    <td class="old-bg">OLD</td>
                    <td class="<?=!isset($details['old']['game_name']) ?: 'bg-info'?>">
                        <?=isset($details['old']['game_name']) ? lang($details['old']['game_name']):null ?>
                    </td
>                    <td class="<?=!isset($details['old']['dlc_enabled']) ?: 'bg-info'?>">
                        <?=isset($details['old']['dlc_enabled']) ? $details['old']['dlc_enabled']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['progressive']) ?: 'bg-info'?>">
                        <?=isset($details['old']['progressive']) ? $details['old']['progressive']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['flash_enabled']) ?: 'bg-info'?>">
                        <?=isset($details['old']['flash_enabled']) ? $details['old']['flash_enabled']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['offline_enabled']) ?: 'bg-info'?>">
                        <?=isset($details['old']['offline_enabled']) ? $details['old']['offline_enabled']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['mobile_enabled']) ?: 'bg-info'?>">
                        <?=isset($details['old']['mobile_enabled']) ? $details['old']['mobile_enabled']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['note']) ?: 'bg-info'?>">
                        <?=isset($details['old']['note']) ? $details['old']['note']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['status']) ?: 'bg-info'?>">
                        <?=isset($details['old']['status']) ? $details['old']['status']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['flag_show_in_site']) ?: 'bg-info'?>">
                        <?=isset($details['old']['flag_show_in_site']) ? $details['old']['flag_show_in_site']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['no_cash_back']) ?: 'bg-info'?>">
                        <?=isset($details['old']['no_cash_back']) ? $details['old']['no_cash_back']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['attributes']) ?: 'bg-info'?>">
                        <?=isset($details['old']['attributes']) ? $details['old']['attributes']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['game_order']) ?: 'bg-info'?>">
                        <?=isset($details['old']['game_order']) ? $details['old']['game_order']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['html_five_enabled']) ?: 'bg-info'?>">
                        <?=isset($details['old']['html_five_enabled']) ? $details['old']['html_five_enabled']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['english_name']) ?: 'bg-info'?>">
                        <?=isset($details['old']['english_name']) ? $details['old']['english_name']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['related_game_desc_id']) ?: 'bg-info'?>">
                        <?=isset($details['old']['related_game_desc_id']) ? $details['old']['related_game_desc_id']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['enabled_freespin']) ?: 'bg-info'?>">
                        <?=isset($details['old']['enabled_freespin']) ? $details['old']['enabled_freespin']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['sub_game_provider']) ?: 'bg-info'?>">
                        <?=isset($details['old']['sub_game_provider']) ? $details['old']['sub_game_provider']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['enabled_on_android']) ?: 'bg-info'?>">
                        <?=isset($details['old']['enabled_on_android']) ? $details['old']['enabled_on_android']:null ?>
                    </td>
                    <td class="<?=!isset($details['old']['enabled_on_ios']) ?: 'bg-info'?>">
                        <?=isset($details['old']['enabled_on_ios']) ? $details['old']['enabled_on_ios']:null ?>
                    </td>
                </tr>
                <tr>
                    <td  class="vcenter game-code-list"><strong><?=$key?></strong></td>
                    <td class="game-b-b">New</td>
                    <td class="<?=!isset($details['new']['game_name'])?:'bg-success'?>">
                        <?=isset($details['new']['game_name']) ? lang($details['new']['game_name']):null ?>
                    </td>
                    <td class="<?=!isset($details['new']['dlc_enabled'])?:'bg-success'?>">
                        <?=isset($details['new']['dlc_enabled']) ? $details['new']['dlc_enabled']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['progressive'])?:'bg-success'?>">
                        <?=isset($details['new']['progressive']) ? $details['new']['progressive']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['flash_enabled'])?:'bg-success'?>">
                        <?=isset($details['new']['flash_enabled']) ? $details['new']['flash_enabled']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['offline_enabled'])?:'bg-success'?>">
                        <?=isset($details['new']['offline_enabled']) ? $details['new']['offline_enabled']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['mobile_enabled'])?:'bg-success'?>">
                        <?=isset($details['new']['mobile_enabled']) ? $details['new']['mobile_enabled']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['note'])?:'bg-success'?>">
                        <?=isset($details['new']['note']) ? $details['new']['note']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['status'])?:'bg-success'?>">
                        <?=isset($details['new']['status']) ? $details['new']['status']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['flag_show_in_site'])?:'bg-success'?>">
                        <?=isset($details['new']['flag_show_in_site']) ? $details['new']['flag_show_in_site']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['no_cash_back'])?:'bg-success'?>">
                        <?=isset($details['new']['no_cash_back']) ? $details['new']['no_cash_back']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['attributes'])?:'bg-success'?>">
                        <?=isset($details['new']['attributes']) ? $details['new']['attributes']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['game_order'])?:'bg-success'?>">
                        <?=isset($details['new']['game_order']) ? $details['new']['game_order']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['html_five_enabled'])?:'bg-success'?>">
                        <?=isset($details['new']['html_five_enabled']) ? $details['new']['html_five_enabled']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['english_name'])?:'bg-success'?>">
                        <?=isset($details['new']['english_name']) ? $details['new']['english_name']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['related_game_desc_id'])?:'bg-success'?>">
                        <?=isset($details['new']['related_game_desc_id']) ? $details['new']['related_game_desc_id']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['enabled_freespin'])?:'bg-success'?>">
                        <?=isset($details['new']['enabled_freespin']) ? $details['new']['enabled_freespin']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['sub_game_provider'])?:'bg-success'?>">
                        <?=isset($details['new']['sub_game_provider']) ? $details['new']['sub_game_provider']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['enabled_on_android'])?:'bg-success'?>">
                        <?=isset($details['new']['enabled_on_android']) ? $details['new']['enabled_on_android']:null ?>
                    </td>
                    <td class="<?=!isset($details['new']['enabled_on_ios'])?:'bg-success'?>">
                        <?=isset($details['new']['enabled_on_ios']) ? $details['new']['enabled_on_ios']:null ?>
                    </td>
                </tr>
                <?php endforeach ?>

              </tbody>

            </table>
          </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

</div>
<script type="text/javascript" src="<?=site_url().'resources/datatables/dataTables.buttons.min.js'?>"></script>
<script type="text/javascript" src="<?=site_url().'resources/datatables/jszip.min.js'?>"></script>
<script type="text/javascript" src="<?=site_url().'resources/datatables/buttons.html5.min.js'?>"></script>
<script type="text/javascript">
    $("#collapseSubmenuGameDescription").addClass("in");
    $("a#view_game_description").addClass("active");
    $("a#viewGameListSettings").addClass("active");
    $("#myTable").DataTable({
         dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l>" +
            "<'dt-information-summary1 text-info pull-left' i>t<'text-center'r>" +
            "<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        buttons: [
            {
                extend: 'csvHtml5',  // csvHtml5 , copyHtml5, excelHtml5
                exportOptions: {
                    columns: ':visible'
                },
                className:'btn btn-sm btn-primary',
                text: '<?=lang('CSV Export')?>',
                filename:  '<?=lang('Active Player Report')?>'
            }
        ]
    });
</script>