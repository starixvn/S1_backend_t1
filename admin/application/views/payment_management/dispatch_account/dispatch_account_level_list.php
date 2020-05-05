<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="icon-diamond"></i> <?=lang('dispatch_account.lvl');?> - <b><?=lang($datas[0]['group_name']);?></b>
            <div class="pull-right">
                <a href="<?=BASEURL . 'dispatch_account_management/increaseDispatchAccountLevel/' . $group_id?>" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info' : 'btn-warning'?>">
                    <span class="glyphicon glyphicon-plus-sign"></span> <?=lang('dispatch_account_level.increase_new_level');?>
                </a>
                <a href="<?=BASEURL . 'dispatch_account_management/dispatchAccountGroupList'?>" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info' : 'btn-default'?>" id="add_news">
                    <span class="glyphicon glyphicon-remove"></span>
                </a>
            </div>
        </h4>
        <div class="clearfix"></div>
    </div>
    <div class="panel panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12">
                <div id="tag_table">
                    <table class="table table-striped table-hover" id="level_list_table" style="margin: 0px 0 0 0; width: 100%;">
                        <thead>
                            <tr>
                                <th></th>
                                <th><?=lang('dispatch_account_level.level_order');?></th>
                                <th><?=lang('player.lvlname');?></th>
                                <th><?=lang('dispatch_account_level.level_member_limit');?></th>
                                <th><?=lang('dispatch_account_level.level_observation_period');?></th>
                                <th><?=lang('dispatch_account_level.level_single_max_deposit');?></th>
                                <th><?=lang('dispatch_account_level.level_total_deposit');?></th>
                                <th><?=lang('dispatch_account_level.level_deposit_count');?></th>
                                <th><?=lang('dispatch_account_level.level_total_withdraw');?></th>
                                <th><?=lang('dispatch_account_level.level_withdraw_count');?></th>
                                <th><?=lang('dispatch_account_level.player_number');?></th>
                                <th><?=lang('cms.createdon');?></th>
                                <th><?=lang('cms.updatedon');?></th>
                                <th style="min-width: 230px;"><?=lang('lang.action');?></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($datas)): ?>
                                <?php $i = 0; ?>
                                <?php foreach ($datas as $data): ?>
                                    <tr>
                                        <td></td>
                                        <td><?=$i++?></td>
                                        <td><?=$data['level_name'] == '' ? '-' : $data['level_name']?></td>
                                        <td><?=$data['level_member_limit'] == '' ? '-' : $data['level_member_limit']?></td>
                                        <td><?=$data['level_observation_period'] == '' ? '-' : $data['level_observation_period']?></td>
                                        <td><?=$data['level_single_max_deposit'] == '' ? '-' : $data['level_single_max_deposit']?></td>
                                        <td><?=$data['level_total_deposit'] == '' ? '-' : $data['level_total_deposit']?></td>
                                        <td><?=$data['level_deposit_count'] == '' ? '-' : $data['level_deposit_count']?></td>
                                        <td><?=$data['level_total_withdraw'] == '' ? '-' : $data['level_total_withdraw']?></td>
                                        <td><?=$data['level_withdraw_count'] == '' ? '-' : $data['level_withdraw_count']?></td>
                                        <td><?=$data['player_count'] == '' ? '-' : $data['player_count']?></td>
                                        <td><?=$data['created_at'] == '' ? '-' : $data['created_at']?></td>
                                        <td><?=$data['updated_at'] == '' ? '-' : $data['updated_at']?></td>
                                        <td>
                                            <div class="action_dispatch_account_level">
                                                <a href="<?=BASEURL . 'dispatch_account_management/getDispatchAccountLevel/' . $data['id']?>">
                                                    <button class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>"><?=lang('dispatch_account_level.edit');?></button>
                                                </a>
                                                <a href="<?=BASEURL . 'dispatch_account_management/getDispatchLevelPlayerList/' . $data['id']?>">
                                                    <button class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>"><?=lang('dispatch_account_level.player_list');?></button>
                                                </a>
                                            <?php if($data['level_order'] > 0) { ?>
                                                <a href="<?=BASEURL . 'dispatch_account_management/setPlayersToResetLevel/'. $data['id']?>" onClick='return confirm("Are you sure to set all the players of this level back to reset level?")'>
                                                    <button class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-info'?>"><?=lang('dispatch_account_level.reset_back');?></button>
                                                </a>
                                                <?php if(count($datas) > 2) : ?>
                                                <a href="<?=BASEURL . 'dispatch_account_management/deleteDispatchAccountLevel/'. $data['id']. '/'. $data['group_id']?>" onClick='return confirm("<?=lang('sys.gd4');?>")'>
                                                    <button class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-info'?>"><?=lang('dispatch_account_level.delete');?></button>
                                                </a>
                                                <?php endif; ?>
                                            <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#level_list_table').DataTable({
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "dom":"<'panel-body' <'pull-right'f><'pull-right progress-container'>l>t<'panel-body'<'pull-right'p>i>",
            buttons: [
            {
              extend: 'colvis',
              postfixButtons: [ 'colvisRestore' ]
            }
            ],
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ]
        });
    });
</script>