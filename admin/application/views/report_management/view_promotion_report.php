<?php include APPPATH . "/views/includes/popup_promorules_info.php";?>

<form class="form-horizontal" id="search-form" method="get" role="form">

<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapsePromotionReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapsePromotionReport" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
            <div class="row">
                <!-- Bonus Release From -->
                <div class="col-md-3 col-lg-3">
                    <label class="control-label"><?=lang('report.p38');?></label>
                    <input id="search_payment_date" class="form-control input-sm dateInput user-success" data-start="#byBonusPeriodJoinedFrom" data-end="#byBonusPeriodJoinedTo" data-time="false" autocomplete="off">
                    <input type="hidden" id="byBonusPeriodJoinedFrom" name="byBonusPeriodJoinedFrom" value="<?=$conditions['byBonusPeriodJoinedFrom'];?>">
                    <input type="hidden" id="byBonusPeriodJoinedTo" name="byBonusPeriodJoinedTo" value="<?=$conditions['byBonusPeriodJoinedTo'];?>">
                </div>
                <div class="col-md-3 col-lg-3 hide">
                    <label class="control-label" style="display:block;"><?php echo lang('Enabled date'); ?></label>
                    <input type="checkbox" data-off-text="<?php echo lang('off'); ?>" data-on-text="<?php echo lang('on'); ?>"  name="enableDate" data-size='mini' value='true' <?php echo $conditions['enableDate'] ? 'checked="checked"' : ''; ?>>
                </div>
                <!-- Promo Type -->
                 <div class="col-md-3 col-lg-3">
                    <label class="control-label"><?=lang('Promo Type');?></label>
                    <select name="byPromotionType" id="byPromotionType" class="form-control input-sm">
                        <option value="">-- <?=lang('N/A');?> --</option>
                        <?php foreach ($allPromoTypes as $key => $value) {?>
                            <option value="<?=$value['id']?>" <?=$conditions['byPromotionType'] == $value['id'] ? 'selected' : ''?>><?=lang($value['label'])?></option>
                        <?php }?>
                    </select>
                </div>
                <!-- Promo Rule -->
                <div class="col-md-3 col-lg-3">
                    <label class="control-label"><?=lang('Promo Rule');?></label>
                    <select name="byPromotionId" id="byPromotionId" class="form-control input-sm">
                        <option value="">-- <?=lang('N/A');?> --</option>
                        <?php foreach ($allPromo as $key => $value) {?>
                            <option value="<?=$value['id']?>" <?=$conditions['byPromotionId'] == $value['id'] ? 'selected' : ''?>><?=$value['label']?></option>
                        <?php }?>
                    </select>
                </div>
                <!-- Promotion Status -->
                <div class="col-md-3 col-lg-3">
                    <label class="control-label"><?php echo lang('Promotion Status'); ?></label>
                    <select name="byPromotionStatus" id="byPromotionStatus" class="form-control input-sm">
                        <option value="">-- <?php echo lang('N/A'); ?> --</option>
                        <option value="<?php echo Player_promo::TRANS_STATUS_APPROVED; ?>" <?=$conditions['byPromotionStatus'] == Player_promo::TRANS_STATUS_APPROVED ? 'selected' : ''?>><?php echo lang('Approved'); ?></option>
                        <option value="<?php echo Player_promo::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION; ?>" <?=$conditions['byPromotionStatus'] == Player_promo::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION ? 'selected' : ''?>><?php echo lang('Finished'); ?></option>
                        <option value="<?php echo Player_promo::TRANS_STATUS_DECLINED; ?>" <?=$conditions['byPromotionStatus'] == Player_promo::TRANS_STATUS_DECLINED ? 'selected' : ''?>><?php echo lang('Declined'); ?></option>
                    </select>
                </div>
            </div>

            <div class="row">
                <!-- Player Username -->
                <div class="col-md-3 col-lg-3">
                    <label class="control-label"><?=lang('Player Username');?></label>
                    <input type="text" name="byUsername" class="form-control input-sm" placeholder=' <?=lang('report.p03');?>'
                    value="<?php echo $conditions['byUsername']; ?>"/>
                </div>
                <!-- Player Level -->
                <div class="col-md-3 col-lg-3">
                    <label class="control-label"><?=lang('Player Level');?></label>
                    <select name="byPlayerLevel" id="byPlayerLevel" class="form-control input-sm">
                        <option value="" <?=empty($conditions['byPlayerLevel']) ? 'selected' : ''?>>--  <?=lang('lang.selectall');?> --</option>

                        <?php foreach ($vipGroupListWithLevel as $key => $value) {?>
                            <option value="<?=$value['vipsettingcashbackruleId']?>" <?=$conditions['byPlayerLevel'] == $value['vipsettingcashbackruleId'] ? 'selected' : ''?>><?=lang($value['groupName']).' - '.lang($value['vipLevelName'])?></option>
                        <?php }?>
                    </select>
                </div>
                <!-- Bonus Amount >= -->
               <div class="col-md-3 col-lg-3">
                    <label class="control-label"><?=lang('report.p36');?></label>
                    <input type="number" min="0" name="byBonusAmountGreaterThan" value="<?=$conditions['byBonusAmountGreaterThan']?>" class="form-control input-sm" placeholder='<?=lang('report.p37');?>'/>
                </div>
                <!-- Bonus Amount <= -->
                <div class="col-md-3 col-lg-3">
                    <label class="control-label"><?=lang('report.p35');?></label>
                    <input type="number" min="0" name="byBonusAmountLessThan" value="<?=$conditions['byBonusAmountLessThan']?>" class="form-control input-sm" placeholder='<?=lang('report.p37');?>'/>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12 text-right" style="margin-top: 30px;">
                    <input type="reset" value="<?=lang('lang.reset');?>" class="btn btn-danger btn-sm hidden" onclick="removeCurrentValues()">
                    <input class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" type="submit" value="<?=lang('lang.search');?>" />
                </div>
            </div>
        </div>
    </div>
</div>
</form>
        <!--end of Sort Information-->


        <div class="panel panel-primary">
            <div class="panel-heading custom-ph">
                <h4 class="panel-title custom-pt"><i class="icon-bullhorn"></i> <?=lang('report.p43');?></h4>
            </div>
            <div class="panel-body">
                <!-- result table -->
                <div id="logList" class="table-responsive">
                    <table class="table table-striped table-hover table-condensed" id="promotion_report_table" style="width:100%;">
                        <thead>
                            <tr>
                                <th><?=lang('Processed Date');?></th>
                                <th><?=lang('Player Username');?></th>
                                <th><?=lang('Player Level');?></th>
                                <th><?=lang('Promo Rule');?></th>
                                <th><?=lang('Promotion Status');?></th>
                                <th><?=lang('Amount');?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                            </tr>
                            <tr>
                                <th colspan="5" style="text-align:right">Sub total:</th>
                                <th><span id="sub_amount" class="text-right">0.00</span><br></th>
                            </tr>
                            <tr>
                                <th colspan="5" style="text-align:right">Total:</th>
                                <th><span id="total_amount" class="text-right">0.00</span><br></th>
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

<script type="text/javascript">
     var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";
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

        $('.bookmark-this').click(_pubutils.addBookmark);

        $('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').trigger('submit');
            }
        });

        $('#promotion_report_table').DataTable({
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            lengthMenu: JSON.parse('<?=json_encode($this->utils->getConfig('default_datatable_lengthMenu'))?>'),
            autoWidth: false,
            searching: false,
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
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
                        var d = {'extra_search': $('#search-form').serializeArray(), 'export_format': 'csv', 'export_type': export_type,
                            'draw':1, 'length':-1, 'start':0};
                        <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                                    $("#_export_excel_queue_form").attr('action', site_url('/export_data/promotion_report'));
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
                { className: 'text-right', targets: [ 4 ] },
            ],
            "order": [ 0, 'desc' ],
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/promotion_report", data, function(data) {
                    var  subTotal =0;
                    var sub = 0;
                    console.log('data',settings);
                    $.each(data.data, function(i, v){
                        sub = v[5].replace(/<(?:.|\n)*?>/gm, '');
                        if(Number.parseFloat(sub)){
                            subTotal+= Number.parseFloat(sub);
                        }
                    });
                    $('#sub_amount').text(parseFloat(subTotal).toFixed(2));
                    callback(data);
                    $('#total_amount').text(data.summary[0].total_amount);
                    if ( $('#promotion_report_table').DataTable().rows( { selected: true } ).indexes().length === 0 ) {
                        $('#promotion_report_table').DataTable().buttons().disable();
                    }
                    else {
                        $('#promotion_report_table').DataTable().buttons().enable();
                    }
                },'json');
            },
        });
    });

    function removeCurrentValues(){
        $("input[type='number']").removeAttr('value');
        $("input[type='text']").removeAttr('value');
        $("select option").removeAttr('selected');
    }
</script>