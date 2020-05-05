<div class="panel panel-primary hidden">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapsePaymentReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>
    <div id="collapsePaymentReport" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
            <form id="search-form" action="<?= site_url('/report_management/viewPaymentReport'); ?>" method="get">
                <div class="row">
                    <!-- Date -->
                    <div class="form-group col-md-3 col-lg-3">
                        <label class="control-label">
                            <?= lang('Date'); ?>
                        </label>
                        <input id="search_payment_date" style="font-weight: bold" class="form-control input-sm dateInput user-success" data-start="#by_date_from" data-end="#by_date_to" data-time="false" autocomplete="off" />
                        <input type="hidden" id="by_date_from" name="by_date_from" value="<?=$conditions['by_date_from'];?>" />
                        <input type="hidden" id="by_date_to" name="by_date_to" value="<?=$conditions['by_date_to'];?>" />
                    </div>
                    <div class="form-group col-md-2 col-lg-2 hide">
                        <div>
                            <label class="control-label">
                                <?= lang('Enabled date'); ?>
                            </label>
                        </div>
                        <input type="checkbox" data-off-text="<?= lang('off'); ?>" data-on-text="<?= lang('on'); ?>" name="enable_date" data-size='mini' value='true' <?= $conditions['enable_date'] ? 'checked="checked"' : ''; ?> />
                    </div>
                    <!-- Transaction Type -->
                    <div class="form-group col-md-2 col-lg-2">
                        <label class="control-label"><?=lang('Transaction Type')?></label>
                        <?php echo form_dropdown('by_transaction_type', $trans_list, $conditions['by_transaction_type'], 'id="by_transaction_type" class="form-control input-sm group-reset"'); ?>
                    </div>
                    <!-- username -->
                    <div class="form-group col-md-4 col-lg-4">
                        <label for="username" class="control-label">
                            <input type="radio" name="search_by" value="1" <?= $conditions['search_by']  == '1' ? 'checked="checked"' : '' ?> />
                            <?= lang('Similar');?> <?= lang('Username'); ?>
                            <input type="radio" name="search_by" value="2" <?= $conditions['search_by']  == '2' ? 'checked="checked"' : '' ?> />
                            <?= lang('Exact'); ?> <?=lang('Username'); ?>
                        </label>
                        <input type="text" name="by_username" id="by_username" value="<?= $conditions['by_username']; ?>" class="form-control input-sm group-reset" />
                    </div>
                    <!-- group-by -->
                    <div class="form-group col-md-2 col-lg-2">
                        <label class="control-label"><?=lang('Group By')?></label>
                        <?= form_dropdown('group_by', $group_by_list, $conditions['group_by'], 'id="group_by" class="form-control input-sm group-reset"'); ?>
                    </div>
                </div>

                <div class="row">
                    <!-- player level -->
                    <div class="form-group col-md-3 col-lg-3">
                        <label class="control-label"><?=lang('Player Level')?></label>
                        <?= form_dropdown('by_player_level', $vipgrouplist, $conditions['by_player_level'], 'id="by_player_level" class="form-control input-sm group-reset"'); ?>
                    </div>
                    <!-- affiliate_username -->
                    <div class="form-group col-md-2 col-lg-2">
                        <label class="control-label" for="affiliate_username"><?=lang('Affiliate Username')?> </label>
                        <input type="text" name="affiliate_username" id="affiliate_username" class="form-control input-sm group-reset"
                        value='<?php echo $conditions["affiliate_username"]; ?>'/>
                    </div>
                    <!-- Agent Username -->
                    <div class="form-grou col-md-4 col-lg-4">
                        <label class="control-label" for="agent_name"><?=lang('Agent Username')?> </label>
                        <div class="input-group">
                            <input type="text" name="agent_name" id="agent_name" class="form-control input-sm group-reset" value='<?= $conditions["agent_name"]; ?>'/>
                            <span class="input-group-addon input-sm">
                                <input type="checkbox" id="include_all_downlines" name="include_all_downlines"  <?= $conditions['include_all_downlines']  == 'on' ? 'checked="checked"' : '' ?>/>
                                <?=lang('Include All Downline Agents')?>
                            </span>
                        </div>
                    </div>
                    <!-- referrer username -->
                    <div class="form-group col-md-2 col-lg-2">
                        <label class="control-label" for="referrer_username"><?=lang('pay_report.referrer_username')?> </label>
                        <input type="text" name="referrer_username" id="referrer_username" class="form-control input-sm group-reset" value='<?= $conditions["referrer_username"]; ?>'/>
                    </div>
                </div>

                <div class="row">
                    <!-- admin username -->
                    <div class="form-group col-md-3 col-lg-3">
                        <label class="control-label" for="admin_username"><?=lang('pay_report.admin_username')?> </label>
                        <input type="text" name="admin_username" id="admin_username" class="form-control input-sm group-reset" value='<?= $conditions["admin_username"]; ?>'/>
                    </div>
                    <div class="form-group col-md-2 col-lg-2">
                        <label class="control-label"><?=lang('report.p16')?></label>
                        <input type="number" min="0" name="by_amount_greater_than" id="by_amount_greater_than" class="form-control input-sm group-reset" placeholder="<?=lang('report.p17')?>" value="<?php echo $conditions['by_amount_greater_than']; ?>"/>
                    </div>
                    <div class="form-group col-md-2 col-lg-2">
                        <label class="control-label"><?=lang('report.p15')?></label>
                        <input type="number" min="0" name="by_amount_less_than" id="by_amount_less_than" class="form-control input-sm group-reset" placeholder="<?=lang('report.p17')?>" value="<?php echo $conditions['by_amount_less_than']; ?>"/>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-2 col-md-offset-10">
                        <div class="pull-right">
                            <input type="button" id="btnResetFields" value="<?=lang('lang.reset'); ?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-danger'?>">
                            <button type="submit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>"><?=lang("lang.search")?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="icon-credit-card"></i>
            <?=lang("report.p22")?>
        </h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-condensed" id="result_table">
                <thead>
                    <tr>
                        <th><?=lang("Date")?></th>
                        <th><?=lang("Player Username")?></th>
                        <th><?=lang("Affiliate Username")?></th>
                        <th><?=lang("Agent Username")?></th>
                        <th><?=lang("pay_report.referrer_username")?></th>
                        <th><?=lang("Admin Username")?></th>
                        <th><?=lang("Group Level")?></th>
                        <th><?=lang("Payment Type")?></th>
                        <th><?=lang("Promotion Category")?></th>
                        <th><?=lang("Transaction Type")?></th>
                        <th><?=lang("Amount")?></th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr></tr>
                    <tr>
                        <th colspan="10" style="text-align:right"><?=lang("Sub Total")?></th>
                        <th><span id="sub_amount" class="pull-right">0.00</span><br></th>
                    </tr>
                    <tr>
                        <th colspan="10" style="text-align:right"><?=lang("Total")?></th>
                        <th><span id="total_amount" class="pull-right">0.00</span><br></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
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

<script type="text/javascript">
    $(document).ready(function(){
        var hide_targets=<?=json_encode($hide_cols); ?>;

        var dataTable = $('#result_table').DataTable({
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            autoWidth: false,
            searching: false,
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
                }
                <?php if ($export_report_permission) {?>
                ,{
                    text: "<?= lang('CSV Export'); ?>",
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                    action: function ( e, dt, node, config ) {
                        var form_params=$('#search-form').serializeArray();
                       var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
                            'draw':1, 'length':-1, 'start':0};
                        <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                                    $("#_export_excel_queue_form").attr('action', site_url('/export_data/payment_report'));
                                    $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                    $("#_export_excel_queue_form").submit();
                        <?php }?>
                    }
                }
                <?php } ?>
            ],
            columnDefs: [
                { className: 'text-right', targets: [ 10 ] },
                { "visible": false, "targets": hide_targets }
            ],
            order: [ 0, 'desc' ],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/payment_report", data, function(data) {
                    var subTotal = 0;
                    var sub = 0;
                    var convertedSub = 0;
                    $.each(data.data, function(i, v){
                        sub = v[10].replace(/<(?:.|\n|)*?>/gm, '');
                        convertedSub = sub.replace(',', '');
                        if(Number.parseFloat(convertedSub)){
                            subTotal+= Number.parseFloat(convertedSub);
                        }
                    });
                    $('#sub_amount').text(subTotal.toLocaleString('en', {minimumFractionDigits: 2}));
                    callback(data);
                    $('#total_amount').text(data.summary[0].total_amount);
                    if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                        dataTable.buttons().disable();
                    }
                    else {
                        dataTable.buttons().enable();
                    }
                }, 'json');
            }
        });

        var date_today = new moment().format('YYYY-MM-DD');

        $('#btnResetFields').click(function() {
            $('.group-reset').val('');
            $('#include_all_downlines').prop('checked', false);
            $("#search_payment_date").val(date_today + " to " + date_today);
        });
    });
</script>