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
<div class="content-container">
    <!-- search form {{{1 -->
    <form class="form-horizontal" id="search-form">
        <input type="hidden" name="parent_id" value="<?php echo $parent_id?>"/>
        <input type="hidden" name="parent_name" value="<?php echo $parent_name?>"/>
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
                        <div class="col-md-offset-2 col-md-4">
                            <label class="control-label"><?=lang('Agent Username');?></label>
                            <input type="text" name="agent_name" class="form-control input-sm"
                            placeholder=' <?=lang('Enter Agent Username');?>' value="<?=@$agent_username?>"/>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label"><?=lang('Player Username');?></label>
                            <input type="text" name="player_username" class="form-control input-sm"
                            placeholder=' <?=lang('Enter Player Username');?>' value="<?=@$player_username?>"/>
                        </div>

                        <div class="col-md-offset-2 col-md-4">
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

                        <div class="col-md-2">
                            <label class="control-label">Minimum Credit Amount</label>
                            <input type="number" name="min_credit_amount" class="form-control input-sm user-success" min="0" placeholder=" Enter Minimum Credit Amount" value="">
                        </div>

                        <div class="col-md-2">
                            <label class="control-label">Maximum Credit Amount</label>
                            <input type="number" name="max_credit_amount" class="form-control input-sm user-success" min="0" placeholder=" Enter Maximum Credit Amount" value="">
                        </div>

                        <!--
                        <div class="col-md-6 col-lg-6">
                            <label class="control-label"><?=lang('IP Used');?></label>
                            <input type="text" name="ip_used" id="ip_used" class="form-control input-sm"
                            placeholder=' <?=lang('Enter IP Address for Transactions');?>' />
                        </div> -->

                        <div class="col-md-offset-2 col-md-8" style="margin-top: 10px;">

                            <fieldset>
                                <legend><?=lang('player.ut02')?></legend>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input id="selectAll" type="checkbox" checked="checked" onchange="$('.checkall').prop('checked', this.checked);">
                                        <label for="selectAll"><?=lang('lang.selectall')?></label>
                                        <br/>
                                    </div>
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
                                        <input id="transaction_type_24" name="transaction_type" value="24" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_24"><?=lang('transaction.transaction.type.24')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_25" name="transaction_type" value="25" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_25"><?=lang('transaction.transaction.type.25')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_26" name="transaction_type" value="26" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_26"><?=lang('transaction.transaction.type.26')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_27" name="transaction_type" value="27" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_27"><?=lang('transaction.transaction.type.27')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_28" name="transaction_type" value="28" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_28"><?=lang('transaction.transaction.type.28')?></label>
                                        <br/>
                                    </div>
                                    <div class="col-md-4">
                                        <input id="transaction_type_29" name="transaction_type" value="29" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_29"><?=lang('transaction.transaction.type.29')?></label>
                                        <br/>
                                    </div>
                                    <?php
                                    if ($this->utils->isEnabledFeature('rolling_comm_for_player_on_agency') && $this->utils->isEnabledRollingCommByAgentInSession()){
                                    ?>
                                    <div class="col-md-4">
                                        <input id="transaction_type_29" name="transaction_type" value="13" class="checkall" type="checkbox" checked="checked">
                                        <label for="transaction_type_29"><?=lang('Rolling Commission')?></label>
                                        <br/>
                                    </div>
                                    <?php
                                    }
                                    ?>
                            </fieldset>
                        </div>
                        <div class="col-md-offset-2 col-md-8" style="margin-top: 10px;">
                            <div class="pull-right">
                                <input type="button" class="btn btn-sm btn-info btn_today" value="<?php echo lang('Today');?>">
                                <input type="button" class="btn btn-sm btn-info btn_yesterday" value="<?php echo lang('Yesterday');?>">
                                <input type="button" class="btn btn-sm btn-info btn_this_week" value="<?php echo lang('This Week');?>">
                                <input type="button" class="btn btn-sm btn-info btn_last_week" value="<?php echo lang('Last Week');?>">
                                <input type="button" class="btn btn-sm btn-info btn_this_month" value="<?php echo lang('This Month');?>">
                                <input type="button" class="btn btn-sm btn-info btn_last_month" value="<?php echo lang('Last Month');?>">
                                <input type="button" class="btn btn-sm btn-info btn_this_year" value="<?php echo lang('This Year');?>">
                            </div>

                            <input type="button" value="<?=lang('lang.reset');?>" class="btn btn-default btn-sm" onclick="window.location.href='<?php echo site_url('agency/credit_transactions'); ?>'">
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
            <h4 class="panel-title"><i class="fa fa-money"></i> <?=lang('Credit Transactions');?></h4>
        </div>
        <div class="panel-body">
            <!-- credit transactions table {{{2 -->
            <div class="table-responsive">
                <table class="table table-striped table-hover table-condensed" id="credit_transactions_table">
                    <thead>
                        <tr>
                            <th><?=lang('Date');?></th>
                            <th><?=lang('From User');?></th>
                            <th><?=lang('To User');?></th>
                            <th><?=lang('Amount');?></th>
                            <th><?=lang('Before Balance');?></th>
                            <th><?=lang('After Balance');?></th>
                            <th><?=lang('Remarks');?></th>
                            <!-- <th><?=lang('IP Used');?></th> -->
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
        searching: false,
        dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        buttons: [
            { text: '<?php echo lang("Column visibility"); ?>',extend: 'colvis', postfixButtons: [ 'colvisRestore' ] }
        ],
        columnDefs: [
            { className: 'text-right', targets: [ 4 ] },
        ],
        order: [ 0, 'desc' ],
        processing: true,
        serverSide: true,
        ajax: function (data, callback, settings) {
            data.extra_search = $('#search-form').serializeArray();
            $.post(base_url + "api/credit_transactions", data, function(data) {
                callback(data);
            },'json')
            .fail( function (jqxhr, status_text)  {
                if ( jqxhr.status >= 300 && jqxhr.status < 500 ) {
                    if (confirm('<?= lang('session.timeout') ?>')) {
                        window.location.href = '/';
                    }
                }
                else {
                    alert(status_text);
                }
            });
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
