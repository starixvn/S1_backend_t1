<table class="table table-hover table-bordered" id="cashbackTable" style="margin: 0px 0 0 0; width: 100%;">
    <thead>
        <th></th>
        <th><?=lang('player.uc01');?></th>
        <th><?=lang('player.uc02');?></th>
    </thead>

    <tbody>
        <?php if (!empty($cashback_history)) {
	?>
            <?php foreach ($cashback_history as $key => $value) {?>
                <tr>
                    <td></td>
                    <td><?=$value['amount']?></td>
                    <td><?=$value['receivedOn']?></td>
                </tr>
        <?php
}
}
?>
    </tbody>
</table>