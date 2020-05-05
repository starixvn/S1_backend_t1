<table class="table">
    <th><?=lang('cashier.36')?></th>
    <th><?=lang('con.pb')?></th>
    <th><?=lang('lang.action');?></th>
    <?php foreach ($walletAccount as $key) {
	?>
        <?php if ($key['walletType'] != Payment_management::WALLET_CASHBACK) {
		?>
            <?php if ($key['walletType'] == Payment_management::WALLET_MAIN) {?>
                <tr>
                    <td>
                        <label for="<?=$key['walletType']?>"><?=lang('pay.mainwalltbal') . ' ' . lang('pay.amt');?>:</label>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="currentMainwalletBal" readonly value="<?=$key['balance']?>" />
                    </td>
                    <td>
                        <input type='button' class='btn btn-info btn-sm' onclick="adjustBalance('main')" value='<?=lang('pay.adjust')?>'>
                    </td>
                </tr>
            <?php } else {?>
                <tr>
                    <td>
                        <label for="<?=$key['walletType']?>"><?=$key['game'] . ' ' . lang('pay.subwalltbal') . ' ' . lang('pay.amt');?>:</label>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="subwalletCurrentBal" readonly value="<?=$key['balance']?>" />
                    </td>
                    <td>
                        <input type='button' class='btn btn-info btn-sm' onclick="adjustBalance('<?=$key['game']?>')" value='<?=lang('pay.adjust')?>'>
                    </td>
                </tr>
            <?php }
		?>
        <?php }
	?>
    <?php }
?>
</table>