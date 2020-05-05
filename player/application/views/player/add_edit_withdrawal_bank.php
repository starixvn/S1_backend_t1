<div class="row">
    <div class="col-md-12">
        <div class="panel panel-warning panel-og">
            <div class="panel-heading">
                <!-- <h4 class="panel-title pull-left"><span class="glyphicon glyphicon-edit"></span> <?=!empty($bank['playerBankDetailsId']) ? 'Edit' : 'Add'?> Bank Details (<?=ucwords($dw_bank);?>)</h4> -->
                <h4 class="panel-title pull-left"><span class="glyphicon glyphicon-edit"></span> <?=!empty($bank['playerBankDetailsId']) ? lang('cashier.104') : lang('cashier.103')?></h4>
                <div class="btn-group pull-right" style="margin: 5px 0;">
                    <a href="#liveHelp" class="btn btn-default btn-sm text-uppercase" style="font-weight: bold;"><?=lang('cashier.40');?> <span class="glyphicon glyphicon-comment"></span></a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="panel-body">
                <form action="<?=BASEURL . 'smartcashier/postWithdrawalBankDetails/'?>" method="post" role="form" class="form-horizontal">
                    <input type="hidden" name="player_bank_details_id" value="<?=!empty($bank['playerBankDetailsId']) ? $bank['playerBankDetailsId'] : ''?>">
                    <input type="hidden" name="dw_bank" value="1" />

                    <div class="row">
                        <h6><label class="col-md-2 col-md-offset-2 control-label"  for="bank_name"><?=lang('cashier.67');?>:<i style="color:#ff6666;">*</i> </label></h6>

                        <div class="col-md-4">
                            <select name="bank_name" id="bank_name" class="form-control input-sm" required>
                                <option value=""><?=lang('cashier.73');?></option>
                                <?php foreach ($banks as $row) {?>
                                    <option value="<?=$row['bankTypeId']?>" <?php echo set_select('bank_name', $row['bankName']); ?> <?=!empty($bank['bankTypeId']) && $bank['bankTypeId'] == $row['bankTypeId'] ? 'selected' : ''?>><?=$row['bankName']?></option>
                                <?php }
?>
                            </select>
                                <?php echo form_error('bank_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            <br/>
                        </div>
                    </div>

                    <div class="row">
                        <h6><label class="col-md-2 col-md-offset-2 control-label"  for="bank_account_fullname"><?=lang('cashier.68');?><i style="color:#ff6666;">*</i> </label></h6>

                        <div class="col-md-4">
                            <input type="text" name="bank_account_fullname" id="bank_account_fullname" class="form-control input-sm" value="<?php if (!empty($bank['bankAccountFullName'])) {echo $bank['bankAccountFullName'];} else {echo set_value('bank_account_fullname');}
?>" data-toggle="popover" data-placement="right" data-trigger="hover" title="Notice" data-content="Please enter your valid Bank Account Fullname." required>
                                <?php echo form_error('bank_account_fullname', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            <br/>
                        </div>
                    </div>

                    <div class="row">
                        <h6><label class="col-md-2 col-md-offset-2 control-label"  for="bank_account_number"><?=lang('cashier.69');?><i style="color:#ff6666;">*</i> </label></h6>

                        <div class="col-md-4">
                            <input type="text" name="bank_account_number" id="bank_account_number" class="form-control input-sm" value="<?php if (!empty($bank['bankAccountNumber'])) {echo $bank['bankAccountNumber'];} else {echo set_value('bank_account_number');}
?>"  required>
                                <?php echo form_error('bank_account_number', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            <br/>
                        </div>
                    </div>

                    <div class="row">
                        <h6><label class="col-md-2 col-md-offset-2 control-label"  for="bank_address"><?=lang('cashier.70');?> </label></h6>

                        <div class="col-md-4">
                            <input type="text" name="bank_province" id="bank_province" class="form-control input-sm" value="<?php if (!empty($bank['province'])) {echo $bank['province'];} else {echo set_value('province');}
?>" >
                            <!-- <textarea name="province" id="province" class="form-control input-sm" style="width: 255px; max-width: 255px; height: 60px;  max-height: 60px;" data-toggle="popover" data-placement="right" data-trigger="hover" title="Notice" data-content="Please enter your Bank Province."><?php if (!empty($bank['bankAddress'])) {echo $bank['bankAddress'];} else {echo set_value('bank_address');}
?></textarea> -->
                                <?php echo form_error('bank_province', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            <br/>
                        </div>
                    </div>

                    <div class="row">
                        <h6><label class="col-md-2 col-md-offset-2 control-label"  for="bank_account_number"><?=lang('cashier.71');?>: </label></h6>

                        <div class="col-md-4">
                            <input type="text" name="bank_city" id="bank_city" class="form-control input-sm" value="<?php if (!empty($bank['city'])) {echo $bank['city'];} else {echo set_value('city');}
?>" >
                                <?php echo form_error('bank_city', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            <br/>
                        </div>
                    </div>

                    <div class="row">
                        <h6><label class="col-md-2 col-md-offset-2 control-label"  for="bank_account_number"><?=lang('cashier.72');?>: </label></h6>

                        <div class="col-md-4">
                            <input type="text" name="bank_branch" id="bank_branch" class="form-control input-sm" value="<?php if (!empty($bank['branch'])) {echo $bank['branch'];} else {echo set_value('branch');}
?>" >
                                <?php echo form_error('bank_branch', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            <br/>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2 col-md-offset-4">
                            <input type="submit" class="btn btn-hotel" value="<?=lang('cashier.105');?>">
                        </div>
                    </div>

                </form>

                <div class="row">
                    <div class="col-md-3 pull-right">
                        <span class="help-block" style="color:#ff6666;"> * <?=lang('cashier.100');?>.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
