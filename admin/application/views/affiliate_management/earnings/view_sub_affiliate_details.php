<?php
$enable_tier = false;
if(isset($settings['enable_commission_by_tier']) && $settings['enable_commission_by_tier'] == true){
    $enable_tier = true;
}
?>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th><?=lang('Username')?></th>
                <th class="text-center"><?=lang('Level')?></th>
                <th class="text-center"><?=lang('Commission')?></th>
                <th class="text-center"><?=lang('Parent Commission Rate')?></th>
                <th class="text-center"><?=lang('Parent Commission Amount')?></th>
            </tr>
        </thead>
        <tbody>
        <?php if ( count($sub_aff_commission_breakdown) > 0 ): ?>
            <?php foreach ($sub_aff_commission_breakdown as $breakdown): ?>
                <tr>
                    <td><?=$breakdown['username']?></td>
                    <td align="center"><?=$breakdown['level']?></td>
                    <?php if(!$enable_tier){ ?>
                        <td align="center"><?=number_format($breakdown['commission_amount'],2)?></td>
                    <?php }else { ?>
                        <td align="center"><?=number_format($breakdown['commission_amount_by_tier'],2)?></td>
                    <?php } ?>
                    <td align="center"><?=$breakdown['commission_rate']?>%</td>
                    <td align="center"><?=number_format($breakdown['commission_from_sub_affiliate'],2)?></td>
                </tr>
            <?php endforeach ?>
        <?php else: ?>
            <tr>
                <td colspan="5" align="center"><?=lang('no_data_in_data_tables')?></td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
</div>