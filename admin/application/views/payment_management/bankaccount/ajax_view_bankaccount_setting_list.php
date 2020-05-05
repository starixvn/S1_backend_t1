<table class="table table-striped table-hover table-condensed" id="my_table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                                    <th><?= lang('pay.bankacctorder'); ?></th>
                                    <th><?= lang('pay.bankname'); ?></th>
                                    <th><?= lang('pay.acctname'); ?></th>
                                    <th><?= lang('pay.acctnum'); ?></th>
                                    <th><?= lang('pay.branchname'); ?></th>
                                    <th><?= lang('pay.playerlev'); ?></th>
                                    <th><?= lang('pay.dailymaxdepamt'); ?></th>
                                    <th><?= lang('pay.remark'); ?></th>
                                    <th><?= lang('cms.createdon'); ?></th>
                                    <th><?= lang('cms.createdby'); ?></th>
                                    <th><?= lang('cms.updatedon'); ?></th>
                                    <th><?= lang('cms.updatedby'); ?></th>
                                    <th><?= lang('lang.status'); ?></th>
                                    <th><?= lang('lang.action'); ?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php 
                                    if(!empty($banks)) {
                                        $cnt=1;
                                        foreach($banks as $row) {
                                ?>  
                                                <tr>
                                                    <td>
                                                        <?php //if($row['otcPaymentMethodId'] != 1){ ?>
                                                            <input type="checkbox" class="checkWhite" id="<?= $row['otcPaymentMethodId']?>" name="bankaccount[]" value="<?= $row['otcPaymentMethodId']?>" onclick="uncheckAll(this.id)"/>
                                                        <?php //d}  ?>
                                                    </td>
                                                    <!-- <td><?= $row['accountOrder'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['accountOrder'] ?></td> -->
                                                    <td><?= $cnt ?>
                                                    <td><?= $row['bankName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['bankName'] ?></td>
                                                    <td><?= $row['accountName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['accountName'] ?></td>
                                                    <td><?= $row['accountNumber'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['accountNumber'] ?></td>
                                                    <td><?= $row['branchName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['branchName'] ?></td>
                                                    <td>
                                                        <?php
                                                            foreach ($row['bankAccountPlayerLevelLimit'] as $key) {
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
                                                        <div class="actionBankAccountGroup">
                                                                <?php if($row['status'] == 'active'){ ?>
                                                                    <a href="<?= BASEURL . 'bankaccount_management/activateBankAccount/'.$row['otcPaymentMethodId'].'/'.'inactive' ?>">
                                                                    <span data-toggle="tooltip" title="<?= lang('lang.deactivate'); ?>" class="glyphicon glyphicon-ok-sign" data-placement="top">
                                                                    </span>
                                                                </a>
                                                                <?php } else{ ?>
                                                                    <a href="<?= BASEURL . 'bankaccount_management/activateBankAccount/'.$row['otcPaymentMethodId'].'/'.'active' ?>">
                                                                    <span data-toggle="tooltip" title="<?= lang('lang.activate'); ?>" class="glyphicon glyphicon-remove-circle" data-placement="top">
                                                                    </span>
                                                                    </a>
                                                                <?php }  ?>

                                                                <!-- <a href="<?= BASEURL . 'bankaccount_management/bankAccountBackupManager/'.$row['otcPaymentMethodId'].'/'.$row['bankName'] ?>">
                                                                    <span data-toggle="tooltip" title="Add Backup" class="glyphicon glyphicon-hdd" data-toggle="tooltip" data-placement="top">
                                                                    </span>
                                                                </a> -->

                                                                <span class="glyphicon glyphicon-edit editBankAccountBtn" data-toggle="tooltip" title="<?= lang('lang.edit'); ?>" onclick="BankAccountManagementProcess.getBankAccountDetails(<?= $row['otcPaymentMethodId'] ?>)" data-placement="top">
                                                                </span>
                                                                <?php //if($row['otcPaymentMethodId'] != 1){ ?>
                                                                    <a href="<?= BASEURL . 'bankaccount_management/deleteBankAccountItem/'.$row['otcPaymentMethodId'] ?>">
                                                                        <span data-toggle="tooltip" title="<?= lang('lang.delete'); ?>" class="glyphicon glyphicon-trash" data-placement="top">
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
                                            <td colspan="15" style="text-align:center"><?= lang('lang.norec'); ?>
                                            </td>
                                        </tr>
                                <?php   }
                                ?>
                            </tbody>
                        </table>
                        <div class="col-md-12 col-offset-0">
                            <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
                        </div>