<style>
    .font-bold{
        font-weight: bold;
    }
</style>
<div class="panel panel-primary hidden">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapsePlayerReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapsePlayerReport" class="panel-collapse <?= $this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
            <form id="form-filter" class="form-horizontal" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <label class="control-label"><?=lang('report.sum02')?></label>
                        <input class="form-control dateInput input-sm" data-start="#date_from" data-end="#date_to" data-time="true"/>
                        <input type="hidden" id="date_from" name="date_from" value="<?=$conditions['date_from'];?>"/>
                        <input type="hidden" id="date_to" name="date_to" value="<?=$conditions['date_to'];?>"/>
                    </div>
                    <div class="col-md-3">
                        <label for="username" class="control-label">
                            <input type="radio" name="search_by" value="1" checked <?=$conditions['search_by'] == '1' ? 'checked="checked"' : '' ?> /> <?=lang('Similar');?>
                            <?=lang('Username'); ?>
                            <input type="radio" name="search_by" value="2" <?=$conditions['search_by'] == '2' ? 'checked="checked"' : ''?> /> <?=lang('Exact'); ?>
                            <?=lang('Username'); ?>
                        </label>
                        <input type="text" name="username" id="username" class="form-control input-sm" value="<?= $conditions['username'];?>" <?= ($conditions['group_by'] != 'player_id' && $conditions['group_by'] != '') ? 'disabled' : ''   ?> />
                    </div>
                    <div class="col-md-2">
                        <label for="referrer" class="control-label"><?=lang('Referrer')?></label>
                        <input type="text" name="referrer" id="referrer" class="form-control input-sm" value="<?=$conditions['referrer']; ?>"/>
                    </div>
                    <div class="col-md-3">
                        <label for="player_tag" class="control-label"><?=lang('exclude_player')?></label>
                        <select name="tag_list[]" id="tag_list" multiple="multiple" class="form-control input-sm">
                            <option value="notag" id="notag" <?=is_array($selected_tags) && in_array('notag', $selected_tags) ? "selected" : "" ?>><?=lang('player.tp12')?></option>
                            <?php if (!empty($tags)): ?>
                                <?php foreach ($player_tags as $tag): ?>
                                    <option value="<?=$tag['tagId']?>" <?=is_array($selected_tags) && in_array($tag['tagId'], $selected_tags) ? "selected" : "" ?> ><?=$tag['tagName']?></option>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label class="control-label" for="playerlevel"><?=lang('report.pr03')?></label>
                        <select name="playerlevel" id="playerlevel" class="form-control input-sm">
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
                    <div class="col-md-2">
                        <label class="control-label" for="group_by"><?=lang('report.g14')?> </label>
                        <select name="group_by" id="group_by" class="form-control input-sm">
                            <option value="player_id" <?php echo  ($conditions['group_by'] == 'player_id') ? 'selected' : ''  ?> ><?=lang('report.pr01')?></option>
                            <option value="playerlevel" <?php echo  ($conditions['group_by'] == 'playerlevel') ? 'selected' : ''  ?> ><?=lang('player.07')?></option>
                            <?php if($this->utils->isEnabledFeature('show_search_affiliate') && !$this->utils->isEnabledFeature('close_aff_and_agent')): ?>
                                <option value="affiliate_id" <?php echo  ($conditions['group_by'] == 'affiliate_id') ? 'selected' : ''  ?> ><?php echo lang('Affiliate'); ?></option>
                            <?php endif?>
                            <?php if(!$this->utils->isEnabledFeature('close_aff_and_agent')): ?>
                                <option value="agent_id" <?php echo  ($conditions['group_by'] == 'agent_id') ? 'selected' : ''  ?> ><?php echo lang('Agency'); ?></option>
                            <?php endif?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label" for="depamt2"><?=lang('report.pr31') . " >="?></label>
                        <input type="text" name="depamt2" id="depamt2" class="form-control number_only input-sm" value="<?= $conditions['depamt2'];?>"/>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label" for="depamt1"><?=lang('report.pr31') . " <="?></label>
                        <input type="text" name="depamt1" id="depamt1" class="form-control number_only input-sm" value="<?= $conditions['depamt1'];?>"/>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label" for="widamt2"><?=lang('report.pr32') . " >="?></label>
                        <input type="text" name="widamt2" id="widamt2" class="form-control number_only input-sm" value="<?= $conditions['widamt2'];?>"/>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label" for="widamt1"><?=lang('report.pr32') . " <="?></label>
                        <input type="text" name="widamt1" id="widamt1" class="form-control number_only input-sm" value="<?= $conditions['widamt1'];?>"/>
                    </div>
                </div>
                <div class="row">
                    <?php if(!$this->utils->isEnabledFeature('close_aff_and_agent')){?>
                        <div class="col-md-4">
                            <label class="control-label" for="agent_name"><?=lang('Agent Username')?> </label>
                            <div class="input-group">
                                <input type="text" name="agent_name" id="agent_name" class="form-control input-sm" value="<?= $conditions['agent_name'];?>"/>
                                <span class="input-group-addon">
                                    <input type="checkbox" name="include_all_downlines" value="true" <?=$conditions['include_all_downlines'] == 'true' ? 'checked="checked"' : '' ?>/>
                                    <?=lang('Include Downlines')?>
                                </span>
                            </div>
                        </div>
                    <?php }?>
                    <div class="col-md-2">
                        <label for="affiliate_agent" class="control-label"><?=lang('Under Affiliate/Agent Status')?></label>
                        <select name="affiliate_agent" id="affiliate_agent" class="form-control input-sm">
                            <option value=""><?=lang('lang.selectall')?></option>
                            <option value="2" <?php if($conditions['affiliate_agent'] == 2) echo 'selected'; ?>><?=lang('Under Affiliate Only')?></option>
                            <option value="3" <?php if($conditions['affiliate_agent'] == 3) echo 'selected'; ?>><?=lang('Under Agent Only')?></option>
                            <option value="4" <?php if($conditions['affiliate_agent'] == 4) echo 'selected'; ?>><?=lang('Under Affiliate or Agent')?></option>
                            <option value="1" <?php if($conditions['affiliate_agent'] == 1) echo 'selected'; ?>><?=lang('Not under any Affiliate or Agent')?></option>
                        </select>
                    </div>
                    <?php if($this->utils->isEnabledFeature('show_search_affiliate') && !$this->utils->isEnabledFeature('close_aff_and_agent') ){?>
                        <div class="col-md-4">
                            <label class="control-label" for="affiliate_name"><?=lang('Affiliate Username')?> </label>
                            <div class="input-group">
                                <input type="text" name="affiliate_name" id="affiliate_name" class="form-control input-sm" value="<?= $conditions['affiliate_name'];?>" />
                                <span class="input-group-addon">
                                    <input type="checkbox" name="aff_include_all_downlines" value="true" <?=$conditions['aff_include_all_downlines'] == 'true' ? 'checked="checked"' : '' ?>/>
                                    <?=lang('Include Downlines')?>
                                </span>
                            </div>
                        </div>
                    <?php }?>
                    <?php if($this->utils->isEnabledFeature('show_search_affiliate_tag') && !$this->utils->isEnabledFeature('close_aff_and_agent')){?>
                        <div class="col-md-2">
                            <label class="control-label"><?=lang('Affiliate Tag'); ?></label><br>
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
                    <div class="col-md-2 col-md-offset-10" style="padding-top: 20px">
                        <input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn btn-portage btn-sm pull-right' : 'btn btn-info btn-sm pull-right' ?>">
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
                        <th id="th-register-date" style="min-width: 95px;"><?=lang('report.pr10')?></th>
                        <th id="th-first-deposit-date" style="min-width: 95px;"><?=lang('aff.ap06')?></th>
                        <th id="th-total-cashback-bonus" ><?=lang('report.sum15')?></th>
                        <th id="th-total-deposit-bonus" ><?=lang('report.pr15')?></th>
                        <th id="th-total-referral-bonus" ><?=lang('report.pr17')?></th>
                        <th id="th-manual-bonus"><?=lang('transaction.manual_bonus')?></th>
                        <th id="th-subtract-bonus" ><?=lang('transaction.transaction.type.10')?></th>
                        <th id="th-total-bonus"><?=lang('report.pr18')?> &nbsp;<i class="fa fa-info-circle"></i></th>
                        <th id="th-total-first-deposit" ><?=lang('player.75')?></th>
                        <th id="th-total-deposit"><?=lang('report.pr21')?></th>
                        <th id="th-deposit-times"><?=lang('yuanbao.deposit.times')?></th>
                        <th id="th-deposit-and-bonus" style="min-width: 50px;"><?=lang('DNB')?> &nbsp;<i class="fa fa-info-circle"></i></th>
                        <th id="th-bonus-over-deposit" style="min-width: 60px;"><?=lang('BOD%')?> &nbsp;<i class="fa fa-info-circle"></i></th>
                        <th id="th-total-withdrawal" ><?=lang('report.pr22')?></th>
                        <th id="th-deposit-minus-withdrawal" ><?=lang('Net Deposit')?> &nbsp;<i class="fa fa-info-circle"></i></th>
                        <th id="th-withdrawal-over-deposit" style="min-width: 60px;"><?=lang('WOD%')?> &nbsp;<i class="fa fa-info-circle"></i></th>
                        <th id="th-total-bets" ><?=lang('cms.totalbets')?></th>
                        <th id="th-turn-around-time" style="min-width: 50px;"><?=lang('TAT')?> &nbsp;<i class="fa fa-info-circle"></i></th>
                        <th id="th-total-win"><?=lang('Win')?></th>
                        <th id="th-total-loss"><?=lang('Loss')?></th>
                        <th id="th-payout" style="min-width: 60px;"><?=lang('Payout')?> &nbsp;<i class="fa fa-info-circle"></i></th>
                        <th id="th-payout-rate"><?=lang('sys.payoutrate')?> &nbsp;<i class="fa fa-info-circle"></i></th>
                        <th id="th-total-revenue"><?=lang('Game Revenue')?> &nbsp;<i class="fa fa-info-circle"></i></th>
                        <th id="th-bet-details" style="min-width:170px;"><?=lang('Bet Detail')?></th>
                    </tr>
                </thead>
                <tfoot>
                    <?php if ($this->utils->isEnabledFeature('show_total_for_player_report')) {?>
                        <tr>
                            <th><?=lang('Sub Total')?></th>
                            <th colspan="2" ></th>
                            <?php if($this->utils->isEnabledFeature('show_risk_score')): ?>
                                <th></th>
                            <?php endif?>
                            <?php if($this->utils->isEnabledFeature('show_kyc_status')): ?>
                                <th></th>
                            <?php endif?>
                            <th></th>
                            <?php if( !$this->utils->isEnabledFeature('close_aff_and_agent') && $this->utils->isEnabledFeature('show_search_affiliate')): ?>
                                <th></th>
                            <?php endif?>
                            <?php if( !$this->utils->isEnabledFeature('close_aff_and_agent')): ?>
                                <th></th>
                            <?php endif?>
                            <th></th>
                            <th><span class="sub-total-cashback-bonus">0.00</span></th>
                            <th><span class="sub-total-deposit-bonus">0.00</span></th>
                            <th><span class="sub-total-referral-bonus">0.00</span></th>
                            <th><span class="sub-total-manual-bonus">0.00</span></th>
                            <th><span class="sub-total-subtract-bonus">0.00</span></th>
                            <th><span class="sub-total-bonus-add">0.00</span></th>
                            <th><span class="sub-total-firstdeposits">0.00</span></th>
                            <th><span class="sub-total-deposit-add">0.00</span></th>
                            <th><span class="sub-total-deposit-times-add">0.00</span></th>
                            <th><span class="sub-total-dnb">0.00</span></th>
                            <th><span class="sub-total-bod">0.00%</span></th>
                            <th><span class="sub-total-withdrawal-add">0.00</span></th>
                            <th><span class="sub-total-dw-add">0.00</span></th>
                            <th><span class="sub-total-wod">0.00%</span></th>
                            <th><span class="sub-total-bets-add">0.00</span></th>
                            <th><span class="sub-total-tat">0.00</span></th>
                            <th><span class="sub-total-win">0.00</span></th>
                            <th><span class="sub-total-loss">0.00</span></th>
                            <th><span class="sub-total-payout-add">0.00</span></th>
                            <th><span class="sub-total-payout-rate">0.00%</span></th>
                            <th><span class="sub-total-game-revenue">0.00</span></th>
                            <th></th>
                        </tr>

                        <!--TODO NOT POSSIBLE NOW DUE TOTALS NOT TALLYING-->
                        <tr>
                            <th class="text-primary"><?=lang('Total')?></th>
                            <th colspan="2" ></th>
                            <?php if($this->utils->isEnabledFeature('show_risk_score')): ?>
                                <th></th>
                            <?php endif?>
                            <?php if($this->utils->isEnabledFeature('show_kyc_status')): ?>
                                <th></th>
                            <?php endif?>
                            <th></th>
                            <?php if( !$this->utils->isEnabledFeature('close_aff_and_agent') && $this->utils->isEnabledFeature('show_search_affiliate')): ?>
                                <th></th>
                            <?php endif?>
                            <?php if( !$this->utils->isEnabledFeature('close_aff_and_agent')): ?>
                                <th></th>
                            <?php endif?>
                            <th></th>
                            <th class="text-right"><span class="total-cashback-bonus text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-deposit-bonus text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-referral-bonus text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-manual-bonus text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-subtract-bonus text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-bonus-add text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-firstdeposits text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-deposit-add text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-deposit-times-add text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-dnb text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-bod text-primary">0.00%</span></th>
                            <th class="text-right"><span class="total-withdrawal-add text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-dw text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-wod text-primary">0.00%</span></th>
                            <th class="text-right"><span class="total-bets-add text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-tat text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-win text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-loss text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-payout-add text-primary">0.00</span></th>
                            <th class="text-right"><span class="total-payout-rate text-primary">0.00%</span></th>
                            <th class="text-right"><span class="total-game-revenue text-primary">0.00</span></th>
                            <th></th>
                        </tr>
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
                <h4 class="modal-title" id="myModalLabel"><?php echo lang('Export Specific Columns') ?></h4>
            </div>
            <div class="modal-body" id="checkboxes-export-selected-columns">
            ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="export-selected-columns" ><?php echo lang('CSV Export'); ?></button>
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
                    } else {
                        checkboxes += '<label><input type="checkbox" class="export-select-checkbox" value="'+currentColumns[i].alias+'">'+currentColumns[i].name+'</label>';
                    }
                } else {
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

                $('#myModal').modal('show');
                render();
                attachExportCheckboxesEvent();
            }
        }
    }());

    $(document).ready(function(){
        $('#tag_list').multiselect({
            enableFiltering: true,
            includeSelectAllOption: true,
            selectAllJustVisible: false,
            buttonWidth: '100%',
            buttonText: function(options, select) {
                if (options.length === 0) {
                    return 'Select Tags';
                }
                else {
                    var labels = [];
                    options.each(function() {
                        if ($(this).attr('label') !== undefined) {
                            labels.push($(this).attr('label'));
                        }
                        else {
                            labels.push($(this).html());
                        }
                    });
                    return labels.join(', ') + '';
                }
            }
        });

        var PLAYER_REPORT_DT_CONFIG   =  <?php echo json_encode($this->config->item('player_report_dt_config'))?>,
            tableColumns              = [],
            textRightTargets          = PLAYER_REPORT_DT_CONFIG.text_right_targets,
            hiddenColsTargets         = PLAYER_REPORT_DT_CONFIG.hidden_cols_targets,
            disableColsTargets        = PLAYER_REPORT_DT_CONFIG.disable_cols_order_target,
            defaultExportCols         = PLAYER_REPORT_DT_CONFIG.default_export_cols,
            textRightTargetsIndexes   = [],
            hiddenColsTargetsIndexes  = [],
            disableColsTargetsIndexes = [],
            j = 0;

        $( "#myTable" ).find('th').each(function( index ) {
            if ($(this).attr('id') !== undefined){
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
            pageLength: 50,
            autoWidth: false,
            searching: false,
            dom: "<'panel-body' <'pull-right'B><'#export_select_columns.pull-left'><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            columnDefs: [
                { className: 'text-right font-bold', targets: textRightTargetsIndexes },
                { visible: false, targets:  hiddenColsTargetsIndexes },
                { orderable: false, targets: disableColsTargetsIndexes }
            ],
            colsNamesAliases:[],
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: "<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : '' ?>"
                },
                <?php if ($export_report_permission) {?>
                    {
                        text: "<?php echo lang('Export Specific Columns') ?>",
                        className:"<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn btn-sm btn-scooter' : 'btn btn-sm btn-success' ?>",
                        action: function ( e, dt, node, config ) {
                            var columns = dataTable.init().colsNamesAliases;
                            var  selected = PLAYER_REPORT_DT_CONFIG.default_export_cols;

                            PER_COLUMN_CSV_EXPORTER.openModal(columns,selected);
                        }
                    },
                    {
                        text: "<?php echo lang('CSV Export'); ?>",
                        className:"<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn btn-sm btn-portage export-all-columns' : 'btn btn-sm btn-primary export-all-columns' ?>",
                        action: function ( e, dt, node, config ) {
                            var form_params=$('#form-filter').serializeArray();
                            var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,'draw':1, 'length':-1, 'start':0};
                            utils.safelog(d);

                            $("#_export_excel_queue_form").attr('action', site_url('/export_data/player_reports_2'));
                            $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                            $("#_export_excel_queue_form").submit();
                        }
                    }
                <?php } ?>
            ],
            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            "initComplete": function(settings){
                $('#myTable thead th').each(function () {
                    var $td = $(this);
                    if($td.attr("id") == 'th-total-bonus'){
                        $td.attr('title', '<?=lang("tb_formula")?>');
                    }
                    if($td.attr("id") == 'th-deposit-and-bonus'){
                        $td.attr('title', '<?=lang("dnb_formula")?>');
                    }
                    if($td.attr("id") == 'th-bonus-over-deposit'){
                        $td.attr('title', '<?=lang("bod_formula")?>');
                    }
                    if($td.attr("id") == 'th-deposit-minus-withdrawal'){
                        $td.attr('title', '<?=lang("nd_formula")?>');
                    }
                    if($td.attr("id") == 'th-withdrawal-over-deposit'){
                        $td.attr('title', '<?=lang("wod_formula")?>');
                    }
                    if($td.attr("id") == 'th-turn-around-time'){
                        $td.attr('title', '<?=lang("tat_formula")?>');
                    }
                    if($td.attr("id") == 'th-payout'){
                        $td.attr('title', '<?=lang("games_report_payout_formula")?>');
                    }
                    if($td.attr("id") == 'th-payout-rate'){
                        $td.attr('title', '<?=lang("payout_rate_formula")?>');
                    }
                    if($td.attr("id") == 'th-total-revenue'){
                        $td.attr('title', '<?=lang("games_report_revenue_formula")?>');
                    }
                });

                /* Apply the tooltips */
                $('#myTable thead th[title]').tooltip({
                    "container": 'body'
                });
            },
            ajax: function (data, callback, settings) {
                data.extra_search = $('#form-filter').serializeArray();
                $.post(base_url + "api/playerReports2", data, function(data) {

                    //add to datatable property
                    dataTable.init().colsNamesAliases = data.cols_names_aliases;

                    <?php if ($this->utils->isEnabledFeature('show_total_for_player_report')) {?>
                        $('.sub-total-cashback-bonus').text(addCommas(parseFloat(data.subtotals.subtotals_cashback_bonus).toFixed(2)));
                        $('.sub-total-deposit-bonus').text(addCommas(parseFloat(data.subtotals.subtotals_deposit_bonus).toFixed(2)));
                        $('.sub-total-referral-bonus').text(addCommas(parseFloat(data.subtotals.subtotals_referral_bonus).toFixed(2)));
                        $('.sub-total-manual-bonus').text(addCommas(parseFloat(data.subtotals.subtotals_manual_bonus).toFixed(2)));
                        $('.sub-total-subtract-bonus').text(addCommas(parseFloat(data.subtotals.subtotals_subtract_bonus).toFixed(2)));
                        $('.sub-total-bonus-add').text(addCommas(parseFloat(data.subtotals.subtotals_total_bonus).toFixed(2)));
                        $('.sub-total-firstdeposits').text(addCommas(parseFloat(data.subtotals.subtotals_first_deposit).toFixed(2)));
                        $('.sub-total-deposit-add').text(addCommas(parseFloat(data.subtotals.subtotals_total_deposit).toFixed(2)));
                        $('.sub-total-deposit-times-add').text(addCommas(parseFloat(data.subtotals.subtotals_total_deposit_times)));
                        $('.sub-total-dnb').text(addCommas(parseFloat(data.subtotals.subtotals_dnb).toFixed(2)));
                        $('.sub-total-bod').text(data.subtotals.subtotals_bod);
                        $('.sub-total-withdrawal-add').text(addCommas(parseFloat(data.subtotals.subtotals_total_withdrawal).toFixed(2)));
                        $('.sub-total-dw-add').text(addCommas(parseFloat(data.subtotals.subtotals_total_dw).toFixed(2)));
                        $('.sub-total-wod').text(addCommas(parseFloat(data.subtotals.subtotals_wod).toFixed(2))+'%');
                        $('.sub-total-bets-add').text(addCommas(parseFloat(data.subtotals.subtotals_total_bets).toFixed(2)));
                        $('.sub-total-tat').text(addCommas(parseFloat(data.subtotals.subtotals_tat).toFixed(2)));
                        $('.sub-total-win').text(addCommas(parseFloat(data.subtotals.subtotals_total_win).toFixed(2)));
                        $('.sub-total-loss').text(addCommas(parseFloat(data.subtotals.subtotals_total_loss).toFixed(2)));
                        $('.sub-total-payout-add').text(addCommas(parseFloat(data.subtotals.subtotals_total_payout).toFixed(2)));
                        $('.sub-total-payout-rate').text(addCommas(parseFloat(data.subtotals.subtotals_payout_rate).toFixed(2))+'%');
                        $('.sub-total-game-revenue').text(addCommas(parseFloat(data.subtotals.subtotals_game_revenue).toFixed(2)));

                        $('.total-cashback-bonus').text(data.total.total_cashback);
                        $('.total-deposit-bonus').text(data.total.total_deposit_bonus);
                        $('.total-referral-bonus').text(data.total.total_referral_bonus);
                        $('.total-manual-bonus').text(data.total.total_add_bonus);
                        $('.total-subtract-bonus').text(data.total.total_subtract_bonus);
                        $('.total-bonus-add').text(data.total.total_total_bonus);
                        $('.total-firstdeposits').text(data.total.total_first_deposit);
                        $('.total-deposit-add').text(data.total.total_deposit);
                        $('.total-deposit-times-add').text(addCommas(parseFloat(data.total.total_deposit_times)));
                        $('.total-dnb').text(data.total.total_dnb);
                        $('.total-bod').text(data.total.total_bod);
                        $('.total-withdrawal-add').text(data.total.total_withdrawal);
                        $('.total-dw').text(data.total.total_dw);
                        $('.total-wod').text(data.total.total_wod);
                        $('.total-bets-add').text(data.total.total_bets);
                        $('.total-tat').text(data.total.total_tat);
                        $('.total-win').text(data.total.total_win);
                        $('.total-loss').text(data.total.total_loss);
                        $('.total-payout-add').text(data.total.total_payout);
                        $('.total-payout-rate').text(data.total.total_payout_rate);
                        $('.total-game-revenue').text(data.total.total_game_revenue);
                        $('.total-dw-add').text(data.total.total_dw);
                    <?php }?>

                    callback(data);
                }, 'json');
            }
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

            $.post(site_url('/export_data/player_reports_2'), d, function(data){
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
