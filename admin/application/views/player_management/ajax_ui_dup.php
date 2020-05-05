<div data-file-info="ajax_ui_dup.php" data-datatable-selector="#dup-table">
    <div class="dt-buttons btn-group">
        <a onclick="batch_tagging();" class="btn btn-sm btn-scooter">
            <span><?=lang('sys.batchAddTag')?></span>
        </a>
        <?php if($this->utils->isEnabledFeature('link_account_in_duplicate_account_list')): ?>
            <a onclick="batch_linking_accounts()" class="btn btn-sm btn-portage">
                <span><?=lang('sys.linkAcct')?></span>
            </a>
        <?php endif; ?>
    </div>
    <hr />
    <div class="clearfix">
        <table id="dup-table" class="table table-bordered">
            <thead>
                <?php $dup_enalbed_column = $this->utils->getConfig('duplicate_account_info_enalbed_condition') ?>

                <th class="unorderable-col"><input type="checkbox" class="setAll" onclick="setTagAll()"> <?=lang('sys.setTage');?></th>
                <?php if($this->utils->isEnabledFeature('link_account_in_duplicate_account_list')): ?>
                    <th class="unorderable-col"><input type="checkbox" class="linkAll" onclick="linkTagAll()"> <?=lang('sys.linkAcct');?></th>
                <?php endif; ?>

                <th><?=lang('Username')                 ?></th>
                <th data-col="rate"><?=lang('Total Rate')?></th>
                <th><?=lang('Possibly Duplicate')       ?></th>
                <th><?=lang('Related Total Rate')       ?></th>

                <?php if (in_array('ip', $dup_enalbed_column)) : ?>
                    <th><?=lang('Reg IP')                   ?></th>
                    <th><?=lang('Login IP')                 ?></th>
                    <th><?=lang('Deposit IP')               ?></th>
                    <th><?=lang('Withdraw IP')              ?></th>
                    <th><?=lang('Transfer Main To Sub IP')  ?></th>
                    <th><?=lang('Transfer Sub To Main IP')  ?></th>
                <?php endif; ?>

                <?php if (in_array('realname', $dup_enalbed_column)) : ?>
                    <th><?=lang('Real Name')            ?></th>
                <?php endif; ?>

                <?php if (in_array('password', $dup_enalbed_column)) : ?>
                    <th><?=lang('Password')             ?></th>
                <?php endif; ?>

                <?php if (in_array('email', $dup_enalbed_column)) : ?>
                    <th><?=lang('Email')                ?></th>
                <?php endif; ?>

                <?php if (in_array('mobile', $dup_enalbed_column)) : ?>
                    <th><?=lang('Mobile')               ?></th>
                <?php endif; ?>

                <?php if (in_array('address', $dup_enalbed_column)) : ?>
                    <th><?=lang('Address')          ?></th>
                <?php endif; ?>

                <?php if (in_array('city', $dup_enalbed_column)) : ?>
                    <th><?=lang('City')                 ?></th>
                <?php endif; ?>

                <?php if (in_array('country', $dup_enalbed_column)) : ?>
                    <th><?=lang('pay.country')          ?></th>
                <?php endif; ?>

                <?php if (in_array('cookie', $dup_enalbed_column)) : ?>
                    <th><?=lang('Cookie')               ?></th>
                <?php endif; ?>

                <?php if (in_array('referrer', $dup_enalbed_column)) : ?>
                    <th><?=lang('From')                 ?></th>
                <?php endif; ?>

                <?php if (in_array('device', $dup_enalbed_column)) : ?>
                    <th><?=lang('Device')               ?></th>
                <?php endif; ?>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
    var tag_title  = '<?=lang('sys.batchAddTag') ?>';
    var link_title = '<?=lang('sys.linkAcct') ?>';

    function dupAccounts(playerId) {
        var unorderable_col = [];

        var elem = $('#dup-table thead tr th');
        elem.filter(function(index){
            if ($(this).hasClass('unorderable-col')) {
                unorderable_col.push(index);
            }
        }).index();

        var rate_col = $('th[data-col="rate"]').index();
        $('#dup-table').DataTable({
            dom: "<'row'<'col-md-12'<'pull-right'B><'pull-right progress-container'>l<'dt-information-summary2 text-info pull-left' i>>><'table-responsive't><'row'<'col-md-12'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>>",
            autoWidth: false,
            searching: false,
            columnDefs: [
                {
                    orderable: false,
                    targets: unorderable_col
                }
            ],
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: ['btn-linkwater']
                }
            ],
            order: [
                [rate_col, 'desc']
            ],
            ajax: function (data, callback, settings) {
                $.post('/api/duplicate_account_info_by_playerid/' + playerId + '/for_player_info', {'is_count_related_total_rate': true}, function(data) {
                    callback(data);
                },'json');
            }
        });

        ATTACH_DATATABLE_BAR_LOADER.init('dup-table');
    }

    function batch_tagging(){
        var selectedRow = $('input[id="tags"]:checked');
        if( selectedRow.length == 0 ) {
            var content = '<?=lang("No player selected.")?>';
            error_modal(tag_title, content);
            return false;
        }

        var player_id_ar = [];
        selectedRow.each(function(){
            var player_id = $(this).val();
            if (player_id_ar.indexOf(player_id) < 0) {
                player_id_ar.push(player_id);
            }
        });
        var player_id_str = player_id_ar.join(',');

        $('input[name="playerIDs"]').val(player_id_str);
        $('input[name="subject_player_id"]').val($('#player_id').val());

        $('#addTagModal').modal('show');
    }

    function saveTags(){
        if( $('#all_tag_list').val() == "" ){
            var content = '<?=lang("No tag selected.")?>';
            error_modal(tag_title, content);
        }

        $.ajax({
            url: '/player_management/tagPlayerByBatch',
            type: 'POST',
            async: false,
            data: $('form[id="add_batch_tags"]').serialize(),
            success: function(resp) {
                $('#addTagModal').modal('hide');

                var table = $('#dup-table').DataTable();
                table.ajax.reload();

                if (resp.success == false) {
                    var content = '<?= lang('Error') ?> : ' + resp.message;
                    error_modal(tag_title, content);
                } else {
                    var content = '<?= lang('success') ?>';
                    success_modal(tag_title, content);
                }
            }
        });
    }

    function batch_linking_accounts(){
        var selectedRow = $('input[id="linkaccts"]:checked');
        var player_id_ar = [];
        var player_username_ar = [];

        if( selectedRow.length == 0 ){
            var content = '<?=lang("No player selected.")?>';
            error_modal(link_title, content);
            return false;
        }

        selectedRow.each(function(){
            var player_id = $(this).val();
            if (player_id_ar.indexOf(player_id) < 0) {
                player_id_ar.push(player_id);
            }
            var player_username = $(this).attr('username_val');
            if (player_username_ar.indexOf(player_username) < 0) {
                player_username_ar.push(player_username);
            }
        });
        var player_id_str = player_id_ar.join(',');
        var player_username_str = player_username_ar.join(',');

        $('input[name="linkAcctsplayerIDs"]').val(player_id_str);
        $('#linkAcctsplayerUsernames').text(player_username_str);
        $('input[name="linkAcctsplayerUserId"]').val($('#player_id').val());

        $('#addLinkAcctModal').modal('show');
    }

    function saveLinkAccounts() {
        $.ajax({
            url: '/player_management/linkAcctByBatch',
            type: 'POST',
            async: false,
            data: $('form[id="link_batch_accts"]').serialize(),
            success: function(resp) {
                $('#addLinkAcctModal').modal('hide');

                var table = $('#dup-table').DataTable();
                table.ajax.reload();

                var title = '<?=lang('sys.linkAcct') ?>';
                if (resp.success == false) {
                    var content = '<?= lang('Error') ?> : ' + resp.message;
                    error_modal(title, content);
                } else {
                    var content = '<?= lang('success') ?>';
                    success_modal(title, content);
                }
            }
        });
    }


    function setTagAll(){
        if($(".setAll").prop('checked') === true){
            $(".checktags").prop('checked', true);
        } else {
            $(".checktags").prop('checked', false);
        }
    }

    function countTagBoxes() {
        var checkboxes = $('.checktags');
        var totalCheckboxes = checkboxes.size();
        if(checkboxes.filter(':checked').size() !== totalCheckboxes) {
            $('.setAll').prop('checked', false);
        } else {
            $('.setAll').prop('checked', true);
        }
    }

    function linkTagAll() {
        if($(".linkAll").prop('checked') === true){
            $(".checkaccts").prop('checked', true);
        } else {
            $(".checkaccts").prop('checked', false);
        }
    }

    function countLinkBoxes() {
        var checkboxes = $('.checkaccts');
        var totalCheckboxes = checkboxes.size();
        if(checkboxes.filter(':checked').size() !== totalCheckboxes) {
            $('.linkAll').prop('checked', false);
        } else {
            $('.linkAll').prop('checked', true);
        }
    }

    $(document).on("click",".dup_modal_trigger",function(e) {
        e.preventDefault();
        var username = $(this).data('username');
        get_duplicate_account_detail_by_username_for_modal(username);
    });

    function get_duplicate_account_detail_by_username_for_modal(username) {
        $('#duplicateAccountModal').modal('show');
        $('#duplicateTableModal').DataTable({
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            autoWidth: false,
            searching: false,
            destroy: true,
            order: [ 1, 'desc' ],
            buttons: [{
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ],
                className: ['btn-linkwater'],
            }],
            ajax: function (data, callback, settings) {
                $.post(base_url + "api/duplicate_account_info/" + username, {'is_request_for_modal': true}, function(data) {
                    callback(data);
                },'json');
            },
        });
    }
</script>
