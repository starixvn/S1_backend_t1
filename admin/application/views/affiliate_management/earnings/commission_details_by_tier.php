<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th><?=lang('Level')?></th>
                <th class="text-right"><?=lang('Amount')?></th>
                <th class="text-right"><?=lang('sys.rate')?></th>
                <th class="text-right"><?=lang('Total')?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( isset($earnings['commission_amount_breakdown']) ): ?>
                <?php foreach ($earnings['commission_amount_breakdown'] as $breakdown): ?>
                    <tr>
                        <td><?=$breakdown['level']?></td>
                        <td align="right"><?=number_format($breakdown['amount'],2)?></td>
                        <td align="right"><?=$breakdown['rate'] * 100?>%</td>
                        <td align="right"><?=number_format( ($breakdown['amount'] * $breakdown['rate']) ,2)?></td>
                    </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td colspan="15"><?=lang('no_data_in_data_tables')?></td>
                </tr>
            <?php endif ?>
        </tbody>
        <tfoot>
        <?php if ( isset($earnings['commission_amount_breakdown']) ) {
            $total_commission = 0;
            foreach ($earnings['commission_amount_breakdown'] as $breakdown) {
                $total_commission = $total_commission + ($breakdown['amount'] * $breakdown['rate']);
            }?>
            <tr>
                <td><strong><?=lang('Total Commission');?></strong></td>
                <td colspan="2"></td>
                <td align="right"><?=number_format($total_commission, 2)?></td>
            </tr>
        <?php } ?>

        </tfoot>
    </table>
</div>