<table class="table table-hover table-bordered" id="adjustmentTable" style="margin: 0px 0 0 0; width: 100%;">
    <thead>
        <th></th>
        <th><?= lang('player.uab01'); ?></th>
        <th><?= lang('player.uab02'); ?></th>
        <th><?= lang('player.uab03'); ?></th>
        <th><?= lang('player.uab04'); ?></th>
        <th><?= lang('player.uab05'); ?></th>
        <th><?= lang('player.uab06'); ?></th>
    </thead>

    <tbody>
        <?php if(!empty($balance_adjustment_history)) { ?>
            <?php foreach ($balance_adjustment_history as $key => $value) { ?>
                <tr>
                    <td></td>
                    <td><?= $value['adjustedOn'] ?></td>
                    <td><?= ($value['wallet'] == null) ? lang('player.uab07') : $value['wallet'] ?></td>
                    <td><?= $value['amountChanged'] ?></td>
                    <td><?= $value['newBalance'] ?></td>
                    <td><?= $value['reason'] ?></td>
                    <td><?= $value['adjust'] ?></td>
                </tr>
        <?php 
                } 
            }
        ?>
    </tbody>
</table>