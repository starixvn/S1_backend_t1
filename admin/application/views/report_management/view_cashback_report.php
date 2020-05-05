<form class="form-horizontal" id="search-form" method="get" role="form">
<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?php echo lang("Cashback Report"); ?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseCashbackReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapseCashbackReport" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
            <div class="row">
                <!-- Date -->
                <div class="col-md-3 col-lg-3">
                    <label class="control-label"><?php echo lang('Date'); ?></label>
                    <input id="search_cashback_date" class="form-control input-sm dateInput user-success" data-start="#by_date_from" data-end="#by_date_to" data-time="false" autocomplete="off">
                    <input type="hidden" id="by_date_from" name="by_date_from" value="<?=$conditions['by_date_from'];?>">
                    <input type="hidden" id="by_date_to" name="by_date_to" value="<?=$conditions['by_date_to'];?>">
                </div>
                <div class="col-md-3 col-lg-3 hide">
                    <label class="control-label"><?php echo lang('Enabled date'); ?></label>
                    <input type="checkbox" data-off-text="<?php echo lang('off'); ?>" data-on-text="<?php echo lang('on'); ?>"  name="enable_date" id="enable_date" data-size='mini' value='true' <?php echo $conditions['enable_date'] ? 'checked="checked"' : ''; ?>>
                </div>
                <!-- Paid -->
                <div class="col-md-3 col-lg-3">
                    <label class="control-label"><?php echo lang('Paid'); ?></label>
                    <select name="by_paid_flag" id="by_paid_flag" class="form-control input-sm">
                        <option value="" <?=empty($conditions['by_paid_flag']) ? 'selected' : ''?>>--  <?php echo lang('None'); ?> --</option>
                        <option value="1" <?php echo ($conditions['by_paid_flag'] === '1') ? 'selected' : ''; ?>><?php echo lang('Paid'); ?></option>
                        <option value="0" <?php echo ($conditions['by_paid_flag'] === '0') ? 'selected' : ''; ?> ><?php echo lang('Not pay'); ?></option>
                    </select>
                </div>
                <!-- Player Username -->
                <div class="col-md-3 col-lg-3">
                    <label class="control-label"><?php echo lang('Player Username'); ?></label>
                    <input type="text" name="by_username" id="by_username" class="form-control input-sm" placeholder='<?php echo lang('Enter Username'); ?>'
                    value="<?php echo $conditions['by_username']; ?>"/>
                </div>
                <!-- Player Level -->
                <div class="col-md-3 col-lg-3">
                    <label class="control-label"><?php echo lang('Player Level'); ?></label>
                    <?php echo form_dropdown('by_player_level', $vipgrouplist, $conditions['by_player_level'], 'id="by_player_level" class="form-control input-sm"') ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 col-lg-3">
                    <label class="control-label"><?php echo lang('Cashback Amount <='); ?></label>
                    <input type="text" name="by_amount_less_than" id="by_amount_less_than" value="<?=$conditions['by_amount_less_than']?>" class="form-control input-sm number_only" placeholder='<?php echo lang('Enter Amount'); ?>'/>
                </div>
                <div class="col-md-3 col-lg-3">
                    <label class="control-label"><?php echo lang('Cashback Amount >='); ?></label>
                    <input type="text" name="by_amount_greater_than" id="by_amount_greater_than" value="<?=$conditions['by_amount_greater_than']?>" class="form-control input-sm number_only" placeholder='<?php echo lang('Enter Amount'); ?>'/>
                </div>
                <?php if($this->utils->isEnabledFeature('enable_friend_referral_cashback')){?>
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label"><?php echo lang('Cashback Type'); ?></label>
                        <select name="by_cashback_type" id="by_cashback_type" class="form-control input-sm">
                            <option value="" <?=empty($conditions['by_cashback_type']) ? 'selected' : ''?>>--  <?php echo lang('None'); ?> --</option>
                            <option value="1" <?php echo ($conditions['by_cashback_type'] == Group_level::NORMAL_CASHBACK) ? 'selected' : ''; ?>><?php echo lang('Normal Cashback'); ?></option>
                            <option value="2" <?php echo ($conditions['by_cashback_type'] == Group_level::FRIEND_REFERRAL_CASHBACK) ? 'selected' : ''; ?> ><?php echo lang('Friend Referral Cashback'); ?></option>
                        </select>
                    </div>
                <?php }?>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 p-t-15 text-right">
                    <?php if ($this->permissions->checkPermissions('manually_calculate_cashback') && $this->authentication->isSuperAdmin()) {?>
                    <!-- <input class="btn btn-sm btn-primary btn_calc_cashback" type="button" value="<?php echo lang('Manually Calculate Cashback'); ?>" /> -->
                    <?php }?>
                    <?php if ($this->permissions->checkPermissions('manually_pay_cashback')) {?>
                        <input class="btn btn-sm btn_payall m-r-30 <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-danger'?>" type="button" value="<?php echo lang('Pay Today Cashback'); ?>" />
                    <?php }?>
                    <input type="button" id="btnResetFields" value="<?php echo lang('Reset'); ?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-danger'?>">
                    <input class="btn btn-sm btn-primary" type="submit" value="<?php echo lang('Search'); ?>" />
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
                <?php if ($this->permissions->checkPermissions('manually_pay_cashback')) {?>
<!--                 <input class="form-input select_all" type="checkbox" value="true"> <?php echo lang('Select All'); ?>
                <input class="btn btn-sm btn-danger btn_paynow" type="button" value="<?php echo lang('Only Pay Selected'); ?>" />
 -->                <?php }?>
                <!-- result table -->
                <div id="logList" class="table-responsive">
                    <table class="table table-striped table-hover table-condensed" id="report_table" style="width:100%;">
                        <thead>
                            <tr>
                                <th><?php echo lang('Date'); ?></th>
                                <th><?php echo lang('Player Username'); ?></th>
                                <th><?php echo lang('Player Level'); ?></th>
                                <th><?php echo lang('Amount'); ?></th>
                                <th><?php echo lang('Bet Amount'); ?></th>
                                <th><?php echo lang('Original Bet Amount'); ?></th>
                                <th><?php echo lang('Paid'); ?></th>
                                <th><?php echo lang('Game Platform'); ?></th>
                                <th><?php echo lang('Game Type'); ?></th>
                                <th><?php echo lang('Game'); ?></th>
                                <th><?php echo lang('Updated at'); ?></th>
                                <th><?php echo lang('Paid date'); ?></th>
                                <th><?php echo lang('Paid amount'); ?></th>
                                <th><?php echo lang('Withdraw Condition amount'); ?></th>
                                <?php if($this->utils->isEnabledFeature('enable_friend_referral_cashback')){?>
                                    <th><?php echo lang('Cashback Type'); ?></th>
                                    <th><?php echo lang('Referred Player for Cashback'); ?></th>
                                <?php }?>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="<?php echo $this->utils->isEnabledFeature('enable_friend_referral_cashback') ? 16 : 14 ?>" class="text-right">
                                    <?php echo lang('Total'); ?>: <span id="total_amount">0.00</span><br>
                                </td>
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
        <form id="_export_csv_form" class="hidden" method="POST" target="_blank">
        <input name='json_search' id = "json_csv_search" type="hidden">
        </form>
<?php }?>
<!-- <form method="POST" id="frm_selected_all" action="<?php echo site_url('/vipsetting_management/pay_selected'); ?>">
<input type="hidden" name="selected_id_value" id="selected_id_value">
<input type="hidden" name="redirect_url" id="redirect_url">
</form>
 -->
<script type="text/javascript">
    // $(".letters_numbers_only").keydown(function (e) {
    //         var code = e.keyCode || e.which;
    //         // Allow: backspace, delete, tab, escape, enter and .
    //         if ($.inArray(code, [46, 8, 9, 27, 32, 13, 110, 190]) !== -1 ||
    //              // Allow: Ctrl+A
    //         ( e.ctrlKey === true) || ( e.metaKey === true) ||
    //              // Allow: home, end, left, right, down, up
    //             (code >= 35 && code <= 40)) {
    //                  // let it happen, don't do anything
    //                  return;
    //         }
    //         // Ensure that it is a number and stop the keypress
    //         if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105) && (e.ctrlKey === true || code < 65 || code > 90)) {
    //             e.preventDefault();
    //         }
    //     });

    function renderSelectAll(){
        var selectedAll=$(".chk_row").length==$(".chk_row:checked").length;

        $(".select_all").prop('checked', selectedAll);
    }

    function changeSelectRow(){

        renderSelectAll();

    }

    $(document).ready(function(){
        var dateFrom = $("#by_date_from").val();
        var dateTo = $("#by_date_from").val();
        // $('#search-form').submit( function(e) {
        //     e.preventDefault();
        //     dataTable.ajax.reload();
        // });
        var arrayfrom = dateFrom.split(' ');
        var arrayto = dateTo.split(' ');

        var d = new Date();
        var month = d.getMonth()+1;
        var day = d.getDate();

        var datetoday = d.getFullYear() + '-' +
        (month<10 ? '0' : '') + month + '-' +
        (day<10 ? '0' : '') + day;

        $('#btnResetFields').click(function() {
            $("#by_date_from").val(datetoday);
            $("#by_date_from").val(datetoday);
            $("#enable_date").prop('checked', true);
            $("#by_paid_flag").val("");
            $("#by_username").val("");
            $("#by_player_level").val("");
            $("#by_amount_less_than").val("");
            $("#by_amount_greater_than").val("");
            $("#by_cashback_type").val("");
            $("#search_cashback_date").val($("#by_date_from").val() +" to "+ $("#by_date_from").val());
        });


        $(".btn_calc_cashback").click(function(){
            if(confirm("<?php echo lang('Do you want manually calculate cashback?'); ?>")){
                // $("#return_url").val(window.location.href);
                $("#search-form").attr("method", "POST")
                    .attr("action", "<?php echo site_url('/vipsetting_management/calc_cashback'); ?>")
                    .submit();
            }
        });

        $(".btn_payall").click(function(){
            //confirm
            if(confirm("<?php echo lang('Do you want pay all unpaid cashback?'); ?>")){
                // $("#return_url").val(window.location.href);
                $("#search-form").attr("method", "POST")
                    .attr("action", "<?php echo site_url('/vipsetting_management/pay_all_cashback'); ?>")
                    .submit();
            }
        });

        $(".btn_paynow").click(function(){
            //pay now
            if(confirm("<?php echo lang('Do you want pay selected unpaid cashback?'); ?>")){
                var selectedIdArr=[];
                $(".chk_row:checked").each(function(){
                    selectedIdArr.push($(this).val());
                });

                $("#selected_id_value").val(selectedIdArr.join(','));
                // utils.safelog(selectedIdArr);
                $("#redirect_url").val(window.location.href);
                $("#frm_selected_all").submit();
            }
        });

        $(".select_all").change(function(){
            //
            $(".chk_row").prop("checked", $(this).prop("checked"));
        });

        $('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').trigger('submit');
            }
        });

        $('#report_table').DataTable({
            lengthMenu: JSON.parse('<?=json_encode($this->utils->getConfig('default_datatable_lengthMenu'))?>'),
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            autoWidth: false,
            searching: false,
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
                }
                <?php if ($export_report_permission) {?>
                ,{
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                    action: function ( e, dt, node, config ) {
                    //  var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                       var d = {'extra_search': $('#search-form').serializeArray(), 'export_format': 'csv', 'export_type': export_type,
                            'draw':1, 'length':-1, 'start':0};
                        <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                                    $("#_export_excel_queue_form").attr('action', site_url('/export_data/cashback_report'));
                                    $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                    $("#_export_excel_queue_form").submit();
                        <?php }?>
                    }
                }
                <?php }
?>
            ],
            columnDefs: [
                { sortable: false, targets: [ 1 ] },
                { className: 'text-right', targets: [ 3,4,5,12 ] }
                // { visible: false, targets: [ 8 ] }
            ],
            "order": [ 0, 'desc' ],
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/cashback_report", data, function(data) {
                    callback(data);
                    $('#total_amount').text(data.summary[0].total_amount);
                    if ( $('#report_table').DataTable().rows( { selected: true } ).indexes().length === 0 ) {
                        $('#report_table').DataTable().buttons().disable();
                    }
                    else {
                        $('#report_table').DataTable().buttons().enable();
                    }
                },'json');
            },
        });
    });
</script>