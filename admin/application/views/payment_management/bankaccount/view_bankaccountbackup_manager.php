<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> <b>(<?= ucwords($bankName); ?>)</b> <?= lang('pay.backupman'); ?> </h4>
        <a href="<?= BASEURL . 'bankaccount_management/viewBankAccountManager' ?>" class="btn btn-primary btn-sm pull-right" id="add_news"><span class="glyphicon glyphicon-remove"></span></a>
        <div class="clearfix"></div>
    </div>
    <div class="panel panel-body" id="details_panel_body">
        <!-- add bank account -->
        <div class="row add_bankaccount_sec">
            <div class="col-md-12">
                <div class="well" style="overflow: auto">
                    <form action="<?= BASEURL . 'bankaccount_management/addBankAccountBackup' ?>" method="post" role="form">
                        <div class="row">
                            <div class="col-md-3">
                                <h6><label for=""><?= lang('pay.ui35'); ?>: </label></h6>
                                <input type="hidden" name="otcPaymentMethodId" class="form-control" id="otcPaymentMethodId" >
                                <input type="text" name="bankName" class="form-control input-sm" required>
                                <?php echo form_error('bankName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>

                            <div class="col-md-3">
                                <h6><label for=""><?= lang('pay.acctname'); ?>: </label></h6>
                                <input type="text" name="accountName" class="form-control input-sm" id="accountName" required>
                                <?php echo form_error('accountName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>

                            <div class="col-md-3">
                                <h6><label for=""><?= lang('pay.acctnumber'); ?>: </label></h6>
                                <input type="number" name="accountNumber" class="form-control input-sm" id="accountNumber" required>
                                <?php echo form_error('accountNumber', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>

                            <div class="col-md-3">
                                <h6><label for=""><?= lang('pay.branchname'); ?>: </label></h6>
                                <input type="text" name="branchName" class="form-control input-sm" required>
                                <?php echo form_error('branchName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                             <div class="col-md-6">
                                <h6><label for="dailyMaxDepositAmount"><?= lang('pay.dailymaxdepamt'); ?>: </label></h6>
                                <input type="number" maxlength="12" name="dailyMaxDepositAmount" class="form-control input-sm" id="editDailyMaxDepositAmount" required>
                                <?php echo form_error('dailyMaxDepositAmount', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-6">
                                <h6><label for="">Remarks: </label></h6>
                                <textarea name="description" id="description" class="form-control input-sm" cols="10" rows="2" style="max-height: 90px;" required></textarea>
                                <?php echo form_error('description', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-md-3">

                                    <input type="submit" value="<?= lang('lang.add'); ?>" class="btn btn-sm btn-primary review-btn" data-toggle="modal" />
                                    <span class="btn btn-sm btn-danger addbankaccount-cancel-btn" data-toggle="modal" /><?= lang('lang.cancel'); ?></span>
                            </div>
                        </div>
                    </form>
                </div>
                <hr/>
            </div>
        </div>

        <!-- edit bank account -->
        <div class="row edit_bankaccount_sec">
            <div class="col-md-12">
                <div class="well" style="overflow: auto">
                    <form action="<?= BASEURL . 'bankaccount_management/addBankAccount' ?>" method="post" role="form">
                        <div class="row">
                            <div class="col-md-4">
                                <h6><label for=""><?= lang('pay.bankname'); ?>: </label></h6>
                                <input type="hidden" name="bankAccountId" class="form-control" id="editBankAccountId" >
                                <input type="text" name="bankName" id="editBankName" class="form-control input-sm" required>
                                <?php //echo form_error('bankName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>

                            <div class="col-md-4">
                                <h6><label for=""><?= lang('pay.acctname'); ?>: </label></h6>
                                <input type="text" name="accountName" class="form-control input-sm" id="editBankAccountName" required>
                                <?php echo form_error('accountName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>

                            <div class="col-md-4">
                                <h6><label for=""><?= lang('pay.acctnumber'); ?>: </label></h6>
                                <input type="number" name="accountNumber" class="form-control input-sm" id="editBankAccountNumber" required>
                                <?php echo form_error('accountNumber', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>


                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-6">
                                <h6><label for=""><?= lang('pay.branchname'); ?>: </label></h6>
                                <input type="text" name="branchName" id="editBankAccountBranchName" class="form-control input-sm" required>
                                <?php echo form_error('branchName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-6">
                                <h6><label for="dailyMaxDepositAmount"><?= lang('pay.dailymaxdepamt'); ?>: </label></h6>
                                <input type="number" maxlength="12" name="dailyMaxDepositAmount" class="form-control input-sm" id="editDailyMaxDepositAmount" required>
                                <?php echo form_error('dailyMaxDepositAmount', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>

                            <br/><br/>

                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <h6><label class="control-label"><?= lang('pay.curbankacctplayerlev'); ?>:</label></h6>
                                    <i><span class='currentBankAccountPlayerLevelLimit'></span></i>
                                    <br/><br/>
                                    <?php echo form_multiselect('playerLevels[]', $levels, $form['playerLevels'], 'id="playerLevels" class="form-control chosen-select" data-placeholder="<?= lang("pay.sellevedit"); ?>" data-untoggle="checkbox" data-target="#toggle-checkbox-2"') ?>
                                    <p class="help-block pull-left"><i>applicable levels for this bank account</i></p>
                                    <div class="checkbox pull-right" style="margin-top: 5px">
                                        <label><input type="checkbox" id="toggle-checkbox-2" data-toggle="checkbox" data-target="#playerLevels option"<?= isset($form['promoLevels']) && sizeof($form['promoLevels']) == sizeof($levels) ? 'checked' : '' ?>> <?= lang('lang.selectall'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <h6><label for=""><?= lang('pay.description'); ?>: </label></h6>
                                <textarea name="description" id="editBankAccountDescription" class="form-control input-sm" cols="10" rows="3" style="max-height: 90px;" required></textarea>
                                <?php echo form_error('description', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                     <br/>
                                    <input type="submit" value="<?= lang('lang.save'); ?>" class="btn btn-sm btn-primary review-btn" data-toggle="modal" />
                                    <span class="btn btn-sm btn-danger editbankaccount-cancel-btn" data-toggle="modal" /><?= lang('lang.cancel'); ?></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form action="<?= BASEURL . 'bankaccount_management/deleteSelectedBankAccount'?>" method="post" role="form">
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="glyphicon glyphicon-trash"></i> <?= lang('cms.deletesel'); ?>
                    </button>
                    <a href="#" class="btn btn-sm btn-primary" id="add_bankaccount"><span id="addBankAccountGlyhicon" class="glyphicon glyphicon-plus-sign"></span> <?= lang('pay.addbackup'); ?></a>

                    <hr/>
                    <div id="backupbankaccount_table">
                        <table class="table table-striped table-hover table-condensed" id="my_table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                                    <th><?= lang('pay.bankacctorder'); ?></th>
                                    <th><?= lang('pay.bankname'); ?></th>
                                    <th><?= lang('pay.acctname'); ?></th>
                                    <th><?= lang('pay.acctnum'); ?></th>
                                    <th><?= lang('pay.branchname'); ?></th>
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
                                                    <td><input type="checkbox" class="checkWhite" id="<?= $row['otcPaymentMethodId']?>" name="bankaccount[]" value="<?= $row['otcPaymentMethodId']?>" onclick="uncheckAll(this.id)"/></td>
                                                    <!-- <td><?= $row['accountOrder'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $row['accountOrder'] ?></td> -->
                                                    <td><?= $cnt ?>
                                                    <td><?= $row['bankName'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $row['bankName'] ?></td>
                                                    <td><?= $row['accountName'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $row['accountName'] ?></td>
                                                    <td><?= $row['accountNumber'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $row['accountNumber'] ?></td>
                                                    <td><?= $row['branchName'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $row['branchName'] ?></td>
                                                    <td><?= $row['description'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $row['description'] ?></td>
                                                    <td><?= $row['createdOn'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $row['createdOn'] ?></td>
                                                    <td><?= $row['createdBy'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $row['createdBy'] ?></td>
                                                    <td><?= $row['updatedOn'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $row['updatedOn'] ?></td>
                                                    <td><?= $row['updatedBy'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $row['updatedBy'] ?></td>
                                                    <td><?= $row['status'] ?></td>

                                                    <td>
                                                        <div class="actionVipGroup">
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

                                                                <span class="glyphicon glyphicon-edit editBankAccountBtn" data-toggle="tooltip" title="<?= lang('lang.edit'); ?>" onclick="BankAccountManagementProcess.getBankAccountDetails(<?= $row['otcPaymentMethodId'] ?>)" data-placement="top">
                                                                </span>

                                                                <a href="<?= BASEURL . 'bankaccount_management/addBankAccountBackup/'.$row['otcPaymentMethodId'] ?>">
                                                                    <span data-toggle="tooltip" title="<?= lang('pay.addbackup'); ?>" class="glyphicon glyphicon-plus-sign" data-placement="top">
                                                                    </span>
                                                                </a>

                                                                <a href="<?= BASEURL . 'bankaccount_management/deleteBankAccountItem/'.$row['otcPaymentMethodId'] ?>">
                                                                    <span data-toggle="tooltip" title="<?= lang('lang.delete'); ?>" class="glyphicon glyphicon-trash" data-placement="top">
                                                                    </span>
                                                                </a>
                                                            </div>

                                                    </td>
                                                </tr>
                                <?php
                                            $cnt++;
                                        }
                                    }
                                    else{ ?>
                                        <tr>
                                            <td colspan="13" style="text-align:center"><?= lang('lang.norec'); ?>
                                            </td>
                                        </tr>
                                <?php   }
                                ?>
                            </tbody>
                        </table>
                        <div class="col-md-12 col-offset-0">
                            <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
