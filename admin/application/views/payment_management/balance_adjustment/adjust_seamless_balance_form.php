<?=$this->load->view("resources/third_party/bootstrap-tagsinput")?>
<div >
<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><span class="text-danger" style="font-weight: bold;"><?=$platform_name?></span></h4>
    </div>
    <div class="panel-body">

        <form class="form-horizontal" action="/payment_management/adjust_seamless_balance_post/<?=implode('/', array($platform_id, $transaction_type, $player_id))?>" method="post" onsubmit="return submitForm();">
            <?=$double_submit_hidden_field?>
            <div class="form-group">
                <label class="col-md-3"><?=lang('pay.transactionType')?></label>
                <div class="col-md-9">
                    <span class="bg-danger"><?=lang('transaction.transaction.type.' . $transaction_type)?></span>
                </div>
            </div>
           

            <div class="form-group">
                <label class="col-md-3"><?=lang('pay.amt')?></label>
                <div class="col-md-9">
                    <input type="number" class="form-control" name="amount" step='any' min="0.01" required="required">
                </div>
            </div>


            <div class="form-group">
                <label class="col-md-3"><?=lang('pay.reason')?></label>
                <div class="col-md-9">
                    <textarea name="reason" class="form-control" rows="5" required="required"></textarea>
                </div>
            </div>
              

            <div class="form-group">
                <div class="col-md-offset-3 col-md-9">
                    <div role="toolbar" class="btn-toolbar">
                        <button type="submit" class="btn btn-primary btn_submit"><?=lang('lang.submit')?></button>
                        <button type='reset' class="btn btn-default"><?=lang('lang.reset')?></button>
                    </div>
                </div>
            </div>

        </form>
    </div> <!-- EOF .panel-body -->
    <div class="panel-footer hidden"></div>
</div>


</div>
<script type="text/javascript">
function submitForm(){
    if(confirm('<?=lang('balanceadjustment.confirm.adjustbalance')?>')){
        $('.btn_submit').prop('disabled',true);
        return true;

    }
    return false;
}
</script>
