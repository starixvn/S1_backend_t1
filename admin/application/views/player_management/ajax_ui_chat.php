<table class="table table-hover table-bordered" id="messageTable" style="margin: 0px 0 0 0; width: 100%;">
    <thead>
        <th></th>
        <th><?= lang('player.uct01'); ?></th>
        <th><?= lang('player.uct03'); ?></th>
        <th><?= lang('player.uct04'); ?></th>
        <th><?= lang('player.uct05'); ?></th>
        <th><?= lang('player.uct02'); ?></th>
    </thead>

    <tbody>
        <?php if(!empty($chat_history)) { ?>
            <?php foreach ($chat_history as $key => $value) { ?>
                <tr>
                    <td></td>
                    <td><?= $value['subject'] ?></td>
                    <td><?= $value['sender'] ?></td>
                    <td><?= $value['recipient'] ?></td>
                    <td><?= $value['message'] ?></td>
                    <td><?= $value['date'] ?></td>
                </tr>
        <?php 
                } 
            }
        ?>
    </tbody>
</table>