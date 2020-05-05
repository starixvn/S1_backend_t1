<div class="table-responsive">
     <table class="table table-hover table-bordered" id="linkedAccountsTable">
        <thead>
            <th>#</th>
            <th><?=lang('player.01')?></th><!-- username -->
            <th><?=lang('player.06')?></th><!-- mail -->
            <th><?=lang('Last Login IP')?></th>
            <th><?=lang('Linked Date')?></th>
            <th><?=lang('Remarks')?></th>
            <th><?=lang('lang.status')?></th>
        </thead>

        <tbody>
    <?php                                
    $cnt = 0;
    if (!empty($linked_accounts)) {
        foreach ($linked_accounts as $key) {
            $cnt++;
                ?>
                <tr>
                    <td><?=$cnt?></td>
                    <td><a target="_blank" href="<?php echo site_url('player_management/userInformation/'.$key['playerId'])?>"><?=$key['username']?></a></td>
                    <td><?=$key['email'] ?: "N/A"?></td>
                    <td><?=$key['last_login_ip'] ?: "N/A"?></td>
                    <td><?=$key['link_datetime']?></td>
                    <td><?=$key['remarks'] ?: "N/A"?></td>
                    <td><?=$key['blocked'] ? lang('Blocked') : "Active";?></td>
                </tr>
    <?php }
        }?>
         </tbody>
    </table>
</div>

<script>
    $(document).ready(function(){
        $('#linkedAccountsTable').DataTable({
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
             dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'text-center'r><'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
             buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ]
                },                
            ],
            "order": [ 0, 'asc' ]
        });
    });
</script>