<div class="row">
    <div class="col-md-12">
        <div class="panel panel-warning panel-og">
            <div class="panel-heading">
                <h4 class="panel-title pull-left"><span class="glyphicon glyphicon-edit"></span> <?=!empty($bank['playerBankDetailsId']) ? lang('cashier.104') : lang('cashier.103')?><!-- Bank Details  (<?=ucwords($dw_bank);?>) --></h4>
                <div class="btn-group pull-right" style="margin: 5px 0;">
                    <a href="#liveHelp" class="btn btn-default btn-sm text-uppercase" style="font-weight: bold;"><?=lang('cashier.40');?> <span class="glyphicon glyphicon-comment"></span></a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="panel-body">
                <form action="<?=BASEURL . 'smartcashier/postBankDetails/'?>" method="post" role="form" class="form-horizontal">
                    <input type="hidden" name="player_bank_details_id" value="<?=!empty($bank['playerBankDetailsId']) ? $bank['playerBankDetailsId'] : ''?>">
                    <input type="hidden" name="dw_bank" value="0" />
                    <div class="row">
                        <h6><label class="col-md-2 col-md-offset-2 control-label"  for="bank_name"><?=lang('cashier.67');?>:<i style="color:#ff6666;">*</i> </label></h6>

                        <div class="col-md-4">
                            <!-- <select name="bank_name" id="bank_name" class="form-control input-sm" data-toggle="popover" data-placement="top" data-trigger="hover" title="Notice" data-content="Please select name of the bank."> -->
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
                        <h6><label class="col-md-2 col-md-offset-2 control-label" for="bank_account_fullname"><?=lang('cashier.68');?><i style="color:#ff6666;">*</i> </label></h6>
                        <div class="col-md-4">
                            <input type="text" name="bank_account_fullname" id="bank_account_fullname" required class="form-control input-sm" value="<?php if (!empty($bank['bankAccountFullName'])) {echo $bank['bankAccountFullName'];} else {echo set_value('bank_account_fullname');}
?>">
                                <?php echo form_error('bank_account_fullname', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            <br/>
                        </div>
                    </div>

                    <div class="row">
                        <h6><label class="col-md-2 col-md-offset-2 control-label"  for="bank_account_number"><?=lang('cashier.69');?><i style="color:#ff6666;">*</i> </label></h6>

                        <div class="col-md-4">
                            <input type="text" name="bank_account_number" id="bank_account_number" required class="form-control input-sm" value="<?php if (!empty($bank['bankAccountNumber'])) {echo $bank['bankAccountNumber'];} else {echo set_value('bank_account_number');}
?>" >
                                <?php echo form_error('bank_account_number', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            <br/>
                        </div>
                    </div>

                    <div class="row">
                        <h6><label class="col-md-2 col-md-offset-2 control-label" for="bank_address"><?=lang('cashier.102');?>: </label></h6>

                        <div class="col-md-4">
                            <textarea name="bank_address" id="bank_address" class="form-control input-sm"><?php if (!empty($bank['bankAddress'])) {echo $bank['bankAddress'];} else {echo set_value('bank_address');}
?></textarea>
                                <?php echo form_error('bank_address', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
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
