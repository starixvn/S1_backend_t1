<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="icon-diamond"></i> <?=lang('pay.dispatch_account')?>
            <img id="refresh-loader" src="<?=$this->utils->imageUrl('ajax-loader.gif')?>" style="width:30px; display:none; float:right;" />
            <button href="javascript:void(0)" class="btn btn-xs pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>" id="refresh_player_belong_level" style='color:#fff;margin-top: -4px;' onclick="refreshPlayerBelongLevel();">
                <span id="reset_dispatch_account_group_glyhicon" class="glyphicon"></span>
                <?=lang('dispatch_account_level.refresh'); ?>
            </button>
            <a href="javascript:void(0)" class="btn btn-xs pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-warning'?>" id="add_dispatch_group" style="color:#fff;margin-top: -4px;margin-right: <?=$this->utils->getConfig('use_new_sbe_color') ? '4' : '20'?>px">
                <span id="add_dispatch_account_group_glyhicon" class="glyphicon glyphicon-plus-sign"></span>
                <?=lang('dispatch_account_group.add'); ?>
            </a>
        </h4>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body" id="details_panel_body">
        <!-- add dispatch account group -->
        <div class="row add_dispatch_group_sec">
            <div class="col-md-12">
                <div class="well" style="overflow: auto">
                    <form id="add_dispatch_group_form" action="<?=site_url('dispatch_account_management/addDispatchAccountGroup')?>" class="form-horizontal" method="post" role="form" class="form-inline" enctype="multipart/form-data">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6 i_required">
                                    <label class="control-label" for="group_name"><span style="color: #ea2f10;">* </span><?=lang('player.grpname');?>: </label>
                                    <input type="text" id="group_name" name="group_name" required="required" class="form-control input-sm">
                                </div>
                                <div class="col-md-2 i_required">
                                    <label class="control-label" for="group_level_count"><span style="color: #ea2f10;">* </span><?=lang('player.grplvlcnt');?>: </label>
                                    <input type="number" id="group_level_count" name="group_level_count" required="required" class="form-control input-sm" min="1">
                                </div>
                                <div class="col-md-2 i_required">
                                        <label class="control-label" for="level_member_limit"><span style="color: #ea2f10;">* </span><?=lang('dispatch_account_level.level_member_limit');?>: </label>
                                        <input type="number" id="level_member_limit" name="level_member_limit" required="required" class="form-control input-sm" min="<?=$min_member_limit?>">
                                </div>
                                <div class="col-md-2 i_required">
                                    <label class="control-label" for="level_observation_period"><?=lang('dispatch_account_level.set_observation_period');?></label>
                                    <input type="number" id="level_observation_period" name="level_observation_period" class="form-control input-sm" min="0">
                                </div>
                                <div class="col-md-12 i_required">
                                    <label class="control-label" for="group_description"><?=lang('pay.description');?>: </label>
                                    <textarea name="group_description" id="group_description" class="form-control input-sm" style="resize:none"></textarea>
                                </div>
                            </div>
                            <fieldset id="depositPaymentType" style="padding-bottom: 20px">
                                <legend style="padding-bottom: 8px">
                                    <label class="control-label"><?=lang('dispatch_account_batch.condition_setting');?></label>
                                </legend>
                                <label class="control-label" style="color: #ea2f10;"><?=lang('dispatch_account_batch.condition_setting.message');?></label>
                                <div class="row">
                                    <div class="col-md-4 i_required">
                                        <label class="control-label" for="level_total_deposit"><?=lang('dispatch_account_level.level_total_deposit');?>: </label>
                                        <input type="number" id="level_total_deposit" name="level_total_deposit" class="form-control input-sm" min="0">
                                    </div>
                                    <div class="col-md-4 i_required">
                                        <label class="control-label" for="level_total_withdraw"><?=lang('dispatch_account_level.level_total_withdraw');?>: </label>
                                        <input type="number" id="level_total_withdraw" name="level_total_withdraw" class="form-control input-sm" min="0">
                                    </div>
                                    <div class="col-md-4 i_required">
                                        <label class="control-label" for="level_single_max_deposit"><?=lang('dispatch_account_level.level_single_max_deposit');?>:</label>
                                        <input type="number" id="level_single_max_deposit" name="level_single_max_deposit" class="form-control input-sm" min="0">
                                    </div>
                                    <div class="col-md-4 i_required">
                                        <label class="control-label" for="level_deposit_count"><?=lang('dispatch_account_level.level_deposit_count');?>: </label>
                                        <input type="number" id="level_deposit_count" name="level_deposit_count" class="form-control input-sm" min="0">
                                    </div>
                                    <div class="col-md-4 i_required">
                                        <label class="control-label" for="level_withdraw_count"><?=lang('dispatch_account_level.level_withdraw_count');?>: </label>
                                        <input type="number" id="level_withdraw_count" name="level_withdraw_count" class="form-control input-sm" min="0">
                                    </div>
                                </div>
                            </fieldset>
                            <div class="pull-right">
                                <span class="btn btn-sm add_dispatch_group_cancel_btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default '?>" data-toggle="modal" <?=$this->utils->getConfig('use_new_sbe_color') ? ' style="margin-top:15px;"' : ''?>><?=lang('lang.cancel');?></span>
                                <input type="submit" value="<?=lang('lang.add');?>" class="btn btn-sm review-btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>" data-toggle="modal" <?=$this->utils->getConfig('use_new_sbe_color') ? ' style="margin-top:15px;"' : ''?>/>
                            </div>
                        </div>
                    </form>
                </div>
                <hr/>
            </div>
        </div>

        <!-- edit dispatch group -->
        <div class="row edit_dispatch_group_sec">
            <div class="col-md-12">
                <div class="well" style="overflow: auto">
                    <form id="edit_dispatch_group_form" action="<?=site_url('dispatch_account_management/addDispatchAccountGroup')?>" class="form-horizontal" method="post" role="form" enctype="multipart/form-data">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6 i_required">
                                    <label class="control-label" for="group_name"><?=lang('player.grpname');?>: </label>
                                    <input type="text" id="editGroupName" name="group_name" class="form-control input-sm">
                                    <input type="hidden" id="edit_group_id" name="group_id" class="form-control input-sm">
                                </div>
                                <div class="col-md-6">
                                    <input type="hidden" id="edit_group_level_count" name="group_level_count" class="form-control input-sm">
                                </div>
                                <div class="col-md-12 i_required">
                                    <label class="control-label" for="editGroupDescription"><?=lang('pay.description');?>: </label>
                                    <textarea name="group_description" id="editGroupDescription" class="form-control input-sm" style="resize:none"></textarea>
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit" value="<?=lang('lang.save');?>" class="btn btn-sm btn-info review-btn" data-toggle="modal" />
                                <span class="btn btn-sm btn-default edit_dispatch_group_cancel_btn" data-toggle="modal" /><?=lang('lang.cancel');?></span>
                            </div>
                        </div>
                    </form>
                </div>
                <hr/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <br>
                <div class="clearfix"></div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th><?=lang('player.grpname');?></th>
                            <th><?=lang('player.grplvlcnt');?></th>
                            <th><?=lang('pay.description');?></th>
                            <th><?=lang('cms.createdon');?></th>
                            <th><?=lang('cms.updatedon');?></th>
                            <th><?=lang('lang.status');?></th>
                            <th><?=lang('lang.action');?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($data)) :?>
                    <?php foreach ($data as $datai) :?>
                    <tr>
                        <td><?=$datai['group_name'] == '' ? '<i class="text-muted">' . lang("lang.norecord") . '<i/>' : anchor(site_url('dispatch_account_management/getDispatchAccountLevelList/' . $datai['id']), lang($datai['group_name']));?></td>
                        <td><?=$datai['group_level_count'] == '' ? '<i class="text-muted">' . lang("lang.norecord") . '<i/>' : $datai['group_level_count']?></td>
                        <td><?=$datai['group_description'] == '' ? '<i class="text-muted">' . lang("lang.norecord") . '<i/>' : $datai['group_description']?></td>
                        <td><?=$datai['created_at'] == '' ? '<i class="text-muted">' . lang("lang.norecord") . '<i/>' : $datai['created_at']?></td>
                        <td><?=$datai['updated_at'] == '' ? '<i class="text-muted">' . lang("lang.norecord") . '<i/>' : $datai['updated_at']?></td>
                        <td><?=$datai['status'] == '' ? '<i class="text-muted">' . lang("cms.nodailymaxwithdrawal") . '<i/>' : $datai['status']?></td>
                        <td>
                            <div class="actionVipGroup">
                                <span class="glyphicon glyphicon-edit edit_dispatch_group_btn" data-toggle="tooltip" title="<?=lang('lang.edit');?>" onclick="DispatchAccountManagementProcess.getDispatchAccountGroupDetails(<?=$datai['id']?>,<?= $this->language_function->getCurrentLanguage(); ?>)" data-placement="top">
                                </span>
                                <?php if ($datai['id'] != 1) :?>
                                <a class = "delete-dispatch-account-group" href="<?=site_url('dispatch_account_management/fakeDeleteDispatchAccountGroup/' . $datai['id'])?>" onClick='return confirm("<?=lang('sys.gd4');?>")'>
                                    <span data-toggle="tooltip" title="<?=lang('lang.delete');?>" class="glyphicon glyphicon-trash" data-placement="top"></span>
                                </a>
                                <?php endif;?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach;?>
                    <?php endif;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>
<script>
    $(document).ready(function() {
        DispatchAccountManagementProcess.initializeGroupList();
    });
    if($('#group_level_count').val() == ''){
            $('#group_level_count').val('1');
        }
    if($('#level_observation_period').val() == ''){
            $('#level_observation_period').val('0');
        }
    if($('#level_single_max_deposit').val() == ''){
                $('#level_single_max_deposit').val('0');
    }
    if($('#level_total_deposit').val() == ''){
        $('#level_total_deposit').val('0');
    }
    if($('#level_deposit_count').val() == ''){
        $('#level_deposit_count').val('0');
    }
    if($('#level_total_withdraw').val() == ''){
        $('#level_total_withdraw').val('0');
    }
    if($('#level_withdraw_count').val() == ''){
        $('#level_withdraw_count').val('0');
    }
</script>