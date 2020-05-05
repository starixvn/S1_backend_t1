
<div class="container-fluid">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title"><span style="font-weight: bold;"><?php echo lang('Batch Add Cashback Bonus');?></span></h4>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" action="<?php echo site_url('/marketing_management/post_manually_batch_add_cashback_bonus');?>"
                method="post" onsubmit="return submitForm();" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="col-md-12"><?=lang('hint.batch_add_cashback_bonus')?></label>
                </div>
                <div class="form-group">
                    <label class="col-md-3"><?=lang('CSV File')?></label>
                    <div class="col-md-9 form-inline">
                        <div class="">
                            <input type="file" name="manually_batch_add_cashback_bonus_csv_file" class="form-control input-sm" required="required" accept=".csv">
                        </div>
                        <span class="help-block" style="color: #999;"><?=lang('cms.notes')?> : <?= $csv_note ?></span>
                    </div>
                </div>
               <!--  <div class="form-group">
                    <label class="col-md-3"><?=lang('cms.06');?></label>
                    <div class="col-md-9 form-inline">
                        <select class="form-control" name="promo_cms_id" required>
                            <?php foreach ($promoCms as $v): ?>
                                <option value="<?php echo $v['promoCmsSettingId']; ?>"><?php echo $v['promoName'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div> -->
                <!-- <div class="form-group">
                    <label class="col-md-3"><?=lang('Status')?></label>
                    <div class="col-md-9 form-inline">
                        <label class="radio-inline"><input type="radio" name="status" value="0" checked="checked"   required="required" aria-required="true"><?=lang('sale_orders.status.3')?></label>
                        <label class="radio-inline"><input type="radio" name="status" value="1" required="required"  aria-required="true"><?=lang('sale_orders.status.5')?></label>
                    </div>
                </div> -->
                <div class="form-group">
                    <label class="col-md-3"><?=lang('pay.reason')?></label>
                    <div class="col-md-9">
                        <textarea id="reason" name="reason" class="form-control" rows="5"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-3 col-md-9">
                        <div class="error alert alert-danger hide">
                            <strong><?=lang('Error'); ?>!</strong> <?=lang('con.d02'); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-3 col-md-3">
                        <div role="toolbar" class="btn-toolbar">
                            <button type='reset' class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default' ?>"><?=lang('lang.reset')?></button>
                            <button type="submit" class="btn btn_submit <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary' ?>" onclick="return confirm('<?=lang('confirm.request')?>')"><?=lang('lang.submit')?></button>
                        </div>
                    </div> <!-- EOF .col-md-offset-3.col-md-3 -->
                </div>

            </form>
        </div>
        <div class="panel-footer"></div>
    </div>
</div>
<script type="text/javascript">

var reasonFld = $('#reason');
var errorMsg = $('.error');
$('.dateInput').each( function() {
    initDateInput($(this));
});

function submitForm(){
    if(reasonFld.val() == '') {
        errorMsg.removeClass('hide');
        return false;
    }
    errorMsg.addClass('hide');
    $('.btn_submit').prop('disabled',true);
    return true;
}

$(document).ready(function(){
    $('#view_batch_balance_adjustment').addClass('active');
});

</script>
