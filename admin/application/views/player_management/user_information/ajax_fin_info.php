<div role="tabpanel" class="tab-pane active" id="finInfo">
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?php if ($this->permissions->checkPermissions('add_player_bank_info')): ?>
                    <a href="#" onclick="modal('/player_management/addPlayerBankInfo/<?=$player['playerId']?>/0','<?=lang('player.ui57')?>')" class="btn btn-scooter btn-xs">
                        <i class="input-xs glyphicon glyphicon-plus"></i> <?=lang('player.ui57')?>
                    </a>
                    <a href="#" onclick="modal('/player_management/addPlayerBankInfo/<?=$player['playerId']?>/1','<?=lang('player.ui58')?>')" class="btn btn-portage btn-xs">
                        <i class="input-xs glyphicon glyphicon-plus"></i> <?=lang('player.ui58')?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-md-12">
            <label><?=lang('player.ui34')?>: </label>
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="bankInfoDepositTable" style="margin: 0px 0 0 0; width: 100%;">
                    <thead>
                        <th><?=lang('player.ui35')?></th>
                        <th><?=lang('player.ui36')?></th>
                        <th><?=lang('player.ui37')?></th>
                        <th><?=lang('Province')?></th>
                        <th><?=lang('City')?></th>
                        <th><?=lang('Branch')?></th>
                        <th><?=lang('player.ui38')?></th>
                        <th><?=lang('Verify Status')?></th>
                        <th><?=lang('lang.status')?></th>
                        <th><?=lang('lang.action')?></th>
                    </thead>
                    <tbody>
                    <?php
                    if (!empty($deposit_bankdetails)) {
                        $default = false;
                        if (!empty($deposit_bankdetails)) {
                            $default = $this->player_manager->checkIfValueExists($deposit_bankdetails, 'isDefault', '1');
                        }

                        foreach ($deposit_bankdetails as $key => $value) { ?>
                            <tr>
                                <td><?=lang($value['bankName'])?></td>
                                <td><?=$value['bankAccountFullName']?></td>
                                <td><?=$value['bankAccountNumber']?></td>
                                <td><?=$value['province']?></td>
                                <td><?=$value['city']?></td>
                                <td><?=$value['branch']?></td>
                                <td><?=$value['bankAddress'] == '' ? lang('lang.norecord') : $value['bankAddress']?></td>
                                <td>
                                    <?php if($value['verified'] == 0 && $this->permissions->checkPermissions('set_financial_account_to_verified')):?>
                                        <a onclick="verifyFinancialAccount('<?=$value['playerBankDetailsId']?>', '<?=$player['playerId']?>')">
                                            <?=lang('Set to Verified')?>
                                        </a>
                                    <?php elseif($value['verified'] == 0):?>
                                        <?=lang('Unverified')?>
                                    <?php elseif($value['verified'] == 1):?>
                                        <span class="text-success"><b><?=lang('Verified')?></b></span>
                                    <?php endif;?>
                                </td>
                                <td><?=($value['status'] == 0) ? lang('lang.active') : lang('Blocked')?></td>
                                <td>
                                    <?php if ($value['isDefault'] == 0 && $default == false && $value['status'] == 0) {?>
                                        <a
                                            class="btn btn-portage btn-xs m-b-5 disabled-deposit-btn"
                                            href="<?=site_url('player_management/playerBankInfoSetDefault/' . $value['playerBankDetailsId'] . '/1/' . $player['playerId'])?>"
                                            <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?> onclick="return disabledBankDefaultBtn(<?=$value['dwBank']?>);"
                                        ><?=lang('player.ui55')?></a>
                                    <?php } elseif ($value['isDefault'] == 1) {?>
                                        <a
                                            class="btn btn-danger btn-xs m-b-5"
                                            href="<?=site_url('player_management/playerBankInfoSetDefault/' . $value['playerBankDetailsId'] . '/0/' . $player['playerId'])?>"
                                            <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                        ><?=lang('player.ui56')?></a>
                                    <?php } ?>

                                    <?php if ($this->permissions->checkPermissions('edit_player_bank_info')):?>
                                        <a
                                            class="btn btn-scooter btn-xs m-b-5"
                                            href="javascript:void(0);"
                                            onclick="modal('/player_management/editPlayerBankInfo/<?=$value['playerBankDetailsId']?>','<?=lang('lang.edit') . ' ' . lang('player.ui07')?>')"
                                            <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                        ><?=lang('lang.edit')?></a>
                                    <?php endif; ?>

                                    <?php if ($this->permissions->checkPermissions('enable_disable_player_bank_info')):?>
                                        <?php if ($value['status'] == 0) {?>
                                            <a
                                                class="btn btn-danger btn-xs m-b-5"
                                                href="<?=site_url('player_management/playerBankInfoChangeStatus/' . $value['playerBankDetailsId'] . '/1/' . $player['playerId'])?>"
                                                <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                            ><?=lang('lang.deactivate')?></a>
                                        <?php } else {?>
                                            <a
                                                class="btn btn-portage btn-xs m-b-5"
                                                href="<?=site_url('player_management/playerBankInfoChangeStatus/' . $value['playerBankDetailsId'] . '/0/' . $player['playerId'])?>"
                                                <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                            ><?=lang('lang.activate')?></a>
                                        <?php } ?>
                                    <?php endif; ?>

                                    <?php if ($this->permissions->checkPermissions('delete_player_bank_info')):?>
                                        <a
                                            class="btn btn-danger btn-xs m-b-5"
                                            href="#"
                                            onclick="deletePlayerBankInfo(<?=$value['playerBankDetailsId']?>, '<?=lang($value['bankName'])?>', '<?=$value['playerId']?>');"
                                            <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                        ><?=lang('lang.delete')?></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <label><?=lang('player.ui39')?>: </label>
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="bankInfoWithdrawalTable" style="margin: 0px 0 0 0; width: 100%;">
                    <thead>
                        <th><?=lang('player.ui35')?></th>
                        <th><?=lang('player.ui36')?></th>
                        <th><?=lang('player.ui37')?></th>
                        <th><?=lang('Province')?></th>
                        <th><?=lang('City')?></th>
                        <th><?=lang('Branch')?></th>
                        <th><?=lang('player.ui38')?></th>
                        <th><?=lang('Verify Status')?></th>
                        <th><?=lang('lang.status')?></th>
                        <th><?=lang('lang.action')?></th>
                    </thead>
                    <tbody>
                    <?php
                    if (!empty($withdrawal_bankdetails)) {
                        $default = false;
                        if (!empty($withdrawal_bankdetails)) {
                            $default = $this->player_manager->checkIfValueExists($withdrawal_bankdetails, 'isDefault', '1');
                        }

                        foreach ($withdrawal_bankdetails as $key => $value) {?>
                            <tr>
                                <td><?=lang($value['bankName'])?></td>
                                <td><?=$value['bankAccountFullName']?></td>
                                <td><?=$value['bankAccountNumber']?></td>
                                <td><?=$value['province']?></td>
                                <td><?=$value['city']?></td>
                                <td><?=$value['branch']?></td>
                                <td><?=$value['bankAddress'] == '' ? lang('lang.norecord') : $value['bankAddress']?></td>
                                <td>
                                    <?php if($value['verified'] == 0 && $this->permissions->checkPermissions('set_financial_account_to_verified')):?>
                                        <a onclick="verifyFinancialAccount('<?=$value['playerBankDetailsId']?>', '<?=$player['playerId']?>')">
                                            <?=lang('Set to Verified')?>
                                        </a>
                                    <?php elseif($value['verified'] == 0):?>
                                        <?=lang('Unverified')?>
                                    <?php elseif($value['verified'] == 1):?>
                                        <span class="text-success"><b><?=lang('Verified')?></b></span>
                                    <?php endif;?>
                                </td>
                                <td><?=($value['status'] == 0) ? lang('lang.active') : lang('Blocked')?></td>
                                <td>
                                    <?php if ($value['isDefault'] == 0 && $default == false) {?>
                                        <a
                                            class="btn btn-portage btn-xs m-b-5 disabled-withdrawal-btn"
                                            href="<?=site_url('player_management/playerBankInfoSetDefault/' . $value['playerBankDetailsId'] . '/1/' . $player['playerId'] . '/1')?>"
                                            <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?> onclick="return disabledBankDefaultBtn(<?=$value['dwBank']?>);"
                                        ><?=lang('player.ui55')?></a>
                                    <?php } elseif ($value['isDefault'] == 1) {?>
                                        <a
                                            class="btn btn-danger btn-xs m-b-5"
                                            href="<?=site_url('player_management/playerBankInfoSetDefault/' . $value['playerBankDetailsId'] . '/0/' . $player['playerId'] . '/1')?>"
                                            <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                        ><?=lang('player.ui56')?></a>
                                    <?php } ?>

                                    <?php if ($this->permissions->checkPermissions('edit_player_bank_info')):?>
                                        <a
                                            class="btn btn-scooter btn-xs m-b-5"
                                            href="javascript:void(0);"
                                            onclick="modal('/player_management/editPlayerBankInfo/<?=$value['playerBankDetailsId']?>','<?=lang('lang.edit') . ' ' . lang('player.ui07')?>')"
                                            <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                        ><?=lang('lang.edit')?></a>
                                    <?php endif; ?>

                                    <?php if ($this->permissions->checkPermissions('enable_disable_player_bank_info')):?>
                                        <?php if ($value['status'] == 0) {?>
                                            <a
                                                class="btn btn-danger btn-xs m-b-5"
                                                href="<?=site_url('player_management/playerBankInfoChangeStatus/' . $value['playerBankDetailsId'] . '/1/' . $player['playerId'])?>"
                                                <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                            ><?=lang('lang.deactivate')?></a>
                                        <?php } else {?>
                                            <a
                                                class="btn btn-portage btn-xs m-b-5"
                                                href="<?=site_url('player_management/playerBankInfoChangeStatus/' . $value['playerBankDetailsId'] . '/0/' . $player['playerId'])?>">
                                                <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                                <?=lang('lang.activate')?></a>
                                        <?php } ?>
                                    <?php endif; ?>

                                    <?php if ($this->permissions->checkPermissions('delete_player_bank_info')):?>
                                        <a
                                            class="btn btn-danger btn-xs m-b-5"
                                            href="#"
                                            onclick="deletePlayerBankInfo(<?=$value['playerBankDetailsId']?>, '<?=lang($value['bankName'])?>', '<?=$value['playerId']?>');"
                                            <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                        ><?=lang('lang.delete')?></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
    var title = "<?=lang('userinfo.tab05');?>";
    function refresh_fin_info() {
        changeUserInfoTab(5);
        $('#simpleModal').modal('hide');
    }

    $(document).ready(function(){
        $('#bankInfoDepositTable').DataTable({
            dom:"<'panel-body' <'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [{
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ]
            }],
            responsive: {
                details: { type: 'column' }
            },
            order: [ 0, 'asc' ]
        });

        $('#bankInfoWithdrawalTable').DataTable({
            dom:"<'panel-body' <'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [{
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ]
            }],
            responsive: {
                details: { type: 'column' }
            },
            order: [ 0, 'asc' ]
        });
    });


    function verifyFinancialAccount(bank_details_id, player_id){
        if(confirm('<?=lang('Set Financial Account to Verified?')?>')){
            window.location = base_url + "player_management/playerBankInfoSetToVerified/" + bank_details_id + "/" + player_id;
        }
    }

    function disabledBankDefaultBtn(dwBank){
        //dwBank = 0 deposit
        //dwBank = 1 withdrawal
        if(dwBank == '0'){
            $('.disabled-deposit-btn').addClass('disabled');
        }else if(dwBank == '1'){
            $('.disabled-withdrawal-btn').addClass('disabled');
        }
        return true;
    }

    function deletePlayerBankInfo(bank_details_id, bankname, player_id) {
        if (confirm("<?=lang('sys.gd4')?>" + bankname + '?')) {
            window.location = base_url + "player_management/deletePlayerBankInfo/" + bank_details_id + "/" + player_id;
        }
    }
</script>



