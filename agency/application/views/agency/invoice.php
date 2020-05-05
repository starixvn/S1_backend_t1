<?php
/**
 *   filename:   invoice.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-03
 *   @brief:     view for agent creating
 */
?>

<!-- form transaction {{{1 -->    
<div class="container">
    <form method="POST" id="transaction-form" 
        action="<?=site_url('agency/create_invoice')?>" accept-charset="utf-8">
        <div class="panel panel-primary ">
            <!-- panel heading of transaction {{{2 -->    
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="glyphicon glyphicon-list-alt"></i> 
                    <?=lang('Invoice');?> 
                </h4>
                <div class="clearfix"></div>
            </div> <!-- panel heading of transaction }}}2 -->    

            <!-- panel body of transaction  {{{2 -->    
            <div class="panel panel-body" id="transaction_panel_body">
                <!-- Basic Info {{{3 -->    
                <div class="col-md-12">
                    <!-- input invoice_file (required) {{{4 -->
                    <div class="col-md-3 fields">
                        <label for="invoice_file">
                            <font style="color:red;">*</font> 
                            <?=lang('Invoice File');?>
                        </label>

                        <input type="text" name="invoice_file" id="invoice_file" class="form-control " 
                        value="" data-toggle="tooltip" 
                        title="<?=lang('Invoice File');?>" readonly>

                        <span class="errors"><?php echo form_error('invoice_file'); ?></span>
                        <span id="error-invoice_file" class="errors"></span>
                    </div> <!-- input invoice_file (required) }}}4 -->
                </div>
                <!-- Basic Info }}}3 -->    
                <!-- button row {{{3 -->
                <div class="row">
                    <div class="col-md-2 col-lg-2" style="padding: 10px;">
                    </div>
                    <div class="col-md-6 col-lg-6" style="padding: 10px;">
                        <input type="button" class="btn btn-info btn-sm agent-oper" value="<?=lang('Download');?>" 
                        onclick="alert('Please Create Invoice File Before Download')">
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
// end of invoice.php
