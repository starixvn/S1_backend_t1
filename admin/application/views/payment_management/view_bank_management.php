<!-- start edit new ranking setting -->
<div id="editDivPaymentMethod">
    <label for="ranlingLevel" style="text-transform:uppercase;"><?= lang('pay.paymethod'); ?></label>
    <div class="well" style="overflow: auto">
        <!-- start sort dw list -->
        <form action="<?= BASEURL . 'payment_management/actionPaymentMethod' ?>" method="post" role="form">
            <div class="row">
                <div class="col-md-1">
                    <h6><label for=""><?= lang('pay.bankname'); ?>: </label></h6>
                </div>

                <div class="col-md-2">
                    <input type="hidden" name="otcPaymentMethodId" class="form-control" id="otcPaymentMethodId">
                    <input type="text" name="bankName" class="form-control input-sm" id="bankName" placeholder="<?= lang('lang.enter') . ' ' . lang('pay.bankname'); ?>">
                    <?php echo form_error('bankName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>

                <div class="col-md-1">
                    <h6><label for=""><?= lang('pay.acctname'); ?>: </label></h6>
                </div>

                <div class="col-md-2">
                    <input type="text" name="accountName" class="form-control input-sm" id="accountName" placeholder="<?= lang('lang.enter') . ' ' . lang('pay.acctname'); ?>">
                    <?php echo form_error('accountName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>

                <div class="col-md-1">
                    <h6><label for=""><?= lang('pay.acctnumber'); ?>: </label></h6>
                </div>

                <div class="col-md-2">
                    <input type="text" name="accountNumber" class="form-control input-sm number_only" id="accountNumber" placeholder="<?= lang('lang.enter') . ' ' . lang('pay.acctnumber'); ?>">
                    <?php echo form_error('accountNumber', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>

                <div class="col-md-1">
                    <h6><label for=""><?= lang('pay.procssng') . ' ' . lang('pay.time') ?>: </label></h6>
                </div>

                <div class="col-md-2">
                    <input type="text" name="processingTime" class="form-control input-sm" id="processingTime" placeholder="<?= lang('lang.enter') . ' ' . lang('pay.process') . ' ' . lang('pay.time'); ?>">
                    <?php echo form_error('processingTime', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>
            </div>

            <div class="row">
                <div class="col-md-1">
                    <h6><label for="dailyMaxDepositAmount"><?= lang('pay.dailymaxdepamt'); ?>: </label></h6>
                </div>

                <div class="col-md-2">
                    <input type="text" maxlength="12" name="dailyMaxDepositAmount" class="form-control input-sm" id="dailyMaxDepositAmount" placeholder="<?= lang('lang.enter') . ' ' . lang('pay.dailymaxdepamt'); ?>">
                    <?php echo form_error('dailyMaxDepositAmount', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>

                <div class="col-md-1">
                    <h6><label for=""><?= lang('pay.description'); ?>: </label></h6>
                </div>

                <div class="col-md-3">
                    <textarea name="description" id="description" class="form-control input-sm" cols="10" rows="1" style="max-width: 204px; max-height: 80px;" placeholder="<?= lang('lang.enter') . ' ' . lang('pay.description'); ?>"></textarea>
                    <?php echo form_error('description', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>
            </div>

            <div class="col-md-3">
                    <input type="submit" value="<?= lang('lang.save'); ?>" class="btn btn-info review-btn btn-sm"/>
                    <input type="reset" value="<?= lang('pay.reset'); ?>" class="btn btn-default btn-sm"/>
            </div>
        </form>
    </div>
</div>
<!-- end edit new ranking setting -->

<div class="row">
    <!-- start request list -->
    <div class="col-md-12" id="toggleView">
        <div class="col-md-5"></div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="col-md-8">
                    <h4 class="panel-title custom-pt">
                        <i class="glyphicon glyphicon-list-alt"></i> <?= lang('pay.bankmangt'); ?>
                        <span class="choosenDateRange">&nbsp;</span>
                    </h4>
                </div>
                <div class="clearfix"></div>
            </div>

            <!-- start data table -->
            <div class="panel panel-body" id="ranking_panel_body">
                <div id="paymentList" class="table-responsive">
                    <table class="table table-striped table-hover" id="myTable">
                        <thead>
                            <tr>
                                <th><?= lang('pay.bankname'); ?></th>
                                <th><?= lang('pay.acctnumber'); ?></th>
                                <th><?= lang('pay.acctname'); ?></th>
                                <th><?= lang('pay.dailymaxdepamt'); ?></th>
                                <th><?= lang('pay.createdon'); ?></th>
                                <th><?= lang('pay.updatedon'); ?></th>
                                <th><?= lang('lang.status'); ?></th>
                                <th><?= lang('lang.action'); ?></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                                if(!empty($banks)) {
                                    foreach($banks as $row) {
                            ?>
                                            <tr>
                                                <td><?= $row['bankName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['bankName'] ?></td>
                                                <td><?= $row['accountNumber'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['accountNumber'] ?></td>
                                                <td><?= $row['accountName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['accountName'] ?></td>
                                                <td><?= $row['dailyMaxDepositAmount'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['dailyMaxDepositAmount'] ?></td>
                                                <td><?= $row['createdOn'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['createdOn'] ?></td>
                                                <td><?= $row['updatedOn'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $row['updatedOn'] ?></td>
                                                <td><?= $row['status'] == '0' ? '<i class="help-block">'. lang("pay.normal") .'<i/>' : '<i class="help-block">Locked</i>' ?></td>

                                                <td>
                                                    <a href="<?= BASEURL . 'payment_management/changeStatusPaymentMethod/'.$row['otcPaymentMethodId'] ?>">
                                                        <span class="glyphicon glyphicon-lock" data-toggle="tooltip" title="<?= lang('pay.lockunlock') . " " . lang('pay.paymethod'); ?>"  data-placement="top">
                                                        </span>
                                                    </a>
                                                    <a href="#editPaymentMethod">
                                                        <span class="glyphicon glyphicon-edit" data-toggle="tooltip" title="<?= lang('lang.edit') . " " . lang('pay.paymethod'); ?>"  data-placement="top" onclick="PaymentManagementProcess.getOTCPaymentMethodDetails(<?= $row['otcPaymentMethodId'] ?>)">
                                                        </span>
                                                    </a>
                                                    <a href="<?= BASEURL . 'payment_management/deletePaymentMethod/'.$row['otcPaymentMethodId'] ?>">
                                                        <span class="glyphicon glyphicon-trash" data-toggle="tooltip" title="<?= lang('lang.delete') . " " . lang('pay.paymethod'); ?>"  data-placement="top">
                                                        </span>
                                                    </a>

                                                </td>
                                            </tr>
                            <?php
                                    }
                                }
                                else{ ?>
                                    <tr>
                                        <td colspan="7" style="text-align:center"><?= lang('pay.norec'); ?>
                                        </td>
                                    </tr>
                            <?php   }
                            ?>
                        </tbody>
                    </table>

                </div>
            </div>
            <!-- end data table -->

            <div class="panel-footer">
                <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>

            </div>
        </div>
    </div>
    <!-- end request list -->

</div>