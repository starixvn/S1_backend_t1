<h4><b><?=lang('Points');?></b></h4>
<div class="panel panel-default">
    <table class="table table-hover table-striped table-bordered">
        <thead>
            <tr>
                <th><?=lang('Date');?></th>
               <!--  <th>id</th> -->
                <th><?=lang('Point');?></th>
               <!--  <th>process</th> -->
                <th><?=lang('Transaction Type');?></th>
                <th><?=lang('Before Balance');?></th>
                <th><?=lang('After Balance');?></th>
                <th><?=lang('con.plm74');?></th>
                <th><?=lang('player.uw06');?> </th>
                <th><?=lang('cms.promoname');?></th>
                <th><?=lang('sys.gd16');?></th>
                <th><?=lang('sys.dm4');?></th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($points)) {
	?>
                <?php foreach ($points as $row) {?>
                <tr>
                    <td><?=$row['created_at']?></td>
                    <!-- <td><?=$row['id']?></td>  -->
                    <td><?=$row['point']?></td> 
                    <!-- <td><?= ($row['flag']  == 1 ) ? 'MANUAL' : 'AUTO'  ?> </td>  -->
                    <td><?= ($row['transaction_type']  == Point_transactions::DEPOSIT_POINT ) ?  lang('Deposit')  : lang('Bets')  ?> </td> 
                    <td><?=$row['before_balance']?></td>
                    <td><?=$row['after_balance']?></td> 
                    <td><?= ($row['payment_account_name']) ? $row['payment_account_name'] : '<i class="text-muted">' . lang('cashier.92') . '</i>'?></td> 
                    <td><?= ($row['system_code']) ? $row['system_code'] : '<i class="text-muted">' . lang('cashier.92') . '</i>'?></td> 
                    <td><?= ($row['promoName']) ? $row['promoName'] : '<i class="text-muted">' . lang('cashier.92') . '</i>'?></td> 
                    <td><?=$row['status'] == Point_transactions::APPROVED ? lang('Approved') : lang('Declined')?></td>  
                    <td><?=$row['note']?></td>  
                </tr>
                <?php }
	?>
            <?php } else {?>
                    <tr>
                        <td colspan="11" style="text-align:center"><span class="help-block"><?=lang('cashier.32');?></span></td>
                    </tr>
            <?php }
?>
        </tbody>
    </table>

    <br/>

    <div class="col-md-12 col-offset-0">
        <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links();?> </ul>
    </div>
</div> 


