<?php include __DIR__.'/../../includes/big_wallet_details.php'; ?>

<style type="text/css">
    .btn.disabled.loading, .btn[disabled].loading, fieldset[disabled] .btn.loading {
        cursor: progress;
    }
</style>
<div class="pad content">
<div class="row">
    <div class="col-md-3">
        <div class="panel panel-primary panel_player_info">
            <div class="panel-heading">
            <?=$player['username']?>
            </div>
            <div class="panel-body">
            <?php if ($this->utils->getConfig('show_point_on_player')) {?>
            <?php echo lang('VIP'); ?>: <small><?php echo $player['groupName'] . ' - ' . $player['levelName']; ?></small>
            <?php }?>
            <table class="table table-striped table-bordered">
                <tbody>
                    <tr>
                        <th><?=lang('cashier.02')?></th>
                        <td align="right" class='mainwallet_value'><?=$this->utils->displayCurrency($player['totalBalanceAmount'])?></td>
                    </tr>

                    <?php
$subwallets = array();
foreach ($subwallet as $key => $value) {
    $subwallets[$value['typeId']] = $value['totalBalanceAmount'];
    ?>
                        <tr>
                            <th><?=$value['game']?> <?=lang('cashier.41')?></th>
                            <td align="right"><div class='subwallet_value <?="wallet_" . $value['typeId'];?>' id="<?=strtolower($value['game']) . '_wallet'?>"><?=$this->utils->displayCurrency($value['totalBalanceAmount']);?></div>
                                <?php if ($this->config->item('show_realtime_balance')) {?>
                                    <button style='display:none;' data-callapi='query_balance' data-callapi-params='{"systemId":<?=$value['typeId'];?>}' data-callapi-state='{"show":"#<?=strtolower($value['game']) . '_wallet'?>","after":"recalcTotalBalance"}' data-callapi-autostart='true'></button>
                                <?php }
    ?>
                            </td>
                        </tr>
                    <?php }
?>

                    <?php if ($this->utils->getConfig('show_point_on_player')) {?>
                    <tr>
                        <th><?php echo lang('Point'); ?></th>
                        <td align="right" class='point_value'><?=$this->utils->displayCurrency($player['point'])?></td>
                    </tr>
                    <?php }?>

                </tbody>
            </table>
            </div>
        <div class="panel-footer">
            <?=lang('cashier.05')?>: <span id='playerTotalBalance' style="font-weight: bold;"><?=$this->utils->displayCurrency($totalBalance)?></span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button class='btn btn-primary btn-sm btn-inline loading refreshBalanceButton' onclick='refreshBalance()'><i class="glyphicon glyphicon-refresh"></i> <?=lang('lang.refreshbalance')?></button>
        </div>
        </div>

        <div class="form-horizontal">
            <div class="form-group">
                <div class="col-xs-6">
                    <a href="<?=site_url('iframe_module/iframe_playerSettings')?>" class="btn btn-primary btn-block"><i class="pull-left glyphicon glyphicon-edit"></i> <?=lang('cashier.15')?></a>
                </div>
                <div class="col-xs-6">
                    <a href="<?=site_url('iframe_module/iframe_bankDetails')?>" class="btn btn-primary btn-block"><i class="pull-left glyphicon glyphicon-briefcase"></i> <?=lang('cashier.16')?></a>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-6">
                    <a href="<?=site_url('iframe_module/iframe_makeDeposit')?>" class="btn btn-primary btn-block"><i class="pull-left glyphicon glyphicon-save"></i> <?=lang('Deposit')?></a>
                </div>
                <div class="col-xs-6">
                    <a href="<?=site_url('iframe_module/iframe_viewWithdraw/')?>" class="btn btn-primary btn-block"><i class="pull-left glyphicon glyphicon-open"></i> <?=lang('Withdrawal')?></a>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-6">
                    <a href="<?=site_url('iframe_module/iframe_myPromo')?>" class="btn btn-primary btn-block"><i class="pull-left glyphicon glyphicon-tag"></i> <?=lang('cashier.myPromo')?></a>
                </div>
                <div class="col-xs-6">
                    <a href="<?=site_url('iframe_module/iframe_promos')?>" class="btn btn-danger btn-block"><i class="pull-left glyphicon glyphicon-tags"></i> <?=lang('cashier.promotion')?></a>
                </div>
            </div>

            <?php if( $this->utils->getConfig('withdraw_verification') == 'withdrawal_password') { ?>
            <div class="form-group">
                <div class="col-xs-6">
                    <a href="<?=site_url('iframe_module/iframe_changePassword')?>" class="btn btn-primary btn-block"><i class="pull-left glyphicon glyphicon-lock"></i> <?=lang('mod.changepass')?></a>
                </div>
                <div class="col-xs-6">
                    <a href="<?=site_url('player_center/iframe_changeWithdrawPassword')?>" class="btn btn-primary btn-block"><i class="pull-left glyphicon glyphicon-pencil"></i><?=lang('Withdraw Change Password')?></a>
                </div>
            </div>
            <?php } ?>

            <div class="form-group">
                <div class="col-xs-6">
                    <a href="<?=$site?>" class="btn btn-primary btn-block" target="_parent"><i class="pull-left glyphicon glyphicon-play"></i> <?=lang('cashier.toGame')?></a>
                </div>
            </div>

            <div class="form-group">
                <div class="col-xs-6">
                    <a href="<?=site_url('iframe_module/iframe_logout')?>" class="btn btn-default btn-block"><i class="pull-left glyphicon glyphicon-log-out"></i> <?=lang('cashier.logout')?></a>
                </div>

                <?php if( $this->utils->getConfig('withdraw_verification') != 'withdrawal_password') { ?>
                    <div class="col-xs-6">
                        <a href="<?=site_url('iframe_module/iframe_changePassword')?>" class="btn btn-primary btn-block"><i class="pull-left glyphicon glyphicon-lock"></i> <?=lang('mod.changepass')?></a>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div>

    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-body">
                <h4><strong><i class="glyphicon glyphicon-transfer"></i> <?=lang('cashier.06')?></strong></h4>
                <hr/>
                <form name='transfer_wallet_form' class="form-horizontal" action="<?=site_url('iframe_module/verifyMoneyTransfer/' . $player['playerId'])?>" method="POST" onsubmit='return submitTransfer();'>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="transfer_from"><?=lang('cashier.07')?></label>
                            <select name="transfer_from" id="transfer_from" onchange="return transferFrom(this.value)" oninvalid="requiredField(this, '<?=lang('cashier.selectAccount')?>')" class="form-control input-sm" required>
                                <option value=""><?=lang('cashier.18')?></option>
                                <option value="0" <?=(set_value('transfer_from') == '0') ? 'selected' : ''?> ><?=lang('cashier.02')?></option>
                                <?php foreach ($game as $key => $value) {?>
                                    <option value="<?=$value['id']?>" <?=(set_value('transfer_from') == $value['id']) ? 'selected' : ''?> ><?=$value['system_code']?> <?=lang('cashier.41')?></option>
                                <?php }
?>
                            </select>
                            <label style="color: red;"><?=form_error('transfer_from')?></label>
                        </div>
                        <div class="col-md-6">
                            <label for="transfer_to"><?=lang('cashier.08')?></label>
                            <div class="input-group">
                                <select name="transfer_to" id="transfer_to" onchange="return transferTo(this.value)" class="form-control input-sm" oninvalid="this.setCustomValidity('<?=lang('cashier.selectAccount')?>')" required>
                                    <option value=""><?=lang('cashier.19')?></option>
                                    <option value="0" <?=(set_value('transfer_to') == '0') ? 'selected' : ''?> ><?=lang('cashier.02')?></option>
                                    <?php foreach ($game as $key => $value) {?>
                                        <option value="<?=$value['id']?>" <?=(set_value('transfer_to') == $value['id']) ? 'selected' : ''?> ><?=$value['system_code']?> <?=lang('cashier.41')?></option>
                                    <?php }
?>
                                </select>
                                <span class="input-group-btn">
                                    <button class="btn btn-default btn-sm" type="button" id="reset_fromto" onclick="return resetAll();" data-toggle="tooltip" data-placement="top" title="<?=lang('cashier.reset.fromto')?>">
                                        <i class="glyphicon glyphicon-refresh"></i>
                                    </button>
                                </span>
                            </div><!-- /input-group -->
                            <label style="color: red;"><?=form_error('transfer_to')?></label>
                        </div>
                        <div class="col-md-6">
                            <div><label for="amount"><?=lang('cashier.09')?></label></div>
                            <input type="text" min='0' required name="amount" id="amount" oninvalid="requiredField(this, '<?=lang('cashier.enterAmount')?>')" class="form-control amount_only input-sm"/>
                            <label style="color: red;"><?=form_error('amount')?></label>
                        </div>
                        <div class="col-md-2">
                            <input type="submit" id='transfer_button' class="btn btn-block btn-primary btn-sm loading" value="<?=lang('cashier.10')?>" style="margin-top: 26px;"/>
                        </div>
                    </div>
                </form>

                <div class="panel panel-default">
                    <div class="panel-body table-responsive">
                        <div class="text-right">
                            <div class="form-inline">
                                <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
                                <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
                                <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
                                <input type="button" class="btn btn-primary btn-sm" id="btn-submit" value="<?=lang('lang.search')?>"/>
                            </div>
                        </div>
                        <hr/>
                        <table id="transaction-table" class="table table-striped table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th><?=lang('player.ut01')?></th>
                                    <th><?=lang('player.ut02')?></th>
                                    <th><?=lang('player.ut03')?></th>
                                    <th><?=lang('player.ut04')?></th>
                                    <th><?=lang('player.ut05')?></th>
                                    <th><?=lang('player.ut06')?></th>
                                    <th><?=lang('player.ut07')?></th>
                                    <th><?=lang('player.ut08')?></th>
                                    <th><?=lang('cms.promoCat')?></th>
                                    <th><?=lang('Changed Balance')?></th>
                                    <th><?=lang('player.ut10')?></th>
                                    <th><?=lang('player.ut11')?></th>
                                    <th><?=lang('player.ut12')?></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- end of row-->
</div>
<?php include( $this->utils->getIncludeView('cashier_js.php') ); ?>

<script type="text/javascript">
    function submitTransfer(){
        $('#transfer_button').prop('disabled',true);
    }

    // START MONEY TRANSFER IMPROVE UX ==========================================
    function requiredField( field, msg ){

        if (field.value == '') {
            field.setCustomValidity(msg);
        }
    }

    function transferFrom( value ){

        var transferTo = document.getElementById("transfer_to"),
            transferFrom = document.getElementById("transfer_from"),
            transferOptions = listSelectOptions( transferTo );

        transferFrom.setCustomValidity('');

        if( value == "" ){
            reset( transferTo );
            reset( transferFrom );

        }else if( value == 0 ){

            if( transferTo.value == 0 && transferTo.value != "" ) reset( transferTo );
            var len = transferOptions.length,
                ctr;

            for( ctr = 0; ctr < len; ctr++ ){
                if( transferOptions[ctr] != value ) continue;
                transferTo.options[ctr].classList.add('hide');
            }

        }else{

            if( transferTo.value != 0 || transferTo.value == "" ) reset( transferTo );
            var len = transferOptions.length,
                ctr;

            for( ctr = 0; ctr < len; ctr++ ){
                if( transferOptions[ctr] == 0 ) continue;
                transferTo.options[ctr].classList.add('hide');
            }

        }

    }

    function transferTo( value ){

        var transferTo = document.getElementById("transfer_to"),
            transferFrom = document.getElementById("transfer_from"),
            transferOptions = listSelectOptions( transferFrom );

        transferTo.setCustomValidity('');

        if( value == "" ){

            reset( transferFrom );
            reset( transferTo );

        }else if( value == 0 ){

            if( transferFrom.value != 0 && transferFrom.value != "" ) return false;
            reset( transferFrom );

            var len = transferOptions.length,
                ctr;

            for( ctr = 0; ctr < len; ctr++ ){
                if( transferOptions[ctr] != value ) continue;
                transferFrom.options[ctr].classList.add('hide');
            }

        }else{

            if( transferFrom.value != 0 || transferFrom.value == "" ) reset( transferFrom );

            var len = transferOptions.length,
                ctr;

            for( ctr = 0; ctr < len; ctr++ ){
                if( transferOptions[ctr] == 0 ) continue;
                transferFrom.options[ctr].classList.add('hide');
            }

        }

    }

    function listSelectOptions( element ){

        var list = [];
        var i;

        for (i = 0; i < element.length; i++) {
            list.push(element.options[i].value);
        }

        return list;

    }

    function resetAll(){

        var transferTo = document.getElementById("transfer_to"),
            transferFrom = document.getElementById("transfer_from");

        reset(transferFrom);
        reset(transferTo);

    }

    function reset( element ){

        for (i = 0; i < element.length; i++) {
            element.options[i].classList.remove('hide');
        }

        element.options[0].selected = true;
    }

    function requestPromo(promoCmsSettingId){
        if(confirm('<?php echo lang("confirm.request"); ?>')){
            //goto page
            window.location.href='<?=site_url("iframe_module/request_promo");?>/'+promoCmsSettingId;
        }
    }

    $(function() {
        var dataTable = $('#transaction-table').DataTable({
            pageLength: 5,
            lengthMenu: [ 5, 10, 25, 50, 100 ],
            searching: false,
            columnDefs: [
                { className: 'text-right', targets: [ 4,5,6,9 ] },
                { visible: false, targets: [ 2,3,10,11,12 ] }
            ],
            order: [[0, 'desc']],

            processing: true,
            serverSide: true,
            ajax: {
                url: '/api/transactionHistory/<?=$player['playerId']?>',
                type: 'post',
                data: function ( d ) {
                    d.extra_search = [
                        {
                            'name':'dateRangeValueStart',
                            'value':$('#dateRangeValueStart').val(),
                        },
                        {
                            'name':'dateRangeValueEnd',
                            'value':$('#dateRangeValueEnd').val(),
                        },
                        {
                            'name': 'disabled_transaction_types',
                            'value': '3' //FEE_FOR_PLAYER
                        }
                        // {
                        //     'name': 'disabled_transaction_types',
                        //     'value': '4' //FEE_FOR_OPERATOR
                        // }
                    ];
                },
            },
            <?php if ($currentLang == '2') {?>
                language: {
                    url: '<?=$this->utils->jsUrl('dataTables.chinese.json')?>',
                },
            <?php }
?>
        });

        $('#btn-submit').click( function() {
            dataTable.ajax.reload();
        });
    });

    // END MONEY TRANSFER IMPROVE UX ==========================================


</script>