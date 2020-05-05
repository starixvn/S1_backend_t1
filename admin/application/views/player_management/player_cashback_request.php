<link href="<?=$this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/daterangepicker.css')?>" rel="stylesheet">
<script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/daterangepicker.js')?>"></script>
<div class="table-responsive">
    <?php if($can_user_cashback): ?>
        <?php if(empty($cashback_request)): ?>
            <table id="resultTable" class="table-deposit-option table">
                <tr class="deposit-info default_row opbg">
                    <td class="mc-table-title"><?=lang('Game Bet Which Had Cashback')?></td>
                    <td><?=$summary_paid?></td>
                </tr>

                <tr class="deposit-info default_row opbg">
                    <td class=""></td>
                    <td class="col-deposit-option" >
                        <a href="#" id="request_cashback" class="btn btn-info"
                           title="<?=lang('xpj.cashback.cashback_immediately') . lang('xpj.cashback.cashback_settle')?>">
                            <?=lang('xpj.cashback.cashback_settle')?></a>
                    </td>
                </tr>
            </table>
        <?php else : ?>
            <table id="resultTable" class="table-deposit-option table">
                <tr class="deposit-info default_row opbg">
                    <td class="mc-table-title"><?=lang('xpj.cashback.request_datetime')?></td>
                    <td><?=$cashback_request->request_datetime?></td>
                </tr>
                <tr class="deposit-info default_row opbg">
                    <td class="mc-table-title"><?=lang('xpj.cashback.request_amount')?></td>
                    <td><?=$cashback_request->request_amount?></td>
                </tr>
                <tr class="deposit-info default_row opbg">
                    <td class="mc-table-title"><?=lang('xpj.cashback.status')?></td>
                    <td><?=lang('xpj.cashback.pending')?></td>
                </tr>
            </table>
        <?php endif; ?>
    <?php else : ?>
        <table id="resultTable" class="table-deposit-option table">
            <tr class="deposit-info default_row opbg">
                <th colspan="3"><?=lang('xpj.cashback.can_not_cashback')?></th>
            </tr>
        </table>
    <?php endif; ?>
</div>

<?php if($can_user_cashback): ?>
    <div style="">
        <div class="table-responsive">
            <table id="paid_cashback_table" class="table">
                <tr>
                    <td><?php echo lang('Cashback Time');?>:</td>
                    <td>
                        <input type="text" name="cashback_time" id="cashback_time" value="" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo lang('Game Platform');?>:</td>
                    <td>
                        <select id="cashback_game_platform">
                            <option value=""><?php echo lang('All');?></option>
                            <?php foreach($game_platforms as $key=>$label) { ?>
                                <option value="<?php echo $key;?>"><?php echo $label;?></option>
                            <?php }?>
                        </select>

                    </td>
                </tr>
                <tr>
                    <td><?php echo lang('Total Bet');?>:</td>
                    <td><span id="total_bet"></span></td>
                </tr>
                <tr>
                    <td><?php echo lang('Bet Available for Cashback');?>:</td>
                    <td><span id="available_for_cashback_bet"></span></td>
                </tr>
                <tr>
                    <td><?php echo lang('Available Cashback Amount');?>:</td>
                    <td><span id="available_cashback"></span></td>
                </tr>
                <tr>
                    <td><?php echo lang('Calculate Time');?>:</td>
                    <td><span id="calculate_time"></span></td>
                </tr>
            </table>
        </div>
    </div>
<?php endif; ?>


<script>

    var currentDate = moment(new Date()).format('YYYY-MM-DD');
    var fetchCashbackOnAdmin = true;
    var baseUrl = '<?=base_url();?>';

    $(function(){

        getPlayerCashbackStatus(currentDate);

        $("#cashback_game_platform" ).change(function() {
            getPlayerCashbackStatus($('#cashback_time').val());
        });

        $("#cashback_time").on('apply.daterangepicker', function() {
            var cashBackTime = $(this).val();
            getPlayerCashbackStatus(cashBackTime);
        });

        $('#request_cashback').on('click', function(){
            var availableForCashbackBet = parseFloat( $("#available_for_cashback_bet").html() );
            var availableCashback = parseFloat( $("#available_cashback").html() );
            if( availableForCashbackBet <= 0 && availableCashback <= 0 ){
                alert("<?=lang('xpj.cashback.no_available_cashback')?>");
                return false;
            }

            <?php if($disable_request){ ?>
                alert("<?php echo $disable_hint;?>");
            <?php }else{ ?>
                $.ajax({
                    url: baseUrl + 'player_management/cashbackRequest/<?=$player_id?>/' + fetchCashbackOnAdmin,
                    type: "POST",
                    data: {date_type: $("#cashback_time").val()},
                    success: function(data) {
                        $('#resultTable').html(data);
                    },
                    error: function(){
                        $("#request_cashback").show();
                    }
                }).always(function(){
                });
            <?php } ?>
        });

        $('input[name="cashback_time"]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    });

    function getPlayerCashbackStatus(cashbackTime){

        var dateType = cashbackTime == currentDate ? 'today' : cashbackTime;

        var params = {
            "cashback_game_platform"    : $("#cashback_game_platform").val(),
            "date_type"                 : dateType
        };

        $.post('/player_management/get_cashback_stat/<?=$player_id?>/' + fetchCashbackOnAdmin, params, function(data){
            if(data){
                $('#total_bet').html(data['total_bet']);
                $('#available_for_cashback_bet').html(data['available_for_cashback_bet']);
                $('#available_cashback').html(data['available_cashback']);
                // $('#calculate_time').html(data['calculate_time']);
                $calc_time = data['calculate_time'][0];
                $('#calculate_time').html($calc_time['start'] + ' &ndash; ' + $calc_time['end']);
            }
        }, 'json').fail(function(){
            alert("<?=lang('error.default.message')?>");
        });
    }
</script>


