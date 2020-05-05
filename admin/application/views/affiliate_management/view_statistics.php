<style type="text/css">
#collapseCashbackReport input{
    font-weight:bold;
}
</style>
<form class="form-horizontal" id="search-form" method="get" role="form">

<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?php echo $title; ?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseCashbackReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
            <?php include __DIR__ . "/../includes/report_tools.php" ?>
        </h4>
    </div>


    <div id="collapseCashbackReport" class="panel-collapse <?php echo $this->utils->getConfig('default_open_search_panel') ? '' : 'collapse in' ?>">
        <div class="panel-body">
                <div class="row">
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label" for="period"><?=lang('aff.ap07');?>:</label>
                        <input type="text" class="form-control input-sm dateInput" id = "filterDate" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="false"/>
                        <input type="hidden" id="dateRangeValueStart" name="by_date_from" value="<?php echo $conditions['by_date_from']; ?>" />
                        <input type="hidden" id="dateRangeValueEnd" name="by_date_to" value="<?php echo $conditions['by_date_to']; ?>" />
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label"><?php echo lang('Enabled date'); ?></label><br>
                        <input type="checkbox" id="enable_date" data-off-text="<?php echo lang('off'); ?>" data-on-text="<?php echo lang('on'); ?>" name="enable_date" data-size='mini' value='true' <?php echo $conditions['enable_date'] ? 'checked="checked"' : ''; ?>>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label"><?php echo lang('Affiliate status'); ?></label>
                        <div>
                        <select id="by_status" name="by_status" class="form-control input-sm">
                            <option value="" <?php echo empty($conditions['by_status']) ? 'selected' : ''; ?> >--  <?php echo lang('None'); ?> --</option>
                            <option value="0" <?php echo ($conditions['by_status'] === '0') ? 'selected' : ''; ?> ><?php echo lang('Active Only'); ?></option>
                            <option value="1" <?php echo ($conditions['by_status'] === '1') ? 'selected' : ''; ?> ><?php echo lang('Inactive only'); ?></option>
                            <option value="2" <?php echo ($conditions['by_status'] === '2') ? 'selected' : ''; ?> ><?php echo lang('Deleted only'); ?></option>
                            <option value="-1" <?php echo ($conditions['by_status'] === '-1') ? 'selected' : ''; ?> ><?php echo lang('No empty affiliate'); ?></option>
                        </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label"><?php echo lang('Affiliate Username'); ?></label>
                        <input type="text" name="by_affiliate_username" class="form-control input-sm" placeholder='<?php echo lang('Enter Username'); ?>'
                        value="<?php echo $conditions['by_affiliate_username']; ?>"/>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label"><?php echo lang('Show game platform'); ?></label><br>
                        <input type="checkbox" id="show_game_platform" data-off-text="<?php echo lang('off'); ?>" data-on-text="<?php echo lang('on'); ?>" name="show_game_platform" data-size='mini' value='true' <?php echo $conditions['show_game_platform'] ? 'checked="checked"' : ''; ?>>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label"><?=lang('total_cashback_date')?></label>
                        <?php echo form_dropdown('by_total_cashback_date_type', $total_cashback_date_type, $conditions['by_total_cashback_date_type'], 'class="form-control input-sm" id="by_total_cashback_date_type"'); ?>
                    </div>
                    <div class="col-md-3 col-lg-3">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label"><?=lang('Affiliate Tag');?> </label>
                    </div>
                </div>

                <div class="row">
                    <?php if(isset($tags) && !empty($tags)):?>
                        <?php foreach ($tags as $tag_id => $tag) {?>
                            <div class="col-md-2">
                                <label class="control-label">
                                    <input class="control-label afftags" data-size='mini' type="checkbox" name="tag_id[]" value="<?=$tag_id?>" <?=in_array($tag_id, $conditions['tag_id']) ? 'checked="checked"' : ''?>>
                                    <?=$tag?>
                                </label>
                            </div>
                        <?php }?>
                    <?php endif;?>
                </div>

                <div class="row">
                    <div class="col-md-3 col-lg-3" style="padding: 10px;">
                        <input type="button" value="<?php echo lang('Reset'); ?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-danger'?>" onclick="resetForm()">
                        <input class="btn btn-sm pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" type="submit" value="<?php echo lang('Search'); ?>" />
                    </div>
                </div>
        </div>
    </div>

</div>
</form>
        <!--end of Sort Information-->


        <div class="panel panel-primary">
            <div class="panel-heading custom-ph">
                <h4 class="panel-title custom-pt"><i class="icon-bullhorn"></i> <?php echo lang('Report Result'); ?></h4>
            </div>
            <div class="panel-body">
                <!-- result table -->
                <div id="logList" class="table-responsive">
                    <table class="table table-striped table-hover table-condensed" id="report_table" style="width:100%;">
                        <thead>
                            <tr>
                                <th><?php echo lang('Affiliate Username'); ?></th>
                                <th><?php echo lang('Real Name'); ?></th>
                                <th><?php echo lang('aff.aj05'); ?></th>
                                <th><?php echo lang('Affiliate Level'); ?></th>
                                <th><?php echo lang('Total Sub-affiliates'); ?></th>
                                <th><?php echo lang('Total registered players'); ?></th>
                                <th><?php echo lang('Total deposited players'); ?></th>
                                <th><?php echo lang('Total Bet'); ?></th>
                                <th><?php echo lang('Total Win'); ?></th>
                                <th><?php echo lang('Total Loss'); ?></th>
                                <th><?php echo lang('Company Win/Loss'); ?></th>
                                <th><?php echo lang('Company Income'); ?></th>
                                <th><?php echo lang('Total Cashback'); ?></th>
                                <th><?php echo lang('Total Bonus'); ?></th>
                                <th><?php echo lang('Total Deposit'); ?></th>
                                <th><?php echo lang('Total Withdraw'); ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr id="subtotal">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="text-right total-sub-affiliates"><?php echo lang('Total Sub-affiliates'); ?></th>
                                <th class="text-right total-registered-players"><?php echo lang('Total registered players'); ?></th>
                                <th class="text-right total-deposited-players"><?php echo lang('Total deposited players'); ?></th>
                                <th class="text-right total-bet"><?php echo lang('Total Bet'); ?></th>
                                <th class="text-right total-win"><?php echo lang('Total Win'); ?></th>
                                <th class="text-right total-loss"><?php echo lang('Total Loss'); ?></th>
                                <th class="text-right company-win-loss"><?php echo lang('Company Win/Loss'); ?></th>
                                <th class="text-right company-income"><?php echo lang('Company Income'); ?></th>
                                <th class="text-right total-cashback"><?php echo lang('Total Cashback'); ?></th>
                                <th class="text-right total-bonus"><?php echo lang('Total Bonus'); ?></th>
                                <th class="text-right total-deposit"><?php echo lang('Total Deposit'); ?></th>
                                <th class="text-right total-withdraw"><?php echo lang('Total Withdraw'); ?></th>
                            </tr>
                            <tr id="grandtotal">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="text-right total-sub-affiliates"><?php echo lang('Total Sub-affiliates'); ?></th>
                                <th class="text-right total-registered-players"><?php echo lang('Total registered players'); ?></th>
                                <th class="text-right total-deposited-players"><?php echo lang('Total deposited players'); ?></th>
                                <th class="text-right total-bet"><?php echo lang('Total Bet'); ?></th>
                                <th class="text-right total-win"><?php echo lang('Total Win'); ?></th>
                                <th class="text-right total-loss"><?php echo lang('Total Loss'); ?></th>
                                <th class="text-right company-win-loss"><?php echo lang('Company Win/Loss'); ?></th>
                                <th class="text-right company-income"><?php echo lang('Company Income'); ?></th>
                                <th class="text-right total-cashback"><?php echo lang('Total Cashback'); ?></th>
                                <th class="text-right total-bonus"><?php echo lang('Total Bonus'); ?></th>
                                <th class="text-right total-deposit"><?php echo lang('Total Deposit'); ?></th>
                                <th class="text-right total-withdraw"><?php echo lang('Total Withdraw'); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!--end of result table -->
            <div class="panel-footer"></div>
        </div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
        <input name='json_search' type="hidden">
    </form>
<?php }?>
<script type="text/javascript">
    function getCurrentDate(d){
        var month = d.getMonth()+1;
        var day = d.getDate();
        var output = d.getFullYear() + '-' +
            ((''+month).length<2 ? '0' : '') + month + '-' +
            ((''+day).length<2 ? '0' : '') + day;
        return output;
    }

    function resetForm(){
        //get current date
        var date_from = new Date();
        //get 7 days before
        date_from.setDate(date_from.getDate() - 7);
        var from = getCurrentDate(date_from);
        var to = getCurrentDate(new Date());
        //set default value
        $('#dateRangeValueStart').val(from + " 00:00:00");
        $('#dateRangeValueEnd').val(to + " 23:59:59");
        $("#filterDate").data('daterangepicker').setStartDate(from);
        $("#filterDate").data('daterangepicker').setEndDate(to);
        $("input[name='by_affiliate_username']").val("");
        $("#by_status").val("0").change();
        $(".afftags").bootstrapSwitch('state', false);
        $("#enable_date").bootstrapSwitch('state', true);
        $("#show_game_platform").bootstrapSwitch('state', false);
        $("select option").removeAttr('selected');
    }

    $(document).ready(function(){
        // $('#search-form').submit( function(e) {
        //     e.preventDefault();
        //     dataTable.ajax.reload();
        // });

        $("input[type='checkbox']").bootstrapSwitch();

        // $("input[type='checkbox']").on('switchChange.bootstrapSwitch', function(event, state) {
        // //   // console.log(this); // DOM element
        // //   // console.log(event); // jQuery event
        // //   // console.log(state); // true | false
        // //   $('#'+$(this).attr('name')+'Field').val(state ? 'true' : 'false');
        //     $(this).val(state ? 'true' : 'false');
        // });

        

        $('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').trigger('submit');
            }
        });

        $('#report_table').DataTable({
            autoWidth: false,
            searching: false,
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            // "responsive": {
            //     details: {
            //         type: 'column'
            //     }
            // },
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: "<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>"
                }
                <?php if ($export_report_permission) {?>
                ,{
                    text: '<?php echo lang("lang.export_excel"); ?>',
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                    action: function ( e, dt, node, config ) {

                        var form_params=$('#search-form').serializeArray();
                        var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
                            'draw':1, 'length':-1, 'start':0};

                        $("#_export_excel_queue_form").attr('action', site_url('/export_data/affiliate_statistics'));
                        $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                        $("#_export_excel_queue_form").submit();

                    }
                }
                <?php }
?>
            ],
            columnDefs: [
                { sortable: false, targets: [ 5,6,7,8,9,10,11,12,13,14,15 ] },
                { className: 'text-right', targets: [ 3,4,5,6,7,8,9,10,11,12,13,14,15 ] },
                { visible: false, targets: [ 3,4 ] }
            ],
            "order": [ 0, 'asc' ],
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/affiliate_statistics", data, function(data) {

                    var total_sub_affiliates = 0,
                        total_registered_players = 0,
                        total_deposited_players = 0,
                        total_bet = 0,
                        total_win = 0,
                        total_loss = 0,
                        company_win_loss = 0,
                        company_income = 0,
                        total_cashback = 0,
                        total_bonus = 0,
                        total_deposit = 0,
                        total_withdraw = 0;

                    var showGamePlatform = $("#show_game_platform").bootstrapSwitch('state');

                    $.each(data.data, function(i, v) {
                        total_sub_affiliates += numeral(v[4]).value();
                        total_registered_players += numeral(v[5]).value();
                        total_deposited_players += numeral(v[6]).value();
                        if (showGamePlatform) {
                            total_bet = 0;
                            total_win = 0;
                            total_loss = 0;
                        } else {
                            total_bet += numeral(v[7]).value();
                            total_win += numeral(v[8]).value();
                            total_loss += numeral(v[9]).value();
                        }
                        company_win_loss += numeral($(v[10]).text()).value();
                        company_income += numeral($(v[11]).text()).value();
                        total_cashback += numeral(v[12]).value();
                        total_bonus += numeral(v[13]).value();
                        total_deposit += numeral(v[14]).value();
                        total_withdraw += numeral(v[15]).value();
                    });

                    $('#subtotal .total-sub-affiliates').text(parseInt(total_sub_affiliates));
                    $('#subtotal .total-registered-players').text(parseInt(total_registered_players));
                    $('#subtotal .total-deposited-players').text(parseInt(total_deposited_players));
                    $('#subtotal .total-bet').text(numeral(total_bet).format('0,0.00'));
                    $('#subtotal .total-win').text(numeral(total_win).format('0,0.00'));
                    $('#subtotal .total-loss').text(numeral(total_loss).format('0,0.00'));

                    $('#subtotal .company-win-loss').text(numeral(company_win_loss).format('0,0.00')).removeClass('text-success text-danger');
                    if (company_win_loss > 0) {
                        $('#subtotal .company-win-loss').addClass('text-success');
                    } else if (company_win_loss < 0) {
                        $('#subtotal .company-win-loss').addClass('text-danger');
                    }

                    $('#subtotal .company-income').text(numeral(company_income).format('0,0.00')).removeClass('text-success text-danger');
                    if (company_income > 0) {
                        $('#subtotal .company-income').addClass('text-success');
                    } else if (company_income < 0) {
                        $('#subtotal .company-income').addClass('text-danger');
                    }

                    $('#subtotal .total-cashback').text(numeral(total_cashback).format('0,0.00'));
                    $('#subtotal .total-bonus').text(numeral(total_bonus).format('0,0.00'));
                    $('#subtotal .total-deposit').text(numeral(total_deposit).format('0,0.00'));
                    $('#subtotal .total-withdraw').text(numeral(total_withdraw).format('0,0.00'));

                    $('#grandtotal .total-sub-affiliates').text(parseInt(data.summary.total_sub_affiliates));
                    $('#grandtotal .total-registered-players').html(parseInt(data.summary.total_registered_player));
                    $('#grandtotal .total-deposited-players').html(parseInt(data.summary.total_deposited_player));
                    $('#grandtotal .total-bet').html(data.summary.total_bet);
                    $('#grandtotal .total-win').html(data.summary.total_win);
                    $('#grandtotal .total-loss').html(data.summary.total_loss);
                    $('#grandtotal .company-win-loss').html(data.summary.total_win_loss);
                    $('#grandtotal .company-income').html(data.summary.total_income);
                    $('#grandtotal .total-cashback').html(data.summary.total_cashback);
                    $('#grandtotal .total-bonus').html(data.summary.total_bonus);
                    $('#grandtotal .total-deposit').html(data.summary.total_deposit);
                    $('#grandtotal .total-withdraw').html(data.summary.total_withdraw);

                    callback(data);
                    if ( $('#report_table').DataTable().rows( { selected: true } ).indexes().length === 0 ) {
                        $('#report_table').DataTable().buttons().disable();
                    }
                    else {
                        $('#report_table').DataTable().buttons().enable();
                    }
                },'json');
            }
        });


    });
</script>
