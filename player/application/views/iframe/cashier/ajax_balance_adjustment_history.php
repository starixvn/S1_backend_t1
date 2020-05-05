<h4><b><?= lang('cashier.34'); ?></b></h4>
<div class="panel panel-default">
    <table class="table table-hover table-striped table-bordered">
        <thead>
            <tr>
                <th><?= lang('cashier.35'); ?></th>
                <th><?= lang('cashier.36'); ?></th>
                <th><?= lang('cashier.37'); ?></th>
                <th><?= lang('cashier.38'); ?></th>
                <th><?= lang('cashier.39'); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php if(!empty($balanceAdjustmentHistory)) { ?>
                <?php foreach($balanceAdjustmentHistory as $row) { ?>
                    <tr>
                        <td><?= $row['adjustedOn']?></td>
                        <td><?= ($row['walletType'] == null) ? '' : $row['walletType'] == '0' ? lang('cashier.02') : lang('cashier.42') ?></td>
                        <td><?= ($row['amountChanged'] == null) ? '' : $row['amountChanged'] ?></td>
                        <td><?= ($row['newBalance'] == null) ? '' : $row['newBalance'] ?></td>
                        <td><?= ($row['reason'] == null || $row['reason'] == '0'  || ($row['walletType'] != '0' && $row['show_flag'] == '0')) ? lang('lang.norecyet') : $row['reason'] ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                    <tr>
                        <td colspan="5" style="text-align:center"><span class="help-block"><?= lang('cashier.32'); ?></span></td>
                    </tr>
            <?php } ?>
        </tbody>
    </table>

    <br/>

    <div class="col-md-12 col-offset-0">
        <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
    </div>
</div>
