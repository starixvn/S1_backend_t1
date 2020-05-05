<?php
    $pending_permission=$this->permissions->checkPermissions('pending_request');
    $allow_multiple_select = $this->permissions->checkPermissions('approve_decline_deposit') && $this->utils->isEnabledFeature('enable_batch_approve_and_decline') && ($conditions['dwStatus']=='requestAll' ||  $conditions['dwStatus']=='requestToday');

    // When no permission, redirect to 'approved today'
    if (!$pending_permission) {
        $dwStatus = $this->input->get('dwStatus');
        if($dwStatus == 'requestToday' || $dwStatus == 'requestAll'){
            redirect('/payment_management/deposit_list/approvedToday');
        }
    }
?>

<style>
    .dashboard-stat.blue .details .desc {
        font-size: 14px;
    }
    .dashboard-stat.green .details .desc {
        font-size: 14px;
    }
    .dashboard-stat.red .details .desc {
        font-size: 14px;
    }
    .notes-textarea {
        resize: none;
        height: 200px !important;
        margin-bottom: 10px;
    }
    .add-notes-btn {
        padding-right: 26px;
        padding-left: 26px;
    }
    #bootstrap_dialog_id .modal-footer {
        padding: 20px;
        text-align: center;
        border-top: 1px solid #e5e5e5;
        overflow: hidden;
    }
    #depositPaymentType label.checkbox-inline {
        width: 16.66667%;
        float: left;
        margin: 0;
    }
    @media(max-width:1280px){
        #depositPaymentType label.checkbox-inline {
        width: 19%;
        }
    }
    @media(max-width:992px){
        #depositPaymentType label.checkbox-inline {
        width: 50%;
        }
    }
    .dashboard-stat .dashboard-radio-btn:hover .clickable-number,
    .dashboard-stat .dashboard-radio-btn:hover .desc,
    .dashboard-stat .dashboard-radio-btn:hover .number,
    .dashboard-stat.checked .dashboard-radio-btn.checked .clickable-number,
    .dashboard-stat.checked .dashboard-radio-btn.checked .desc,
    .dashboard-stat.checked .dashboard-radio-btn.checked .number {
        font-weight: bold !important;
        text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
    }
    .dashboard-stat .visual {
        height: 128px;
    }
    .dashboard-stat .details .number {
        padding-top: 35px;
    }
    .dashboard-stat .clickable-details .desc {
        padding-top: 22px;
    }
    .dashboard-stat .clickable-details .clickable-number {
        padding-top: 15px;
    }
    .dashboard-stat.checked .dashboard-radio-btn.checked:before {
        display: block;
        content: '';
        position: absolute;
        left: 5%;
        top: 7%;
        height: 90%;
        width: 90%;
        border: 1px solid #fff;
        border-radius: 6px;
    }
    .dashboard-stat.checked .col-md-7 .dashboard-radio-btn.checked:before {
        left: 0%;
        width: 100%;
    }
    .dashboard-stat>.col-md-7 {
        margin-top: 5px;
    }
    .dashboard-stat .col-md-7 .col-md-4 .clickable-details {
        left: inherit;
        right: 10px;
    }
    .dashboard-stat .label-style-pending-today {
        height: 58px;
    }
    .dashboard-stat .label-style {
        height: 70px;
    }
    .dashboard-stat .details {
        padding: 0 15px;
    }
    .dashboard-stat .dashboard-radio-btn.col-md-12:after {
        content: '';
        display: block;
        border: 1px solid rgb(0, 0, 0, 0.2);
        border-top-color: rgba(255, 255, 255, 0.3);
        border-right: 0;
        border-left: 0;
        height: 2px;
        top: 2px;
        position: absolute;
        width: 95%;
        left: 2.5%;
    }
    .dashboard-stat .dashboard-radio-btn.col-md-12.checked:after {
        border: 0;
    }
    .dashboard-stat .visual>i {
        margin-left: 0px;
        font-size: 80px;
        line-height: 80px;
    }
    .dashboard-stat.curiousblue .clickable-details{
        color: #fff;
    }
    .dashboard-stat .clickable-details .desc{
        font-size: 14px;
    }
    @media(max-width: 1280px) {
        .dashboard-stat .details .number {
            padding-top: 20px;
        }
        .dashboard-stat .clickable-details .desc {
            padding-top: 15px;
        }
    }
    .title-css {
        margin-top: 0;
        padding-bottom: 10px;
        font-size: 16px
    }
    .dashboard-stat .dashboard-radio-btn{
        cursor: pointer;
    }
    .dashboard-stat label.col-md-7 .col-md-1{
        text-align: center;
    }
    .dashboard-stat .details .number,
    .dashboard-stat .clickable-details .clickable-number{
        font-size: 28px;
    }
    .dashboard-stat .details .desc,
    .dashboard-stat .clickable-details .desc{
        font-size: 12px !important;
    }
    .dashboard-stat .col-md-6 .clickable-details .clickable-number,
    .dashboard-stat .col-md-5 .clickable-details .clickable-number{
        padding-top: 10px;
    }
    .dashboard-stat .col-md-6 .clickable-details .desc,
    .dashboard-stat .col-md-5 .clickable-details .desc{
        padding-top: 0 !important;
        line-height: 1;
    }
    .dashboard-stat .col-md-6 .clickable-details,
    .dashboard-stat .col-md-5 .clickable-details{
        right: 10px;
        left: auto;
    }
</style>

<form id="search-form" <?=$this->config->item('deposit_list_use_post') ? 'method="POST" action="?"' : ''?> >
    <h4 class="title-css"><?=lang('deposit_list.deposit_list_quick_filter')?></h4>
    <div class="row hide">
        <?php if(isset($payment_flag)) { ?>
            <input type="text" value="<?php echo $payment_flag ?>" name="paymentType">
        <?php } ?>
    </div>
    <div class="row">
        <?php if($pending_permission){ ?>
            <div class="col-md-6 col-xs-12">
                <div class="dashboard-stat curiousblue request <?php echo $conditions['dwStatus']=='requestAll'||$conditions['dwStatus']=='requestToday' ||$conditions['dwStatus']=='requestBankDeposit' || $conditions['dwStatus']=='request3rdParty' ? 'checked' : '' ?>">
                    <label class="col-md-5 dashboard-radio-btn count_month">
                        <input type="radio" name="dwStatus" value="requestAll" class="dwStatus" style="display:none" <?=$conditions['dwStatus'] == 'requestAll' ? 'checked="checked"' : ''?> />
                        <div class="visual">
                            <i class="fa fa-square-o"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                <span id="deposit_request_cnt">--</span>
                            </div>
                            <div class="desc"> <?=lang('deposit_list.pending_requests_total')?> </div>
                        </div>
                    </label>
                    <label class="col-md-7">
                        <label class="clearfix col-md-6 dashboard-radio-btn p-0 count_today_bank">
                            <input type="radio" name="dwStatus" value="requestBankDeposit" class="dwStatus" style="display:none" <?=$conditions['dwStatus'] == 'requestBankDeposit' ? 'checked="checked"' : ''?> />
                            <div class="col-md-12 label-style">
                                <div class="visual"></div>
                                <div class="clickable-details">
                                    <div class="clickable-number">
                                        <span id="deposit_request_cnt_today_manual">--</span>
                                        <div class="desc"><?= lang('deposit_list.bank_deposit') ?></div>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <div class="clearfix col-md-1 p-0">
                            <label class="col-md-12 label-style">
                                <div class="visual"></div>
                                <div class="clickable-details">
                                    <div class="desc">/</div>
                                </div>
                            </label>
                        </div>
                        <label class="clearfix col-md-5 p-0 dashboard-radio-btn count_today_3rd">
                            <input type="radio" name="dwStatus" value="request3rdParty" class="dwStatus" style="display:none" <?=$conditions['dwStatus'] == 'request3rdParty' ? 'checked="checked"' : ''?> />
                            <div class="col-md-12 label-style">
                                <div class="visual"></div>
                                <div class="clickable-details">
                                    <div class="clickable-number">
                                        <span id="deposit_request_cnt_today_auto">--</span>
                                        <div class="desc"><?= lang('deposit_list.3rd_party') ?></div>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <label class="clearfix col-md-12 dashboard-radio-btn p-0 count_today">
                            <input type="radio" name="dwStatus" value="requestToday" class="dwStatus" style="display:none" <?=$conditions['dwStatus'] == 'requestToday' ? 'checked="checked"' : ''?> />
                            <div class="col-md-8 label-style-pending-today">
                                <div class="visual"></div>
                                <div class="clickable-details">
                                    <div class="desc"> <?=lang('deposit_list.pending_requests_today')?></div>
                                </div>
                            </div>
                            <div class="col-md-4 label-style-pending-today">
                                <div class="visual"></div>
                                <div class="clickable-details">
                                    <div class="clickable-number">
                                        <span id="total_deposit_request_cnt_today_auto_and_manual">--</span>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </label>
                </div>
            </div>
        <?php } ?>

        <div class="col-md-3 col-xs-12">
            <div class="dashboard-stat bermuda approved <?php echo $conditions['dwStatus'] == 'approvedAll'||$conditions['dwStatus'] == 'approvedToday' ? 'checked' : '' ?>">
                <label class="col-md-6 dashboard-radio-btn count_month">
                    <input type="radio" name="dwStatus" value="approvedAll" class="dwStatus" style="display:none" <?=$conditions['dwStatus'] == 'approvedAll' ? 'checked="checked"' : ''?> />
                    <div class="visual">
                        <i class="fa fa-check-square-o"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                            <span id="deposit_approved_cnt">--</span>
                        </div>
                        <div class="desc">
                            <?=lang('deposit_list.approved_requests_this_month')?> <br>
                        </div>
                    </div>
                </label>
                <label class="col-md-6 dashboard-radio-btn count_today">
                    <input type="radio" name="dwStatus" value="approvedToday" class="dwStatus" style="display:none" <?=$conditions['dwStatus'] == 'approvedToday' ? 'checked="checked"' : ''?> />
                    <div class="visual"></div>
                    <div class="details">
                        <div class="number">
                            <span id="deposit_approved_cnt_today">--</span>
                        </div>
                        <div class="desc"> <?=lang('deposit_list.approved_requests_today')?> </div>
                    </div>
                </label>
            </div>
        </div>

        <div class="col-md-3 col-xs-12 ">
            <div class="dashboard-stat charm declined <?php echo $conditions['dwStatus']  == 'declinedAll'||$conditions['dwStatus']  == 'declinedToday' ? 'checked' : '' ?>">
                <label class="col-md-6 dashboard-radio-btn count_month">
                    <input type="radio" name="dwStatus" value="declinedAll" class="dwStatus" style="display:none" <?=$conditions['dwStatus'] == 'declinedAll' ? 'checked="checked"' : ''?> />
                    <div class="visual">
                        <i class="fa fa-minus-square-o"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                            <span id="deposit_declined_cnt">--</span>
                        </div>
                        <div class="desc"> <?=lang('deposit_list.declined_requests_this_month')?> </div>
                    </div>
                </label>
                <label class="col-md-6 dashboard-radio-btn count_today">
                    <input type="radio" name="dwStatus" value="declinedToday" class="dwStatus" style="display:none" <?=$conditions['dwStatus'] == 'declinedToday' ? 'checked="checked"' : ''?> />
                    <div class="visual"></div>
                    <div class="details">
                        <div class="number">
                            <span id="deposit_declined_cnt_today">--</span>
                        </div>
                        <div class="desc"> <?=lang('deposit_list.declined_requests_today')?> </div>
                    </div>
                </label>
            </div>
        </div>
        <!-- Control the fast screening month or the day -->
        <div class="col-md-2" style="display:none;">
            <label>
                <div class="date-range">
                    <input type="radio" id="date_range_month" name="date_range" <?php echo $conditions['date_range']  == '1' ? 'checked="checked"' : '' ?> value="1"/><?=lang('date_range_month')?><br>
                    <input type="radio" id="date_range_today" name="date_range" <?php echo $conditions['date_range']  == '2' ? 'checked="checked"' : '' ?> value="2"/><?=lang('date_range_today')?>
                </div>
            </label>
        </div>
    </div>

    <div class="panel panel-primary hidden">
        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="fa fa-search"></i> <?=lang("lang.search")?>
                <span class="pull-right">
                    <a data-toggle="collapse" href="#collapseDepositList" class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
                </span>
            </h4>
        </div>
        <div id="collapseDepositList" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'in collapse'?>">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label search-time" for="search_time">
                                <?=lang('pay.transperd')?>
                                <input style='margin-left:20px' id="request_time" type="radio" name="search_time" value="1" <?php echo $conditions['search_time']  == '1' ? 'checked="checked"' : '' ?>/> <?php echo lang('pay.reqtime');?>
                                <input style='margin-left:20px' id="updated_on" type="radio" name="search_time" value="2" <?php echo $conditions['search_time']  == '2' ? 'checked="checked"' : '' ?>/> <?php echo lang('pay.updatedon'); ?>
                            </label>
                            <div class="input-group">
                                <input id="search_deposit_date" class="form-control input-sm dateInput" data-time="true" data-start="#deposit_date_from" data-end="#deposit_date_to"/>
                                <span class="input-group-addon input-sm">
                                    <input type="checkbox" id="search_enable_date" data-off-text="<?php echo lang('off'); ?>" data-on-text="<?php echo lang('on'); ?>" data-size='mini' value='<?php echo $conditions['enable_date']?>' <?php echo empty($conditions['enable_date']) ? '' : 'checked="checked"'; ?>>
                                    <input type="hidden" name="enable_date" value='<?php echo $conditions['enable_date']?>'>
                                </span>
                            </div>
                            <input type="hidden" id="deposit_date_from" name="deposit_date_from" value="<?=$conditions['deposit_date_from'];?>"/>
                            <input type="hidden" id="deposit_date_to" name="deposit_date_to" value="<?=$conditions['deposit_date_to'];?>"/>
                            <div class="checkbox" >
                                <label>
                                    <input type="checkbox" name="excludeTimeout" id="excludeTimeout"  <?php echo $conditions['excludeTimeout']  ? 'checked="checked"' : '' ?>  />
                                    <?=lang('cms.excludeTimeout')?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" for="status"><?=lang('status')?></label>
                            <select class="form-control input-sm select-status" name="search_status">
                                <option value ="allStatus"  ><?=lang("All")?> </option>
                                <?php foreach($searchStatus as $status => $value): ?>
                                    <option value ="<?php echo $status?>" ><?php echo $value?> </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" for="secure_id"><?=lang('deposit_list.order_id')?></label>
                            <input id="secure_id" type="text" name="secure_id"  value="<?php echo $conditions['secure_id']; ?>"  class="form-control input-sm"/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" for="username"><?=lang('pay.username')?></label>
                            <input id="username" type="text" name="username" value="<?php echo $conditions['username']; ?>"  class="form-control input-sm"/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" for="realname"><?=lang('pay.realname')?></label>
                            <input id="realname" type="text" name="realname" value="<?php echo $conditions['realname']; ?>" class="form-control input-sm"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" for="external_order_id"><?php echo lang('External ID'); ?></label>
                            <input id="external_order_id" type="text" name="external_order_id" value="<?php echo $conditions['external_order_id']; ?>"  class="form-control input-sm"/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" for="bank_order_id"><?php echo lang('Bank Order ID'); ?></label>
                            <input id="bank_order_id" type="text" name="bank_order_id" value="<?php echo $conditions['bank_order_id']; ?>"  class="form-control input-sm"/>
                        </div>
                    </div>
                    <?php if(!$this->utils->isEnabledFeature('close_aff_and_agent')): ?>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label" for="affiliate"><?=lang("Affiliate")?></label>
                                <input id="affiliate" type="text" name="affiliate" value="<?php echo $conditions['affiliate']; ?>" class="form-control input-sm"/>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if($conditions['dwStatus'] != 'requestToday' && $conditions['dwStatus'] != 'requestAll' ): ?>
                        <div class="col-md-2" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="processed_by"><?=lang('pay.procssby')?></label>
                                <select class="form-control input-sm" name="processed_by">
                                    <option value =""  ><?=lang("lang.selectall")?> </option>
                                    <?php foreach($users as $u): ?>
                                        <option value ="<?php echo $u['userId']?>" <?php echo $conditions['processed_by'] == $u['userId'] ? 'selected' : '' ?> ><?php echo $u['username']?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" for="amount_from"><?=lang('Deposit Amount')?> &gt;=</label>
                            <input id="amount_from" type="number" min="0" name="amount_from" value="<?php echo $conditions['amount_from']; ?>" class="form-control input-sm"/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" for="amount_to"><?=lang('Deposit Amount')?> &lt;=</label>
                            <input id="amount_to" type="number" min="0" name="amount_to" value="<?php echo $conditions['amount_to']; ?>"  class="form-control input-sm"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <fieldset id="depositPaymentType" style="padding-bottom: 14px">
                                <legend>
                                    <label class="control-label"><?=lang('pay.payment_account_flag');?></label>
                                </legend>
                                <label class="checkbox-inline">
                                    <input id="payment_flag_1" value='<?= $conditions['payment_flag_1']?>' type="checkbox" <?= empty($conditions['payment_flag_1']) ? '' : 'checked="checked"'?> > <?=lang('pay.manual_online_payment')?>
                                    <input type="hidden" name="payment_flag_1" value='<?= $conditions['payment_flag_1']?>'>
                                </label>
                                <label class="checkbox-inline">
                                    <input id="payment_flag_2" value='<?= $conditions['payment_flag_2']?>' type="checkbox" <?= empty($conditions['payment_flag_2']) ? '' : 'checked="checked"'?> > <?=lang('pay.auto_online_payment')?>
                                    <input type="hidden" name="payment_flag_2" value='<?= $conditions['payment_flag_2']?>'>
                                </label>
                                <label class="checkbox-inline">
                                    <input id="payment_flag_3" value='<?= $conditions['payment_flag_3']?>' type="checkbox" <?= empty($conditions['payment_flag_3']) ? '' : 'checked="checked"'?> > <?=lang('pay.local_bank_offline')?>
                                    <input type="hidden" name="payment_flag_3" value='<?= $conditions['payment_flag_2']?>'>
                                </label>
                            </fieldset>
                            <?php if(is_array($payment_account_list)): ?>
                                <fieldset style="padding-bottom: 8px">
                                    <legend>
                                        <label class="control-label"><?=lang('pay.collection_account_name');?></label>
                                        <a id="payment_account_toggle_btn" style="text-decoration:none; border-radius:2px;" class="btn btn-scooter btn-xs"><span class="fa fa-plus-circle"> <?=lang("Expand All")?></span></a>
                                    </legend>
                                    <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                <input id="payment_account_id" name="select_all" type="checkbox" value="<?= $conditions['select_all'] ?>" onclick="checkAll(this.id)" <?php if($conditions['select_all']){?>checked="checked"<?php }?>  > <?=lang('lang.selectall');?>
                                            </label>
                                        </div>
                                    </div>
                                    <input type="checkbox" name="default_state"  class="hidden" checked="checked"/>
                                    <input type="checkbox" name="select_all_payments"  class="hidden" checked="checked"/>
                                    <div id="payment_account_toggle">
                                        <?php foreach (($payment_account_chunks = array_chunk($payment_account_list, ceil(count($payment_account_list) / 3))) as $i => $payment_account_chunk) :?>
                                            <div class="col-md-3">
                                                <?php foreach ($payment_account_chunk as $payment_account) :
                                                    $id = $payment_account->payment_account_id;
                                                    $checked = $conditions['select_all'] || (!empty($conditions['payment_account_id_'.$id]) && ($conditions['payment_account_id_'.$id] == 'true'));
                                                ?>
                                                    <div class="checkbox">
                                                        <label>
                                                             <input name="payment_account_id_<?=$id?>" value="true" class="payment_account_id" type="checkbox" <?=$checked ? 'checked="checked"': '' ?>/>
                                                             <?=lang($payment_account->payment_type) . ' - ' . $payment_account->payment_account_name?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </fieldset>
                            <?php else:?>
                                <fieldset style="padding-bottom: 8px">
                                    <legend>
                                        <label class="control-label"><?=lang('pay.collection_account_name');?></label>
                                    </legend>
                                </fieldset>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-offset-9 col-md-3 text-right" style="padding-top: 25px">
                        <button type="submit" id="searchBtn" class="btn btn-sm btn-portage search-btn" style="width:68px;"><?=lang('lang.search')?></button>
                        <input type="hidden" name="searchBtn" value='<?php echo $conditions['searchBtn']?>'>
                        <button type="button" class="btn btn-sm btn-danger clear-btn" style="width:68px;" onclick="resetSearch()" ><?=lang('lang.clear')?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-save"></i> <?=lang('con.drl')?>
            <div class="pull-right">
                <?php if ($allow_multiple_select) : ?>
                    <a href="#" onclick="batchProcessOrderId('APPROVE');" class="btn btn-emerald btn-xs">
                        <i class="fa fa-thumbs-up"></i>
                        <span class="hidden-xs"><?=lang('Approve Selected')?></span>
                    </a>
                    <a href="#" onclick="batchProcessOrderId('DECLINE');" class="btn btn-danger btn-xs">
                        <i class="fa fa-thumbs-down"></i>
                        <span class="hidden-xs"><?=lang('Decline Selected')?></span>
                    </a>
                <?php endif; ?>
                <?php if($this->permissions->checkPermissions('new_deposit')) {?>
                    <a href="<?=site_url('payment_management/newDeposit')?>" class="btn btn-info btn-xs">
                        <i class="fa fa-plus"></i>
                        <span class="hidden-xs"><?=lang('lang.newDeposit')?></span>
                    </a>
                <?php } ?>
                <a href="<?=site_url('payment_management/getSaleOrderReport')?>" class="btn btn-info btn-xs">
                    <i class="fa fa-list"></i>
                    <span class="hidden-xs"><?=lang('payment.depositProcessingTimeRecord')?></span>
                </a>
            </div>
        </h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="deposit-table">
                <thead>
                    <tr>
                        <th>
                            <div class="clearfix" style="width:65px;">
                                <?php if ($allow_multiple_select) : ?>
                                    <div class="col-md-3" style="padding:0 1px 0 2px"><input type="checkbox" name="chkAll" id="chkAll"></div>
                                <?php endif; ?>
                                <div class="col-md-9" style="padding:0 2px 0 2px"><?=lang('lang.action');?></div>
                            </div>
                        </th>
                        <th><?=lang('lang.status');?></th>
                        <th><?=lang('deposit_list.order_id');?></th>
                        <th><?=lang('system.word38');?></th>
                        <th><?=lang('pay.payment_account_flag');?></th>
                        <th><?=lang('pay.reqtime');?></th>
                        <th><?=lang('Deposit Datetime');?></th>
                        <th><?=lang('system.word39');?></th>
                        <th><?=lang("Affiliate")?></th>
                        <th><?=lang('pay.playerlev');?></th>
                        <th><?=lang('Deposit Amount');?></th>
                        <th><?=lang('pay.collection_name');?></th>
                        <th><?=lang('pay.ip') . ' ' . lang('pay.locatn');?></th>
                        <th><?=lang('pay.updatedon');?></th>
                        <th><?=lang('cms.timeoutAt');?></th>
                        <th><?=lang('pay.procsson');?></th>
                        <th><?=lang('pay.collection_account_name');?></th>
                        <th><?=lang('con.bnk20');?></th>
                        <th><?=lang('pay.deposit_payment_name');?></th>
                        <th><?=lang('pay.deposit_payment_account_name');?></th>
                        <th><?=lang('pay.deposit_payment_account_number');?></th>
                        <th><?=lang('pay.deposit_transaction_code');?></th>
                        <th><?=lang('pay.promoname');?></th>
                        <th><?=lang('pay.promobonus');?></th>
                        <th><?=lang('External ID'); ?></th>
                        <th><?=lang('Bank Order ID'); ?></th>
                        <th><?=lang('Deposit Datetime From Player'); ?></th>
                        <th><?=lang('Mode of Deposit'); ?></th>
                        <th style="min-width:200px;"><?=lang('Player Deposit Note');?></th>
                        <th style="min-width:400px;"><?=lang('pay.procssby');?></th>
                        <th style="min-width:400px;"><?=lang('External Note');?></th>
                        <th style="min-width:400px;"><?=lang('Internal Note');?></th>
                        <th style="min-width:600px;"><?=lang('Action Log');?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>

<?php
    $start_today = date("Y-m-d") . ' 00:00:00';
    $end_today = date("Y-m-d") . ' 23:59:59';
?>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
        <input name='json_search' type="hidden">
    </form>
<?php }?>

<script type="text/javascript">

    <?php if(!empty($this->utils->getConfig('deposit_list_columnDefs'))) : ?>
        <?php if(!empty($this->utils->getConfig('deposit_list_columnDefs')['not_visible_payment_management'])) : ?>
            var visible_target = JSON.parse("<?= json_encode($this->utils->getConfig('deposit_list_columnDefs')['not_visible_payment_management']) ?>" ) ;
        <?php else: ?>

            var visible_target = [ 6,7,10, 12, 13, 14, 16, 17, 19, 20, 21, 22, 23 ];
        <?php endif; ?>
        <?php if(!empty($this->utils->getConfig('deposit_list_columnDefs')['className_text-right_payment_management'])) : ?>
            var className_text = JSON.parse("<?= json_encode($this->utils->getConfig('deposit_list_columnDefs')['className_text-right_payment_management']) ?>" ) ;
        <?php else: ?>
            var className_text = [ 5 ];
        <?php endif; ?>
    <?php else: ?>
        var visible_target = [ 6,7,10, 12, 13, 14, 16, 17, 19, 20, 21, 22, 23 ];
        var className_text = [ 5 ];
    <?php endif; ?>

    var testDate = '<?=$end_today?>';

    var depositSetting = '<?php echo $depositCountList; ?>';
    var depositCountSetting = {
        thisWeek    : '<?=Payment_management::DEPOSIT_THIS_WEEK?>',
        thisMonth   : '<?=Payment_management::DEPOSIT_THIS_MONTH?>',
        thisYear    : '<?=Payment_management::DEPOSIT_THIS_YEAR?>',
        total       : '<?=Payment_management::DEPOSIT_TOTAL_ALL?>'
    };

    function setDate(depositSetting) {
        var startDate;
        var endDate;
        if (depositSetting == depositCountSetting.thisWeek) {
            startDate = moment().startOf('isoweek').format("YYYY-MM-DD 00:00:00");
            endDate   = moment().endOf('isoweek').format("YYYY-MM-DD 23:59:59");
        } else if(depositSetting == depositCountSetting.thisMonth) {
            startDate = moment().startOf('month').format("YYYY-MM-DD 00:00:00");
            endDate   = moment().endOf('month').format("YYYY-MM-DD 23:59:59");
        } else if(depositSetting == depositCountSetting.thisYear) {
            startDate = moment().startOf('year').format("YYYY-MM-DD 00:00:00");
            endDate   = moment().endOf('year').format("YYYY-MM-DD 23:59:59");
        } else {    // depositCountSetting.total
            startDate = '';
            endDate = '';
        }

        if(startDate != '' || endDate != '') {
            $('input[name=enable_date]').prop('checked',true);
            $('#deposit_date_from').val(startDate);
            $('#deposit_date_to').val(endDate);
        }
    }

    function getSearchStatus(dwStatus){
        switch (dwStatus) {

            case '<?=Sale_order::VIEW_STATUS_REQUEST_ALL?>':
            case '<?=Sale_order::VIEW_STATUS_REQUEST_TODAY?>':
            case '<?=Sale_order::VIEW_STATUS_REQUEST?>':
            case '<?=Sale_order::VIEW_STATUS_REQUEST_BANKDEPOSIT?>':
            case '<?=Sale_order::VIEW_STATUS_REQUEST_3RDPARTY?>':
                $('.select-status').val('<?php echo Sale_order::STATUS_PROCESSING?>');

                break;
            case '<?=Sale_order::VIEW_STATUS_APPROVED_ALL?>':
            case '<?=Sale_order::VIEW_STATUS_APPROVED_TODAY?>':
            case '<?=Sale_order::VIEW_STATUS_APPROVED?>':
                $('.select-status').val('<?php echo Sale_order::STATUS_SETTLED?>');

                break;
            case '<?=Sale_order::VIEW_STATUS_DECLINED_ALL?>':
            case '<?=Sale_order::VIEW_STATUS_DECLINED_TODAY?>':
            case '<?=Sale_order::VIEW_STATUS_DECLINED?>':
                $('.select-status').val('<?php echo Sale_order::STATUS_DECLINED?>');
                $('#updated_on').prop('checked', 'checked');
                break;
        }

    }

    function checkedDateRangeAddClass(dwStatus){
        switch (dwStatus) {
            case '<?=Sale_order::VIEW_STATUS_REQUEST_ALL?>':
            case '<?=Sale_order::VIEW_STATUS_APPROVED_ALL?>':
            case '<?=Sale_order::VIEW_STATUS_DECLINED_ALL?>':
        　       $('.count_month').addClass('checked');
        　       break;
            case '<?=Sale_order::VIEW_STATUS_REQUEST_TODAY?>':
            case '<?=Sale_order::VIEW_STATUS_APPROVED_TODAY?>':
            case '<?=Sale_order::VIEW_STATUS_DECLINED_TODAY?>':
        　       $('.count_today').addClass('checked');
                break;
            case '<?=Sale_order::VIEW_STATUS_REQUEST_BANKDEPOSIT?>':
        　       $('.count_today_bank').addClass('checked');
                break;
            case '<?=Sale_order::VIEW_STATUS_REQUEST_3RDPARTY?>':
        　       $('.count_today_3rd').addClass('checked');
                break;
            default:
        }
    }

    function resetSearch(){
        $('.dateInput').data('daterangepicker').setStartDate(moment().startOf('day').format('Y-MM-DD HH:mm:ss'));
        $('.dateInput').data('daterangepicker').setEndDate(moment().endOf('day').format('Y-MM-DD HH:mm:ss'));
        $('.select-status').val('allStatus');
        $('#request_time').prop('checked', 'checked');
        $('#depositPaymentType input[type=\'checkbox\']').prop('checked', false);
        $("#payment_account_id").prop("checked", true);
        $(".payment_account_id").prop("checked", true);
        $('input[name=enable_date]').prop('checked',true);
        $('#excludeTimeout').prop('checked', false);
        clearSearchInputVal();
    }

    function clearSearchInputVal(){
        $('#secure_id').val('');
        $('#username').val('');
        $('#realname').val('');
        $('#external_order_id').val('');
        $('#bank_order_id').val('');
        $('#affiliate').val('');
        $('#amount_from').val('');
        $('#amount_to').val('');
        $('#amount_from').val('');
        $('#amount_to').val('');
    }

    function reomvedCountDateClass(){
        $('.count_month').removeClass('checked');
        $('.count_today').removeClass('checked');
        $('.count_today_bank').removeClass('checked');
        $('.count_today_3rd').removeClass('checked');
    }

    function handler_search_enable_date_change() {
        var checked = $('#search_enable_date').is(':checked');
        $('#search_deposit_date').removeAttr('disabled');
        if (!checked) {
            $('#search_deposit_date').attr('disabled', 1);
        }
    }



    //checked select status and link search time
    $('.select-status').change(function(){
        var getValue = $('.select-status').val();
        if(getValue == 'allStatus' || getValue == '3'){
            $('#request_time').prop('checked', 'checked');
        }else{
            $('#updated_on').prop('checked', 'checked');
        }
    });

    //click search input and clear dashbord css
    $('#request_time ,#updated_on ,#search_deposit_date ,.select-status ,#secure_id ,#username ,#realname ,#external_order_id ,#bank_order_id ,#affiliate , #amount_from ,#amount_to ,.clear-btn ,#depositPaymentType ,#payment_account_toggle_btn ,#searchBtn').click(function () {
        reomvedCountDateClass();
        $('.dwStatus').prop("checked", false);
    });


    $(document).ready( function() {
        function getQueryParameters() {
            var queryString = location.search.slice(1),
                params = {};

            queryString.replace(/([^=]*)=([^&]*)&*/g, function (_, key, value) {
                params[key] = value;
            });

            return params;
        }

        function setQueryParameters(params) {
            //console.log(params);
            var url = window.location.href;
                query = [],
                value = '';

            for(key in params) {
                value = params[key];
                query.push(key + "=" + value);
            }

            window.history.pushState({page:url}, null, '?' + query.join("&"));
        }

        var desc = 5;
            if($('#updated_on').is(':checked')){
                desc = 13;
            }

        var dataTable = $('#deposit-table').DataTable({

            <?php if(!empty($this->utils->getConfig('deposit_list_default_page_size'))){ ?>
            pageLength: <?php echo $this->utils->getConfig('deposit_list_default_page_size');?>,
            <?php }?>
            lengthMenu: JSON.parse('<?=json_encode($this->utils->getConfig('default_datatable_lengthMenu'))?>'),
            autoWidth: false,
            searching: false,

            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>

            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: 'btn-linkwater'
                }
                <?php

                    if( $this->permissions->checkPermissions('export_deposit_lists') ){

                ?>
                    ,{
                        text: "<?php echo lang('CSV Export'); ?>",
                        className:'btn btn-sm btn-portage',
                        action: function ( e, dt, node, config ) {

                            var form_params=$('#search-form').serializeArray();

                            var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
                                'draw':1, 'length':-1, 'start':0};

                            $("#_export_excel_queue_form").attr('action', site_url('/export_data/depositList/null/true'));
                            $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                            $("#_export_excel_queue_form").submit();

                        }
                    }

                <?php
                    }
                ?>
            ],

            columnDefs: [
                { sortable: false, targets: [ 0 ] },
                { visible: false, targets: visible_target },
                { className: 'text-right', targets: className_text },
                <?php if (!$this->utils->isEnabledFeature('enable_deposit_datetime')) { ?>
                    { "targets": [ 26 ], className: "noVis hidden" },
                    { "targets": [ 27 ], className: "noVis hidden" },
                <?php } ?>
                <?php if($this->utils->isEnabledFeature('close_aff_and_agent')): ?>
                    { "targets": [ 8 ], className: "noVis hidden" },
                <?php endif?>
            ],
            order: [[ desc, 'desc']],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/depositList/0/<?= $allow_multiple_select ?>", data, function(data) {
                    callback(data);
                    if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                        dataTable.buttons().disable();
                    }
                    else {
                        dataTable.buttons().enable();
                    }
                },'json');
            }
        });


        <?php if($conditions['searchBtn'] == '0') : ?>

            getSearchStatus('<?=$conditions['dwStatus']?>');

            <?php if($conditions['dwStatus'] == Sale_order::VIEW_STATUS_REQUEST_ALL || $conditions['dwStatus'] == Sale_order::VIEW_STATUS_REQUEST_TODAY || $conditions['dwStatus'] == Sale_order::VIEW_STATUS_REQUEST_BANKDEPOSIT || $conditions['dwStatus'] == Sale_order::VIEW_STATUS_REQUEST_3RDPARTY) : ?>
                $('#request_time').prop('checked', 'checked');
            <?php else: ?>
                $('#updated_on').prop('checked', 'checked');
            <?php endif; ?>

        <?php else: ?>
            $('.select-status').val('<?= $conditions['search_status']?>');
        <?php endif; ?>

        $('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').trigger('submit');
            }
        });

        //trigger enable_date check box
        $('#search_enable_date').change(function(){
            if($(this).is(':checked')) {
                handler_search_enable_date_change();
                $(this).prop('checked', true);
                $('input[name="enable_date"]').val('1');

            }else{
                handler_search_enable_date_change();
                $(this).prop('checked', false);
                $('input[name="enable_date"]').val('0');
            }
        }).trigger('change');

        $('#payment_flag_1').change(function(){
            if($(this).is(':checked')) {
                $(this).prop('checked', true);
                $('input[name="payment_flag_1"]').val('1');

            }else{
                $(this).prop('checked', false);
                $('input[name="payment_flag_1"]').val('0');
            }
        }).trigger('change');

        $('#payment_flag_2').change(function(){
            if($(this).is(':checked')) {
                $(this).prop('checked', true);
                $('input[name="payment_flag_2"]').val('1');

            }else{
                $(this).prop('checked', false);
                $('input[name="payment_flag_2"]').val('0');
            }
        }).trigger('change');

        $('#payment_flag_3').change(function(){
            if($(this).is(':checked')) {
                $(this).prop('checked', true);
                $('input[name="payment_flag_3"]').val('1');

            }else{
                $(this).prop('checked', false);
                $('input[name="payment_flag_3"]').val('0');
            }
        }).trigger('change');

        $('#searchBtn').click(function(){
            $('input[name="searchBtn"]').val('1');
        }).trigger('submit');

        $('input[type="radio"].dwStatus').change( function() {
            var dwStatus = $(this).val();

            $('.dashboard-stat').removeClass('checked');
            $('.dashboard-stat.'+dwStatus).addClass('checked');

            //when checked dashbord link search status and search time
            getSearchStatus(dwStatus);

            $('#search-form').on('submit', function(){
                if(dwStatus.indexOf("All") == -1){
                    $('#deposit_date_from').val(moment().startOf('day').format('Y-MM-DD HH:mm:ss'));
                    $('#deposit_date_to').val(moment().endOf('day').format('Y-MM-DD HH:mm:ss'));
                    $('input[name="enable_date"]').val('1');
                    $('#excludeTimeout').prop('checked', false);
                    $('input[name="payment_flag_1"]').val('1');
                    $('input[name="payment_flag_2"]').val('1');
                    $('input[name="payment_flag_3"]').val('1');
                    $("#payment_account_id").prop("checked", true);
                    $(".payment_account_id").prop("checked", true);
                    clearSearchInputVal();
                    if(dwStatus == "requestBankDeposit"){
                        $('input[name="payment_flag_1"]').val('1');
                        $('input[name="payment_flag_2"]').val('0');
                        $('input[name="payment_flag_3"]').val('1');
                    }
                    else if(dwStatus == "request3rdParty"){
                        $('input[name="payment_flag_1"]').val('0');
                        $('input[name="payment_flag_2"]').val('1');
                        $('input[name="payment_flag_3"]').val('0');
                    }
                }else{
                    $('input[name="payment_flag_1"]').val('1');
                    $('input[name="payment_flag_2"]').val('1');
                    $('input[name="payment_flag_3"]').val('1');
                    $("#payment_account_id").prop("checked", true);
                    $(".payment_account_id").prop("checked", true);
                    $('#deposit_date_from').val(moment().startOf('month').format('Y-MM-DD HH:mm:ss'));
                    $('#deposit_date_to').val(moment().endOf('day').format('Y-MM-DD HH:mm:ss'));
                    $('input[name="enable_date"]').val('1');
                    $('#excludeTimeout').prop('checked', false);
                    clearSearchInputVal();
                }
            });

            $('#search-form').trigger('submit');
        });



        $('.dashboard-stat .dashboard-radio-btn').click( function () {
            $('.date-range').removeAttr('checked');
            if ($(this).hasClass('count_month')) {
                $('#date_range_month').prop('checked', 'checked');
                $('input[name="searchBtn"]').val('0');
            }else if($(this).hasClass('count_today')){
                $('#date_range_today').prop('checked', 'checked');
                $('input[name="searchBtn"]').val('0');
            }else if($(this).hasClass('count_today_bank')){
                $('#date_range_today').prop('checked', 'checked');
                $('input[name="searchBtn"]').val('0');
            }else if($(this).hasClass('count_today_3rd')){
                $('#date_range_today').prop('checked', 'checked');
                $('input[name="searchBtn"]').val('0');
            }
        });

        var radioCheckedMon = $('#date_range_month').is(':checked');
        var radioCheckedToday = $('#date_range_today').is(':checked');
        var dwStatusVal = '<?=$conditions['dwStatus']?>';
        var searchBtnVal = $('input[name="searchBtn"]').val();
        if(searchBtnVal == '1'){
            reomvedCountDateClass();
        }else if(radioCheckedMon){
            checkedDateRangeAddClass(dwStatusVal);
        }else if(radioCheckedToday){
            checkedDateRangeAddClass(dwStatusVal);
        }

        $.fn.dataTable.Api.register( 'column().title()', function () {
            var colheader = this.header();
            return $(colheader).text().trim();
        } );

        var request_time_index = '';

        for (var i = dataTable.columns().header().length - 1; i >= 0; i--) {
            if(dataTable.column(i).title() == "<?=lang('pay.reqtime')?>")
                request_time_index = i;
        }

        dataTable.on('order.dt', function(e, dt){
            // change input value
            var params = {};
                columnIndex = dt.aaSorting[0][0],
                columnDirection = dt.aaSorting[0][1];

            $('#datatable_index').val(columnIndex);
            $('#datatable_direction').val(columnDirection);

            // change url value
            var urlParams = getQueryParameters();
            urlParams['datatable_index'] = columnIndex;
            urlParams['datatable_direction'] = columnDirection;
            setQueryParameters(urlParams);

        });

        $('#depositDetailsModal').on('hidden.bs.modal', function (e) {
            dataTable.ajax.reload(null, false);
        });

    });
</script>

<!-- start depositDetailsModal-->
<div class="row">
    <div class="modal fade" id="depositDetailsModal" tabindex="-1" role="dialog" data-keyboard="true" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal_full">
            <div class="modal-content modal-content-three">
                <div class="modal-header">
                    <a class='notificationRefreshList' href="<?=site_url('payment_management/deposit_list/' . $conditions['dwStatus'] )?>">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">×</span><span class="sr-only"><?=lang("lang.close")?></span>
                        </button>
                    </a>
                    <h4 class="modal-title" id="myModalLabel"><i class="icon-note"></i>&nbsp;<?=lang("pay.deposit") . ' ' . lang("pay.req") . ' ' . lang("lang.details");?></h4>
                </div>

                <div class="modal-body" id="deposit_panel">
                    <div class="row">
                        <div class="col-md-12" id="checkPlayer">
                            <!-- Deposit transaction -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <?=lang("pay.deposit") . ' ' . lang('lang.info');?>
                                                <a href="#personal" id="hide_deposit_info" class="btn btn-info btn-sm pull-right">
                                                    <i class="glyphicon glyphicon-chevron-down" id="hide_deposit_info_up"></i>
                                                </a>
                                                <div class="clearfix"></div>
                                            </h4>
                                        </div>

                                        <div class="panel-body" id="deposit_info_panel_body" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-3">
                                                        <label><?=lang("pay.sale_order_id");?>:</label>
                                                        <div class="readonly_field secure_id"></div>
                                                        <label>&nbsp;</label>
                                                        <div class="readonly_field deposit_slip"></div>

                                                        <label><?=lang('pay.reference')?>: </label>
                                                        <span class="reference_no"></span><br>

                                                        <div>
                                                            <label><?=lang('cashier.55')?>: </label>
                                                            <span class="player_deposit_time "></span>
                                                        </div>

                                                        <div>
                                                            <label><?=lang('Deposit Method')?>: </label>
                                                            <span class="player_deposit_method"></span>
                                                        </div>

                                                        <script>
                                                            $(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
                                                                event.preventDefault();
                                                                $(this).ekkoLightbox();
                                                            });
                                                        </script>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label><?=lang("pay.user") . ' ' . lang("pay.name");?>:</label>
                                                        <div class="readonly_field username"></div>
                                                        <?php if($this->utils->isEnabledFeature('enable_upload_deposit_receipt')): ?>
                                                            <label><?=lang("pay.deposit") . ' ' . lang("Invoice");?>:</label>
                                                            <div class="readonly_field" id="deposit_receipt"></div>
                                                        <?php endif;?>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label><?=lang("pay.realname");?>:</label>
                                                        <div class="readonly_field realname"></div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label><?=lang('pay.playerlev');?>:</label>
                                                        <div class="readonly_field group_level_name"></div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label><?=lang('pay.memsince');?>: </label>
                                                        <div class="readonly_field member_since"></div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label><?=lang('pay.approved_deposit_count');?>: </label>
                                                        <div class="readonly_field approved_deposit_count"></div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label><?=lang('player.ut11');?>: </label>
                                                        <div class="readonly_field external_order_id"></div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label><?=lang('pay.bank_order_id');?>: </label>
                                                        <div class="readonly_field bank_order_id"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- start payment method -->
                                            <hr/>
                                            <h4><?=lang('pay.paytmethdetls');?></h4>
                                            <hr/>

                                            <!-- start paypal payment method -->
                                            <div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-3">
                                                            <label><?=lang('pay.deposit') . ' ' . lang('pay.amt');?>:</label>
                                                            <div class="readonly_field amount"></div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label><?=lang('pay.deposit_payment_name');?>:</label>
                                                            <div class="readonly_field player_payment_type_name"></div>
                                                            <div class="readonly_field player_payment_account_name"></div>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label><?=lang('pay.deposit_payment_account_number');?>:</label>
                                                            <div class="readonly_field player_payment_account_number"></div>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label><?=lang('pay.deposit_transaction_code');?>:</label>
                                                            <div class="readonly_field player_deposit_transaction_code"></div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label><?=lang('Deposit Datetime');?>:</label>
                                                            <div class="readonly_field player_deposit_datetime"></div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-3">
                                                            <label><?=lang('pay.deposit_payment_branch_name');?>:</label>
                                                            <div class="readonly_field player_payment_branch_name"></div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label><?=lang('pay.deposit') . ' ' . lang('pay.ip') . ' ' . lang('lang.and') . ' ' . lang('pay.locatn');?>:</label>
                                                            <div class="readonly_field loc_info"></div>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label><?=lang('deposit.depositor_name');?>:</label>
                                                            <div class="readonly_field realname"></div>
                                                        </div>
                                                        <?php if ($this->utils->isEnabledFeature('enable_deposit_datetime')) :?>
                                                            <div class="col-md-2">
                                                                <label><?=lang('Deposit Datetime From Player');?>:</label>
                                                                <div class="readonly_field player_deposit_time"></div>
                                                            </div>

                                                            <div class="col-md-2">
                                                                <label><?=lang('Mode of Deposit');?>:</label>
                                                                <div class="readonly_field player_mode_of_deposit"></div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end paypal payment method -->

                                            <!-- start collection account -->
                                            <hr/>
                                            <h4><?=lang('pay.payment_account');?></h4>
                                            <hr/>
                                            <div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-4">
                                                            <label><?=lang('pay.collection_name');?>:</label>
                                                            <div class="readonly_field payment_type_name"></div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label><?=lang('pay.collection_account_name');?>:</label>
                                                            <div class="readonly_field payment_account_name"></div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label><?=lang('pay.collection_account_number');?>:</label>
                                                            <div class="readonly_field payment_account_number"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </br>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-4">
                                                            <label><?=lang('con.bnk20')?>:</label>
                                                            <div class="readonly_field collection_account_note"></div>
                                                            <br>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><?=lang('con.bnk10')?>:</label>
                                                            <input type="number" class="form-control" id="transaction_fee">
                                                            <br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end collection account -->

                                            <!-- start promotion -->
                                            <div id="promotion_request">
                                                <h4 class="page-header"><?php echo lang('Promotion');?></h4>

                                                <div class="readonly_field"><span id='promotionName'></span></div>

                                                <div class="readonly_field"><span id='promotionDescription'></span></div>

                                                <div class="readonly_field"><?php echo lang('Bonus Amount');?>: <span id='bonusAmount'></span></div>

                                                <div class="readonly_field"><?php echo lang('Withdraw Condition');?>: <span id='withdrawConditionAmount'></span></div>

                                                <div class="readonly_field"><?php echo lang('Release to');?>: <span id='release_to_wallet'></span></div>

                                                <div class="radio approve_decline hide">
                                                    <label>
                                                        <input type="radio" name="approve_promotion" id="approve_promotion" checked="checked" value="true">
                                                        <?=lang('Approve this promotion')?><br>
                                                        <input type="radio" name="approve_promotion" id="decline_promotion" value="false">
                                                        <?=lang('Decline this promotion')?><br>
                                                    </label>
                                                </div>
                                            </div>
                                            <!-- end promotion -->

                                            <!-- start account promotion -->
                                            <div id="promotion_account">
                                                <h4 class="page-header"><?php echo lang('Promotion for collection account');?></h4>

                                                <div class="readonly_field"><span class='promotionName'></span></div>

                                                <div class="readonly_field"><span class='promotionDescription'></span></div>
                                            </div>
                                            <!-- end account promotion -->

                                            <!-- start subwallet -->
                                            <div id="subwallet_request">
                                                <h4 class="page-header"><?php echo lang('SubWallet');?></h4>

                                                <div class="readonly_field">
                                                    <?php echo lang('Player requires to transfer to this subwallet')?> : <span id='subWalletName'></span>
                                                </div>

                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="approvedSubWallet" id="approvedSubWallet" checked="checked" value="true">
                                                        <?=lang('Approve to transfer to this subwallet')?><br>
                                                    </label>
                                                </div>
                                            </div>
                                            <!-- end subwallet -->

                                            <!-- start user self join -->
                                            <div id="player_group_level_request" style="display: none;">
                                                <h4 class="page-header"><?=lang('Player Group Level Request');?></h4>

                                                <div class="readonly_field"><?=lang('This player has requested to change his player group level from')?> <span class="label label-info group_level_name"></span> <?=lang('to')?> <span class="label label-info request_group_level_name"></span></div>

                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="approvedPlayerGroupLevelRequest_cbx" id="approvedPlayerGroupLevelRequest_cbx">
                                                        <?=lang('Approve change player group level request')?><br>
                                                        (<?=lang('This will only take effect on approved deposit request')?>)
                                                    </label>
                                                </div>
                                            </div>
                                            <!-- end user self join -->

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h4 class="page-header"><?=lang('lang.notes');?></h4>
                                                    <label><?=lang('Player Deposit Note')?>:</label>
                                                    <textarea class="form-control deposit-playerlog-notes notes-textarea" readonly></textarea>
                                                </div>
                                                <hr/>
                                                <div class="col-md-6">
                                                    <label><?=lang('Internal Note Record')?>:</label>
                                                    <textarea class="form-control deposit-internal-notes notes-textarea" readonly></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><?=lang('External Note Record')?>:</label>
                                                    <textarea class="form-control deposit-external-notes notes-textarea" readonly></textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" id="playerDepositDetails"></div>
                                                <div class="col-md-12" id="playerDepositSlip"></div>
                                                <div id="response-sec">
                                                    <div class="request-only" style="display: none;">
                                                        <div class="clearfix">
                                                            <div class="col-md-6 response-sec-sub">
                                                                <label><?=lang('Add Internal Note');?>:</label>
                                                                <textarea class="form-control notes-textarea" maxlength="500" id="internalRemarksTxt"></textarea>
                                                                <button type='button' class="btn btn-scooter response-sec pull-right add-notes-btn" id="internal_note" onclick="addNotes('InternalNote')">
                                                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" style="padding-right: 4px"></span> <?=lang('Add')?>
                                                                </button>
                                                            </div>
                                                            <div class="col-md-6 response-sec-sub">
                                                                <label><?=lang('Add External Note');?>:</label>
                                                                <textarea class="form-control notes-textarea" maxlength="500" id="externalRemarksTxt"></textarea>
                                                                <button type='button' class="btn btn-scooter response-sec pull-right add-notes-btn" id="externall_note" onclick="addNotes('ExternalNote')">
                                                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" style="padding-right: 4px"></span> <?=lang('Add')?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 response-sec-sub">
                                                            <?php if($this->permissions->checkPermissions('approve_decline_deposit')): ?>
                                                                <br><br>
                                                                <button class="btn btn-md btn-primary response-sec" id="btn_check_deposit" onclick="return checkDepositStatus(this)" style="display: none">
                                                                    <?=lang('Check Deposit Status')?>
                                                                </button>
                                                                <?php if($this->utils->isEnabledFeature('trigger_deposit_list_send_message')): ?>
                                                                    <!-- <input type="hidden" name="playerId" id="playerId" value="">
                                                                    <input type="hidden" name="msgSubject" id="msgSubject" value="<?=lang('trigger_deposit_list_send_message.subject')?>">
                                                                    <select id="sectpl" onchange="fillcontent(this.options[this.selectedIndex].value)">
                                                                        <option value=""><?=lang('deposit.msgtpl')?></option>
                                                                    </select> -->
                                                                <?php endif;?>

                                                                <span id="approveDeclineBtn">
                                                                    <button class="btn btn-md btn-scooter" id="approve_btn" onclick="return checkPaymentAccountStatusDisabled();">
                                                                        <?=lang('lang.approve');?>
                                                                    </button>
                                                                    <button class="btn btn-md btn-danger" id="decline_btn" onclick="return declineDepositNow();">
                                                                        <?=lang('pay.declnow');?>
                                                                    </button>
                                                                </span>
                                                            <?php endif;?>
                                                            <button class="btn btn-md btn-linkwater" onclick="closeDepositDetailModal()"><?=lang('lang.close');?></button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pull-right" id="closeBtn" style="display:none">
                                                    <button class="btn btn-md btn-default" onclick="closeDepositDetailModal()"><?=lang('lang.close');?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end of Deposit transaction-->
                        </div>
                    </div>
                </div>
                <div class="col-md-12" id="deposit_panel_msg"></div>
            </div>
        </div>
    </div>
</div>
<!-- end depositDetailsModal-->

<div class="modal fade" id="lockedModal" style="margin-top:130px !important;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><?= lang('Locked transaction') ?></h4>
            </div>
            <input type="hidden" id="hiddenId">
            <div class="modal-body">
                <p id="locked-message"></p>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="batchProcessModal" style="margin-top:130px !important;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title batch-process-title"><?= lang('Batch Process Summary')?></h4>
            </div>
            <div class="modal-body">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                        <span class="progressbar-text"><?= lang('Processing....') ?></span>
                    </div>
                </div>
                <table class="table table-striped" id="batchProcessTable">
                    <thead>
                        <tr>
                            <th width="30"><?= lang('lang.status') ?></th>
                            <th width="50"><?= lang('ID') ?></th>
                            <th><?= lang('Remarks') ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<div class="modal fade in" id="mainModal" tabindex="-1" role="dialog" aria-labelledby="mainModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mainModalLabel"></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<!--  SHOW DEPOSIT RECEIPT   -->
<div class="modal fade" id="view_deposit_receipt_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?=lang('Deposit').' '.lang('Invoce')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img class="viewDepositReceipt" src="" name="deposit_receipt">
                <input type="hidden" name="deposit_recript_id">
                <input type="hidden" id="deposit_receipt_player_id">
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" id="remove_receipt_btn" style="display: none" onclick="return removeReceipt();"><?=lang('Remove');?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=lang('Close')?></button>
            </div>
        </div>
    </div>
</div>
<!--  SHOW DEPOSIT RECEIPT   -->

<script type="text/javascript">
    <?php
        echo $this->utils->generateLangArray(array('lang.norecyet', 'confirm.decline', 'confirm.approve', 'error.default.message',
        'info.deposit_has_been_declined', 'info.deposit_has_been_approved', 'text.loading', 'lang.close', 'text.checking', 'text.unchecking', 'text.checking.message', 'auto.setchecking.message1', 'internet_banking', 'over_the_counter_deposit', 'atm_transfer', 'mobile_banking', 'cash_deposit_machine','wechat','alipay','tenpay','qqpay'));
    ?>
    var deposit_slip_image_path = "<?php echo $deposit_slip_image_path;?>";
    var deposit_receipt_image_path = "<?php echo $deposit_receipt_image_path;?>";
    var current_status="<?php echo $conditions['dwStatus'] ; ?>";
    var status_request="<?php echo Sale_order::STATUS_PROCESSING; ?>";
    var current_order_id=null;
    var system_id=null;
    var depositMethodNames = new Array();

    depositMethodNames['<?=Sale_order::PLAYER_DEPOSIT_METHOD_UNSPECIFIED?>'] = '<?=lang('lang.norecyet')?>';
    <?php foreach(Sale_order::PLAYER_DEPOSIT_METHODS as $depositMethod => $depositMethodValue) : ?>
        depositMethodNames['<?=$depositMethodValue?>'] = '<?=lang($depositMethod)?>';
    <?php endforeach; ?>

    var success_trans = 0;
    var fail_trans = 0;
    var totalTransation = 0;
    var totalCompleteTrans = 0;
    var currentLang = "<?= $this->language_function->getCurrentLanguage(); ?>";

    $('#decline-button-sec').hide();
    $('#approve-button-sec').hide();
    $('#remarks-sec').hide();
    $('#response-sec').show();

    $("#chkAll").click(function(){
        $('.chk-order-id').not(this).prop('checked', this.checked);
    });

    function modal(load, title) {
        var target = $('#mainModal .modal-body');
        $('#mainModalLabel').html(title);
        target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(load);
        $('#mainModal').modal('show');
    }

    function checkDepositStatus(btn) {
        var id = current_order_id;
        var depositAPI = system_id;
        var deposit_api_before_submit_dialog = <?= json_encode($this->CI->utils->getConfig('deposit_api_before_submit_dialog')) ?>;
        if(deposit_api_before_submit_dialog[depositAPI]){
            BootstrapDialog.show({
                title: deposit_api_before_submit_dialog[depositAPI]['title'],
              message: deposit_api_before_submit_dialog[depositAPI]['message'],
              buttons: [{
                label: deposit_api_before_submit_dialog[depositAPI]['confirm_label'],
                action: function(dialog) {
                    var value = dialog.getModalBody().find('#player_input').val();

                    $.ajax({
                        'url' : base_url +'payment_management/checkDepositStatus/' + id + '/' + system_id + '/' + value,
                        'type' : 'POST',
                        'dataType' : "json"
                    }).done( function(data) {
                        alert('<?=lang('Save check Deposit Status msg to action log.')?>');
                    });

                    dialog.close();
                }
              }, {
                label: deposit_api_before_submit_dialog[depositAPI]['close_label'],
                action: function(dialog) {
                  dialog.close();
                  return;
                }
              }]
            });
        }
        else{
            $.ajax({
                'url' : base_url +'payment_management/checkDepositStatus/' + id + '/' + system_id,
                'type' : 'POST',
                'cache' : false,
                'dataType' : "json"
            }).done( function(data) {
                alert('<?=lang('Save check Deposit Status msg to action log.')?>');
            });
        }
    }

    $('#depositDetailsModal').on('hidden.bs.modal', function () {
        var id=current_order_id;
        utils.safelog(id);
        unlockedTransaction(id);
    });

    function addNotes(noteBtn) {
        if(noteBtn == 'InternalNote'){
            var remarkTxt = $('#internalRemarksTxt').val();
            if(remarkTxt == ''){
                return;
            }
            addInternaNotesText(remarkTxt);
        }else if(noteBtn == 'ExternalNote'){
            var remarkTxt = $('#externalRemarksTxt').val();
            if(remarkTxt == ''){
                return;
            }
            addExternalNotesText(remarkTxt);
        }else{
            return;
        }
    }

    function addInternaNotesText(notes) {
        var saleOrderId = current_order_id;
        if(saleOrderId == ""){
            saleOrderId = current_order_id;
        }
        unlockedTransaction(saleOrderId);

        $.ajax({
            'url' : base_url + 'payment_management/addDepositNotes/2/' + saleOrderId,
            'type' : 'POST',
            'data' : { 'notes' : notes },
            'dataType': 'json',
            cache:false,
            ifModified :true ,
            'success' : function (data) {
                $("#internalRemarksTxt").val('');
                var notes = data.notes, notesLength = notes.length ;
                var depositNotes = $('.deposit-internal-notes');
                    depositNotes.val(notes);
                if(notesLength > 1){
                    depositNotes.scrollTop(depositNotes[0].scrollHeight - depositNotes.height());
                }
                alert("<?=lang('Notes has been added.')?>");
            },
        });
    }

    function addExternalNotesText(notes) {
        var saleOrderId = current_order_id;

        if(saleOrderId == ""){
            saleOrderId = current_order_id;
        }
        unlockedTransaction(saleOrderId);

        $.ajax({
            'url' : base_url + 'payment_management/addDepositNotes/3/' + saleOrderId,
            'type' : 'POST',
            'data' : { 'notes' : notes },
            'dataType': 'json',
            'success' : function (data) {
                $("#externalRemarksTxt").val('');
                var notes = data.notes, notesLength = notes.length ;
                var depositNotes = $('.deposit-external-notes');
                    depositNotes.val(notes);
                if(notesLength > 1){
                    depositNotes.scrollTop(depositNotes[0].scrollHeight - depositNotes.height());
                }
                alert("<?=lang('Notes has been added.')?>");
            },
        });
    }

    function showDetialNotes(saleOrderId, note_type) {
        $.ajax({
            'url' : base_url + 'payment_management/getDepositDetialNotes/' + saleOrderId + '/' + note_type,
            'type' : 'POST',
            'dataType': 'json',
            'success' : function (data) {
                var allNotes = data.formatNotes, secure_id = data.secure_id, noteSubTitle = data.noteSubTitle;
                var subtitle = '<div>'+ noteSubTitle +'</div>' + '<br><textarea class="form-control" rows="15" readonly style="resize: none;"></textarea>';
                if(data.success) {
                    BootstrapDialog.show({
                        id: 'bootstrap_dialog_id',
                        title: 'NO.' + secure_id,
                        message: $(subtitle).val(allNotes.trim()),
                        buttons: [{
                            label: 'Close',
                            action: function(dialogItself){
                                dialogItself.close();
                            }
                        }]
                    });
                }else{
                    alert('<?=lang("Something is wrong, show notes detail failed")?>');
                }
            },
        });
    }

    function showHint(txt){
        var html  = '';
        html += '<p>';
        html += txt;
        html += '</p>';
        $('#playerDepositDetails').html(html);
    }

    function lockDeposit(requestId, callbackable) {
        $.ajax(
            base_url +'payment_management/userLockDeposit/'+requestId,
            {
                cache: false,
                method: 'POST',
                dataType: 'json',
                error: function(){
                    alert("<?=lang('Lock failed')?>");
                },
                success: function(data){
                    if(data){
                        if(data['lock_result']) {
                            showModal();
                            callbackable();
                        } else {
                            lockedModal(data.message);
                        }
                    }else{
                        lockedModal("<?=lang('Lock failed')?>");
                    }
                }
            }
        );

    }

    function lockedModal(message) {
        $('#lockedModal').modal('show');
        $('#locked-message').html(message);
    }

    function showModal() {
        $('#depositDetailsModal').modal('show');
    }

    function getDepositDetail(id) {
        current_order_id = id;

        lockDeposit(id, function(){
            showHint(lang['text.loading']);
            var _norecyet = lang['lang.norecyet'];

            $.ajax({
                'url' : base_url +'payment_management/get_deposit_detail/' + id,
                'type' : 'GET',
                'cache' : false,
                'dataType' : "json"
            }).done( function(data) {
                if (data && data['id']) {
                    showHint('');

                    current_order_id = data['id'];

                    $.each(data, function(k,v) {
                        <?php /**
                         * NOTE: v is transcribed to lang['lang.norecyet'] here for all k != 'deposit_slip'
                         * rendering guard conditions (v==null) (v=='') (v=='null') useless
                         * Use (v != _norecyet) for guard condition instead
                         * OGP-9539 rupert.chen
                         */ ?>

                        if(v == null || v == '' || v == 'null'){
                            if(k != 'deposit_slip'){
                                v = lang['lang.norecyet'];
                            }
                        }

                        if(k == 'deposit_slip' ){
                            if(v != '' && v != 'null' && v != null && v!='0'){
                                v = '<a href="'+deposit_slip_image_path+v+'" id="link-deposit-slip" data-toggle="lightbox"  data-title=""> <img src="'+deposit_slip_image_path+v+'" style="width:150px;"/> </a> ';
                            }
                        }

                        <?php if($this->utils->isEnabledFeature('enable_upload_deposit_receipt')): ?>
                            if(k == 'deposit_receipt_info'){
                                var deposit_receipt = $('#deposit_receipt');
                                var none = "<?=lang('N/A');?>";
                                var file_path =[] ;
                                var file_id = [];
                                var img_area = '';
                                var player_id = '';

                                if(v != '' && v != 'null' && v != null && v!='0' && v!=none && v != _norecyet){
                                    var file = JSON.parse(v);
                                    $.each(file, function(index,value){
                                        if(file.length > 0){
                                            file_path.push(deposit_receipt_image_path + value.sales_order_id + '/' + value.id);
                                            file_id.push(value.id);
                                        }
                                        player_id = value.player_id;
                                    });

                                    for(var i=0; i<file_path.length; i++){
                                        img_area += '<a href="javascript:void(0)" onclick="viewUploadDespositReceiptModal(\''+file_id[i]+'\',\''+ file_path[i] +'\',\''+ player_id +'\')"> <img src="'+file_path[i]+'" style="width:100px;"/> </a>';
                                    }
                                    $('#remove_receipt_btn').show();
                                    deposit_receipt.html(img_area);
                                }else{
                                    $('#remove_receipt_btn').hide();
                                    deposit_receipt.html(none);
                                }
                            }
                        <?php endif;?>

                        if(k == 'player_deposit_time' && v == '0000-00-00 00:00:00' ){
                            <?php if ($this->utils->isEnabledFeature('trigger_deposit_list_send_message')): ?>
                                $('#deposit_time').val(v);
                            <?php endif?>
                            v = lang['lang.norecyet'];
                        }

                        if(k =='reference_no'){
                            $('#link-deposit-slip').attr('data-title', v);
                        }

                        if(k =='reference_no'  && (v == '0' || v == ''  || v == null || v == 'null')){
                            v = lang['lang.norecyet'];
                        }

                        if(k == 'system_id'){
                            system_id = v;
                        }
                        <?php if ($this->utils->isEnabledFeature('trigger_deposit_list_send_message')): ?>
                            if(k == 'player_id' ){
                                $('#playerId').val(v);
                            }
                        <?php endif?>

                        if(k =='sale_order_player_notes'){
                            if((v != 'N/A') && (v != '无') && v != _norecyet) {
                                if(v.length != 0) {
                                    var notes = v,
                                    notesStr ="",
                                    notesLength = v.length ;
                                    for(var i=0; i < notesLength ; i++){
                                        notesStr += "["+notes[i].created_at+"] : "+notes[i].content+"\n";
                                    }
                                    var depositNotes = $('.deposit-playerlog-notes');
                                        depositNotes.val(notesStr);
                                    if(notesLength > 1){
                                        depositNotes.scrollTop(depositNotes[0].scrollHeight - depositNotes.height());
                                    }
                                    $('#link-deposit-slip').attr('data-title', v);
                                }
                            }
                        }

                        if(k =='sale_order_internal_notes'){
                            if((v != 'N/A') && (v != '无') && v != _norecyet) {
                                if(v.length != 0) {
                                    var notes = v,
                                    notesStr ="",
                                    notesLength = v.length ;
                                    for(var i=0; i < notesLength ; i++){
                                        notesStr += "["+notes[i].created_at+"] "+notes[i].creater_name+": "+notes[i].content+"\n";
                                    }
                                    var depositNotes = $('.deposit-internal-notes');
                                        depositNotes.val(notesStr);
                                    if(notesLength > 1){
                                        depositNotes.scrollTop(depositNotes[0].scrollHeight - depositNotes.height());
                                    }
                                    $('#link-deposit-slip').attr('data-title', v);
                                }
                            }
                        }

                        if(k =='sale_order_external_notes'){
                            if((v != 'N/A') && (v != '无') && v != _norecyet) {
                                if(v.length != 0) {
                                    var notes = v,
                                    notesStr ="",
                                    notesLength = v.length ;
                                    for(var i=0; i < notesLength ; i++){
                                        notesStr += "["+notes[i].created_at+"] "+notes[i].creater_name+": "+notes[i].content+"\n";
                                    }
                                    var depositNotes = $('.deposit-external-notes');
                                        depositNotes.val(notesStr);
                                    if(notesLength > 1){
                                        depositNotes.scrollTop(depositNotes[0].scrollHeight - depositNotes.height());
                                    }
                                    $('#link-deposit-slip').attr('data-title', v);
                                }
                            }
                        }

                        if(k == 'player_mode_of_deposit') {
							if(v == '' || v == null || v == 'null' || v == 'N/A' || v == '无'){
								v = lang['lang.norecyet'];
							}else{
								v = lang[v];
							}
						}

                        $('#depositDetailsModal .' + k).html(v);
                    });

                    var v=data['promotion_account'];
                    if((typeof v != 'undefined') && (v != 'N/A') && (v != '无') && (v != '') && (v != null) && v && v != _norecyet){
                        utils.safelog('is not null');
                        $('#promotion_account').show();
                        $('#promotion_account .promotionName').html(v['promoName']);
                        $('#promotion_account .promotionDescription').html(v['promoDescription']);
                        $('#promotion_account .bonusAmount').html(v['bonusAmount']);
                        $('#promotion_account .withdrawConditionAmount').html(v['withdrawConditionAmount']);
                        $('#promotion_account .release_to_wallet').html(v['release_to_wallet']);
                        <?php if ($this->utils->isEnabledFeature('trigger_deposit_list_send_message')): ?>
                            $("#bonusAmount").val(v['bonusAmount'])
                        <?php endif?>
                    } else {
                        $('#promotion_account').hide();
                        $('#promotion_account .promotionName').html('');
                        $('#promotion_account .promotionDescription').html('');
                        $('#promotion_account .bonusAmount').html('');
                        $('#promotion_account .withdrawConditionAmount').html('');
                        $('#promotion_account .release_to_wallet').html('');
                    }

                    var v=data['playerActivePromo'];
                    if( (typeof v != 'undefined') && (v != 'N/A') && (v != '无') && (v != '') && (v != null) && v && v != _norecyet){
                        utils.safelog('is not null');
                        $('#promotion_request').show();
                        $('#promotionName').html(v['promoName']);
                        $('#promotionDescription').html(v['promoDescription']);
                        $('#bonusAmount').html(v['bonusAmount']);
                        $('#withdrawConditionAmount').html(v['withdrawConditionAmount']);
                        $('#release_to_wallet').html(v['release_to_wallet']);
                    }else{
                        utils.safelog('is null');
                        $('#promotion_request').hide();
                        $('#promotionName').html('');
                        $('#promotionDescription').html('');
                        $('#bonusAmount').html('');
                        $('#withdrawConditionAmount').html('');
                        $('#release_to_wallet').html('');
                    }

                    $('#player_group_level_request').hide();

                    if(data['sub_wallet_name']==''){
                        $('#subwallet_request').hide();
                        $('#approvedSubWallet').prop('disabled',true);
                        $('#approvedSubWallet').prop('checked',false);
                    }else{
                        $('#subwallet_request').show();
                        $('#subWalletName').html(data['sub_wallet_name']);
                        $('#approvedSubWallet').prop('disabled',false);
                        $('#approvedSubWallet').prop('checked',true);
                    }

                    if (isRequestStatus(data['status'])) {
                        changeToApproveDeclineButtons(data['processed_by_userid']);

                        $('.request-only').show();
                        $('#closeBtn').hide();
                        $('#approve_btn').show();
                        $('#decline_btn').show();
                        $('#response-sec').show();
                        if (data.request_group_level_name) {
                            $('#player_group_level_request').show();
                        }
                        $('#transaction_fee').removeAttr('readonly');
                    } else {
                        <?php if($this->utils->isEnabledFeature('enable_deposit_upload_documents')): ?>
                            $('.request-only').show();
                            $('.response-sec-sub').hide();
                        <?php else: ?>
                            $('.request-only').hide();
                        <?php endif;?>

                        $('#closeBtn').show();

                        $('#transaction_fee').attr('readonly','readonly').val(data['transaction_fee']==null?'':data['transaction_fee']);
                        $('#promotion_request .approve_decline').hide();

                        changeToOnlyCloseButton();
                    }
                } else {
                    showHint(lang['error.default.message']);
                    changeToOnlyCloseButton();
                }

                <?php if($this->utils->isEnabledFeature('trigger_deposit_list_send_message')): ?>
                    // getSelectMsgTpl();
                <?php endif?>

                var enabledCheckDepositStatusIdList = <?= json_encode($this->config->item('enabledCheckDepositStatusIdList')) ?>;
                // Only enabled API currently supports manual checking of status
                if(enabledCheckDepositStatusIdList.includes(system_id) == false) {
                    $('#btn_check_deposit').hide();
                }else{
                    $('#btn_check_deposit').show();
                }
            });
        });

        return false;
    }

    function addTransactionFee(transaction_fee, saleorder_id){
        if(transaction_fee!='' && transaction_fee>0){
            $.ajax({
                'url' : base_url +'payment_management/addDepositTransactionFee',
                'type' : 'GET',
                'dataType' : "json",
                'data': {'transaction_fee' :transaction_fee,'saleorder_id':saleorder_id},
                'success' : function(data){
                    if(data['success']){
                        $('#addTransactionFee').hide();
                        $('#transaction_fee').attr('readonly','readonly');
                    }
                }
            });
        }
    }

    function isRequestStatus(status){
        return status==status_request;
    }

    function setApproveButton(enable){
        if(enable){
            $('#repondBtn button').prop('disabled',true);
        }else{
            $('#repondBtn button').prop('disabled',false);
        }
    }

    function approveDeposit() {
        $('#repondBtn').hide();
        $('#remarks-sec').show();
        $('#decline-button-sec').hide();
        $('#approve-button-sec').show();

       return false;
    }

    <?php if($this->utils->isEnabledFeature('enable_upload_deposit_receipt')): ?>
        function removeReceipt(){
            var id = $("#remove_receipt_btn").val();
            var playerId = $('#deposit_receipt_player_id').val();

            if(!confirm("<?=lang('Do you want really delete it?')?>")){
                return;
            }

            $.ajax({
                'url' : site_url('payment_management/delete_deposit_attachment/'),
                'type' : 'POST',
                'cache' : false,
                'data' : { 'picId' : id , 'playerId' : playerId, 'tag' : 'deposit' },
                'dataType' : "json"
            }).done(function(data){
                if(data['msg_type']){
                    alert("<?=lang('Image successfully deleted!')?>");
                    window.location.reload();
                }
            }).fail(function(){
                alert('notify.delete.failed');
            });
            return false;
        }
    <?php endif;?>

    function checkPaymentAccountStatusDisabled(){
        var secureId = $('.secure_id').text();
        var url = '/payment_management/check_paymentAccount_status_disabled/' + secureId;
        $.getJSON(url, function(data) {
            if (data.success) {
                BootstrapDialog.show({
                    title: secureId,
                    type: BootstrapDialog.TYPE_DANGER,
                    message: '<?php echo lang('The collection account is now disabled, are you sure you want to proceed the approvement?'); ?>',
                    spinicon: 'fa fa-spinner fa-spin',
                    buttons: [{
                        icon: 'fa fa-save',
                        label: '<?php echo lang('Confirm'); ?>',
                        cssClass: 'btn-danger',
                        action: function(){
                            approveDepositNow();
                        }
                    }, {
                        label: '<?php echo lang('Cancel'); ?>',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            }
            else{
                approveDepositNow();
            }
        });
    }

    function approveDepositNow() {
        if(!confirm(lang['confirm.approve'])){
            return false;
        }
        var id=current_order_id;
        var actionlogNotes = 'Approve deposit from detail';
        showHint(lang['text.loading']);

        var approve_player_group_level_request = $('#approvedPlayerGroupLevelRequest_cbx').is(':checked');
        var approvedSubWallet=$('#approvedSubWallet').is(':checked');

        //Check if approve_promotion is hidden or not and to avoid the Prompt message You are not valid in this promo
        if($('#promotion_request').css('display') == 'none'){
            var approve_promotion=false;
        }else{
            var approve_promotion=$('#approve_promotion').is(':checked');
        }

        setApproveButton(false);
        var amount = $('.amount').text();
        var max_transaction_fee = amount; //*5/100;
        var transaction_fee = $('#transaction_fee').val();
        var saleorder_id=current_order_id;
        if(transaction_fee!=""){
            if(transaction_fee<=max_transaction_fee&&transaction_fee>=0){
            }else{
                alert("<?php echo lang("Invalid transaction fee");?>");
                return false;
            }
        }

        unlockedTransaction(id);

        $.ajax({
            'url' : site_url('payment_management/set_deposit_approved/'+id),
            'type' : 'POST',
            'data': {actionlogNotes: actionlogNotes, approve_player_group_level_request: approve_player_group_level_request,
                approvedSubWallet: approvedSubWallet,approve_promotion: approve_promotion
            },
            'cache' : false,
            'dataType' : "json"
        }).done(function(data){
            if(data['success']){
                showHint(lang['info.deposit_has_been_approved']);
                changeToOnlyCloseButton();
                <?php if($this->utils->isEnabledFeature('trigger_deposit_list_send_message')): ?>
                    //sendMsgToPlayer(remarks);
                <?php endif?>
                $('#transactionFeeSec').hide();
                $('#player_group_level_request').hide();
                $('#closeBtn').show();
                <?php if($this->utils->isEnabledFeature('enable_deposit_upload_documents')): ?>
                    $('.response-sec-sub').hide();
                <?php else: ?>
                    $('#response-sec').hide();
                <?php endif;?>

                addTransactionFee(transaction_fee, saleorder_id);//add transaction fee
                $('#search-form').trigger('submit');
                if(data['promo_result'] && !data['promo_result']['apply_promo_success']){
                    alert(data['promo_result']['apply_promo_message']);
                }else{
                    alert("<?php echo lang("Deposit Successful");?>");
                }
            }else{
                var error_message=data['error_message'];
                if(error_message==''){
                    error_message=lang['error.default.message'];
                }
                showHint(error_message);
                alert(error_message);
                setApproveButton(true);
                $('#search-form').trigger('submit');
            }
        }).fail(function(){
            showHint(lang['error.default.message']);
            alert(lang['error.default.message']);
            setApproveButton(true);
        });
       return false;
    }

    function declineDepositNow() {
        if(!confirm(lang['confirm.decline'])){
            return false;
        }

        var id=current_order_id;
        var actionlogNotes = 'Decline deposit from detail';
        showHint(lang['text.loading']);
        unlockedTransaction(id);
        $.ajax({
            'url' : site_url('payment_management/set_deposit_declined/'+id),
            'type' : 'POST',
            'data': {actionlogNotes: actionlogNotes},
            'cache' : false,
            'dataType' : "json"
        }).done(
        function(data){
            if(data['success']){
                showHint(lang['info.deposit_has_been_declined']);
                changeToOnlyCloseButton();
                <?php if($this->utils->isEnabledFeature('trigger_deposit_list_send_message')): ?>
                    //sendMsgToPlayer(remarks);
                <?php endif?>
                $('#transactionFeeSec').hide();
                <?php if($this->utils->isEnabledFeature('enable_deposit_upload_documents')): ?>
                    $('.response-sec-sub').hide();
                <?php else: ?>
                    $('#response-sec').hide();
                <?php endif;?>
                $('#search-form').trigger('submit');
            }else{
                showHint(lang['error.default.message']);
                alert('failed');
            }
        }
       );
       return false;
    }

    // ------------- Batch approve and decline function ---------------------------------------
    <?php if ($this->utils->isEnabledFeature('enable_batch_approve_and_decline')) : ?>
    function batchProcessOrderId(processType){
        if ($('.chk-order-id').length) {
            var confirmTypeMessage = "<?= lang('conf.batch.decline') ?>";
            var emptySelectionMessage = "<?= lang('select.deposit.decline') ?>";
            var modalTitle = "<?= lang('lang.batch.decline.summary') ?>";
            var maximum_deposit_request = "<?= lang('lang.maximum.deposit.request') ?>";

            if (processType == "APPROVE") {
                confirmTypeMessage = "<?= lang('conf.batch.approve') ?>";
                emptySelectionMessage = "<?= lang('select.deposit.approve') ?>";
                modalTitle = "<?= lang('lang.batch.approve.summary') ?>";
            }

            if (!$('.chk-order-id:checked').length) {
                alert(emptySelectionMessage);
                return false;
            }

            totalTransation = $('.chk-order-id:checked').length;
            totalCompleteTrans = 0;

            if(totalTransation > 20){
                alert(maximum_deposit_request);
                return false;
            }

            // Process deposit transaction
            if(!confirm(confirmTypeMessage)){
                return false;
            }

            $('.chk-order-id:checked').each(function(i, obj) {
                var orderId = $(this).val();
                $('.batch-process-title').text(modalTitle);
                $('#batchProcessModal').modal('show');

                setTimeout(
                    function () {
                        if (processType == "APPROVE") {
                            approveBatchDeposit(orderId);
                        } else if (processType == "DECLINE") {
                            declineBatchDeposit(orderId);
                        } else {
                            alert('Invalid type!');
                        }
                }, 3000);
            });
        }
    }

    function approveBatchDeposit(orderId) {
        var secureId = null;
        var actionlogNotes = "batch approve";
        var approve_player_group_level_request = false;
        var approvedSubWallet = false;
        var approve_promotion = false;

        // ------- Get deposit details
        $.ajax({
            'url' : base_url +'payment_management/get_deposit_detail/' + orderId,
            'type' : 'GET',
            'cache' : false,
            'dataType' : "json",
        }).done( function(data) {
            if (!data && !data['id']) {
                appendToBatchProcessSummary('Failed', secureId, 'Transation Id not found.');
                return false;
            }

            secureId = data['secure_id'];

            // Approve request
            if(typeof data['sub_wallet_name'] != 'undefined' && data['sub_wallet_name'] != 'N/A' && data['sub_wallet_name'] != '' && data['sub_wallet_name'] != null && data['sub_wallet_name']) {
                approvedSubWallet = true;
            }

            if(typeof data['playerActivePromo'] != 'undefined' && data['playerActivePromo'] != 'N/A' && data['playerActivePromo'] != '' && data['playerActivePromo'] != null && data['playerActivePromo']){
                approve_promotion = true;
            }

            // ------- Check if player lock to specific user
            $.ajax({
            'url' : base_url +'payment_management/userLockDeposit/'+orderId,
            'type' : 'GET',
            'cache' : false,
            'dataType' : "json",
            }).done( function(data) {
                if(data.message) {
                    appendToBatchProcessSummary('Failed', secureId, data.message);
                    return false;
                }

                // ------- Approve deposit
                $.ajax({
                    'url' : site_url('payment_management/set_deposit_approved/'+orderId),
                    'type' : 'POST',
                    'data': {actionlogNotes: actionlogNotes,
                        approve_player_group_level_request: approve_player_group_level_request,
                        approvedSubWallet: approvedSubWallet,
                        approve_promotion: approve_promotion
                    },
                    'cache' : false,
                    'dataType' : "json",
                }).done(function(data){
                    if(data['success']){
                        appendToBatchProcessSummary('Success', secureId, "<?php echo lang("Deposit Successful");?>");
                        return true;
                    }

                    var error_message=data['error_message'];
                    if(error_message == ''){
                        error_message=lang['error.default.message'];
                    }

                    appendToBatchProcessSummary('Failed', secureId, error_message);
                    return false;

                }).fail(function(){
                    appendToBatchProcessSummary('Failed', secureId, lang['error.default.message']);
                    return false;
                }); // ------- End of Approve deposit
            });  // ------- End of Check if player lock to specific user
        });  // ------- End of get deposit details
    }

    function declineBatchDeposit(orderId) {
        var secureId = null;
        var actionlogNotes = "batch decline";

        unlockedTransaction(orderId);

        // ------- Get deposit details
        $.ajax({
            'url' : base_url +'payment_management/get_deposit_detail/' + orderId,
            'type' : 'GET',
            'cache' : false,
            'dataType' : "json",
        }).done( function(data) {
            if (!data && !data['id']) {
                appendToBatchProcessSummary('Failed', secureId, 'Transation Id not found.');
                return false;
            }

            secureId = data['secure_id'];

            // ------ Decline deposit
            $.ajax({
                'url' : site_url('payment_management/set_deposit_declined/'+orderId),
                'type' : 'POST',
                'data': {actionlogNotes: actionlogNotes},
                'cache' : false,
                'dataType' : "json"
            }).done(function(data) {
                if(data['success']){
                    appendToBatchProcessSummary('Success', secureId, lang['info.deposit_has_been_declined']);
                    return true;
                } else {
                    appendToBatchProcessSummary('Failed', secureId, lang['error.default.message']);
                    return false;
                }
            }).fail(function(){
                appendToBatchProcessSummary('Failed', secureId, lang['error.default.message']);
                return false;
            });
        });

        return false;
    }

    function appendToBatchProcessSummary(status, id, remarks) {
        $('#batchProcessTable').append('<tr><td>'+status+'</td><td>'+id+'</td><td>'+remarks+'</td></tr>');

        if (status == 'Failed') {
            fail_trans++;
        } else {
            success_trans++;
        }

        totalCompleteTrans++;

        if (totalCompleteTrans == totalTransation) {
            completeProcess();
        }
    }

    function completeProcess() {
        $( ".progress-bar" ).removeClass('active');
        $( ".progress-bar" ).addClass('progress-bar-warning');
        $(".progressbar-text").text("<?= lang('Done!') ?>");
    }

    <?php endif; ?>
    // ------------- End of Batch approve and decline function -------------------------------


    function closeDepositDetailModal(){
        var id=current_order_id;
        //unlockedTransaction(id);
        $("#depositDetailsModal").modal("hide");
    }

    function unlockedTransaction(salesOrderId) {
        $.post(base_url + 'payment_management/unlockDepositTransaction', {salesOrderId : salesOrderId }, function(){});
    }

    function declineDeposit() {
        $('#remarks-sec').show();
        $('#repondBtn').hide();
        $('#decline-button-sec').show();
    }

    function cancelDecline(){
        $('#remarks-sec').hide();
        $('#repondBtn').show();
    }

    function changeToApproveDeclineButtons(processed_by_userid,status,processed_by_name){
        var locking_checking = "<?=$checking_deposit_locking?>";
        var currentUserId = "<?=$this->authentication->getUserId()?>";
        var autoCheckingConfig = "<?=$auto_checking_request?>";

        if(locking_checking == 1){
            if(status == 'checking'){

                if(processed_by_userid == currentUserId){
                    showHint(lang['text.checking']);
                    $('#repondBtn').show();
                    $('#closeBtn').hide();
                }else{
                    //this transaction is under checking with another user
                    showHint(lang['text.checking.message']+': '+processed_by_name);
                    $('#repondBtn').hide();
                    $('#closeBtn').show();
                }

                if(autoCheckingConfig){
                    $('#deposit_panel').hide();
                    var html  = '';
                        html += '<p>';
                        html += lang['auto.setchecking.message1'];
                        html += '</p>';
                        $('#deposit_panel_msg').html(html);
                }
            }else{
                $('#deposit_panel').show();
                $('#deposit_panel_msg').html("");
                $('#repondBtn').show();
                $('#closeBtn').hide();
            }

        }else{
            $('#deposit_panel').show();
            $('#deposit_panel_msg').html("");
            $('#repondBtn').show();
            $('#closeBtn').hide();
        }
    }

    function changeToOnlyCloseButton(){
        $('#approve_btn').hide();
        $('#decline_btn').hide();
    }

    function viewUploadDespositReceiptModal(id, path, player_id){
        $('img[name=deposit_receipt]').attr('src',path);
        $('#remove_receipt_btn').val(id);
        $('#deposit_receipt_player_id').val(player_id);
        $('#view_deposit_receipt_modal').modal('show');
    }

    $(document).ready(function(){
        //show hide column
        $('.showHideColumn').hide();
        closeCustomColumn();

        $('#batchProcessModal').on('hidden.bs.modal', function () {
            window.location.reload();
        })
    });

    function drag_start(event) {
        var style = window.getComputedStyle(event.target, null);
        event.dataTransfer.setData("text/plain",
        (parseInt(style.getPropertyValue("left"),10) - event.clientX) + ',' + (parseInt(style.getPropertyValue("top"),10) - event.clientY));
    }

    function drag_over(event) {
        event.preventDefault();
        return false;
    }

    function drop(event) {
        var offset = event.dataTransfer.getData("text/plain").split(',');
        var dm = document.getElementById('custom_column');
        dm.style.left = (event.clientX + parseInt(offset[0],10)) + 'px';
        dm.style.top = (event.clientY + parseInt(offset[1],10)) + 'px';
        event.preventDefault();
        return false;
    }

    var dm = document.getElementById('custom_column');
    if(dm){
        dm.addEventListener('dragstart',drag_start,false);
    }

    document.body.addEventListener('dragover',drag_over,false);
    document.body.addEventListener('drop',drop,false);

    function closeCustomColumn() {
        $('#custom_column').hide();
    }
    function openCustomColumn() {
        $('#custom_column').show();
    }

    function checkAll(name) {
        $('.'+name).prop('checked', $('#'+name).prop('checked'));
    }

    function getCurrentDate() {
        return (new Date ((new Date((new Date(new Date())).toISOString() )).getTime() - ((new Date()).getTimezoneOffset()*60000))).toISOString().slice(0, 19).replace('T', ' ');
    }
    $("#hide_deposit_payment_info").click(function() {
        $("#deposit_payment_info_panel_body").slideToggle();
        $("#hide_deposit_payment_info_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    <?php if($this->utils->isEnabledFeature('trigger_deposit_list_send_message')): ?>
        // function getSelectMsgTpl(){

        //     $.ajax({
        //         'url' : '/api/getAllMsgTpl',
        //         'type' : 'get',
        //         'cache' : false,
        //         'dataType' : "json"
        //     }).done(
        //         function(data){
        //             var flg;
        //             var optVal;
        //             $.each(data, function(k,v) {
        //                 flg =v.note;
        //                 if(flg==="Deposits"){
        //                     optVal = v.value;
        //                     $("#sectpl").append('<option value="'+optVal+'">'+v.name+'</option>');
        //                 }

        //             });

        //         }
        //     );
        // }

        // function fillcontent(v){
        //     var k;
        //     k = v.replace('[deposit_time]', $('.player_deposit_datetime').text());
        //     v = k.replace('[deposit_amount]', $('.amount').text());
        //     k = v.replace('[bonusAmount]', $('#bonusAmount').text());
        // }

        // function sendMsgToPlayer(remarks){

        //     $.ajax({
        //         'url' : '/api/sendMessageAdmin',
        //         'type' : 'post',
        //         'data': {playerId: $('#playerId').val(), subject: $('#msgSubject').val(),msg:remarks},
        //         'cache' : false,
        //         'dataType' : "json"
        //     }).done(
        //         function(data){
        //             if(data['flg']) {
        //                 // console.log('yes');
        //                 return true;
        //             }else{
        //                 // console.log('false');
        //                 return false;
        //             }
        //         }
        //     );

        // }
    <?php endif?>

    (function($){
        $("#payment_account_id").change(function(e){

            var isChecked = $(this).is(":checked");
            if(isChecked){
                $(".payment_account_id").prop("checked", isChecked);
            }else{
                $(".payment_account_id").prop("checked", isChecked);
            }
        });

        $(".payment_account_id").change(function(e){
            if(!$(this).is(":checked")){
                $("#payment_account_id").prop("checked", false);
            }else{
                $selected_all=true;
                $(".payment_account_id").each(function(){
                    if(!$(this).is(":checked")){
                        $selected_all=false;
                    }
                });

                $("#payment_account_id").prop("checked", $selected_all);

            }
        });
    })($);

    $(".image-box-list").on("click",function(){
        $(".image-box-list").attr("data-flag_open","true");
    });

    $('#depositDetailsModal').on('hide.bs.modal', function (e) {
        flag = $(".image-box-list").attr("data-flag_open");
        if (flag) {
            $(".image-box-list").removeAttr("data-flag_open");
            $(".closebtn").trigger("click");
            e.stopPropagation();
            return false;
        }
    });

    $('#payment_account_toggle').hide();
    $('#payment_account_toggle_btn').click(function(){
        $('#payment_account_toggle').toggle();
        if($('#payment_account_toggle_btn span').attr('class') == 'fa fa-plus-circle'){
            $('#payment_account_toggle_btn span').attr('class', 'fa fa-minus-circle');
            $('#payment_account_toggle_btn span').html(' <?=lang("Collapse All")?>');
        }
        else{
            $('#payment_account_toggle_btn span').attr('class', 'fa fa-plus-circle');
            $('#payment_account_toggle_btn span').html(' <?=lang("Expand All")?>');
        }
    });

    $(document).ready(function () {
        $.post(
            '/payment_management/deposit_list_header_counts'
        )
        .done(function (resp) {
            var fields = [ 'deposit_request_cnt', 'deposit_request_cnt_today_manual', 'deposit_request_cnt_today_auto', 'deposit_approved_cnt', 'deposit_approved_cnt_today', 'deposit_declined_cnt', 'deposit_declined_cnt_today', 'total_deposit_request_cnt_today_auto_and_manual' ];
            for (var i in fields) {
                var field = fields[i];
                $('#' + field).text(resp[field]);
            }
        })
        .fail(function (xhr, status, errors) {
            console.log('Error', {status: status, xhr_status: xhr.status, errors: errors});
        });
    });
</script>