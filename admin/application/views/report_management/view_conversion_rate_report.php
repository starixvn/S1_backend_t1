<?php include APPPATH . "/views/includes/popup_promorules_info.php";?>

<form class="form-horizontal" id="search-form" method="get" role="form">

<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapsePromotionReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
            <?php include __DIR__ . "/../includes/report_tools.php"?>
        </h4>
    </div>


    <div id="collapsePromotionReport" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4 col-lg-4">
                    <div class="control-label">
                        <label class="control-label"><?=lang('Registeration Date');?></label>
                        <input id="search_registration_date" class="form-control input-sm dateInput" data-time="false" autocomplete="off" data-start="#registration_date_from" data-end="#registration_date_to">
                        <input type="hidden" name="registration_date_from" id="registration_date_from" value="<?=$conditions['registration_date_from'];?>" />
                        <input type="hidden" name="registration_date_to" id="registration_date_to" value="<?=$conditions['registration_date_to'];?>" />
                    </div><!-- EOF .control-label -->
                </div>

                <div class="col-md-4 col-lg-4">
                    <div class="control-label">
                        <label class="control-label" for="search_first_deposit_date"><?=lang('First Deposit Date')?></label>
                        <div class="input-group">
                            <input id="search_first_deposit_date" class="form-control input-sm dateInput" data-time="false" data-start="#first_deposit_date_from" data-end="#first_deposit_date_to" />
                            <span class="input-group-addon input-sm">
                                <input type="checkbox" name="search_first_deposit_date_switch" id="search_first_deposit_date_switch" <?php echo $conditions['search_first_deposit_date_switch']  == 'on' ? 'checked="checked"' : '' ?> >
                            </span>
                            <input type="hidden" name="first_deposit_date_from" id="first_deposit_date_from" value="<?=$conditions['first_deposit_date_from'];?>" />
                            <input type="hidden" name="first_deposit_date_to" id="first_deposit_date_to" value="<?=$conditions['first_deposit_date_to'];?>" />
                        </div>
                    </div><!-- EOF .control-label -->
                </div>
            </div> <!-- EOF .row -->


            <div class="row">
                <div class="col-md-4 col-lg-4">
                    <div class="control-label">
                        <label class="control-label" for="SummaryBy"><?=lang('Summary by')?></label>
                        <select id="SummaryBy" name="SummaryBy"  class="form-control input-sm">
                            <option value="<?=$SummaryBy['All']?>"><?=lang('All');?></option>
                            <option value="<?=$SummaryBy['DirectPlayer']?>" <?php echo ($conditions['SummaryBy']==$SummaryBy['DirectPlayer']) ? 'selected' : ''?> ><?=lang('Direct Player');?></option>
                            <option value="<?=$SummaryBy['Affiliate']?>" <?php echo ($conditions['SummaryBy']==$SummaryBy['Affiliate']) ? 'selected' : ''?> ><?=lang('Affiliate');?></option>
                            <option value="<?=$SummaryBy['Agency']?>" <?php echo ($conditions['SummaryBy']==$SummaryBy['Agency']) ? 'selected' : ''?> ><?=lang('Agency');?></option>
                            <option value="<?=$SummaryBy['Referrer']?>" <?php echo ($conditions['SummaryBy']==$SummaryBy['Referrer']) ? 'selected' : ''?> ><?=lang('Referrer');?></option>
                        </select>
                    </div><!-- EOF .control-label -->
                </div>
                <div class="col-md-4 col-lg-4">
                    <div class="control-label colSummaryBy SummaryByAll">
                    </div><!-- EOF .control-label -->
                    <div class="control-label colSummaryBy SummaryByDirectPlayer hidden">
                    </div><!-- EOF .control-label -->
                    <div class="control-label colSummaryBy SummaryByAffiliate hidden">
                        <label class="control-label" for="affiliate_username"><?=lang('cr.Affiliate Username');?></label>
                        <input type="text" name="affiliate_username" id="affiliate_username" class="form-control input-sm" value="<?=$conditions['affiliate_username']?>">
                    </div><!-- EOF .control-label -->
                    <div class="control-label colSummaryBy SummaryByAgency hidden">
                        <label class="control-label" for="agency_username"><?=lang('Agency Username');?></label>
                        <input type="text" name="agency_username" id="agency_username" class="form-control input-sm" value="<?=$conditions['agency_username']?>">
                    </div><!-- EOF .control-label -->
                    <div class="control-label colSummaryBy SummaryByReferrer hidden">
                        <label class="control-label" for="referrers_username"><?=lang('Referrers Username');?></label>
                        <input type="text" name="referrers_username" id="referrers_username" class="form-control input-sm" value="<?=$conditions['referrers_username']?>">
                    </div><!-- EOF .control-label -->
                </div>
            </div> <!-- EOF .row -->

            <div class="row">
                <div class="col-md-4 col-md-offset-4" style="">
                    <div class="control-label">
                        <input type="reset" value="<?=lang('lang.reset');?>" class="btn btn-danger btn-sm hidden" onclick="removeCurrentValues()">
                        <input class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" type="submit" value="<?=lang('lang.search');?>" />
                    </div><!-- EOF .control-label -->
                </div>
            </div> <!-- EOF .row -->
        </div>
    </div>

</div>
</form>
        <!--end of Sort Information-->


        <div class="panel panel-primary summaryByList summaryByAll">
            <div class="panel-heading custom-ph">
                <h4 class="panel-title custom-pt"><i class="fa fa-crosshairs fa-fw"></i> <?=lang('Conversion Rate Report');?></h4>
            </div>
            <div class="panel-body">
                <!-- result table -->
                <div id="logList" class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="conversion_rate_table" >
                        <thead>
                            <tr>
                                <?php include __DIR__ . '/../includes/cols_for_conversion_rate_list.php';?>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> <!-- EOF .panel-body -->
            <!--end of result table -->
            <!-- <div class="panel-footer"></div> -->
        </div>
<style>
.th {
    font-weight: bold;
}
.hide-border-left{
    border-left-width: 0 !important;
}
.hide-border-right{
    border-right-width: 0 !important;
}
</style>
        <div class="panel panel-primary summaryByList summaryByDirectPlayer hidden">
            <div class="panel-heading custom-ph">
                <h4 class="panel-title custom-pt"><i class="fa fa-crosshairs fa-fw"></i> <?=lang('Conversion Rate Report');?></h4>
            </div>
            <div class="panel-body">
                <!-- result table -->
                <div id="logList4DirectPlayer" class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="conversion_rate_table4direct" >
                        <thead>
                            <tr>
                                <?php include __DIR__ . '/../includes/cols_for_conversion_rate_list4directPlayer.php';?>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="hide-border-left hide-border-right"></th>
                                <th class="hide-border-left hide-border-right"></th>
                                <th class="hide-border-left hide-border-right"></th>
                                <th class="text-left text-primary hide-border-left hide-border-right"><?=lang('Conversion Rate')?>：<span class="total-conversion-rate" data-orig-class="total-payout-rate">0%</span></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div> <!-- EOF .panel-body -->
            <!-- end of result table -->
            <!-- <div class="panel-footer"></div> -->
        </div> <!-- EOF .summaryByDirectPlayer -->

        <div class="panel panel-primary summaryByList summaryByAffiliate hidden">
            <div class="panel-heading custom-ph">
                <h4 class="panel-title custom-pt"><i class="fa fa-crosshairs fa-fw"></i> <?=lang('Conversion Rate Report');?></h4>
            </div>
            <div class="panel-body">
                <!-- result table -->
                <div id="logList4Affiliate" class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="conversion_rate_table4affiliate" >
                        <thead>
                            <tr>
                                <?php include __DIR__ . '/../includes/cols_for_conversion_rate_list4affiliate.php';?>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> <!-- EOF .panel-body -->
            <!--end of result table -->
            <!-- <div class="panel-footer"></div> -->
        </div><!-- EOF .summaryByAffiliate -->

        <div class="panel panel-primary summaryByList summaryByAgency hidden">
            <div class="panel-heading custom-ph">
                <h4 class="panel-title custom-pt"><i class="fa fa-crosshairs fa-fw"></i> <?=lang('Conversion Rate Report');?></h4>
            </div>
            <div class="panel-body">
                <!-- result table -->
                <div id="logList4Agency" class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="conversion_rate_table4agency" >
                        <thead>
                            <tr>
                                <?php include __DIR__ . '/../includes/cols_for_conversion_rate_list4agency.php';?>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> <!-- EOF .panel-body -->
            <!--end of result table -->
            <!-- <div class="panel-footer"></div> -->
        </div><!-- EOF .summaryByAgency -->

        <div class="panel panel-primary summaryByList summaryByReferrer hidden">
            <div class="panel-heading custom-ph">
                <h4 class="panel-title custom-pt"><i class="fa fa-crosshairs fa-fw"></i> <?=lang('Conversion Rate Report');?></h4>
            </div>
            <div class="panel-body">
                <!-- result table -->
                <div id="logList4Referrer" class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="conversion_rate_table4referrer" >
                        <thead>
                            <tr>
                                <?php include __DIR__ . '/../includes/cols_for_conversion_rate_list4referrer.php';?>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> <!-- EOF .panel-body -->
            <!--end of result table -->
            <!-- <div class="panel-footer"></div> -->
        </div><!-- EOF .summaryByReferrer -->

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

        if( typeof( $("input[type='checkbox']").bootstrapSwitch ) !== 'undefined'){
            $("input[type='checkbox']").bootstrapSwitch();
        }
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


        oCR.initDataTableWithSummaryBySelect( $('select[name="SummaryBy"]') );

        $('body').on('change', 'select[name="SummaryBy"]', function(e){
            var theTarget$El = $('select[name="SummaryBy"]');

            $('.colSummaryBy').addClass('hidden');
            $('.colSummaryBy input').attr('disabled', true);

            var selectedColSummaryBy$El = $('.colSummaryBy.SummaryBy'+ theTarget$El.val());
            selectedColSummaryBy$El.removeClass('hidden');
            selectedColSummaryBy$El.find('input').removeAttr('disabled', true);

        });

        $("select[name=SummaryBy]").trigger('change');

        $('body').on('change', 'input[name="search_first_deposit_date_switch"]', function(e){
            if(this.checked) {
                $('#search_first_deposit_date').prop('disabled',false);
            }else{
                $('#search_first_deposit_date').prop('disabled',true);
            }
        })
        $("input[name=search_first_deposit_date_switch]").trigger('change');


    }); // EOF $(document).ready(function(){...

    function removeCurrentValues(){
        $("input[type='number']").removeAttr('value');
        $("input[type='text']").removeAttr('value');
        $("select option").removeAttr('selected');
    }

    /**
     * oCR for conversionRate UI.
     * Wrapper for prevention of dirty variables
     */
    var oCR = (function(){ /// oCR = conversionRate

        var conversionRate = {};

        conversionRate.ADTP = {}; // ADTP = applyDataTableParams, for applyData().

        conversionRate.export_report_permission = false;
        <?php if ($export_report_permission) { ?>
        conversionRate.export_report_permission = true;
        <?php } // EOF if ($export_report_permission)  ?>

        conversionRate.stateSave = false;
        <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
        conversionRate.stateSave = true;
        <?php } ?>

        conversionRate.default_datatable_lengthMenu = JSON.parse('<?=json_encode($this->utils->getConfig('default_datatable_lengthMenu'))?>');

        conversionRate.pageLength = <?=$this->utils->getDefaultItemsPerPage()?>;

        conversionRate.searchForm$El = $('#search-form');


        conversionRate.btnExportCSVOption = {
            text: "<?php echo lang('CSV Export'); ?>",
            className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
            action: function ( e, dt, node, config ) {
                var d = {'extra_search': conversionRate.searchForm$El.serializeArray()
                    , 'export_format': 'csv'
                    , 'export_type': export_type
                    , 'draw':1
                    , 'length':-1
                    , 'start':0
                };
                conversionRate.export_excel_on_queue(d);
            } // EOF action: function ( e, dt, node, config )
        };

        conversionRate.export_excel_on_queue = function(d){
            <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                var _this = this;
                var SummaryBy = _this.getQueryVariable('SummaryBy');
                $("#_export_excel_queue_form")
                $("#_export_excel_queue_form").attr('action', site_url('/export_data/conversion_rate_report/'+ SummaryBy));
                $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                $("#_export_excel_queue_form").submit();
            <?php } // EOF if($this->utils->isEnabledFeature('export_excel_on_queue')){... ?>
        }


        /**
         * initial DataTable Summary By XXX.
         * @param {string} summaryBy The report summary by XXX. ex: all, affiliate, agency and referer.
         */
        conversionRate.initDataTableWith = function(summaryBy){
            var _this = this;

            if( typeof(summaryBy) === 'undefined'){
                summaryBy = 'all';
            }

            switch( summaryBy.toLowerCase() ){
                case 'all':
                    _this.initDataTableSummaryByAll();
                    break;
                case 'directplayer':
                    _this.initDataTableSummaryByDirect();
                    break;
                case 'affiliate':
                    _this.initDataTableSummaryByAffiliate();
                    break;
                case 'agency':
                    _this.initDataTableSummaryByAgency();
                    break;
                case 'referrer':
                    _this.initDataTableSummaryByReferrer();
                    break;
            }
        } // EOF initDataTableWith

        /**
         * Called by initDataTableWith() with 'all' param.
         *
         */
        conversionRate.initDataTableSummaryByAll = function(){
            var _this = this;
            _this.conversionRateTable$El = $('#conversion_rate_table');
            var ajaxUri = base_url + "api/conversion_rate_report/all";

            /**
             * Adjust option of dataTable
             *
             * @param {object} defaultOption The default options for dataTable.
             * @return {object} The option of dataTable for init.
             */
            var callback4AdjustOption = function(defaultsOption){
                var adjustedOption = $.extend(true, {}, defaultsOption);
                // the column of table.
                adjustedOption.columnDefs = [];
                adjustedOption.columnDefs.push({ sortable: false
                                        , targets: [ 1 ]
                                        });
                                        adjustedOption.columnDefs.push({ className: 'text-right'
                                        , targets: [ 4 ]
                                        });

                // right buttons at top of table.
                adjustedOption.buttons = [];
                if (_this.export_report_permission){
                    adjustedOption.buttons.push(_this.btnExportCSVOption);
                } // EOF if (_this.export_report_permission)

                return adjustedOption; /// MUST be reture.
            } // EOF callback4AdjustOption = function(defaultsOption){...
            _this.applyDataTableWithAdjustOption(_this.conversionRateTable$El , ajaxUri, callback4AdjustOption);
        } // EOF initDataTableSummaryByAll

        /**
         * Called by initDataTableWith() with 'direct' param.
         *
         */
        conversionRate.initDataTableSummaryByDirect = function(){
            var _this = this;
            _this.conversionRateTable$El = $('#conversion_rate_table4direct');
            var ajaxUri = base_url + "api/conversion_rate_report/directPlayer";
            // _this.applyDataTable( _this.conversionRateTable$El , ajaxUri);

            var callback4AdjustOption = function(defaultsOption){
                var adjustedOption = $.extend(true, {}, defaultsOption);
                // the column of table.
                adjustedOption.columnDefs = [];
                adjustedOption.columnDefs.push({ sortable: false
                                        , targets: [ 0 ]
                                        });
                adjustedOption.columnDefs.push({ className: 'text-right'
                                        , targets: [ 3 ]
                                        });
                // adjustedOption.columnDefs.push({ className: 'text-center'
                //                         , targets: [ 1, 2 ]
                //                         });

                adjustedOption.drawCallback= function( settings ) {
                    // var api = this.api();
                    console.log('drawCallback.settings.oResponse:');
                    console.log(settings.oResponse);
                    // console.log('drawCallback.this:');
                    // console.log(this );
                    // console.log('drawCallback.this2:');
                    // console.log($(this).find('.total-conversion-rate') );

                    $(this).find('.total-conversion-rate').text(settings.oResponse['conversionRate']);
                }; // EOF drawCallback: function( settings ) {...

                adjustedOption.rowCallback = function(row, data, displayNum, displayIndex, dataIndex){
                    var theRow$El = $(row);
                    if(theRow$El.find('.subTotalCol').length > 0){
                        // for sub total row.
                        theRow$El.addClass('hidden');
                        theRow$El.removeClass('even');
                        theRow$El.removeClass('odd');
                    }
                    if(theRow$El.find('.totalCol').length > 0){
                        // for total row.
                        theRow$El.removeClass('even');
                        theRow$El.removeClass('odd');
                    }
                }; // EOF adjustedOption.rowCallback = function(row, data, displayNum, displayIndex, dataIndex){...
                return adjustedOption; /// MUST be reture.
            } // EOF callback4AdjustOption = function(defaultsOption){...

            _this.applyDataTableWithAdjustOption(_this.conversionRateTable$El , ajaxUri, callback4AdjustOption);
        } // EOF initDataTableSummaryByDirect

        /**
         * Called by initDataTableWith() with 'affiliate' param.
         *
         */
        conversionRate.initDataTableSummaryByAffiliate = function(){
            var _this = this;
            _this.conversionRateTable$El = $('#conversion_rate_table4affiliate');
            var ajaxUri = base_url + "api/conversion_rate_report/affiliate";
            // _this.applyDataTable( _this.conversionRateTable$El , ajaxUri);

            var callback4AdjustOption = function(defaultsOption){
                var adjustedOption = $.extend(true, {}, defaultsOption);
                // the column of table.
                adjustedOption.columnDefs = [];
                adjustedOption.columnDefs.push({ sortable: false
                                        , targets: [ 0 ]
                                        });
                adjustedOption.columnDefs.push({ className: 'text-right'
                                        , targets: [ 4 ]
                                        });
                // adjustedOption.columnDefs.push({ className: 'text-center'
                //                         , targets: [ 1, 2, 3  ]
                //                         });
                adjustedOption.rowCallback = function(row, data, displayNum, displayIndex, dataIndex){
                    var theRow$El = $(row);
                    if(theRow$El.find('.subTotalCol').length > 0){
                        // for sub total row.
                        theRow$El.addClass('hidden');
                        theRow$El.removeClass('even');
                        theRow$El.removeClass('odd');
                    }
                    if(theRow$El.find('.totalCol').length > 0){
                        // for total row.
                        theRow$El.removeClass('even');
                        theRow$El.removeClass('odd');
                    }
                }; // EOF adjustedOption.rowCallback = function(row, data, displayNum, displayIndex, dataIndex){...
                return adjustedOption; /// MUST be reture.
            } // EOF callback4AdjustOption = function(defaultsOption){...
            _this.applyDataTableWithAdjustOption(_this.conversionRateTable$El , ajaxUri, callback4AdjustOption);
        } // EOF initDataTableSummaryByAffiliate
        /**
         * Called by initDataTableWith() with 'agency' param.
         *
         */
        conversionRate.initDataTableSummaryByAgency = function(){
            var _this = this;
            _this.conversionRateTable$El = $('#conversion_rate_table4agency');
            var ajaxUri = base_url + "api/conversion_rate_report/agency";
            // _this.applyDataTable( _this.conversionRateTable$El , ajaxUri);
            var callback4AdjustOption = function(defaultsOption){
                var adjustedOption = $.extend(true, {}, defaultsOption);
                // the column of table.
                adjustedOption.columnDefs = [];
                adjustedOption.columnDefs.push({ sortable: false
                                        , targets: [ 0 ]
                                        });
                adjustedOption.columnDefs.push({ className: 'text-right'
                                        , targets: [ 4 ]
                                        });
                // adjustedOption.columnDefs.push({ className: 'text-center'
                //                         , targets: [ 0, 1, 2, 3  ]
                //                         });
                adjustedOption.rowCallback = function(row, data, displayNum, displayIndex, dataIndex){
                    var theRow$El = $(row);
                    if(theRow$El.find('.subTotalCol').length > 0){
                        // for sub total row.
                        theRow$El.addClass('hidden');
                        theRow$El.removeClass('even');
                        theRow$El.removeClass('odd');
                    }
                    if(theRow$El.find('.totalCol').length > 0){
                        // for total row.
                        theRow$El.removeClass('even');
                        theRow$El.removeClass('odd');
                    }
                }; // EOF adjustedOption.rowCallback = function(row, data, displayNum, displayIndex, dataIndex){...
                return adjustedOption; /// MUST be reture.
            } // EOF callback4AdjustOption = function(defaultsOption){...
            _this.applyDataTableWithAdjustOption(_this.conversionRateTable$El , ajaxUri, callback4AdjustOption);
        } // EOf initDataTableSummaryByAgency
        /**
         * Called by initDataTableWith() with 'referer' param.
         *
         */
        conversionRate.initDataTableSummaryByReferrer = function(){
            var _this = this;
            _this.conversionRateTable$El = $('#conversion_rate_table4referrer');
            var ajaxUri = base_url + "api/conversion_rate_report/referrer";
            // _this.applyDataTable( _this.conversionRateTable$El , ajaxUri);
            var callback4AdjustOption = function(defaultsOption){
                var adjustedOption = $.extend(true, {}, defaultsOption);
                // the column of table.
                adjustedOption.columnDefs = [];
                adjustedOption.columnDefs.push({ sortable: false
                                        , targets: [ 0 ]
                                        });
                adjustedOption.columnDefs.push({ className: 'text-right'
                                        , targets: [ 4 ]
                                        });
                // adjustedOption.columnDefs.push({ className: 'text-center'
                //                         , targets: [ 1, 2, 3  ]
                //                         });
                adjustedOption.rowCallback = function(row, data, displayNum, displayIndex, dataIndex){
                    var theRow$El = $(row);
                    if(theRow$El.find('.subTotalCol').length > 0){
                        // for sub total row.
                        theRow$El.addClass('hidden');
                        theRow$El.removeClass('even');
                        theRow$El.removeClass('odd');
                    }
                    if(theRow$El.find('.totalCol').length > 0){
                        // for total row.
                        theRow$El.removeClass('even');
                        theRow$El.removeClass('odd');
                    }
                }; // EOF adjustedOption.rowCallback = function(row, data, displayNum, displayIndex, dataIndex)
                return adjustedOption; /// MUST be reture.
            } // EOF callback4AdjustOption = function(defaultsOption){...
            _this.applyDataTableWithAdjustOption(_this.conversionRateTable$El , ajaxUri, callback4AdjustOption);
        }// EOF initDataTableSummaryByReferrer

        /**
         * The elemant apply .DataTable().
         * @param {jquery(selector)} theBind$El
         * @param {string} ajaxUri The ajax URI for data loading.
         * @param {object function(object defaultOption)} callback4AdjustOption The scription for adjust option for dataTable init.
         */
        conversionRate.applyDataTableWithAdjustOption = function(theBind$El, ajaxUri, callback4AdjustOption){
            var _this = this;

            if( typeof(callback4AdjustOption) === 'undefined'){
                /**
                 * default of callback4AdjustOption function
                 * @param {object} defaultsOption The option object for init. dataTable.
                 * @return {object} adjustedOption The option object after adjusted.
                 */
                callback4AdjustOption = function(defaultsOption){
                    var adjustedOption = $.extend(true, {}, defaultsOption);
                    return adjustedOption;
                };
            }
            _this.ADTP.columnDefs = []; // defaults
            _this.ADTP.columnDefs.push({ sortable: false
                                    , targets: [ 1 ]
                                });
            _this.ADTP.columnDefs.push({ className: 'text-right'
                                    , targets: [ 3 ]
                                });

            _this.ADTP.buttons = []; // defaults
            _this.ADTP.buttons.push({ extend: 'colvis'
                                    , postfixButtons: [ 'colvisRestore' ]
                                });
            if (_this.export_report_permission){
                _this.ADTP.buttons.push(_this.btnExportCSVOption);
            } // EOF if (_this.export_report_permission)

            // _this.ADTP.ajaxUri = base_url + "api/conversion_rate_report/all";
            _this.ADTP.ajaxUri = ajaxUri;

            if( ! $.fn.DataTable.isDataTable(  theBind$El.selector ) ){
                var defaultOption = {
                // theBind$El.DataTable({
                    stateSave: _this.stateSave,
                    lengthMenu: _this.default_datatable_lengthMenu,
                    autoWidth: false,
                    searching: false,
                    /* Ref. to http://www.datatables.club/reference/option/dom.html */
                    dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
                    pageLength: _this.pageLength,
                    "responsive": {
                        details: {
                            type: 'column'
                        }
                    },
                    buttons: _this.ADTP.buttons,
                    columnDefs: _this.ADTP.columnDefs,
                    "order": [ 0, 'desc' ],
                    processing: true,
                    serverSide: true,
                    ajax: function (data, callback, settings) {
                        data.extra_search = _this.searchForm$El.serializeArray();
                        $.post(_this.ADTP.ajaxUri, data, function(data) {
                            // console.log('in ajax.callback:');
                            // console.log(callback);
                            settings.oResponse = data; // for update .total-conversion-rate in drawCallback().
                            callback(data);
                        },'json');
                    } // EOF ajax: function (data, callback, settings) {...
                // } ); // EOF $('#conversion_rate_table').DataTable({...
                }; // EOF defaultOption
                var applyOption = callback4AdjustOption(defaultOption); // call callback4AdjustOption() for adjust option.
                theBind$El.DataTable(applyOption);
            } /// EOF if( ! $.fn.DataTable.isDataTable(  theBind$El.selector ) )...
        } // EOF applyDataTableWithAdjustOption

        /**
         * hidden/ display the spec field while selected SummaryBy option.
         */
        conversionRate.initDataTableWithSummaryBySelect = function(theSummary$El){
            var theSummaryBy = theSummary$El.val();

            $('.summaryByList').addClass('hidden');
            $('.summaryBy'+theSummaryBy).removeClass('hidden');
            oCR.initDataTableWith( theSummaryBy );
        } // EOF conversionRate.initDataTableWithSummaryBySelect


        /**
         * Using the GET parameter of a URL in JavaScript
         * Utils
         *
         * Ref. to https://stackoverflow.com/a/827378
         */
        conversionRate.getQueryVariable = function(variable) {
            var query = window.location.search.substring(1);
            var vars = query.split("&");
            for (var i=0;i<vars.length;i++) {
                var pair = vars[i].split("=");
                if (pair[0] == variable) {
                return pair[1];
                }
            }
        }

        return conversionRate;

    })(); // EOF oCR = conversionRate


</script>