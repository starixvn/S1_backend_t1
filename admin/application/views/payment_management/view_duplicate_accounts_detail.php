<!-- table info {{{1 -->
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-bullhorn"></i>
            <?php $str = ''; ?>
            <?php if(isset($username)) $str = ' ('. lang('Username'). ': ' . $username . ')';?>
            <?php echo lang('Duplicate Accounts Details').$str; ?>
        </h4>
    </div>
    <div class="panel-body">
        <div id="logList" class="table-responsive">
            <table id="duplicateTable" class="table table-striped table-hover table-bordered"  width=100%>
                <thead>
                    <tr>
                        <th> <?php echo lang('Username');?>(<?php echo lang('Similar');?>: <?= $this->duplicate_account->getRating('username', 'rate_similar'); ?>) </th>
                        <th> <?php echo lang('Total Rate');?></th>
                        <th> <?php echo lang('Reg IP');?>(<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
                        <th> <?php echo lang('Login IP');?>(<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
                        <th> <?php echo lang('Deposit IP');?>(<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
                        <th> <?php echo lang('Withdraw IP');?>(<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
                        <th> <?php echo lang('Transfer Main To Sub IP');?>(<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
                        <th> <?php echo lang('Transfer Sub To Main IP');?>(<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
                        <th> <?php echo lang('Real Name');?>(<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('realname', 'rate_exact') ?> / <?php echo lang('Similar');?>: <?= $this->duplicate_account->getRating('realname', 'rate_similar'); ?>) </th>
                        <th> <?php echo lang('Password');?>(<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('password', 'rate_exact'); ?>) </th>
                        <th> <?php echo lang('Email');?>(<?php echo lang('Similar');?>: <?= $this->duplicate_account->getRating('email', 'rate_similar'); ?>) </th>
                        <th> <?php echo lang('Mobile');?>(<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('phone', 'rate_exact'); ?>) </th>
                        <th> <?php echo lang('Address');?>(<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('address', 'rate_exact'); ?>) </th>
                        <th> <?php echo lang('City');?>(<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('city', 'rate_exact'); ?>) </th>
                        <th> <?php echo lang('Cookie');?>(<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('cookie', 'rate_exact'); ?>) </th>
                        <th> <?php echo lang('From');?>(<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('referrer', 'rate_exact'); ?>) </th>
                        <th> <?php echo lang('Device');?>(<?php echo lang('Exact');?>: <?= $this->duplicate_account->getRating('user_agent', 'rate_exact'); ?>) </th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="panel-footer"></div>
    </div>
</div>
<!-- table info }}}1 -->

<script type="text/javascript">
$(document).ready(function() {
    var playerId = <?=$player_id ?>;
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
    $('#duplicateTable').DataTable({
        autoWidth: false,
            searching: false,
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
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
            "order": [ 1, 'desc' ],
            //processing: true,
            //serverSide: true,
            ajax: function (data, callback, settings) {
                $.post(base_url + "api/dup_accounts_detail/" + playerId, data, function(data) {
                    callback(data);
                },'json');
            },
    });
} );
</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of view_duplicate_account_report.php
