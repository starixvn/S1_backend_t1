<?php
/**
 *   filename:   credit_transactions.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-02
 *   @brief:     view credit transactions for agency sub-system
 */

// set display according to configurations
$panelOpenOrNot = $this->config->item('default_open_search_panel') ? '' : 'collapsed';
$panelDisplayMode = $this->config->item('default_open_search_panel') ? '' : 'collapse in';
if (isset($_GET['search_on_date'])) {
	$search_on_date = $_GET['search_on_date'];
} else {
	$search_on_date = true;
}
?>
<div class="container">
    <!-- search form {{{1 -->
    <form class="form-horizontal" id="search-form">
        <div class="panel panel-primary">
            <!-- panel heading {{{2 -->
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fa fa-search"></i>
                    <?=lang("lang.search")?>
                    <span class="pull-right">
                        <a data-toggle="collapse" href="#collapseAgentList"
                            class="btn btn-info btn-xs <?=$panelOpenOrNot?>">
                        </a>
                    </span>
                </h4>
            </div>
            <!-- panel heading }}}2 -->

            <div id="collapseAgentList" class="panel-collapse <?=$panelDisplayMode?>">
                <!-- panel body {{{2 -->
                <div class="panel-body">
                    <div class="row">
                        <?php if ( ! $this->utils->isEnabledFeature('hide_sub_affiliates_on_affiliate')): ?>
                            <div class="col-md-6">
                                <label class="control-label"><?=lang('Affiliate Username');?></label>
                                <input type="text" name="affiliate_username" class="form-control input-sm"
                                placeholder=' <?=lang('Enter Affiliate Username');?>' value="<?=@$affiliate_username?>"/>
                            </div>
                        <?php endif ?>
                        <div class="col-md-6">
                            <label class="control-label"><?=lang('Player Username');?></label>
                            <input type="text" name="player_username" class="form-control input-sm"
                            placeholder=' <?=lang('Enter Player Username');?>' value="<?=@$player_username?>"/>
                        </div>

                        <div class="col-md-6">
                            <label class="control-label"><?=lang('report.sum02')?></label>
                            <div class="input-group">
                                <input class="form-control input-sm dateInput" id="search-date" data-start="#date_from" data-end="#date_to" data-time="true"/>
                                <input type="hidden" id="date_from" name="date_from" value="<?=@$date_from?>"/>
                                <input type="hidden" id="date_to" name="date_to" value="<?=@$date_to?>"/>
                                <span class="input-group-addon input-sm">
                                    <input type="checkbox" name="search_on_date" id="search_on_date" value="1"
                                    <?php if ($search_on_date) {echo 'checked';} ?>/>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="control-label"><?php echo lang('Minimum Amount'); ?></label>
                            <input type="number" name="min_credit_amount" class="form-control input-sm user-success" min="0" placeholder="<?php echo lang('Enter Minimum Amount'); ?>" value="">
                        </div>

                        <div class="col-md-3">
                            <label class="control-label"><?php echo lang('Maximum Amount'); ?></label>
                            <input type="number" name="max_credit_amount" class="form-control input-sm user-success" min="0" placeholder="<?php echo lang('Enter Maximum Amount'); ?>" value="">
                        </div>

                        <!--
                        <div class="col-md-6 col-lg-6">
                            <label class="control-label"><?=lang('IP Used');?></label>
                            <input type="text" name="ip_used" id="ip_used" class="form-control input-sm"
                            placeholder=' <?=lang('Enter IP Address for Transactions');?>' />
                        </div> -->

                        <div class="col-md-12" style="margin-top: 10px;">

                            <fieldset>
                                <legend><?=lang('player.ut02')?></legend>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input id="transaction_type_1" name="transaction_type" value="1" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_1"><?=lang('transaction.transaction.type.1')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_2" name="transaction_type" value="2" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_2"><?=lang('transaction.transaction.type.2')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_3" name="transaction_type" value="3" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_3"><?=lang('transaction.transaction.type.3')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_4" name="transaction_type" value="4" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_4"><?=lang('transaction.transaction.type.4')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_5" name="transaction_type" value="5" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_5"><?=lang('transaction.transaction.type.5')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_6" name="transaction_type" value="6" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_6"><?=lang('transaction.transaction.type.6')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_7" name="transaction_type" value="7" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_7"><?=lang('transaction.transaction.type.7')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_8" name="transaction_type" value="8" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_8"><?=lang('transaction.transaction.type.8')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_9" name="transaction_type" value="9" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_9"><?=lang('transaction.transaction.type.9')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_10" name="transaction_type" value="10" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_10"><?=lang('transaction.transaction.type.10')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_11" name="transaction_type" value="11" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_11"><?=lang('transaction.transaction.type.11')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_12" name="transaction_type" value="12" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_12"><?=lang('transaction.transaction.type.12')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_13" name="transaction_type" value="13" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_13"><?=lang('transaction.transaction.type.13')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_14" name="transaction_type" value="14" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_14"><?=lang('transaction.transaction.type.14')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_15" name="transaction_type" value="15" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_15"><?=lang('transaction.transaction.type.15')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_19" name="transaction_type" value="19" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_19"><?=lang('transaction.transaction.type.19')?></label>
                                        <br/>
                                    </div>
                            </fieldset>
                        </div>
                        <div class="col-md-12" style="margin-top: 10px;">
                            <div class="pull-right">
                                <input type="button" class="btn btn-sm btn-info btn_today" value="<?php echo lang('Today');?>">
                                <input type="button" class="btn btn-sm btn-info btn_yesterday" value="<?php echo lang('Yesterday');?>">
                                <input type="button" class="btn btn-sm btn-info btn_this_week" value="<?php echo lang('This Week');?>">
                                <input type="button" class="btn btn-sm btn-info btn_last_week" value="<?php echo lang('Last Week');?>">
                                <input type="button" class="btn btn-sm btn-info btn_this_month" value="<?php echo lang('This Month');?>">
                                <input type="button" class="btn btn-sm btn-info btn_last_month" value="<?php echo lang('Last Month');?>">
                                <input type="button" class="btn btn-sm btn-info btn_this_year" value="<?php echo lang('This Year');?>">
                            </div>

                            <input type="button" value="<?=lang('lang.reset');?>" class="btn btn-default btn-sm" onclick="window.location.href='<?php echo site_url('affiliate/affiliate_credit_transactions'); ?>'">
                            <input class="btn btn-sm btn-primary" type="submit" value="<?=lang('lang.search');?>" />
                        </div>
                    </div> <!-- button row }}}3 -->
                </div>
                <!-- panel body }}}2 -->
            </div>
        </div>
    </form> <!-- end of search form }}}1 -->

    <!-- panel for Credit Transactions table {{{1 -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="fa fa-money"></i> <?=lang('Transactions');?></h4>
        </div>
        <div class="panel-body">
            <!-- credit transactions table {{{2 -->
            <div class="table-responsive">
                <table class="table table-striped table-hover table-condensed" id="credit_transactions_table">
                    <thead>
                        <tr>
                            <th><?=lang('Date');?></th>
                            <th><?=lang('Transaction Type');?></th>
                            <th><?=lang('Subwallet');?></th>
                            <th><?=lang('Player');?></th>
                            <th><?=lang('Amount');?></th>
                            <th><?=lang('Before Balance');?></th>
                            <th><?=lang('After Balance');?></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <!--end of credit transactions table }}}2 -->
        </div>
        <div class="panel-footer"></div>
    </div>
    <!-- panel for credit transactions table }}}1 -->
</div>
<!-- JS code {{{1 -->
<script type="text/javascript">
$(document).ready(function(){
    <?php $agent_status = $this->session->userdata('agent_status'); ?>
    <?php if($agent_status == 'suspended') { ?>;
    set_suspended_operations();
    <?php } ?>

    $('.btn_yesterday').click(function(){
        //today
        $from=moment().subtract(1,'days').startOf('day');
        $to=moment().subtract(1,'days').endOf('day');
        submitDateRange($from, $to);
    })

    $('.btn_today').click(function(){
        //today
        $from=moment().startOf('day');
        $to=moment().endOf('day');
        submitDateRange($from, $to);
    })

    $('.btn_last_week').click(function(){
        //this week
        $from=moment().subtract(1,'weeks').startOf('isoWeek');
        $to=moment().subtract(1,'weeks').endOf('isoWeek');

        submitDateRange($from, $to);
    })

    $('.btn_this_week').click(function(){
        //this week
        $from=moment().startOf('isoWeek');
        $to=moment().endOf('day');

        submitDateRange($from, $to);
    })

    $('.btn_last_month').click(function(){
        //this month
        $from=moment().subtract(1,'months').startOf('month');
        $to=moment().subtract(1,'months').endOf('month');

        submitDateRange($from, $to);
    })

    $('.btn_this_month').click(function(){
        //this month
        $from=moment().startOf('month');
        $to=moment().endOf('day');

        submitDateRange($from, $to);
    })

    $('.btn_this_year').click(function(){
        //this year
        $from=moment().startOf('year');
        $to=moment().endOf('day');

        submitDateRange($from, $to);
    })

    $('#search-form').submit( function(e) {
        e.preventDefault();
        dataTable.ajax.reload();
        set_agent_operations();
    });

    $('#search-form input[type="text"]').keypress(function (e) {
        if (e.which == 13) {
            $('#search-form').trigger('submit');
        }
    });

    // DataTable settings {{{2
    var dataTable = $('#credit_transactions_table').DataTable({
        lengthMenu: [50, 100, 250, 500, 1000],
        autoWidth: false,
        <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
            stateSave: true,
        <?php } else { ?>
            stateSave: false,
        <?php } ?>
        searching: false,
        dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        buttons: [
            { extend: 'colvis', postfixButtons: [ 'colvisRestore' ] }
        ],
        columnDefs: [
            { className: 'text-right', targets: [ 3, 4, 5 ] },
        ],
        order: [ 0, 'desc' ],
        processing: true,
        serverSide: true,
        ajax: function (data, callback, settings) {
            data.extra_search = $('#search-form').serializeArray();
            $.post(base_url + "api/affiliate_credit_transactions", data, function(data) {
                callback(data);
            },'json');
        },
    });
    // DataTable settings }}}2

});

function submitDateRange($from, $to){
    $('#date_from').val($from.format('YYYY-MM-DD HH:mm:ss'));
    $('#date_to').val($to.format('YYYY-MM-DD HH:mm:ss'));
    var dateInput=$('#search-date');

    dateInput.data('daterangepicker').setStartDate($from);
    dateInput.data('daterangepicker').setEndDate($to);

    $('#search-form').submit();
}
</script>
<!-- JS code }}}1 -->

<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of credit_transactions.php
