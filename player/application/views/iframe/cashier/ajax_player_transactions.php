<h4><b><?=lang('pay.transactions');?></b></h4>
<div class="panel panel-default">
    <table class="table table-hover table-striped table-bordered">
        <thead>
            <tr>
                <th><?=lang('cashier.35');?></th>
                <th><?=lang('cashier.36');?></th>
                <th><?=lang('pay.amt');?></th>
                <th><?=lang('cashier.37');?></th>
                <th><?=lang('cashier.38');?></th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($balanceAdjustmentHistory)) {?>
                <?php foreach ($balanceAdjustmentHistory as $row) {?>
                    <tr>
                        <td><?php echo $row['created_at']; ?></td>
                        <td><?php echo lang('transaction.transaction.type.' . $row['transaction_type']); ?></td>
                        <td><?php echo $this->utils->roundCurrencyForShow($row['amount']); ?></td>
                        <td><?php echo $this->utils->roundCurrencyForShow($row['before_balance']); ?></td>
                        <td><?php echo $this->utils->roundCurrencyForShow($row['after_balance']); ?></td>
                    </tr>
                <?php }?>
            <?php } else {?>
                    <tr>
                        <td colspan="5" style="text-align:center"><span class="help-block"><?=lang('cashier.32');?></span></td>
                    </tr>
            <?php }?>
        </tbody>
    </table>

    <br/>

    <div class="col-md-12 col-offset-0">
        <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
    </div>
</div>
