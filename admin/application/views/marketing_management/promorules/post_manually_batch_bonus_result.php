
<div class="container">

    <div class="panel panel-primary">

        <div class="panel-heading">
            <h4 class="panel-title"><span style="font-weight: bold;"><?php echo lang('batch.batch_add_bonus_result');?></span></h4>
        </div>

        <div class="panel-body">

            <table class="table table-condensed">
                <tbody>
                <tr>
                    <td><?=lang('batch.success_amount') ?></td>
                    <td><?php echo $success_amount; ?></td>
                </tr>
                <tr>
                    <td><?=lang('batch.success_number') ?></td>
                    <td><?php echo $success_count; ?></td>
                </tr>
                <tr>
                    <td><?=lang('batch.failures_number') ?></td>
                    <td><?php echo $failed_count; ?></td>
                </tr>
                </tbody>
            </table>


            <table class="table table-condensed">
                <caption><?=lang('batch.failures_batch_add_bonus_list') ?></caption>
                <thead>
                <tr>
                    <th><?=lang('Player Username') ?></th>
                    <th><?=lang('batch.failures_reason') ?></th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($failed_usernames as $username => $reason): ?>
                <tr>
                    <td><?php echo $username; ?></td>
                    <td><?php echo $reason; ?></td>
                </tr>
                <?php endforeach; ?>

                </tbody>
            </table>

        </div>

        <div class="panel-footer"></div>
    </div>


</div>

