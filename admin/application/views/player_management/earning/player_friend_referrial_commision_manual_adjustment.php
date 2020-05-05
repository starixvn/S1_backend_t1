<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-6">
                <div class="input-group">
                    <label><?php echo lang('Total Commission');?></label>
                    <input id="total_commission" type="text" class="form-control" placeholder="Total commission" value="<?php echo number_format((float)$total_commission, 2, '.', ',');?>" disabled>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="input-group">
                    <label><?php echo lang('Manual Adjustment');?></label>
                    <form id="formATMC" method="POST" action="<?=site_url('player_management/updateTotalOfFriendReferrialMonthlyCommission/' . $friend_referrial_monthly_id)?>" autocomplete="off">
                        <input type="hidden" name="lang" id="lang" value="<?php echo lang('Are you sure you want to update this commission?');?>">
                        <input id="FRMID" type="hidden" class="form-control" value="<?php echo$friend_referrial_monthly_id;?>">
                        <input id="manual_amount" type="text" class="form-control" placeholder="Enter amount ..." maxlength="10">
                        <input id="total_amount" name="total_amount"  type="hidden" class="form-control"  maxlength="10" value="<?php echo$total_commission;?>">
                        <label style="color: red; font-size: 12px;" id="msg_manual_amount"></label>
                        <button type="submit" style="display: none;"></button>
                    </form>
                </div>
            </div>
        </div>
        <div style="padding-top: 30px;">
            <span style="font-size: 20px;" class="label label-default"><?php echo lang('New Total Commission');?>:</span>
        </div>
        <div style="padding-top: 30px;">
             <h1 class="new_total_commission"><?php echo number_format((float)$total_commission, 2, '.', ',');?></h1>
            <button type="button" class="btn btn-primary btn_commision_update" style="float: right;" disabled><?php echo lang('lang.update')?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#manual_amount").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                 // Allow: Ctrl+A, Command+A
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
                 // Allow: home, end, left, right, down, up
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                     // let it happen, don't do anything
                     return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                $('#msg_manual_amount').text("<?=sprintf(lang('formvalidation.is_numeric2'),lang('Manual Adjustment'))?>");
                e.preventDefault();
            } else {
                $("#manual_amount").unbind('keyup');
                $("#manual_amount").keyup(function (e) {
                    var total_commission = parseFloat($('#total_commission').val());
                    if(!$.trim($('#manual_amount').val()).length){
                        $(document).find('.new_total_commission').text(parseFloat(total_commission.toFixed(2)));
                        $(document).find('#total_amount').val(parseFloat(total_commission.toFixed(2)));
                        $(document).find('.btn_commision_update').prop('disabled', true);
                    }else{
                        var new_commission = total_commission + parseFloat($('#manual_amount').val());
                        $(document).find('.new_total_commission').text(new_commission.toFixed(2));
                        $(document).find('#total_amount').val(parseFloat(new_commission.toFixed(2)));
                        $(document).find('.btn_commision_update').prop('disabled', false);
                    }
                });
                
                $('#msg_manual_amount').text("");
            }
        });
        $(document).on("click",".btn_commision_update",function(){
            var message = $("#lang").val();
           var status = confirm(message);
            if (status == true) {
                $('#formATMC').submit();
            } 
        });
    });
</script>