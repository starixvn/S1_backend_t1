<div id="linked_account_info_panel_body" data-file-info="player_info_linked_account_details.php" data-datatable-selector="#linkedAccountsTable">
    <div class="form-inline">
        <?php if(!empty($linked_accounts)): ?>
            <button class="btn btn-sm btn-portage linkAccountExportList" data-toggle='tooltip' data-placement='top' title='<?=lang('lang.exporttitle')?>' data-original-title='<?=lang('lang.exporttitle')?>'>
                <i class="glyphicon glyphicon-download"></i>
                <?=lang('lang.exporttitle')?>
            </button>
            <input type="hidden" id="playerUsernameInputTxt" value="<?=$player_username ?>">
        <?php endif; ?>
        <button class="btn btn-sm btn-scooter" onclick="showAddLinkedAccountModal()" data-toggle='tooltip' data-placement='top' title='<?=lang('Add Linked Account')?>' data-original-title='<?=lang('Add Linked Account')?>'>
            <i class="glyphicon glyphicon-plus-sign"></i>
            <?=lang('Add')?>
        </button>
    </div>
    <hr />
    <div class="clearfix">
        <table class="table table-bordered" id="linkedAccountsTable">
            <thead>
                <th>#</th>
                <th><?=lang('player.01')?></th><!-- username -->
                <th><?=lang('player.06')?></th><!-- mail -->
                <th><?=lang('Last Login IP')?></th>
                <th><?=lang('Linked Date')?></th>
                <th><?=lang('Remarks')?></th>
                <th><?=lang('lang.status')?></th>
                <th><?=lang('lang.action')?></th>
            </thead>
            <tbody>
            <?php if (!empty($linked_accounts)): ?>
                <?php $cnt = 0; ?>
                <?php foreach ($linked_accounts as $key): ?>
                    <?php $cnt++; ?>
                    <tr>
                        <td><?=$cnt?></td>
                        <td><a target="_blank" href="<?=site_url('player_management/userInformation/'.$key['playerId'])?>"><?=$key['username']?></a></td>
                        <td><?=$key['email'] ?: "N/A"?></td>
                        <td><?=$key['last_login_ip'] ?: "N/A"?></td>
                        <td><?=$key['link_datetime']?></td>
                        <td><?=$key['remarks'] ?: "N/A"?></td>
                        <td><?=$key['blocked'] ? lang('Blocked') : "Active";?></td>
                        <td><?=$key['action_edit_remarks']." ".$key['action_delete_remarks'] ?></td>
                    </tr>
                <?php endforeach;?>
            <?php else: ?>
                <tr>
                    <td colspan="8"><center><?=lang("No Linked Account") ?></center></td>
                </tr>
            </tbody>
            <?php endif; ?>
        </table>
    </div>
</div>


<!-- Linked Account Common Script Start -->
<?php $this->load->view('player_management/linked_account/linked_account_script'); ?>
<!-- Linked Account Common Script End -->

<script type="text/javascript">
    function linkedAccount(playerId){
        $('#add-link-account-modal .js-data-example-ajax').select2('destroy');

        // initial #add-link-account-modal div
        var username = $("#username").val();
        initSelect2WithGetNonLinkedAccountPlayers('#add-link-account-modal .js-data-example-ajax', username);
    }
</script>
