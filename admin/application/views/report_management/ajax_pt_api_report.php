<table class="table table-striped table-hover table-condensed" style="table-layout: fixed; word-wrap: break-word;">
    <thead>
        <tr>
            <th><?= lang('report.api02'); ?></th>                                        
            <th><?= lang('report.api03'); ?></th>
            <th><?= lang('report.api04'); ?></th>
            <th><?= lang('report.api05'); ?></th>
            <th><?= lang('report.api06'); ?></th>
            <th><?= lang('report.api07'); ?></th>
            <th><?= lang('lang.status'); ?></th>
            <th><?= lang('lang.action'); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php if(!empty($logs)) {
                foreach($logs as $row) {
        ?>
                    <tr title="<?= $row['description'] ?>">
                        <td><?= $row['reportType'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['errorReturn'] ?></td>
                        <td><?= $row['description'] ?></td>
                        <td class="col-md-4"><?= $row['apiCallSyntax'] ?></td>
                        <td><?= $row['errorTimeStamp'] ?></td>
                        <td><?= $row['status'] ?></td>    
                        <td><a class="reportActionBtn" href="<?= BASEURL . 'pt_report_management/resolvePTIssueReport/'.$row['issueReportPtApiId'] ?>">
                            <span class="btn-sm btn-info review-btn">
                                <?= lang('report.api08'); ?>
                            </span>
                        </a></td>                                                
                    </tr>
        <?php   }
              }
              else{
                    echo '<tr>';
                    echo "<td colspan=6 style='text-align:center;'>" . lang('report.api09') . "</td>";
                    echo '</td>';
              }
         ?>
    </tbody>
</table>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>