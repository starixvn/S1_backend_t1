    <!-- table info {{{1 -->
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-bullhorn"></i>
            <?php $str = ''; ?>
            <?php if(isset($username)) $str = ' ('. lang('Username'). ': ' . $username . ')';?>
            <?php echo lang('Duplicate Accounts Details').$str; ?>
            <!--
            <a href="javascript:window.close();" class="bookmark-this btn btn-info btn-xs pull-right" style="margin-right: 4px;">
                <?= lang('Close') ?> <i class="fa fa-times"></i>
            -->
            <a href="javascript: window.history.go(-1);" class="bookmark-this btn btn-xs pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> " style="margin-right: 4px;">
                <i class="fa fa-chevron-left"></i>
                <?= lang('Back to list') ?>
            </a>
        </h4>
    </div>
    <div class="panel-body">
        <div id="logList" class="table-responsive">
            <table id="duplicateTable" class="table table-striped table-hover table-bordered"  width=100%>
                <thead>
                    <tr>
                        <th> <?php echo lang('Username');?>
                            <!-- (<?php echo lang('Similar');?>: <?= $this->duplicate_account->getRating('username', 'rate_similar'); ?>)  -->
                        </th>
                        <th> <?php echo lang('Total Rate');?></th>
                        <th> <?php echo lang('Possibly Duplicate');?></th>
                        <th> <?php echo lang('Related Total Rate');?></th>
                        <?php if (in_array('ip', $dup_enalbed_column)) : ?>
                        <th> <?php echo lang('Reg IP');?>
                            <!-- (<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) -->
                        </th>
                        <th> <?php echo lang('Login IP');?>
                            <!-- (<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) -->
                        </th>
                        <th> <?php echo lang('Deposit IP');?>
                            <!-- (<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) -->
                        </th>
                        <th> <?php echo lang('Withdraw IP');?>
                            <!-- (<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) -->
                        </th>
                        <th> <?php echo lang('Transfer Main To Sub IP');?>
                            <!-- (<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) -->
                        </th>
                        <th> <?php echo lang('Transfer Sub To Main IP');?>
                            <!-- (<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) -->
                        </th>
                        <?php endif; ?>

                        <?php if (in_array('realname', $dup_enalbed_column)) : ?>
                        <th> <?php echo lang('Real Name');?>
                            <!-- (<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('realname', 'rate_exact') ?> / <?php echo lang('Similar');?>: <?= $this->duplicate_account->getRating('realname', 'rate_similar'); ?>) -->
                        </th>
                        <?php endif; ?>

                        <?php if (in_array('password', $dup_enalbed_column)) : ?>
                        <th> <?php echo lang('Password');?>
                            <!-- (<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('password', 'rate_exact'); ?>) -->
                        </th>
                        <?php endif; ?>

                        <?php if (in_array('email', $dup_enalbed_column)) : ?>
                        <th> <?php echo lang('Email');?>
                            <!-- (<?php echo lang('Similar');?>: <?= $this->duplicate_account->getRating('email', 'rate_similar'); ?>) -->
                        </th>
                        <?php endif; ?>

                        <?php if (in_array('mobile', $dup_enalbed_column)) : ?>
                        <th> <?php echo lang('Mobile');?>
                            <!-- (<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('phone', 'rate_exact'); ?>) -->
                        </th>
                        <?php endif; ?>

                        <?php if (in_array('address', $dup_enalbed_column)) : ?>
                        <th> <?php echo lang('Address');?>
                            <!-- (<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('address', 'rate_exact'); ?>) -->
                        </th>
                        <?php endif; ?>

                        <?php if (in_array('city', $dup_enalbed_column)) : ?>
                        <th> <?php echo lang('City');?>
                            <!-- (<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('city', 'rate_exact'); ?>) -->
                        </th>
                        <?php endif; ?>

                        <?php if (in_array('country', $dup_enalbed_column)) : ?>
                        <th> <?php echo lang('pay.country');?></th>
                        <?php endif; ?>

                        <?php if (in_array('cookie', $dup_enalbed_column)) : ?>
                        <th> <?php echo lang('Cookie');?>
                            <!-- (<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('cookie', 'rate_exact'); ?>) -->
                        </th>
                        <?php endif; ?>

                        <?php if (in_array('referrer', $dup_enalbed_column)) : ?>
                        <th> <?php echo lang('From');?>
                            <!-- (<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('referrer', 'rate_exact'); ?>) -->
                        </th>
                        <?php endif; ?>

                        <?php if (in_array('device', $dup_enalbed_column)) : ?>
                        <th> <?php echo lang('Device');?>
                            <!-- (<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('user_agent', 'rate_exact'); ?>) -->
                        </th>
                        <?php endif; ?>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="panel-footer"></div>
    </div>
</div>
<!-- table info }}}1 -->
<?php
    include APPPATH . "/views/report_management/duplicate_account_modal.php";
?>


<script type="text/javascript">
    function get_duplicate_account_detail_by_username_for_modal(username) {
        $('#duplicateAccountModal').modal('show');

        $('#duplicateTableModal').DataTable({
            destroy: true,
            autoWidth: false,
            searching: false,
            //processing: true,
            //serverSide: true,
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            order: [ 1, 'desc' ],
                /*
                "responsive": {
                    details: {
                        type: 'column'
                    }
                },  */
            buttons: [
                {
                    extend: 'colvis',
                        postfixButtons: [ 'colvisRestore' ]
                }
            ],
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            ajax: function (data, callback, settings) {
                $.post(base_url + "api/duplicate_account_info/" + username, {'is_request_for_modal': true}, function(data) {
                    callback(data);
                },'json');
            },
        });
    }


$(document).ready(function() {
    $(document).on("click",".dup_modal_trigger",function(e) {
        e.preventDefault();
        var username = $(this).data('username');
        get_duplicate_account_detail_by_username_for_modal(username);
    });

    <?php /*
        var playerId = <?= $player_id ?>;
    */ ?>
    var username = '<?= $username ?>';

    $('#duplicateTable').DataTable({
        autoWidth: false,
        searching: false,
        //processing: true,
        //serverSide: true,
        pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
        order: [ 1, 'desc' ],
            /*
            "responsive": {
                details: {
                    type: 'column'
                }
            },  */
        buttons: [
            {
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ],
                className: "<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>"
            }
        ],
        dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        ajax: function (data, callback, settings) {
            $.post(base_url + "api/duplicate_account_info/" + username, {'is_count_related_total_rate': true}, function(data) {
                callback(data);
            },'json');
        },
    });

    /*
        $('#duplicateTable').DataTable({
order: [[16, 'desc']],
ajax: function (data, callback, settings) {
$.post('api/dup_accounts_detail/' + playerId, data, function(data) {
    //$.post(base_url + 'api/dup_accounts_detail/' + playerId, data, function(data) {
    callback(data);
    },'json');
    },
    });
    $("[data-toggle=popover]").popover({html:true});
     */
} );
</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of view_duplicate_account_report.php
