<table class="table table-hover table-bordered" id="walletTable" style="margin: 0px 0 0 0; width: 100%;">
    <thead>
        <th></th>
        <th><?= lang('player.uw01'); ?></th>
        <th><?= lang('player.uw02'); ?></th>
        <th><?= lang('player.uw03'); ?></th>
        <th><?= lang('player.uw04'); ?></th>
    </thead>

    <tbody>
        <?php if(!empty($wallet_transaction_history)) { ?>
            <?php foreach ($wallet_transaction_history as $key => $value) { ?>
                <tr>
                    <td></td>
                    <td><?= $value['requestDatetime'] ?></td>
                    <td><?= $value['amount'] ?></td>
                    <td><?= ($value['transferFrom'] == null) ? lang('player.uw05') : $value['transferFrom'] . lang('player.uw06') ?></td>
                    <td><?= ($value['transferTo'] == null) ? lang('player.uw05') :  $value['transferTo'] . lang('player.uw06') ?></td>
                </tr>
        <?php 
                } 
            }
        ?>
    </tbody>
</table>