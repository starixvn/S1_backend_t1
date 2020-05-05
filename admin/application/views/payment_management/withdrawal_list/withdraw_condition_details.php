<!--WITHRAWAL CONDITION-->
<div class="col-md-12">
    <fieldset>
        <legend class='togvis'><?=lang('pay.withdrawalCondition')?> <span>[-]</span></legend>
        <div class="table-responsive withrawal_panel_body">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href=".withdrawal-condition-table-tab"><?=lang('Wagering Requirements')?></a></li>
                <li><a data-toggle="tab" href=".deposit-condition-table-tab"><?=lang('Minimum Deposit Requirements')?></a>
            </ul>

            <div class="tab-content">
                <div class="tab-pane panel panel-default fade in active withdrawal-condition-table-tab">
                    <div class="panel-body">
                        <table class="table table-hover table-bordered table-condensed withdrawal-condition-table">
                            <thead>
                            <tr>
                                <th><?=lang('pay.transactionType')?></th>
                                <th><?=lang('Sub-wallet')?></th>
                                <th><?=lang('pay.promoName')?></th>
                                <th><?=lang('cms.promocode')?></th>
                                <th><?=lang('cashier.53')?></th>
                                <th><?=lang('Bonus')?></th>
                                <th><?=lang('pay.startedAt')?></th>
                                <th><?=lang('pay.withdrawalAmountCondition')?></th>
                                <th><?=lang('Note')?></th>
                                <?php if ($enabled_show_withdraw_condition_detail_betting) {?>
                                    <th><?=lang('Betting Amount')?></th>
                                    <th><?=lang('lang.status')?></th>
                                <?php }?>
                            </tr>
                            </thead>
                        </table>
                        <div class="col-md-3 summary-condition-container">
                            <!--#####Load the summary here######-->
                        </div>
                        <div class="col-md-2">
                            <a href="#" data-toggle="tooltip" title="<?=lang('lang.refresh')?>" class="btn btn-sm btn-default refresh-withdrawal-condition" >
                                <i class="glyphicon glyphicon-refresh"></i>
                            </a>
                            <img class="withdawal-condition-loader" src="<?=$this->utils->imageUrl('ajax-loader.gif')?>" />
                        </div>
                    </div>
                </div>

                <div class="tab-pane panel panel-default fade deposit-condition-table-tab">
                    <div class="panel-body">
                        <table class="table table-hover table-bordered table-condensed deposit-condition-table">
                            <thead>
                            <tr>
                                <th><?=lang('pay.transactionType')?></th>
                                <th><?=lang('Sub-wallet')?></th>
                                <th><?=lang('pay.promoName')?></th>
                                <th><?=lang('cms.promocode')?></th>
                                <th><?=lang('cashier.53')?></th>
                                <th><?=lang('Bonus')?></th>
                                <th><?=lang('pay.startedAt')?></th>
                                <th><?=lang('pay.mindepamt').' '.lang('Conditions')?></th>
                                <th><?=lang('Note')?></th>
                                <?php if ($enabled_show_withdraw_condition_detail_betting) {?>
                                    <th><?=lang('Betting Amount')?></th>
                                    <th><?=lang('lang.status')?></th>
                                <?php }?>
                            </tr>
                            </thead>
                            <!--################Dynamically adding rows################-->
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
</div>
<!--WITHRAWAL CONDITION-->


