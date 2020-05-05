<div class="panel panel-primary hidden">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapsePlayerReport" class="btn btn-info btn-xs <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapsePlayerReport" class="panel-collapse <?= $this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
            <form id="form-filter" class="form-horizontal" method="GET">
                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label"><?=lang('report.sum02')?></label>
                        <input class="form-control dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
                        <input type="hidden" id="date_from" name="date_from" value="<?=$conditions['date_from'];?>"/>
                        <input type="hidden" id="date_to" name="date_to" value="<?=$conditions['date_to'];?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="control-label" for="depamt2"><?=lang('report.pr31') . " >="?></label>
                        <input type="text" name="depamt2" id="depamt2" class="form-control number_only" value="<?= $conditions['depamt2'];?>"/>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="depamt1"><?=lang('report.pr31') . " <="?></label>
                        <input type="text" name="depamt1" id="depamt1" class="form-control number_only" value="<?= $conditions['depamt1'];?>"/>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="widamt2"><?=lang('report.pr32') . " >="?></label>
                        <input type="text" name="widamt2" id="widamt2" class="form-control number_only" value="<?= $conditions['widamt2'];?>"/>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="widamt1"><?=lang('report.pr32') . " <="?></label>
                        <input type="text" name="widamt1" id="widamt1" class="form-control number_only" value="<?= $conditions['widamt1'];?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="control-label" for="playerlevel"><?=lang('report.pr03')?></label>
                        <select name="playerlevel" id="playerlevel" class="form-control">
                            <option value=""><?=lang('lang.selectall')?></option>
                            <?php foreach ($allLevels as $key => $value) { ?>
                                <?php if($conditions['playerlevel'] == $value['vipsettingcashbackruleId'] ): ?>
                                    <option value="<?=$value['vipsettingcashbackruleId']?>" selected ><?=lang($value['groupName']) . ' ' . lang($value['vipLevel'])?></option>
                                <?php else: ?>
                                    <option value="<?=$value['vipsettingcashbackruleId']?>"><?=lang($value['groupName']) . ' ' . lang($value['vipLevel'])?></option>
                                <?php endif;?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="username" class="control-label">
                            <input type="radio" name="search_by" value="1" <?=$conditions['search_by'] == '1' ? 'checked="checked"' : '' ?> /> <?=lang('Similar');?>
                            <?=lang('Username'); ?>
                            <input type="radio" name="search_by" value="2" <?=$conditions['search_by'] == '2' ? 'checked="checked"' : ''?> /> <?=lang('Exact'); ?>
                            <?=lang('Username'); ?>
                        </label>
                        <input type="text" name="username" id="username" class="form-control" value="<?= $conditions['username'];?>" <?= ($conditions['group_by'] != 'player_id' && $conditions['group_by'] != '') ? 'disabled' : ''   ?> />
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="group_by"><?=lang('report.g14')?> </label>
                        <select name="group_by" id="group_by" class="form-control">
                            <option value="player_id" <?php echo ($conditions['group_by'] == 'player_id') ? 'selected' : ''  ?> ><?=lang('report.pr01')?></option>
                            <option value="playerlevel" <?php echo ($conditions['group_by'] == 'playerlevel') ? 'selected' : ''  ?> ><?=lang('player.07')?></option>
                            <?php if($this->utils->isEnabledFeature('show_search_affiliate') && !$this->utils->isEnabledFeature('close_aff_and_agent')): ?>
                                <option value="affiliate_id" <?php echo ($conditions['group_by'] == 'affiliate_id') ? 'selected' : ''  ?> ><?=lang('Affiliate'); ?></option>
                            <?php endif?>
                            <?php if(!$this->utils->isEnabledFeature('close_aff_and_agent')): ?>
                                <option value="agent_id" <?php echo ($conditions['group_by'] == 'agent_id') ? 'selected' : ''  ?> ><?=lang('Agency'); ?></option>
                            <?php endif?>
                        </select>
                    </div>
                    <?php if(!$this->utils->isEnabledFeature('close_aff_and_agent')){?>
                        <div class="col-md-3">
                            <label class="control-label" for="agent_name"><?=lang('Agent Username')?> </label>
                            <input type="text" name="agent_name" id="agent_name" class="form-control input-sm" value="<?= $conditions['agent_name'];?>" />
                        </div>
                        <div class="col-md-3 col-lg-2 pull-right">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="include_all_downlines" value="true" <?=$conditions['include_all_downlines'] == 'true' ? 'checked="checked"' : '' ?>/>
                                    <?=lang('Include All Downline Agents')?>
                                </label>
                            </div>
                        </div>
                    <?php }?>
                    <?php if($this->utils->isEnabledFeature('show_search_affiliate_tag') && !$this->utils->isEnabledFeature('close_aff_and_agent')){?>
                        <div class="col-md-3">
                            <label class="control-label"><?php echo lang('Affiliate Tag'); ?></label>
                            <br>
                            <select name="affiliate_tags" id="affiliate_tags" class="form-control input-sm">
                                <option value="">-<?=lang('lang.select');?>-</option>
                                <?php foreach ($tags as $tag) {?>
                                    <?php if($conditions['affiliate_tags'] == $tag['tagId']): ?>
                                        <option value="<?=$tag['tagId']?>" selected ><?=$tag['tagName']?></option>
                                    <?php else: ?>
                                        <option value="<?=$tag['tagId']?>"><?=$tag['tagName']?></option>
                                    <?php endif;?>
                                <?php } ?>
                            </select>
                        </div>
                    <?php }?>
                </div>

                <div class="row">
                    <?php if($this->utils->isEnabledFeature('show_search_affiliate') && !$this->utils->isEnabledFeature('close_aff_and_agent') ){?>
                        <div class="col-md-3">
                            <label class="control-label" for="affiliate_name"><?=lang('Affiliate')?> </label>
                            <input type="text" name="affiliate_name" id="affiliate_name" class="form-control input-sm" value="<?= $conditions['affiliate_name'];?>"  />
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="aff_include_all_downlines" value="true" <?=$conditions['aff_include_all_downlines'] == 'true' ? 'checked="checked"' : '' ?>/>
                                        <?=lang('Include All Downlines Affiliate')?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php }?>
                    <div class="col-md-3">
                        <label for="player_tag" class="control-label"><?=lang('Tag')?></label>
                        <select name="player_tag" id="player_tag" class="form-control input-sm">
                            <option value=""><?=lang('lang.select')?></option>
                            <?php if (!empty($player_tags)): ?>
                                <?php foreach ($player_tags as $tag): ?>
                                    <?php if($conditions['player_tag'] == $tag['tagId']): ?>
                                        <option selected value="<?=$tag['tagId']?>"><?=$tag['tagName']?></option>
                                    <?php else: ?>
                                        <option value="<?=$tag['tagId']?>"><?=$tag['tagName']?></option>
                                    <?php endif;?>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="affiliate_agent" class="control-label"><?=lang('Under Affiliate/Agent Status')?></label>
                        <select name="affiliate_agent" id="affiliate_agent" class="form-control input-sm">
                            <option value=""><?=lang('lang.selectall')?></option>
                            <option value="2" <?php if($conditions['affiliate_agent'] == 2) echo 'selected'; ?>><?=lang('Under Affiliate Only')?></option>
                            <option value="3" <?php if($conditions['affiliate_agent'] == 3) echo 'selected'; ?>><?=lang('Under Agent Only')?></option>
                            <option value="4" <?php if($conditions['affiliate_agent'] == 4) echo 'selected'; ?>><?=lang('Under Affiliate or Agent')?></option>
                            <option value="1" <?php if($conditions['affiliate_agent'] == 1) echo 'selected'; ?>><?=lang('Not under any Affiliate or Agent')?></option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="referrer" class="control-label"><?=lang('Referrer')?></label>
                        <input type="text" name="referrer" id="referrer" class="form-control input-sm" value="<?php echo $conditions['referrer']; ?>"/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-1" style="padding-top: 20px">
                        <input type="submit" value="<?=lang('lang.search')?>" id="search_main"class="btn btn-info btn-sm">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="icon-users"></i> <?=lang('report.s09')?> </h4>
    </div>
    <div class="panel-body">

    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="myTable">
            <thead>
                <tr>
                    <th id="th-username" ><?=lang('report.pr01')?></th>
                    <th id="th-realname" ><?=lang('report.pr02')?></th>
                    <th id="th-tag"  ><?=lang('player.41')?></th>
                    <?php if($this->utils->isEnabledFeature('show_risk_score')): ?>
                        <th id="th-kyc-level" ><?=lang("Risk Level/Score")?></th>
                    <?php endif?>
                    <?php if($this->utils->isEnabledFeature('show_kyc_status')): ?> -
                        <th id="th-kyc-score" ><?=lang("KYC Level/Rate Code")?></th>
                    <?php endif?>
                    <th id="th-player-level" style="min-width:150px;" ><?=lang('report.pr03')?></th>

                    <!--TURN OFF THIS FIELDS WHEN USING SBE LOTTERY-->
                    <?php if( !$this->utils->isEnabledFeature('close_aff_and_agent') && $this->utils->isEnabledFeature('show_search_affiliate')): ?>
                        <th id="th-affiliate" ><?=lang('Affiliate')?></th>
                    <?php endif?>
                    <?php if(!$this->utils->isEnabledFeature('close_aff_and_agent')): ?>
                        <th id="th-agent" ><?=lang("Agent")?></th>
                    <?php endif?>
                    <!--TURN OFF THIS FIELDS WHEN USING SBE LOTTERY-->

                    <th id="th-email" ><?=lang('report.pr04')?></th>
                    <th id="th-phone" ><?=lang('aff.ai15')?></th>
                    <th id="th-registered-by" ><?=lang('report.pr05')?></th>
                    <th id="th-registered-ip" ><?=lang('report.pr06')?></th>
                    <th id="th-last-login-ip"><?=lang('report.pr07')?></th>
                    <th id="th-last-login-date" style="min-width:95px;"><?=lang('report.pr08')?></th>
                    <th id="th-last-logout-date" style="min-width:95px;"><?=lang('report.pr09')?></th>
                    <th id="th-register-date" style="min-width:95px;" ><?=lang('report.pr10')?></th>
                    <th id="th-gender" style="min-width:95px;" ><?=lang('report.pr11')?></th>
                    <th id="th-total-cashback-bonus" ><?=lang('report.sum15')?></th>
                    <th id="th-total-deposit-bonus" ><?=lang('report.pr15')?></th>
                    <th id="th-total-referral-bonus" ><?=lang('report.pr17')?></th>
                    <th id="th-manual-bonus" ><?=lang('transaction.manual_bonus')?></th>
                    <th id="th-subtract-bonus" ><?=lang('transaction.transaction.type.10')?></th>
                    <th id="th-total-bonus" ><?=lang('report.pr18')?></th>
                    <th id="th-total-first-deposit" ><?=lang('report.pr19')?></th>
                    <th id="th-total-second-deposit" ><?=lang('report.pr20')?></th>
                    <th id="th-total-deposit" ><?=lang('report.pr21')?></th>
                    <th id="th-deposit-times" ><?=lang('yuanbao.deposit.times')?></th>
                    <th id="th-total-withdrawal" ><?=lang('report.pr22')?></th>
                    <th id="th-deposit-minus-withdrawal" ><?=lang('Deposit - Withdraw')?></th>
                    <th id="th-total-bets" ><?=lang('cms.totalbets')?></th>
                    <th id="th-payout" ><?=lang('Payout')?></th>
                    <th id="th-payout-rate"  ><?=lang('sys.payoutrate')?></th>
                    <th id="th-bet-details"  style="min-width:170px;"><?=lang('Bet Detail')?></th>
                </tr>
            </thead>
            <tfoot>
                <?php if ($this->utils->isEnabledFeature('show_total_for_player_report')) {?>
                    <?php include __DIR__.'/footer_for_player_report.php'; ?>
                <?php }?>
            </tfoot>
           </table>
    </div>

    </div>
    <div class="panel-footer"></div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="document" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="max-width:300px;margin: 30px auto;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?=lang('Export Specific Columns') ?></h4>
            </div>
            <div class="modal-body" id="checkboxes-export-selected-columns">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('Close');?></button>
                <button type="button" class="btn btn-primary" id="export-selected-columns" ><?=lang('CSV Export'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
        <input name='json_search' type="hidden">
    </form>
<?php }?>

<script type="text/javascript">
    var  PER_COLUMN_CSV_EXPORTER = (function() {
        var currentColumns = [];
        var selectedColumns = [];

        function render(){
            var len = currentColumns.length,len2 =selectedColumns.length, checkboxes='';
            for(var i=0; i<len; i++){
                checkboxes += '<div class="checkbox">';

                if(len2 > 0){
                    if (selectedColumns.indexOf(currentColumns[i].alias) > -1 ) {
                        checkboxes += '<label><input type="checkbox" class="export-select-checkbox" checked value="'+currentColumns[i].alias+'">'+currentColumns[i].name+'</label>';
                    }else{
                        checkboxes += '<label><input type="checkbox" class="export-select-checkbox" value="'+currentColumns[i].alias+'">'+currentColumns[i].name+'</label>';
                    }
                }else{
                    checkboxes += '<label><input type="checkbox" class="export-select-checkbox" value="'+currentColumns[i].alias+'">'+currentColumns[i].name+'</label>';
                }
                checkboxes += '</div>';
            }
            $('#checkboxes-export-selected-columns').html(checkboxes);
        }

        function attachExportCheckboxesEvent(){
            $('.export-select-checkbox').each(function(index, value) {
                $(this).click(function(){
                    if (selectedColumns.indexOf($(this).val()) > -1) {
                        var index = selectedColumns.indexOf($(this).val());
                        selectedColumns.splice(index, 1);
                    }else{
                        selectedColumns.push($(this).val());
                    }
                })
            });
            $("#exportSelectedColumns").remove();
            $("#form-filter").append("<input type='hidden' id='exportSelectedColumns' name='exportSelectedColumns'/>");
        }

        $('#export-selected-columns').click(function(){
            $(this).attr('disabled', 'disabled');
            $("#exportSelectedColumns").val(selectedColumns.join(","));
            $('.export-all-columns').trigger('click');
            //IMPORTANT REMOVE THE THIS ELEMENT AND APPEND NEW TO PREVENT BUG AFTER EXPORT: not fully understand
            $("#exportSelectedColumns").remove();
            $("#form-filter").append("<input type='hidden' id='exportSelectedColumns' name='exportSelectedColumns'/>");
            $(this).removeAttr("disabled");
        });

        return {
            openModal:function(columns,selected) {
                selectedColumns =  selected;
                currentColumns= columns;

                $('#myModal').modal('show')
                render();
                attachExportCheckboxesEvent();
            }
        }
    }());

    $(document).ready(function(){
        var PLAYER_REPORT_DT_CONFIG = <?php echo json_encode($this->config->item('player_report_dt_config'))?>,
           tableColumns = [],
           textRightTargets = PLAYER_REPORT_DT_CONFIG.text_right_targets,
           hiddenColsTargets = PLAYER_REPORT_DT_CONFIG.hidden_cols_targets,
           disableColsTargets = PLAYER_REPORT_DT_CONFIG.disable_cols_order_target,
           defaultExportCols = PLAYER_REPORT_DT_CONFIG.default_export_cols,
           textRightTargetsIndexes = [],
           hiddenColsTargetsIndexes = [],
           disableColsTargetsIndexes = [],
           j=0;

        $( "#myTable" ).find('th').each(function( index ) {
            if($(this).attr('id') !== undefined){
                var id = $(this).attr('id').replace('th-', "");
                tableColumns[id] = index;
            }
        });

        Object.keys(tableColumns).forEach(function(key, index) {
            if (textRightTargets.indexOf(key) > -1) {
                textRightTargetsIndexes.push(tableColumns[key]);
            }
            if (hiddenColsTargets.indexOf(key) > -1) {
                hiddenColsTargetsIndexes.push(tableColumns[key]);
            }
            if (disableColsTargets.indexOf(key) > -1) {
                disableColsTargetsIndexes .push(tableColumns[key]);
            }

            j++
        }, tableColumns);

        var dataTable = $('#myTable').DataTable({
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            lengthMenu: JSON.parse('<?=json_encode($this->utils->getConfig('default_datatable_lengthMenu'))?>'),
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            autoWidth: false,
            searching: false,
            dom: "<'panel-body' <'pull-right'B><'#export_select_columns.pull-left'><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            columnDefs: [
                { className: 'text-right', targets: textRightTargetsIndexes },
                { visible: false, targets:  hiddenColsTargetsIndexes },
                { orderable: false, targets: disableColsTargetsIndexes },
            ],
            colsNamesAliases:[],
            buttons: [
                <?php if ($export_report_permission) {?>
                    {
                        extend: 'colvis',
                        postfixButtons: [ 'colvisRestore' ]
                    }
                    ,{
                        text: "<?=lang('Export Specific Columns')?>",
                        className:'btn btn-sm btn-success',
                        action: function ( e, dt, node, config ) {
                            var columns = dataTable.init().colsNamesAliases;
                            var selected = PLAYER_REPORT_DT_CONFIG.default_export_cols;
                            PER_COLUMN_CSV_EXPORTER.openModal(columns,selected);
                        }
                    }
                    ,{
                        text: "<?=lang('CSV Export');?>",
                        className:'btn btn-sm btn-primary export-all-columns',
                        action: function ( e, dt, node, config ) {
                            var form_params=$('#form-filter').serializeArray();
                            var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,'draw':1, 'length':-1, 'start':0};
                            utils.safelog(d);
                            $("#_export_excel_queue_form").attr('action', site_url('/export_data/player_reports'));
                            $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                            $("#_export_excel_queue_form").submit();
                        }
                    }
                <?php } ?>
            ],
            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#form-filter').serializeArray();
                $.post(base_url + "api/playerReports", data, function(data) { console.log(data)

                    //add to datatable property
                    dataTable.init().colsNamesAliases = data.cols_names_aliases;
                    $('.sub-total-cashback-bonus').text(addCommas(parseFloat(data.subtotals.subtotals_deposit_bonus).toFixed(2)));
                    $('.sub-total-deposit-bonus').text(addCommas(parseFloat(data.subtotals.subtotals_cashback_bonus).toFixed(2)));
                    $('.sub-total-firstdeposits').text(addCommas(parseFloat(data.subtotals.subtotals_first_deposit).toFixed(2)));
                    $('.sub-total-manual-bonus').text(addCommas(parseFloat(data.subtotals.subtotals_manual_bonus).toFixed(2)));
                    $('.sub-total-subtract-bonus').text(addCommas(parseFloat(data.subtotals.subtotals_subtract_bonus).toFixed(2)));
                    $('.sub-total-payout-rate').text(addCommas(parseFloat(data.subtotals.subtotals_payout_rate).toFixed(2))+'%');
                    $('.sub-total-referral-bonus').text(addCommas(parseFloat(data.subtotals.subtotals_referral_bonus).toFixed(2)));
                    $('.sub-total-second-deposit').text(addCommas(parseFloat(data.subtotals.subtotals_second_deposit).toFixed(2)));
                    $('.sub-total-bets-add').text(addCommas(parseFloat(data.subtotals.subtotals_total_bets).toFixed(2)));
                    $('.sub-total-bonus-add').text(addCommas(parseFloat(data.subtotals.subtotals_total_bonus).toFixed(2)));
                    $('.sub-total-deposit-add').text(addCommas(parseFloat(data.subtotals.subtotals_total_deposit).toFixed(2)));
                    $('.sub-total-deposit-times-add').text(addCommas(parseFloat(data.subtotals.subtotals_total_deposit_times)));
                    $('.sub-total-dw-add').text(addCommas(parseFloat(data.subtotals.subtotals_total_dw).toFixed(2)));
                    $('.sub-total-payout-add').text(addCommas(parseFloat(data.subtotals.subtotals_total_payout).toFixed(2)));
                    $('.sub-total-withdrawal-add').text(addCommas(parseFloat(data.subtotals.subtotals_total_withdrawal).toFixed(2)));
                    $('.total-firstdeposits').text(addCommas(parseFloat(data.total.total_first_deposit).toFixed(2)));
                    $('.total-second-deposit').text(addCommas(parseFloat(data.total.total_second_deposit).toFixed(2)));
                    $('.total-bets-add').text(addCommas(parseFloat(data.total.total_bets_add).toFixed(2)));
                    $('.total-payout-add').text(addCommas(parseFloat(data.total.total_payout_add).toFixed(2)));
                    $('.total-payout-rate').text(addCommas(parseFloat(data.total.total_payout_rate).toFixed(2))+'%');

                    callback(data);

                    $('.total-cashback-bonus').text(data.summary[0].total_cashback);
                    $('.total-deposit-bonus').text(data.summary[0].total_deposit_bonus);
                    $('.total-referral-bonus').text(data.summary[0].total_bonus);
                    $('.total-manual-bonus').text(data.summary[0].total_add_bonus);
                    $('.total-subtract-bonus').text(data.summary[0].total_sub_bonus);
                    $('.total-bonus-add').text(data.summary[0].total_total_bonus);
                    $('.total-deposit-add').text(data.summary[0].total_deposit);
                    $('.total-deposit-times-add').text(data.summary[0].total_deposit_times);
                    $('.total-withdrawal-add').text(data.summary[0].total_withdrawal);
                    $('.total-dw-add').text(data.summary[0].total_dw);
                }, 'json');
            },
        });

        $('#group_by').change(function() {
            var value = $(this).val();
            if (value != 'player_id') {
                $('#username').val('').prop('disabled', true);
            } else {
                $('#username').val('').prop('disabled', false);
            }
        });

        $('.export_excel').click(function(){
            var d = {'extra_search':$('#form-filter').serializeArray(), 'draw':1, 'length':-1, 'start':0};
            $.post(site_url('/export_data/player_reports'), d, function(data){
                //create iframe and set link
                if(data && data.success){
                    $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                }else{
                    alert('export failed');
                }
            });
        });
    });

    function addCommas(nStr){
        nStr += '';
        var x = nStr.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }
</script>
