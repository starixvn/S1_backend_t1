<h4><b><?=lang('cashier.24');?></b></h4>
<div class="panel panel-default">
    <table class="table table-hover table-striped table-bordered">
        <thead>
            <tr>
                <th><?=lang('cashier.deposit_datetime');?></th>
                <th><?=lang('cashier.deposit_secure_id');?></th>
                <th><?=lang('cashier.27');?></th>
                <th><?=lang('cashier.28');?></th>
                <th><?=lang('system.word32');?></th>
                <th><?=lang('cashier.29');?></th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($deposits)) {
    ?>
                <?php foreach ($deposits as $row) {?>
                    <tr>
                        <td><?=$row['created_at']?></td>
                        <td><?=$row['id']?></td>
                        <td><?=lang($row['bankName'])?></td>
                        <td><?=lang('sale_orders.status.' . $row['status'])?></td>
                        <td><?=number_format($row['amount'], 2)?></td>
                        <td><?=$row['show_reason_to_player'] == 'true' ? $row['reason'] : '<i class="text-muted">' . lang('cashier.92') . '</i>'?></td>
                    </tr>
                <?php }
    ?>
            <?php } else {?>
                    <tr>
                        <td colspan="6" style="text-align:center"><span class="help-block"><?=lang('cashier.32');?></span></td>
                    </tr>
            <?php }
?>
        </tbody>
    </table>

    <br/>

    <div class="col-md-12 col-offset-0">
        <ul class="pagination pagination-sm pull-right" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links();?> </ul>
    </div>
</div>