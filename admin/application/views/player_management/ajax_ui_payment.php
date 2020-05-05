<table class="table table-hover table-bordered" id="paymentTable" style="margin: 0px 0 0 0; width: 100%;">
    <thead>
        <th></th>
        <th><?= lang('player.upay01'); ?></th>
        <th><?= lang('player.upay02'); ?></th>
        <th><?= lang('player.upay03'); ?></th>
        <th><?= lang('player.upay04'); ?></th>
        <th><?= lang('player.upay05'); ?></th>
        <th><?= lang('lang.status'); ?></th>
    </thead>

    <tbody>
        <?php if(!empty($payment_history)) { ?>
            <?php foreach ($payment_history as $key => $value) { ?>
                <tr>
                    <td></td>
                    <td><?= $value['dwDateTime'] ?></td>
                    <td><?= $value['transactionType'] ?></td>
                    <td><?= ($value['localBankType'] == null) ? lang('player.udw07'):$value['localBankType'] ?></td>
                    <td><?= $value['amount'] ?></td>
                    <td><?= $value['notes'] ?></td>
                    <td><?= $value['dwStatus'] ?></td>
                </tr>
        <?php 
                } 
            }
        ?>
    </tbody>
</table>