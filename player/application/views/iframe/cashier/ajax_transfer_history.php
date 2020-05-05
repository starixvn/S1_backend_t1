<h4><b><?= lang('cashier.23'); ?></b></h4>
<div class="panel panel-default">
    <table class="table table-hover table-striped table-bordered">
        <thead>
            <tr>
                <th><?= lang('cashier.25'); ?></th>
                <th><?= lang('cashier.07'); ?></th>
                <th><?= lang('cashier.43'); ?></th>
                <th><?= lang('cashier.09'); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php if(!empty($transfer_history)) { ?>
                <?php foreach($transfer_history as $row) { ?>
                    <tr>
                        <td><?= $row['requestDateTime']?></td>
                        <td><?= ($row['transferFrom'] == null) ? lang('cashier.02') :$row['transferFrom'] . lang('cashier.41') ?></td>
                        <td><?= ($row['transferTo'] == null) ?  lang('cashier.02'):$row['transferTo'] . lang('cashier.41') ?></td>
                        <td><?= number_format($row['amount'], 2) ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                    <tr>
                        <td colspan="4" style="text-align:center"><span class="help-block"><?= lang('cashier.32'); ?></span></td>
                    </tr>
            <?php } ?>
        </tbody>
    </table>

    <br/>

    <div class="col-md-12 col-offset-0">
        <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
    </div>
</div>