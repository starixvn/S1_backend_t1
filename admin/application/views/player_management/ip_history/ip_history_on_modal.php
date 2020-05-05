<div class="table-responsive">
     <table class="table table-hover table-bordered" id="IPHistoryTable">
        <thead>
            <th><?=lang('traffic.playerip');?></th>
            <th><?=lang('player.sd08');?></th>
            <th><?=lang('pay.referrer');?></th>
            <th><?=lang('pay.useragent');?></th>
            <th><?=lang('player.ub01');?></th>
        </thead>

        <tbody>
    <?php                                
    $cnt = 0;
    if (!empty($ip_history)) {
        foreach ($ip_history as $key) {
            $cnt++;
                ?>
                <tr>
                    <td><?=$key['ip'] ?: "N/A"?></td>
                    <td><?=$key['type'] ? lang('http.type.' . $key['type']) : "N/A"?></td>
                    <td><?=$key['referrer']?></td>
                    <td><?=$key['device'] ?: "N/A"?></td>
                    <td><?=$key['createdat'] ?: "N/A"?></td>
                </tr>
    <?php }
        }?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function(){
        $('#IPHistoryTable').DataTable({
            autoWidth: true,
            searching: true,
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>

            dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ]
                },
            ],

            order: [[4, 'desc']],

        });
    });
</script>