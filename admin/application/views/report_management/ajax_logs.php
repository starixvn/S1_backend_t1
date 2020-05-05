<table class="table table-striped table-hover table-condensed" style="margin: 30px 0 0 0;" id="myTable">
    <thead>
        <tr>
            <th><?= lang('report.log02'); ?></th>
            <th><?= lang('report.log03'); ?></th>
            <th><?= lang('report.log04'); ?></th>
            <th><?= lang('lang.action'); ?></th>
            <th><?= lang('report.log05'); ?></th>
            <th><?= lang('lang.status'); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php if(!empty($logs)) {
                foreach($logs as $row) {
        ?>
                    <tr title="<?= $row['description'] ?>">
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['management'] ?></td>
                        <td><?= $row['userRole'] ?></td>
                        <td><?= $row['action'] ?></td>
                        <td><?= $row['logDate'] ?></td>
                        <td>
                            <span class="help-block" style="<?= $row['status'] == 1 ? 'color:#ff6666;' : 'color:#66cc66;' ?>">
                                <?= $row['status'] == 0 ? lang('report.log06') : lang('report.log07') ?>
                            </span>
                        </td>
                    </tr>
        <?php   }
              }
         ?>
    </tbody>
    </table>

    <br/><br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>