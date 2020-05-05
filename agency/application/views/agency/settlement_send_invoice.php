<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title pull-left">
            <i class="icon-stats-bars2"></i> 
            <?=lang('Send Settlement Invoice');?> 
        </h4>
        <div class="clearfix"></div>
    </div>
    <div class="panel panel-body" id="details_panel_body">
        <div class="col-md-12">
            <input type="hidden" name="settlement_id" value="<?=$settlement_id?>" />
            <input type="button" class="btn btn-sm btn-info" onclick="send_invoice_email()"
            value="<?=lang('Send Through E-mail');?>" />
            <input type="button" class="btn btn-sm btn-info" onclick="send_invoice_skype()"
            value="<?=lang('Send Through Skype');?>" />
        </div>
    </div>
</div>
