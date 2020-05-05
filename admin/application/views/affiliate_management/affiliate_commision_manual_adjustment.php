<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-6">

                <div class="form-group">
                    <label><?php echo lang('Total Commission');?></label>
                    <input id="total_commission" type="text" class="form-control" placeholder="Total commission" value="<?php echo $this->utils->formatCurrencyNoSym($total_commission,2);?>" disabled>
                </div>

                <!-- <div class="form-group">
                    <label>&nbsp;</label>
                    <div style="font-size: 20px;" class="bg-primary"><?php echo lang('New Total Commission');?>:</div>
                </div>

                <div class="form-group">
                     <h1 class="new_total_commission"><?php echo $this->utils->formatCurrencyNoSym($total_commission,2);?></h1>
                </div> -->

            </div>
            <div class="col-lg-6">
                <form id="formATMC" method="POST" action="<?=site_url('affiliate_management/updateTotalOfAffiliateCommission/' . $affiliate_commission_id)?>" autocomplete="off">
                    <div class="form-group">
                        <label><?php echo lang('New Total Commission');?></label>
                        <input type="hidden" name="lang" id="lang" value="<?php echo lang('Are you sure you want to update this commission?');?>">
                        <input id="AFFMID" type="hidden" class="form-control" value="<?php echo$affiliate_commission_id;?>">
                        <!-- <input id="total_amount" name="total_amount"  type="hidden" class="form-control"  maxlength="10" value="<?php echo $this->utils->formatCurrencyNoSym($total_commission,2);?>"> -->
                        <input id="manual_amount" type="text" name="total_amount" class="form-control" placeholder="Enter amount ..." maxlength="10">
                        <!-- <label style="color: red; font-size: 12px;" id="msg_manual_amount"></label> -->
                        <button type="submit" style="display: none;"></button>
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('Note');?></label>
                        <textarea class="form-control" rows="5" name="note" placeholder="<?php echo lang('Adjustment Notes');?>..."><?=$commission_notes?></textarea>
                    </div>
                </form>
            </div>
        </div>

        <button type="button" class="btn btn-primary btn_commision_update" style="float: right;" disabled><?php echo lang('lang.submit')?></button>

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
            if ( ! e.shiftKey && (e.keyCode == 109 || e.keyCode == 189 || (e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105))) {
                $("#manual_amount").keyup(function (e) {
                    $total_commission = parseFloat($('#total_commission').val().replace(',', ''));
                    if(!$.trim($('#manual_amount').val()).length){
                        $('.new_total_commission').text($total_commission.toLocaleString('en',{"minimumFractionDigits":2}));
                        // $('#total_amount').val($total_commission);
                        $('.btn_commision_update').prop('disabled', true);
                    }else{
                        $new_commission = parseFloat($('#manual_amount').val());
                        $('.new_total_commission').text($new_commission.toLocaleString('en',{"minimumFractionDigits":2}));
                        // $('#total_amount').val($new_commission);
                        $('.btn_commision_update').prop('disabled', false);
                    }
                });
            } else {
                e.preventDefault();
            }
        });
        $(document).on("click",".btn_commision_update",function(){
            var message = $("#lang").val();
           var status = confirm(message);
            if (status == true) {
                $('#formATMC').submit();
                /*$affiliate_commission_id = $('#AFFMID').val();
                $url = '<?php echo site_url('affiliate_management/updateTotalOfAffiliateCommission') ?>';
                $.post( $url ,{ id: $affiliate_commission_id } ,function( data ) {
                    console.log(data);
                });*/
            } 
        });
    });
</script>