<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default panel-og">
            <div class="panel-heading">
                <h4 class="panel-title pull-left"><span class="glyphicon glyphicon-edit"></span> <?= lang('cashier.89'); ?></h4>
                <div class="btn-group pull-right" style="margin: 5px 0;">
                    <!-- <a href="#liveHelp" class="btn btn-default btn-sm text-uppercase" style="font-weight: bold;"><?= lang('promo.40'); ?> <span class="glyphicon glyphicon-comment"></span></a> -->
                    <a href="<?= BASEURL . 'messages' ?>" class="btn btn-default btn-sm text-uppercase" style="font-weight: bold;"><?= lang('cashier.40'); ?> <span class="glyphicon glyphicon-comment"></span></a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="panel-body">
                <h4><label for=""><?= lang('cashier.90'); ?></label></h4>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><?= lang('cashier.91'); ?></th>
                            <th><?= lang('cashier.67'); ?></th>
                            <th><?= lang('cashier.68'); ?></th>
                            <th><?= lang('cashier.69'); ?></th>
                            <th><?= lang('cashier.72'); ?></th>
                            <th><?= lang('cashier.102'); ?></th>
                            <th><?= lang('cashier.28'); ?></th>
                            <th><?= lang('cashier.101'); ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $ctr = 1; ?>
                        <?php if(!empty($bank_details['deposit'])) { 
                                $default = $this->player_functions->checkIfValueExists($bank_details['deposit'], 'isDefault', '1');
                                foreach ($bank_details['deposit'] as $row) { 
                        ?>
                                <tr>
                                    <td><?= $ctr ?></td>
                                    <td><?= $row['bankName'] ?></td>
                                    <td><?= ucwords($row['bankAccountFullName']); ?></td>
                                    <td><?= $row['bankAccountNumber'] ?></td>
                                    <td><?= $row['bankAddress'] == '' ? '<i>No Record</i>' : $row['bankAddress']; ?></td>
                                    <td><?= $row['branch'] == '' ? '<i>No Record</i>' : ucwords($row['branch']); ?></td>
                                    <td><?= $row['status'] == 0 ? '<span class="help-block" style="color:#46b8da;">Active</span>' : '<span class="help-block" style="color:#66cc66;">Inactive</span>' ?></td>
                                    <td>
                                        <?php if($row['isDefault'] == 0 && $default == false && $row['status'] == 0) { ?>
                                            <a href="<?= BASEURL . 'smartcashier/setDefaultBankDetails/' . $row['playerBankDetailsId'] . '/1' ?>"><?= lang('cashier.110'); ?></a> |
                                        <?php } elseif($row['isDefault'] == 1) { ?>
                                            <a href="<?= BASEURL . 'smartcashier/setDefaultBankDetails/' . $row['playerBankDetailsId'] . '/0' ?>"><?= lang('cashier.128'); ?></a> |
                                        <?php } ?>

                                        <a href="<?= BASEURL . 'smartcashier/addEditBank/' . 'deposit' . '/' . $row['playerBankDetailsId']?>"><?= lang('cashier.99'); ?></a>

                                        <?php if($row['isDefault'] == 0 && $row['status'] == 0) { ?>
                                            | <a href="<?= BASEURL . 'smartcashier/changeBankStatus/1/' . $row['playerBankDetailsId']?>"><?= lang('cashier.107'); ?></a>
                                        <?php } elseif($row['isDefault'] == 0) { ?>
                                            | <a href="<?= BASEURL . 'smartcashier/changeBankStatus/0/' . $row['playerBankDetailsId']?>"><?= lang('cashier.106'); ?></a>
                                        <?php } ?>

                                        <?php if($row['isDefault'] == 0) { ?>
                                            | <a href="<?= BASEURL . 'smartcashier/deleteBankDetails/' . $row['playerBankDetailsId'] ?>"><?= lang('cashier.108'); ?></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php $ctr++; ?>
                            <?php } ?>
                        <?php } else { ?>
                                <tr>
                                    <td colspan="8" style="text-align:center"><span class="help-block"><?= lang('cashier.32'); ?></span></td>
                                </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <a href="<?= BASEURL . 'smartcashier/addEditBank/' . 'deposit'?>" class="btn btn-sm btn-hotel"> <?= lang('cashier.109'); ?></a>
                <br/>

                <hr/>

                <h4><label for=""><?= lang('cashier.112'); ?></label></h4>
                <table class="table table-striped table-hover table-condensed">
                    <thead>
                        <tr>
                            <th><?= lang('cashier.91'); ?></th>
                            <th><?= lang('cashier.67'); ?></th>
                            <th><?= lang('cashier.68'); ?></th>
                            <th><?= lang('cashier.69'); ?></th>
                            <th><?= lang('cashier.70'); ?></th>
                            <th><?= lang('cashier.71'); ?></th>
                            <th><?= lang('cashier.72'); ?></th>
                            <th><?= lang('cashier.28'); ?></th>
                            <th><?= lang('mess.07'); ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $ctr = 1; ?>
                        <?php if(!empty($bank_details['withdrawal'])) { 
                                $default = $this->player_functions->checkIfValueExists($bank_details['withdrawal'], 'isDefault', '1');

                                foreach ($bank_details['withdrawal'] as $row) { 
                        ?>
                                <tr>
                                    <td><?= $ctr ?></td>
                                    <td><?= $row['bankName'] ?></td>
                                    <td><?= ucwords($row['bankAccountFullName']); ?></td>
                                    <td><?= $row['bankAccountNumber'] ?></td>
                                    <td><?= $row['province'] == '' ? '<i>No Record</i>' : ucwords($row['province']); ?></td>
                                    <td><?= $row['city'] == '' ? '<i>No Record</i>' : ucwords($row['city']); ?></td>
                                    <td><?= $row['branch'] == '' ? '<i>No Record</i>' : ucwords($row['branch']); ?></td>
                                    <td><?= $row['status'] == 0 ? '<span class="help-block" style="color:#46b8da;">Active</span>' : '<span class="help-block" style="color:#66cc66;">Inactive</span>' ?></td>
                                    <td>
                                        <?php if($row['isDefault'] == 0 && $default == false && $row['status'] == 0) { ?>
                                           <a href="<?= BASEURL . 'smartcashier/setDefaultBankDetails/' . $row['playerBankDetailsId'] . '/1' ?>"><?= lang('cashier.110'); ?></a> |
                                        <?php } elseif($row['isDefault'] == 1) { ?>
                                           <a href="<?= BASEURL . 'smartcashier/setDefaultBankDetails/' . $row['playerBankDetailsId'] . '/0' ?>"><?= lang('cashier.128'); ?></a> |
                                        <?php } ?>

                                        <a href="<?= BASEURL . 'smartcashier/addEditWithdrawalBank/' . 'withdrawal' . '/' . $row['playerBankDetailsId']?>"><?= lang('cashier.99'); ?></a>

                                        <?php if($row['isDefault'] == 0 && $row['status'] == 0) { ?>
                                            | <a href="<?= BASEURL . 'smartcashier/changeBankStatus/1/' . $row['playerBankDetailsId'] ?>"><?= lang('cashier.107'); ?></a> |
                                        <?php } elseif($row['isDefault'] == 0) { ?>
                                            | <a href="<?= BASEURL . 'smartcashier/changeBankStatus/0/' . $row['playerBankDetailsId'] ?>"><?= lang('cashier.106'); ?></a> |
                                        <?php } ?>

                                        <?php if($row['isDefault'] == 0) { ?>
                                            | <a href="<?= BASEURL . 'smartcashier/deleteBankDetails/' . $row['playerBankDetailsId'] . '/' . $row['dwBank'] ?>"><?= lang('cashier.108'); ?></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php $ctr++; ?>
                            <?php } ?>
                        <?php } else { ?>
                                <tr>
                                    <td colspan="10" style="text-align:center"><span class="help-block"><?= lang('cashier.32'); ?></span></td>
                                </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <a href="<?= BASEURL . 'smartcashier/addEditWithdrawalBank/' . 'withdrawal'?>" class="btn btn-sm btn-hotel"> <?= lang('cashier.111'); ?></a>
            </div>
        </div>
    </div>
</div>
