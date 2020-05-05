<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-cog"></i> <?= lang('pay.thirdparacctman'); ?>
            <a href="#" class="btn btn-default pull-right" id="add_thirdpartyaccount">
                <span id="addThirdPartyAccountGlyhicon" class="glyphicon glyphicon-plus-sign"></span>
            </a>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <!-- add thirdparty account -->
        <div class="row add_thirdpartyaccount_sec">
            <div class="col-md-12">
                <div class="well" style="overflow: auto">
                    <form class="form-horizontal" id="form_add" action="<?= BASEURL . 'thirdpartyaccount_management/addThirdPartyAccount' ?>" method="post" role="form">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label class="control-label"><?= lang('pay.thirdparpaytman'); ?>: </label>
                                <input type="hidden" name="thirdPartyAccountId" class="form-control" id="thirdPartyAccountId" >
                                <input type="text" name="thirdPartyName" class="form-control input-sm" required>
                                <?php echo form_error('thirdPartyName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label"><?= lang('pay.merchacct'); ?>: </label>
                                <input type="text" name="thirdPartyAccountName" class="form-control input-sm" id="thirdPartyAccountName" required>
                                <?php echo form_error('thirdPartyAccountName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label"><?= lang('con.bnk10'); ?>: </label>
                                <input type="number" name="transactionFee" class="form-control input-sm number_only" id="thirdPartyAccount" required>
                                <?php echo form_error('transactionFee', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>

                            <div class="col-md-3">
                                <label class="control-label"><?= lang('pay.dailymaxdepamt'); ?>: </label>
                                <input type="number" maxlength="12" name="dailyMaxDepositAmount" class="form-control input-sm amount_only" id="dailyMaxDepositAmount" required>
                                <?php echo form_error('dailyMaxDepositAmount', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>

                            <div class="col-md-12">
                                <label class="control-label" class="control-label"><?= lang('pay.curthirdpartacct'); ?>:</label>
                                <?php echo form_multiselect('playerLevels[]', $levels, $form['playerLevels'], 'id="playerLevels" class="form-control chosen-select playerLevels" data-placeholder="'.lang("pay.sellevedit").'" data-untoggle="checkbox" data-target="#toggle-checkbox-3"') ?>
                                <p class="help-block playerLevels-help-block pull-left"><i style="font-size:12px;color:#919191;"><?= lang('pay.applevthirdpartacct'); ?></i></p>
                                <div class="checkbox pull-right" style="margin-top: 5px">
                                    <label><input type="checkbox" id="toggle-checkbox-3" data-toggle="checkbox" data-target="#form_add #playerLevels option"<?= isset($form['promoLevels']) && sizeof($form['promoLevels']) == sizeof($levels) ? 'checked' : '' ?>> <?= lang("lang.selectall"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="control-label" for=""><?= lang('pay.remark'); ?>: </label>
                                <textarea name="description" id="description" class="form-control input-sm" cols="10" rows="3" style="max-height: 90px;" required></textarea>
                                <?php echo form_error('description', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                        </div>
                        <center>
                            <input type="submit" value="<?= lang('lang.add'); ?>" class="btn btn-sm btn-info review-btn custom-btn-size"/>
                            <span class="btn btn-sm btn-default addthirdpartyaccount-cancel-btn"/><?= lang('lang.cancel'); ?></span>
                        </center>
                    </form>
                </div>
                <hr/>
            </div>
        </div>

        <!-- edit thirdParty account -->
        <div class="row edit_thirdpartyaccount_sec">
            <div class="col-md-12">
                <div class="well" style="overflow: auto">
                    <form class="form-horizontal" id="form_edit" action="<?= BASEURL . 'thirdpartyaccount_management/addThirdPartyAccount' ?>" method="post" role="form">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label class="control-label" for=""><?= lang('pay.paymethod'); ?>: </label>
                                <input type="hidden" name="thirdPartyAccountId" class="form-control" id="editThirdPartyAccountId" >
                                <input type="text" id="editThirdPartyName" name="thirdPartyName" class="form-control input-sm" required>
                                <?php echo form_error('thirdPartyName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label" for=""><?= lang('pay.merchacct'); ?>: </label>
                                <input type="text" name="thirdPartyAccountName" class="form-control input-sm" id="editThirdPartyAccountName" required>
                                <?php echo form_error('thirdPartyAccountName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label" for=""><?= lang('con.bnk10'); ?>: </label>
                                <input type="number" name="transactionFee" class="form-control input-sm number_only" id="editThirdPartyAccount" required>
                                <?php echo form_error('transactionFee', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label" for="dailyMaxDepositAmount"><?= lang('pay.dailymaxdepamt'); ?>: </label>
                                <input type="number" maxlength="12" name="dailyMaxDepositAmount" class="form-control input-sm number_only" id="editDailyMaxDepositAmount" required>
                                <?php echo form_error('dailyMaxDepositAmount', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-12">
                                <label class="control-label" class="control-label"><?= lang('pay.curthirdpartacct'); ?>:</label>
                                <?php echo form_multiselect('playerLevels[]', $levels, $form['playerLevels'], 'id="playerLevels" class="form-control chosen-select playerLevels" data-placeholder="'.lang("pay.sellevedit").'" data-untoggle="checkbox" data-target="#toggle-checkbox-2"') ?>
                                <p class="help-block playerLevels-help-block pull-left"><i style="font-size:12px;color:#919191;"><?= lang('pay.applevthirdpartacct'); ?></i></p>
                                <div class="checkbox pull-right" style="margin-top: 5px">
                                    <label><input type="checkbox" id="toggle-checkbox-2" data-toggle="checkbox" data-target="#form_edit #playerLevels option"<?= isset($form['promoLevels']) && sizeof($form['promoLevels']) == sizeof($levels) ? 'checked' : '' ?>> <?= lang("lang.selectall"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="control-label" for=""><?= lang("pay.remark"); ?>: </label>
                                <textarea name="description" id="editThirdPartyAccountDescription" class="form-control input-sm" cols="10" rows="3" style="max-height: 90px;" required></textarea>
                                <?php echo form_error('description', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                        </div>
                        <center>
                            <input type="submit" value="<?= lang("lang.save"); ?>" class="btn btn-sm btn-info review-btn custom-btn-size" data-toggle="modal" />
                            <span class="btn btn-sm btn-default editthirdpartyaccount-cancel-btn custom-btn-size" data-toggle="modal" /><?= lang("lang.cancel"); ?></span>
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
                            <li><a onclick="sortBankAccount('thirdPartyName')">Bank name</a></li>
                            <li><a onclick="sortBankAccount('branchName')">Branch name</a></li>
                            <li><a onclick="sortBankAccount('updatedOn')">Created On</a></li>
                        </ul>
                    </div>
                    <input type="text" class="search-query" placeholder="<?= lang("lang.search"); ?>" name="search" id="search">
                    <input type="button" class="btn btn-sm" value="<?= lang("lang.go"); ?>" onclick="searchBankAccount();">
                </form> -->

                <form action="<?= BASEURL . 'thirdpartyaccount_management/deleteSelectedThirdPartyAccount'?>" method="post" role="form">
                    <div id="thirdpartyaccount_table" class="table-responsive">
                        <table class="table table-striped table-hover table-condensed" id="my_table" style="margin: 0px 0 0 0; width: 100%;">
                            <div class="btn-action">
                                <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="<?= lang('cms.deletesel'); ?>">
                                    <i class="glyphicon glyphicon-trash" style="color:white;"></i>
                                </button>&nbsp;
                                <?php if($export_report_permission){ ?>
                                    <a href="<?= BASEURL . 'thirdpartyaccount_management/exportToExcel' ?>" class="btn btn-sm btn-success btn-sm" data-toggle="tooltip" title="<?= lang('lang.export'); ?>" data-placement="top">
                                        <i class="glyphicon glyphicon-share"></i>
                                    </a>
                                <?php } ?>
                            </div>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                                    <th><?= lang("lang.action"); ?></th>
                                    <th><?= lang("pay.thirdpartyacctorder"); ?></th>
                                    <th><?= lang("pay.thirdparpaytman"); ?></th>
                                    <th><?= lang("pay.merchacct"); ?></th>
                                    <th><?= lang("con.bnk10"); ?></th>
                                    <th><?= lang("pay.playerlevs"); ?></th>
                                    <th><?= lang("pay.dailymaxdepamt"); ?></th>
                                    <th><?= lang('pay.totalbankdeposit'); ?></th>
                                    <th><?= lang("player.upay05"); ?></th>
                                    <th><?= lang("pay.createdon"); ?></th>
                                    <th><?= lang("pay.createdby"); ?></th>
                                    <th><?= lang("pay.updatedon"); ?></th>
                                    <th><?= lang("pay.updatedby"); ?></th>
                                    <th><?= lang("lang.status"); ?></th>
                                    <th><?= lang('pay.remark'); ?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                    if(!empty($thirdparty)) {
                                        $cnt=1;
                                        foreach($thirdparty as $row) {
                                ?>
                                                <tr>
                                                    <td></td>
                                                    <td>
                                                        <?php //if($row['otcPaymentMethodId'] != 1){ ?>
                                                            <input type="checkbox" class="checkWhite" id="<?= $row['thirdpartypaymentmethodaccountId']?>" name="thirdpartyaccount[]" value="<?= $row['thirdpartypaymentmethodaccountId']?>" onclick="uncheckAll(this.id)"/>
                                                        <?php //d}  ?>
                                                    </td>
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

                                                                <a href="#form_edit" class="glyphicon glyphicon-edit editThirdPartyAccountBtn" data-toggle="tooltip" title="<?= lang("lang.edit"); ?>" onclick="ThirdPartyManagementProcess.getThirdPartyAccountDetails(<?= $row['thirdpartypaymentmethodaccountId'] ?>)" data-placement="top">
                                                                </a>
                                                                <?php //if($row['otcPaymentMethodId'] != 1){ ?>
                                                                    <a href="<?= BASEURL . 'thirdpartyaccount_management/deleteThirdPartyAccountItem/'.$row['thirdpartypaymentmethodaccountId'] ?>">
                                                                        <span data-toggle="tooltip" title="<?= lang("lang.delete"); ?>" class="glyphicon glyphicon-trash" data-placement="top">
                                                                        </span>
                                                                    </a>
                                                                <?php //}  ?>
                                                        </div>
                                                    </td>
                                                    <!-- <td><?= $row['accountOrder'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['accountOrder'] ?></td> -->
                                                    <td><?= $cnt ?>
                                                    <td><?= $row['thirdpartyName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['thirdpartyName'] ?></td>
                                                    <td><?= $row['thirdpartyAccountName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['thirdpartyAccountName'] ?></td>
                                                    <td><?= $row['transactionFee'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : number_format($row['transactionFee'],2) ?></td>
                                                    <td align="center">
                                                        <?php
                                                            $memberLevels = null;
                                                            foreach ($row['thirdPartyAccountPlayerLevelLimit'] as $key) {
                                                                $memberLevels[] = $key['groupName'].$key['vipLevel'];
                                                            }
                                                        ?>
                                                        <button type="button" class="btn btn-xs btn-default" title="<?=implode(', ', $memberLevels)?>" data-toggle="tooltip">...</button>
                                                    </td>
                                                    <td><?= $row['dailyMaxDepositAmount'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : number_format($row['dailyMaxDepositAmount'],2) ?></td>
                                                    <td><?= $row['totalDeposit'][0]['totalBankDeposit'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : number_format($row['totalDeposit'][0]['totalBankDeposit'],2) ?></td>
                                                    <td><?= $row['description'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['description'] ?></td>
                                                    <td><?= $row['createdOn'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['createdOn'] ?></td>
                                                    <td><?= $row['createdBy'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['createdBy'] ?></td>
                                                    <td><?= $row['updatedOn'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['updatedOn'] ?></td>
                                                    <td><?= $row['updatedBy'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['updatedBy'] ?></td>
                                                    <td><?= $row['status'] ?></td>
                                                    <td><i class="help-block"><?= ($row['status'] == 'inactive') ? lang('pay.exceeded') : lang('pay.none'); ?><i/></td>
                                                </tr>
                                <?php
                                            $cnt++;
                                        }
                                    }
                                    else{ ?>
                                <?php   }
                                ?>
                            </tbody>
                        </table>
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
                    type: 'column',
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
