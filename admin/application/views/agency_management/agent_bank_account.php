<?php
/**
 *   filename:   agent_bank_account.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-03
 *   @brief:     view for agent creating
 */
?>
<div class="row">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="icon-info"></i> <strong><?= lang('Bank Information'); ?></strong>
                    <a href="<?= BASEURL . 'agency_management/agent_information/' . $agent_id ?>" 
                        class="btn btn-default btn-sm pull-right" id="view_agent">
                        <span class="glyphicon glyphicon-remove"></span>
                    </a>
                </h4>
            </div>

            <div class="panel-body" id="bank_info">
                <!-- Personal Info -->
                <form method="POST" action="<?= BASEURL . 'agency_management/verify_bank_account' ?>" 
                    accept-charset="utf-8">
                    <input type="hidden" name="agent_id" id="agent_id" class="form-control" value="<?= $agent_id ?>">

                    <div class="col-md-12">
                        <!-- input bank_name (required) {{{4 -->
                        <div class="col-md-3 fields">
                            <label for="bank_name">
                                <font style="color:red;">*</font> 
                                <?=lang('Bank Name');?>
                            </label>

                            <input type="text" name="bank_name" id="bank_name" class="form-control " 
                            value="<?=$conditions['bank_name'];?>" data-toggle="tooltip" 
                            title="<?=lang('Bank Name');?>">

                            <span class="errors"><?php echo form_error('bank_name'); ?></span>
                            <span id="error-bank_name" class="errors"></span>
                        </div> <!-- input bank_name (required) }}}4 -->
                        <!-- input account_name  {{{4 -->
                        <div class="col-md-3 fields">
                            <label for="account_name">
                                <?=lang('Account Name');?>
                            </label>

                            <input type="text" name="account_name" id="account_name" class="form-control " 
                            value="<?=$conditions['account_name'];?>" data-toggle="tooltip" 
                            title="<?=lang('Account Name');?>">

                            <span class="errors"><?php echo form_error('account_name'); ?></span>
                            <span id="error-account_name" class="errors"></span>
                        </div> <!-- input account_name  }}}4 -->
                        <!-- input account_number (required) {{{4 -->
                        <div class="col-md-3 fields">
                            <label for="account_number">
                                <font style="color:red;">*</font> 
                                <?=lang('Account Number');?>
                            </label>

                            <input type="text" name="account_number" id="account_number" class="form-control " 
                            value="<?=$conditions['account_number'];?>" data-toggle="tooltip" 
                            title="<?=lang('Account Number');?>">

                            <span class="errors"><?php echo form_error('account_number'); ?></span>
                            <span id="error-account_number" class="errors"></span>
                        </div> <!-- input account_number (required) }}}4 -->
                        <!-- input branch_address  {{{4 -->
                        <div class="col-md-3 fields">
                            <label for="branch_address">
                                <?=lang('Branch Address');?>
                            </label>

                            <input type="text" name="branch_address" id="branch_address" class="form-control " 
                            value="<?=$conditions['branch_address'];?>" data-toggle="tooltip" 
                            title="<?=lang('Branch Address');?>">

                            <span class="errors"><?php echo form_error('branch_address'); ?></span>
                            <span id="error-branch_address" class="errors"></span>
                        </div> <!-- input branch_address  }}}4 -->
                    </div>
                    <br/>

                    <div class="row">
                        <center>
                            <input type="submit" class="btn btn-info btn-sm" value="<?= lang('lang.save'); ?>"/>
                            <a href="<?= BASEURL . 'agency_management/agent_information/' . $agent_id ?>" 
                                class="btn btn-default btn-sm" id="view_agent">
                                <?= lang('lang.cancel'); ?>
                            </a>
                        </center>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agent_bank_account.php
