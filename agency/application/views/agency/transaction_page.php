<?php
/**
 *   filename:   transaction_page.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-03
 *   @brief:     view for agent creating
 */
?>

<!-- form transaction {{{1 -->    
<div class="container">
    <form method="POST" id="transaction-form" 
        action="<?=site_url('agency/do_transaction')?>" accept-charset="utf-8">
        <div class="panel panel-primary ">
            <!-- panel heading of transaction {{{2 -->    
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="glyphicon glyphicon-list-alt"></i> 
                    <?=lang('Transaction');?> 
                </h4>
                <div class="pull-right">Fields with (<font style="color:red;">*</font>) are required.</div>
                <div class="clearfix"></div>
            </div> <!-- panel heading of transaction }}}2 -->    

            <!-- panel body of transaction  {{{2 -->    
            <div class="panel panel-body" id="transaction_panel_body">
                <!-- Basic Info {{{3 -->    
                <div class="col-md-12">
                    <!-- input username (required) {{{4 -->
                    <div class="col-md-3 fields">
                        <label for="username">
                            <font style="color:red;">*</font> 
                            <?=lang('Username');?>
                        </label>

                        <input type="text" name="username" id="username" class="form-control " 
                        value="" data-toggle="tooltip" 
                        title="<?=lang('Username');?>">

                        <span class="errors"><?php echo form_error('username'); ?></span>
                        <span id="error-username" class="errors"></span>
                    </div> <!-- input username (required) }}}4 -->
                    <!-- select user_type {{{4 -->
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label">
                            <font style="color:red;">*</font> 
                            <?=lang('User Type');?>
                        </label>
                        <select name="user_type" id="user_type" class="form-control input-sm"
                            title="<?=lang('Select User Type')?>">
                            <option value="" selected>
                            --  <?=lang('None');?> --
                            </option>
                            <option value="agent">
                            <?=lang('agent');?>
                            </option>
                            <option value="player">
                            <?=lang('player');?>
                            </option>
                        </select>
                        <span class="errors"><?php echo form_error('user_type'); ?></span>
                        <span id="error-user_type" class="errors"></span>
                    </div> <!-- end of select user_type }}}4 -->
                    <!-- select trans_type {{{4 -->
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label">
                            <font style="color:red;">*</font> 
                            <?=lang('Deposit/Withdraw');?>
                        </label>
                        <select name="trans_type" id="trans_type" class="form-control input-sm">
                            <option value="" selected>
                            --  <?=lang('None');?> --
                            </option>
                            <option value="deposit">
                            <?=lang('Deposit');?>
                            </option>
                            <option value="withdraw">
                            <?=lang('Withdraw');?>
                            </option>
                        </select>
                        <span class="errors"><?php echo form_error('trans_type'); ?></span>
                        <span id="error-trans_type" class="errors"></span>
                    </div> <!-- end of select trans_type }}}4 -->
                </div>
                <div class="col-md-12">
                    <!-- input amount {{{4 -->
                    <div class="col-md-6 fields">
                        <label for="amount">
                            <font style="color:red;">*</font> 
                            <?=lang('Amount');?>
                        </label>

                        <input type="text" name="amount" id="amount" class="form-control " 
                        value="<?=set_value('amount', '0');?>" data-toggle="tooltip" 
                        title="<?=lang('Amount');?>">

                        <span class="errors"><?php echo form_error('amount'); ?></span>
                        <span id="error-amount" class="errors"></span>
                    </div> <!-- input amount (required) }}}4 -->
                </div>
                <!-- Basic Info }}}3 -->    
                <!-- button row {{{3 -->
                <div class="row">
                    <div class="col-md-5 col-lg-5" style="padding: 10px;">
                    </div>
                    <div class="col-md-6 col-lg-6" style="padding: 10px;">
                        <?php $reset_url=site_url('agency/transaction_page');?>
                        <input type="button" class="btn btn-default btn-sm" value="<?=lang('lang.reset');?>" 
                        onclick="window.location.href='<?php echo $reset_url; ?>'">
                        <input type="submit" class="btn btn-sm btn-primary agent-oper" value="<?=lang('Save');?>" />
                    </div>
                </div>
                <!-- button row }}}3 -->
            </div> <!-- panel body of transaction  }}}2 -->    
        </div>
    </form> <!-- end of form transaction }}}1 -->    
</div>

<script>
    <?php $agent_status = $this->session->userdata('agent_status'); ?>
    <?php if($agent_status == 'suspended') { ?>;
    set_suspended_operations();
    <?php } ?>
</script>

<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of transaction_page.php
