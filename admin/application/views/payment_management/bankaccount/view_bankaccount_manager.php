<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-coin-dollar"></i> <?= lang('pay.bankacctman'); ?>
            <a href="#" class="btn btn-default pull-right" id="add_bankaccount">
                <span id="addBankAccountGlyhicon" class="glyphicon glyphicon-plus-sign"></span>
            </a>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <!-- add bank account -->
        <div class="row add_bankaccount_sec">
            <div class="col-md-12">
                <div class="well" style="overflow: auto">
                    <form class="form-horizontal" id="form_add" action="<?= BASEURL . 'bankaccount_management/addBankAccount' ?>" method="post" role="form">
                        <div class="form-group">
                            <div class="col-md-2">
                                <label class="control-label" for="" style="font-size:11px;"><?= lang('pay.bankname'); ?>:* </label>
                                <input type="hidden" name="otcPaymentMethodId" class="form-control" id="otcPaymentMethodId" >
                                <input type="text" name="bankName" class="form-control input-sm" required>
                                <?php echo form_error('bankName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="" style="font-size:11px;"><?= lang('pay.acctname'); ?>:* </label>
                                <input type="text" name="accountName" class="form-control input-sm" id="accountName" required>
                                <?php echo form_error('accountName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="" style="font-size:11px;"><?= lang('pay.acctnumber'); ?>:* </label>
                                <input type="number" name="accountNumber" class="form-control input-sm" id="accountNumber" required>
                                <?php echo form_error('accountNumber', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="" style="font-size:11px;"><?= lang('pay.branchname'); ?>:* </label>
                                <input type="text" name="branchName" class="form-control input-sm" required>
                                <?php echo form_error('branchName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="" style="font-size:11px;"><?= lang('pay.dailymaxdepamt'); ?>:* </label>
                                <input type="number" maxlength="12" name="dailyMaxDepositAmount" class="form-control input-sm" id="dailyMaxDepositAmount" required>
                                <?php echo form_error('dailyMaxDepositAmount', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="" style="font-size:11px;"><?= lang('con.bnk10'); ?>:* </label>
                                <input type="number" maxlength="12" name="transactionFee" class="form-control input-sm" id="transactionFee" required min='1'>
                                <?php echo form_error('transactionFee', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-12">
                                <label class="control-label" class="control-label" style="font-size:12px;"><?= lang('pay.playerlev'); ?>:*</label>
                                <?php echo form_multiselect('playerLevels[]', $levels, $form['playerLevels'], 'id="playerLevels" class="form-control input-sm chosen-select playerLevels" data-placeholder="'.lang("cms.selectnewlevel").'" data-untoggle="checkbox" data-target="#toggle-checkbox-2"') ?>
                                <p class="help-block playerLevels-help-block pull-left"><i style="font-size:12px;color:#919191;"><?= lang('pay.applevbankacct'); ?></i></p>
                                <div class="checkbox pull-right" style="margin-top: 5px">
                                    <label><input type="checkbox" id="toggle-checkbox-2" data-toggle="checkbox" data-target="#playerLevels option"<?= isset($form['promoLevels']) && sizeof($form['promoLevels']) == sizeof($levels) ? 'checked' : '' ?>> <?= lang('lang.selectall'); ?></label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="control-label" for=""><?= lang('player.upay05'); ?>:* </label>
                                <textarea name="description" id="description" class="form-control input-sm" cols="10" rows="3" style="max-height: 90px;" required></textarea>
                                <?php echo form_error('description', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                        </div>
                        <center>
                            <input type="submit" value="<?= lang('lang.add'); ?>" class="btn btn-sm btn-info review-btn custom-btn-size" data-toggle="modal" />
                            <span class="btn btn-sm btn-default addbankaccount-cancel-btn" data-toggle="modal" /><?= lang('lang.cancel'); ?></span>
                        </center>
                    </form>
                </div>
                <hr/>
            </div>
        </div>

        <!-- edit bank account -->
        <div class="row edit_bankaccount_sec">
            <div class="col-md-12">
                <div class="well" style="overflow:auto">
                    <form class="form-horizontal" id="form_edit" action="<?= BASEURL . 'bankaccount_management/addBankAccount' ?>" method="post" role="form">
                        <div class="form-group">
                            <div class="col-md-2">
                                <label class="control-label" for=""><?= lang('pay.bankname'); ?>: </label>
                                <input type="hidden" name="bankAccountId" class="form-control" id="editBankAccountId" >
                                <input type="text" name="bankName" id="editBankName" class="form-control input-sm" required>
                                <?php echo form_error('bankName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for=""><?= lang('pay.acctname'); ?>: </label>
                                <input type="text" name="accountName" class="form-control input-sm" id="editBankAccountName" required>
                                <?php echo form_error('accountName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for=""><?= lang('pay.acctnumber'); ?>: </label>
                                <input type="number" name="accountNumber" class="form-control input-sm" id="editBankAccountNumber" required>
                                <?php echo form_error('accountNumber', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for=""><?= lang('pay.branchname'); ?>: </label>
                                <input type="text" name="branchName" id="editBankAccountBranchName" class="form-control input-sm" required>
                                <?php echo form_error('branchName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="dailyMaxDepositAmount"><?= lang('pay.dailymaxdepamt'); ?>: </label>
                                <input type="number" maxlength="12" name="dailyMaxDepositAmount" class="form-control input-sm" id="editDailyMaxDepositAmount" required>
                                <?php echo form_error('dailyMaxDepositAmount', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="" style="font-size:11px;"><?= lang('con.bnk10'); ?>:* </label>
                                <input type="number" maxlength="12" name="transactionFee" class="form-control input-sm" id="editTransactionFee" required min='1'>
                                <?php echo form_error('transactionFee', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-12">
                                <label class="control-label" class="control-label"><?= lang('pay.curbankacctplayerlev'); ?>:</label>
                                <?php echo form_multiselect('playerLevels[]', $levels, $form['playerLevels'], 'id="playerLevels" class="form-control chosen-select playerLevels" data-placeholder="'.lang("pay.sellevedit").'" data-untoggle="checkbox" data-target="#toggle-checkbox-2"') ?>
                                <p class="help-block playerLevels-help-block pull-left"><i style="font-size:12px;color:#919191;">applicable levels for this bank account</i></p>
                                <div class="checkbox pull-right" style="margin-top: 5px">
                                    <label><input type="checkbox" id="toggle-checkbox-2" data-toggle="checkbox" data-target="#playerLevels option"<?= isset($form['promoLevels']) && sizeof($form['promoLevels']) == sizeof($levels) ? 'checked' : '' ?>> <?= lang('lang.selectall'); ?></label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="control-label" for=""><?= lang('pay.remark'); ?>: </label>
                                <textarea name="description" id="editBankAccountDescription" class="form-control input-sm" cols="10" rows="3" style="max-height: 90px;" required></textarea>
                                <?php echo form_error('description', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                        </div>
                        <center>
                            <input type="submit" value="<?= lang('lang.save'); ?>" class="btn btn-sm btn-info review-btn custom-btn-size" data-toggle="modal" />
                            <span class="btn btn-sm btn-default editbankaccount-cancel-btn custom-btn-size" data-toggle="modal" /><?= lang('lang.cancel'); ?></span>
                        </center>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
               <!-- <form class="navbar-search pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
                            Sort By <span class="caret"></span>
                        </button>

                        <ul class="dropdown-menu" role="menu">
                            <li><a onclick="sortBankAccount('bankName')"><?= lang('pay.bankname'); ?></a></li>
                            <li><a onclick="sortBankAccount('branchName')"><?= lang('pay.branchname'); ?></a></li>
                            <li><a onclick="sortBankAccount('updatedOn')"><?= lang('cms.createdon'); ?></a></li>
                        </ul>
                    </div>
                    <input type="text" class="search-query" placeholder="<?= lang('lang.search'); ?>" name="search" id="search">
                    <input type="button" class="btn btn-sm" value="<?= lang('lang.go'); ?>" onclick="searchBankAccount();">
                </form> -->

                <form action="<?= BASEURL . 'bankaccount_management/deleteSelectedBankAccount'?>" method="post" role="form">
                    <div id="bankaccount_table">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-condensed" id="my_table" style="margin: 0px 0 0 0; width: 100%;">
                                <div class="btn-action">
                                    <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="<?= lang('cms.deletesel'); ?>">
                                        <i class="glyphicon glyphicon-trash" style="color:white;"></i>
                                    </button>&nbsp;
                                    <?php if($export_report_permission){ ?>
                                        <a href="<?= BASEURL . 'bankaccount_management/exportToExcel' ?>" class="btn btn-sm btn-success btn-sm" data-toggle="tooltip" title="<?= lang('lang.export'); ?>" data-placement="top">
                                            <i class="glyphicon glyphicon-share"></i>
                                        </a>
                                    <?php } ?>
                                </div>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                                        <th><?= lang('lang.action'); ?></th>
                                        <th><?= lang('pay.bankacctorder'); ?></th>
                                        <th><?= lang('pay.bankname'); ?></th>
                                        <th><?= lang('pay.acctname'); ?></th>
                                        <th><?= lang('pay.acctnumber'); ?></th>
                                        <th><?= lang('pay.branchname'); ?></th>
                                        <th><?= lang('pay.playerlev'); ?></th>
                                        <th><?= lang('pay.dailymaxdepamt'); ?></th>
                                        <th><?= lang('con.bnk10'); ?></th>
                                        <th><?= lang('player.upay05'); ?></th>
                                        <th><?= lang('player.upay05'); ?></th>
                                        <th><?= lang('cms.createdon'); ?></th>
                                        <th><?= lang('cms.createdby'); ?></th>
                                        <th><?= lang('cms.updatedon'); ?></th>
                                        <th><?= lang('cms.updatedby'); ?></th>
                                        <th><?= lang('lang.status'); ?></th>
                                        <th><?= lang('pay.remark'); ?></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                        if(!empty($banks)) {
                                            $cnt=1;
                                            foreach($banks as $row) {
                                    ?>
                                                    <tr>
                                                        <td></td>
                                                        <td>
                                                            <?php //if($row['otcPaymentMethodId'] != 1){ ?>
                                                                <input type="checkbox" class="checkWhite" id="<?= $row['otcPaymentMethodId']?>" name="bankaccount[]" value="<?= $row['otcPaymentMethodId']?>" onclick="uncheckAll(this.id)"/>
                                                            <?php //d}  ?>
                                                        </td>
                                                        <td>
                                                            <div class="actionBankAccountGroup">
                                                                    <!-- <a href="<?= BASEURL . 'bankaccount_management/activateBankAccount/'.$row['otcPaymentMethodId'].'/'.(($row['status'] == 'active') ? 'inactive' : 'active'); ?>">
                                                                        <span data-toggle="tooltip" title="<?= ($row['status'] == 'active') ? lang('lang.activate') : lang('lang.deactivate'); ?>" class="glyphicon glyphicon-ok-sign" data-placement="top">
                                                                        </span>
                                                                    </a> -->
                                                                    <span style="cursor:pointer;" class="glyphicon glyphicon-edit editBankAccountBtn" data-toggle="tooltip" title="<?= lang('lang.edit'); ?>" onclick="BankAccountManagementProcess.getBankAccountDetails(<?= $row['otcPaymentMethodId'] ?>)" data-placement="top"></span>
                                                                    <?php //if($row['otcPaymentMethodId'] != 1){ ?>
                                                                        <a href="<?= BASEURL . 'bankaccount_management/deleteBankAccountItem/'.$row['otcPaymentMethodId'] ?>">
                                                                            <span data-toggle="tooltip" title="<?= lang('lang.delete'); ?>" class="glyphicon glyphicon-trash" data-placement="top">
                                                                            </span>
                                                                        </a>
                                                                    <?php //}  ?>
                                                            </div>
                                                        </td>
                                                        <!-- <td><?= $row['accountOrder'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['accountOrder'] ?></td> -->
                                                        <td><?= $cnt ?>
                                                        <td><?= $row['bankName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['bankName'] ?></td>
                                                        <td><?= $row['accountName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['accountName'] ?></td>
                                                        <td><?= $row['accountNumber'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['accountNumber'] ?></td>
                                                        <td><?= $row['branchName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['branchName'] ?></td>
                                                        <td align="center">
                                                            <?php
                                                                $memberLevels = null;
                                                                foreach ($row['bankAccountPlayerLevelLimit'] as $key) {
                                                                    $memberLevels[] = $key['groupName'].$key['vipLevel'];
                                                                }
                                                            ?>
                                                            <button type="button" class="btn btn-xs btn-default" title="<?=implode(', ', $memberLevels)?>" data-toggle="tooltip">...</button>
                                                        </td>
                                                        <td><?= $row['dailyMaxDepositAmount'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['dailyMaxDepositAmount'] ?></td>
                                                        <td><?= $row['transactionFee'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['transactionFee'] ?></td>
                                                        <td><?= $row['totalDeposit'][0]['totalBankDeposit'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['totalDeposit'][0]['totalBankDeposit'] ?></td>
                                                        <td><?= $row['description'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['description'] ?></td>
                                                        <td><?= $row['createdOn'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['createdOn'] ?></td>
                                                        <td><?= $row['createdBy'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['createdBy'] ?></td>
                                                        <td><?= $row['updatedOn'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['updatedOn'] ?></td>
                                                        <td><?= $row['updatedBy'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['updatedBy'] ?></td>
                                                        <td style="text-transform:lowercase;"><?= $row['status'] ?></td>
                                                        <td><i class="help-block"><?= ($row['status'] == 'inactive') ? lang('pay.exceeded') : lang('pay.none'); ?><i/></td>
                                                    </tr>
                                    <?php
                                                $cnt++;
                                            }
                                        } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
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
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ],
            "dom": '<"top"fl>rt<"bottom"ip>',
            "fnDrawCallback": function(oSettings) {
                $('.btn-action').prependTo($('.top'));
            }
        });

        $('#form_add').submit( function (e) {
            var element = $('#form_add .playerLevels');
            if (element.val() == '' || element.val() == null) {
                element.closest('.col-md-12').addClass('has-error');
                $('#form_add .playerLevels-help-block').text('<?=sprintf(lang("gen.error.required"), lang("pay.playerlev"))?>');
                $('#form_add .chosen-choices').css('border-color', '#a94442');
                return false;
            } else {
                element.closest('.col-md-12').removeClass('has-error');
                $('#form_add .playerLevels-help-block').html('<i style="font-size:12px;color:#919191;"><?=lang('pay.applevbankacct');?></i>');
                $('#form_add .chosen-choices').css('border-color', '');
                return true;
            }
        });

        $("#form_add .playerLevels").change( function() {
            var element = $('#form_add .playerLevels');
            if (element.val() == '' || element.val() == null) {
                element.closest('.col-md-12').addClass('has-error');
                $('#form_add .playerLevels-help-block').text('<?=sprintf(lang("gen.error.required"), lang("pay.playerlev"))?>');
                $('#form_add .chosen-choices').css('border-color', '#a94442');
            } else {
                element.closest('.col-md-12').removeClass('has-error');
                $('#form_add .playerLevels-help-block').html('<i style="font-size:12px;color:#919191;"><?=lang('pay.applevbankacct');?></i>');
                $('#form_add .chosen-choices').css('border-color', '');
            }
        });

        $('#form_edit').submit( function (e) {
            var element = $('#form_edit .playerLevels');
            if (element.val() == '' || element.val() == null) {
                element.closest('.col-md-12').addClass('has-error');
                $('#form_edit .playerLevels-help-block').text('<?=sprintf(lang("gen.error.required"), lang("pay.playerlev"))?>');
                $('#form_edit .chosen-choices').css('border-color', '#a94442');
                return false;
            } else {
                element.closest('.col-md-12').removeClass('has-error');
                $('#form_edit .playerLevels-help-block').html('<i style="font-size:12px;color:#919191;"><?=lang('pay.applevbankacct');?></i>');
                $('#form_edit .chosen-choices').css('border-color', '');
                return true;
            }
        });

        $("#form_edit .playerLevels").change( function() {
            var element = $('#form_edit .playerLevels');
            if (element.val() == '' || element.val() == null) {
                element.closest('.col-md-12').addClass('has-error');
                $('#form_edit .playerLevels-help-block').text('<?=sprintf(lang("gen.error.required"), lang("pay.playerlev"))?>');
                $('#form_edit .chosen-choices').css('border-color', '#a94442');
            } else {
                element.closest('.col-md-12').removeClass('has-error');
                $('#form_edit .playerLevels-help-block').html('<i style="font-size:12px;color:#919191;"><?=lang('pay.applevbankacct');?></i>');
                $('#form_edit .chosen-choices').css('border-color', '');
            }
        });

    });
</script>
