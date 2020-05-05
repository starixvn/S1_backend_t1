                <h4><label for=""><?=lang('cashier.90');?></label></h4>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="5%"><?=lang('cashier.91');?></th>
                            <th width="15%"><?=lang('cashier.67');?></th>
                            <th width="15%"><?=lang('cashier.68');?></th>
                            <th width="15%"><?=lang('cashier.69');?></th>
                            <th width="15%"><?=lang('cashier.102');?></th>
                            <th width="15%"><?= lang('cashier.72'); ?></th>
                            <th width="5%"><?=lang('cashier.28');?></th>
                            <th width="15%"><?=lang('cashier.101');?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $ctr = 1;?>
                        <?php if (!empty($bank_details['deposit'])) {
	$default = $this->player_functions->checkIfValueExists($bank_details['deposit'], 'isDefault', '1');
	foreach ($bank_details['deposit'] as $row) {
		?>
                                <tr>
                                    <td><?=$ctr?></td>
                                    <td><?=lang($row['bankName'])?></td>
                                    <td><?=ucwords($row['bankAccountFullName']);?></td>
                                    <td><?=$row['bankAccountNumber']?></td>
                                    <td><?=$row['bankAddress'] == '' ? '<i>No Record</i>' : $row['bankAddress'];?></td>
                                    <td><?=$row['branch'] == '' ? '<i>No Record</i>' : ucwords($row['branch']);?></td>
                                    <td><?=$row['status'] == 0 ? '<span class="help-block" style="color:#46b8da;">'.lang('Active').'</span>' : '<span class="help-block" style="color:#66cc66;">'.lang('Inactive').'</span>'?></td>
                                    <td>
                                        <?php if ($row['isDefault'] == 0 && $default == false && $row['status'] == 0) {?>
                                            <a href="<?=BASEURL . 'iframe_module/setDefaultBankDetails/' . $row['playerBankDetailsId'] . '/1'?>"><?=lang('cashier.110');?></a> |
                                        <?php } elseif ($row['isDefault'] == 1) {?>
                                            <a href="<?=BASEURL . 'iframe_module/setDefaultBankDetails/' . $row['playerBankDetailsId'] . '/0'?>"><?=lang('cashier.128');?></a> |
                                        <?php }
		?>

                                        <a href="<?=BASEURL . 'iframe_module/addEditBank/' . 'deposit' . '/' . $row['playerBankDetailsId']?>"><?=lang('cashier.99');?></a>

                                        <?php if ($row['isDefault'] == 0 && $row['status'] == 0) {?>
                                            | <a href="<?=BASEURL . 'iframe_module/changeBankStatus/1/' . $row['playerBankDetailsId']?>"><?=lang('cashier.107');?></a>
                                        <?php } elseif ($row['isDefault'] == 0) {?>
                                            | <a href="<?=BASEURL . 'iframe_module/changeBankStatus/0/' . $row['playerBankDetailsId']?>"><?=lang('cashier.106');?></a>
                                        <?php }
		?>

                                        <?php if ($row['isDefault'] == 0) {?>
                                            | <a href="javascript:void(0)" onclick="deleteBankDetails(<?php echo $row['playerBankDetailsId'];?>);"><?=lang('cashier.108');?></a>
                                        <?php }
		?>
                                    </td>
                                </tr>
                                <?php $ctr++;?>
                            <?php }
	?>
                        <?php } else {?>
                                <tr>
                                    <td colspan="8" style="text-align:center"><span class="help-block"><?=lang('cashier.32');?></span></td>
                                </tr>
                        <?php }
?>
                    </tbody>
                </table>
                <a href="<?=BASEURL . 'iframe_module/addEditBank/' . 'deposit'?>" class="btn btn-sm btn-hotel"> <?=lang('cashier.109');?></a>
                <br/>

                <hr/>

                <h4><label for=""><?=lang('cashier.112');?></label></h4>
                <table class="table table-striped table-hover table-condensed">
                    <thead>
                        <tr>
                            <th width="5%"><?=lang('cashier.91');?></th>
                            <th width="15%"><?=lang('cashier.67');?></th>
                            <th width="15%"><?=lang('cashier.68');?></th>
                            <th width="15%"><?=lang('cashier.69');?></th>
                            <th width="15%"><?=lang('cashier.102');?></th>
                            <th width="15%"><?=lang('cashier.72');?></th>
                            <th width="5%"><?=lang('cashier.28');?></th>
                            <th width="15%"><?=lang('mess.07');?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $ctr = 1;?>
                        <?php if (!empty($bank_details['withdrawal'])) {
	$default = $this->player_functions->checkIfValueExists($bank_details['withdrawal'], 'isDefault', '1');

	foreach ($bank_details['withdrawal'] as $row) {
		?>
                                <tr>
                                    <td><?=$ctr?></td>
                                    <td><?=lang($row['bankName'])?></td>
                                    <td><?=ucwords($row['bankAccountFullName']);?></td>
                                    <td><?=$row['bankAccountNumber']?></td>
                                    <td><?=$row['bankAddress'] == '' ? '<i>No Record</i>' : $row['bankAddress'];?></td>
                                    <td><?=$row['branch'] == '' ? '<i>No Record</i>' : ucwords($row['branch']);?></td>
                                    <td><?=$row['status'] == 0 ? '<span class="help-block" style="color:#46b8da;">'.lang('Active').'</span>' : '<span class="help-block" style="color:#66cc66;">'.lang('Inactive').'</span>'?></td>
                                    <td>
                                        <?php if ($row['isDefault'] == 0 && $default == false && $row['status'] == 0) {?>
                                           <a href="<?=BASEURL . 'iframe_module/setDefaultBankDetails/' . $row['playerBankDetailsId'] . '/1'?>"><?=lang('cashier.110');?></a> |
                                        <?php } elseif ($row['isDefault'] == 1) {?>
                                           <a href="<?=BASEURL . 'iframe_module/setDefaultBankDetails/' . $row['playerBankDetailsId'] . '/0'?>"><?=lang('cashier.128');?></a> |
                                        <?php }
		?>

                                        <a href="<?=BASEURL . 'iframe_module/addEditWithdrawalBank/' . 'withdrawal' . '/' . $row['playerBankDetailsId']?>"><?=lang('cashier.99');?></a>

                                        <?php if ($row['isDefault'] == 0 && $row['status'] == 0) {?>
                                            | <a href="<?=BASEURL . 'iframe_module/changeBankStatus/1/' . $row['playerBankDetailsId']?>"><?=lang('cashier.107');?></a>
                                        <?php } elseif ($row['isDefault'] == 0) {?>
                                            | <a href="<?=BASEURL . 'iframe_module/changeBankStatus/0/' . $row['playerBankDetailsId']?>"><?=lang('cashier.106');?></a>
                                        <?php }
		?>

                                        <?php if ($row['isDefault'] == 0) {?>
                                            | <a href="javascript:void(0)" onclick="deleteBankDetails(<?php echo $row['playerBankDetailsId'];?>);"><?=lang('cashier.108');?></a>
                                        <?php }
		?>
                                    </td>
                                </tr>
                                <?php $ctr++;?>
                            <?php }
	?>
                        <?php } else {?>
                                <tr>
                                    <td colspan="10" style="text-align:center"><span class="help-block"><?=lang('cashier.32');?></span></td>
                                </tr>
                        <?php }
?>
                    </tbody>
                </table>
                <a href="<?=BASEURL . 'iframe_module/addEditWithdrawalBank/' . 'withdrawal'?>" class="btn btn-sm btn-hotel"> <?=lang('cashier.111');?></a>
                <br/><br/><br/>
                <a href="<?php echo site_url('iframe_module/iframe_viewCashier')?>" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-circle-arrow-left"></span> <?=lang('button.back');?></a>
            </div>
        </div>
    </div>
</div>

<script>
function deleteBankDetails($bankDetailId){
    if(confirm("<?php echo lang('Do you want delete this bank account');?> ?")){
        window.location.href="<?php echo site_url('/iframe_module/deleteBankDetails');?>/"+$bankDetailId;
    }
}
</script>
