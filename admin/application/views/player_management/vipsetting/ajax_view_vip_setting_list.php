<table class="table table-striped table-hover table-responsive" id="my_table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                                    <th><?= lang('player.grpname'); ?></th>
                                    <th><?= lang('player.grplvlcnt'); ?></th>
                                    <th><?= lang('pay.description'); ?></th>
                                    <th><?= lang('cms.createdon'); ?></th>
                                    <th><?= lang('cms.createdby'); ?></th>
                                    <th><?= lang('cms.updatedon'); ?></th>
                                    <th><?= lang('cms.updatedby'); ?></th>
                                    <th><?= lang('lang.status'); ?></th>
                                    <th><?= lang('lang.action'); ?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php   //var_dump($data);exit();
                                        if(!empty($data)) {
                                            foreach($data as $data) {
                                ?>
                                                <tr>
                                                    <td><input type="checkbox" class="checkWhite" id="<?= $data['vipSettingId']?>" name="vipgroup[]" value="<?= $data['vipSettingId']?>" onclick="uncheckAll(this.id)"/></td>
                                                    <td><?= $data['groupName'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : anchor(BASEURL . 'vipsetting_management/viewVipGroupRules/'.$data['vipSettingId'],  $data['groupName']);?></td>
                                                    <td><?= $data['groupLevelCount'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $data['groupLevelCount'] ?></td>
                                                    <td><?= $data['groupDescription'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $data['groupDescription'] ?></td>
                                                    <td><?= $data['createdOn'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $data['createdOn'] ?></td>
                                                    <td><?= $data['createdBy'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $data['createdBy'] ?></td>
                                                    <td><?= $data['updatedOn'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $data['updatedOn'] ?></td>
                                                    <td><?= $data['updatedBy'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $data['updatedBy'] ?></td>
                                                    <td><?= $data['status'] == '' ? '<i class="help-block">'. lang("cms.nodailymaxwithdrawal") .'<i/>' : $data['status'] ?></td>
                                                    <td>
                                                        <div class="actionVipGroup">
                                                            <?php if($data['status'] == 'active'){ ?>
                                                                <a href="<?= BASEURL . 'vipsetting_management/activateVIPGroup/'.$data['vipSettingId'].'/'.'inactive' ?>">
                                                                <span data-toggle="tooltip" title="<?= lang('lang.deactivate'); ?>" class="glyphicon glyphicon-ok-sign" data-placement="top">
                                                                </span>
                                                            </a>
                                                            <?php } else{ ?>
                                                                <a href="<?= BASEURL . 'vipsetting_management/activateVIPGroup/'.$data['vipSettingId'].'/'.'active' ?>">
                                                                <span data-toggle="tooltip" title="<?= lang('lang.activate'); ?>" class="glyphicon glyphicon-remove-circle" data-placement="top">
                                                                </span>
                                                                </a>
                                                            <?php }  ?>
                                                            
                                                            <span class="glyphicon glyphicon-edit editVipGroupSettingBtn" data-toggle="tooltip" title="<?= lang('lang.edit'); ?>" onclick="PlayerManagementProcess.getVIPGroupDetails(<?= $data['vipSettingId'] ?>)" data-placement="top">
                                                            </span>
                                                            
                                                            <a href="<?= BASEURL . 'vipsetting_management/deleteVIPGroup/'.$data['vipSettingId'] ?>">
                                                                <span data-toggle="tooltip" title="<?= lang('lang.delete'); ?>" class="glyphicon glyphicon-trash" data-placement="top">
                                                                </span>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                <?php       }
                                        } else {
                                 ?>

                                    <tr>
                                        <td colspan="11" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <div class="col-md-12 col-offset-0">
                            <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
                        </div>