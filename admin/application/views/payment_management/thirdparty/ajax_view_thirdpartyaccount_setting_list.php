<table class="table table-striped table-hover table-condensed" id="my_table">
    <thead>
        <tr>
            <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
            <th><?= lang("pay.bankacctorder"); ?></th>
            <th><?= lang("pay.thirdparpaytman"); ?></th>
            <th><?= lang("pay.acctname"); ?></th>
            <th><?= lang("pay.acct"); ?></th>
            <th><?= lang("pay.playerlevs"); ?></th>
            <th><?= lang("pay.dailymaxdepamt"); ?></th>
            <th><?= lang("pay.remark"); ?></th>
            <th><?= lang("pay.createdon"); ?></th>
            <th><?= lang("pay.createdby"); ?></th>
            <th><?= lang("pay.updatedon"); ?></th>
            <th><?= lang("pay.updatedby"); ?></th>
            <th><?= lang("lang.status"); ?></th>
            <th><?= lang("lang.action"); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php 
            if(!empty($thirdparty)) {
                $cnt=1;
                foreach($thirdparty as $row) {
        ?>  
                        <tr>
                            <td>
                                <?php //if($row['otcPaymentMethodId'] != 1){ ?>
                                    <input type="checkbox" class="checkWhite" id="<?= $row['thirdpartypaymentmethodaccountId']?>" name="thirdpartyaccount[]" value="<?= $row['thirdpartypaymentmethodaccountId']?>" onclick="uncheckAll(this.id)"/>
                                <?php //d}  ?>
                            </td>
                            <!-- <td><?= $row['accountOrder'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['accountOrder'] ?></td> -->
                            <td><?= $cnt ?>
                            <td><?= $row['thirdpartyName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['thirdpartyName'] ?></td>
                            <td><?= $row['thirdpartyAccountName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['thirdpartyAccountName'] ?></td>
                            <td><?= $row['thirdpartyAccount'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['thirdpartyAccount'] ?></td>
                            <td>
                                <?php
                                    foreach ($row['thirdPartyAccountPlayerLevelLimit'] as $key) {
                                         //echo $key['groupName'].''.$key['vipLevel'].'('.$key['vipLevelName'].')';
                                         echo $key['groupName'].''.$key['vipLevel'];
                                         echo ',';
                                     } 
                                 ?>
                            </td>
                            <td><?= $row['dailyMaxDepositAmount'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['dailyMaxDepositAmount'] ?></td>
                            <td><?= $row['description'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['description'] ?></td>
                            <td><?= $row['createdOn'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['createdOn'] ?></td>
                            <td><?= $row['createdBy'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['createdBy'] ?></td>
                            <td><?= $row['updatedOn'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['updatedOn'] ?></td>
                            <td><?= $row['updatedBy'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['updatedBy'] ?></td>
                            <td><?= $row['status'] ?></td>

                            <td>
                                <div class="actionThirdPartyAccountGroup">
                                        <?php if($row['status'] == 'active'){ ?>
                                            <a href="<?= BASEURL . 'thirdpartyaccount_management/activateThirdPartyAccount/'.$row['thirdpartypaymentmethodaccountId'].'/'.'inactive' ?>">
                                            <span data-toggle="tooltip" title="<?= lang("lang.deactivate"); ?>" class="glyphicon glyphicon-ok-sign" data-placement="top">
                                            </span>
                                        </a>
                                        <?php } else{ ?>
                                            <a href="<?= BASEURL . 'thirdpartyaccount_management/activateThirdPartyAccount/'.$row['thirdpartypaymentmethodaccountId'].'/'.'active' ?>">
                                            <span data-toggle="tooltip" title="<?= lang("lang.activate"); ?>" class="glyphicon glyphicon-remove-circle" data-placement="top">
                                            </span>
                                            </a>
                                        <?php }  ?>

                                        <!-- <a href="<?= BASEURL . 'thirdpartyaccount_management/thirdPartyAccountBackupManager/'.$row['thirdpartypaymentmethodaccountId'].'/'.$row['thirdPartyName'] ?>">
                                            <span data-toggle="tooltip" title="Add Backup" class="glyphicon glyphicon-hdd" data-toggle="tooltip" data-placement="top">
                                            </span>
                                        </a> -->

                                        <span class="glyphicon glyphicon-edit editThirdPartyAccountBtn" data-toggle="tooltip" title="<?= lang("lang.edit"); ?>" onclick="ThirdPartyManagementProcess.getThirdPartyAccountDetails(<?= $row['thirdpartypaymentmethodaccountId'] ?>)" data-placement="top">
                                        </span>
                                        <?php //if($row['otcPaymentMethodId'] != 1){ ?>
                                            <a href="<?= BASEURL . 'thirdpartyaccount_management/deleteThirdPartyAccountItem/'.$row['thirdpartypaymentmethodaccountId'] ?>">
                                                <span data-toggle="tooltip" title="<?= lang("lang.delete"); ?>" class="glyphicon glyphicon-trash" data-placement="top">
                                                </span>
                                            </a>
                                        <?php //}  ?>
                                </div>
                            </td>
                        </tr>
        <?php    
                    $cnt++;
                }
            }
            else{ ?>
                <tr>
                    <td colspan="15" style="text-align:center"><?= lang("lang.norec"); ?>
                    </td>
                </tr>
        <?php   }
        ?>
    </tbody>
</table>
<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>