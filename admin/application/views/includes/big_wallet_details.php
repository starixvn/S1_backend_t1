<?php
    $gameApiMap = $this->utils->getGameSystemMap();
    $enabled_move_all_to_real = isset($this->permissions) ? $this->permissions->checkPermissions('enabled_move_all_to_real') : false;
?>


<script type="text/javascript">
    <?php include __DIR__.'/base_pubutils_js.php';?>

    $(function(){
        numeral.defaultFormat('0.00');

        var gameApiMap=<?php echo json_encode($gameApiMap);?>;

        _pubutils.renderBigWallet=function(bigWallet){
            var content='<div class="table-responsive">'
                +'<table class="table table-striped table-bordered table-hover">'
                +'<tr><th class="text-center"><?php echo lang('Wallet');?></th>'
                +'<th class="text-center"><?php echo lang('Real Money');?></th>'
                +'<th class="text-center"><?php echo lang('Real Money For Bonus');?></th>'
                +'<th class="text-center"><?php echo lang('Winning').'('.lang('Real Money').')';?></th>'
                +'<th class="text-center"><?php echo lang('Bonus');?></th>'
                +'<th class="text-center"><?php echo lang('Winning').'('.lang('Bonus').')';?></th>'
                +'<th class="warning text-center"><?php echo lang('Pending');?></th>'
                +'<th class="success text-center"><?php echo lang('Withdrawable');?></th>'
                +'<th class="info text-center"><?php echo lang('Subtotal');?></th>'
                +'</tr>';

            //add main
            content+='<tr><td><?php echo lang('Main Wallet');?></td>'
                +'<td class="text-right">'+numeral(bigWallet['main']['real']).format()+'</td>'
                +'<td class="text-right">'+numeral(bigWallet['main']['real_for_bonus']).format()+'</td>'
                +'<td class="text-right">'+numeral(bigWallet['main']['win_real']).format()+'</td>'
                +'<td class="text-right">'+numeral(bigWallet['main']['bonus']).format()+'</td>'
                +'<td class="text-right">'+numeral(bigWallet['main']['win_bonus']).format()+'</td>'
                +'<td class="text-right warning">'+numeral(bigWallet['main']['frozen']).format()+'</td>'
                +'<td class="text-right success">'+numeral(bigWallet['main']['withdrawable']).format()+'</td>'
                +'<td class="text-right info">'+numeral(bigWallet['main']['total']).format()+'</td>'
                +'</tr>';

            //add sub
            $.each(bigWallet['sub'], function(subWalletId, subWallet){
                content+='<tr><td>'+gameApiMap[subWallet['id']]+'</td>'
                    +'<td class="text-right">'+numeral(subWallet['real']).format()+'</td>'
                    +'<td class="text-right">'+numeral(subWallet['real_for_bonus']).format()+'</td>'
                    +'<td class="text-right">'+numeral(subWallet['win_real']).format()+'</td>'
                    +'<td class="text-right">'+numeral(subWallet['bonus']).format()+'</td>'
                    +'<td class="text-right">'+numeral(subWallet['win_bonus']).format()+'</td>'
                    +'<td class="text-right warning">'+numeral(subWallet['frozen']).format()+'</td>'
                    +'<td class="text-right success">'+numeral(subWallet['withdrawable']).format()+'</td>'
                    +'<td class="text-right info">'+numeral(subWallet['total']).format()+'</td>'
                    +'</tr>';
            })

            content+='</table>';

            //add frozen, total withdrawable, total balance
            content+='<table class="table table-striped table-bordered table-hover">'
                +'<tr><th class="text-center"></th>'
                +'<th class="text-center"><?php echo lang('Real Money');?></th>'
                +'<th class="text-center"><?php echo lang('Real Money For Bonus');?></th>'
                +'<th class="text-center"><?php echo lang('Winning').'('.lang('Real Money').')';?></th>'
                +'<th class="text-center"><?php echo lang('Bonus');?></th>'
                +'<th class="text-center"><?php echo lang('Winning').'('.lang('Bonus').')';?></th>'
                +'<th class="warning text-center"><?php echo lang('Total Pending');?></th>'
                +'<th class="success text-center"><?php echo lang('Total Withdrawable');?></th>'
                +'<th class="info text-center"><?php echo lang('Total Balance');?></th>'
                +'</tr>'
                +'<tr><td class="text-center"><?php echo lang('Total');?></td>'
                +'<td class="text-right">'+numeral(bigWallet['total_real']).format()+'</td>'
                +'<td class="text-right">'+numeral(bigWallet['total_real_for_bonus']).format()+'</td>'
                +'<td class="text-right">'+numeral(bigWallet['total_win_real']).format()+'</td>'
                +'<td class="text-right">'+numeral(bigWallet['total_bonus']).format()+'</td>'
                +'<td class="text-right">'+numeral(bigWallet['total_win_bonus']).format()+'</td>'
                +'<td class="text-right warning">'+numeral(bigWallet['total_frozen']).format()+'</td>'
                +'<td class="text-right success">'+numeral(bigWallet['total_withdrawable']).format()+'</td>'
                +'<td class="text-right info">'+numeral(bigWallet['total']).format()+'</td>'
                +'</tr>';


            content+='</table>';

            <?php if($enabled_move_all_to_real){?>
                    content+='<div style="margin-bottom: 15px;"><button class="btn btn-wisppink btn-xs" onclick="_pubutils.move_all_to_real()">Move All To Real Wallet</button></div>';
                    content+='<div style="margin-bottom: 15px;"><button class="btn btn-wisppink btn-xs" onclick="_pubutils.move_pending_to_real()">Move Pending To Real Wallet</button></div>';
            <?php }?>

            content+='</div>';

            BootstrapDialog.show({
                title: '<?php echo lang("Wallet Details");?>',
                size: BootstrapDialog.SIZE_WIDE,
                message: content,
                buttons: [{
                    label: '<?php echo lang("Close");?>',
                    cssClass: 'btn btn-linkwater',
                    action: function(dialogItself){
                        dialogItself.close();
                    }
                }]
            });
        };

        _pubutils.move_all_to_real=function(){
            if(confirm('<?php echo lang("Do you want move all to real wallet?"); ?>')){
                window.location.href="<?php echo site_url('player_management/move_all_to_real');?>/"+_pubutils.playerId;
            }
        };

        _pubutils.move_pending_to_real=function(){
            if(confirm('<?php echo lang("Do you want move pending amount to real on main wallet? Caution: do not do this if there is pending withdraw request for this player."); ?>')){
                window.location.href="<?php echo site_url('player_management/move_pending_to_real');?>/"+_pubutils.playerId;
            }
        };

        _pubutils.renderDiffBalanceDetails=function(diffBigWallet){
            var beforeBal=diffBigWallet['before'];
            var afterBal=diffBigWallet['after'];

            var content='<div class="table-responsive">'
                +'<table class="table table-striped table-bordered table-hover">'
                +'<tr><th class="text-center"><?php echo lang('Wallet');?></th>'
                +'<th><?php echo lang('Balance');?></th>'
                +'<th class="warning text-center"><?php echo lang('Pending');?></th>'
                +'</tr>';

            //add main
            content+='<tr><td rowspan="2" style="vertical-align: middle;"><?php echo lang('Main Wallet');?></td>'
                +'<td class="text-right '+(beforeBal['main_wallet']!=afterBal['main_wallet'] ? 'danger' : 'warning')+'">'+numeral(beforeBal['main_wallet']).format()+'</td>'
                +'<td class="text-right '+(beforeBal['frozen']!=afterBal['frozen'] ? 'danger' : 'warning')+'">'+numeral(beforeBal['frozen']).format()+'</td>'
                +'</tr>';

            content+='<tr>'
                +'<td class="text-right '+(beforeBal['main_wallet']!=afterBal['main_wallet'] ? 'danger' : '')+'">'+numeral(afterBal['main_wallet']).format()+'</td>'
                +'<td class="text-right '+(beforeBal['frozen']!=afterBal['frozen'] ? 'danger' : 'warning')+'">'+numeral(afterBal['frozen']).format()+'</td>'
                +'</tr>';

            $.each(beforeBal['sub_wallet'], function(idx, subWallet){
                var beforeSubWallet=subWallet;
                var afterSubWallet=afterBal['sub_wallet'][idx];

                content+='<tr><td rowspan="2" style="vertical-align: middle;">'+subWallet['game']+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['totalBalanceAmount']!=afterSubWallet['totalBalanceAmount'] ? 'danger' : '')+'">'+numeral(beforeSubWallet['totalBalanceAmount']).format()+'</td>'
                    +'<td class="text-right warning">--</td>'
                    +'</tr>';

                content+='<tr>'
                    +'<td class="text-right '+(beforeSubWallet['totalBalanceAmount']!=afterSubWallet['totalBalanceAmount'] ? 'danger' : '')+'">'+numeral(afterSubWallet['totalBalanceAmount']).format()+'</td>'
                    +'<td class="text-right warning">--</td>'
                    +'</tr>';

            });

            content+='</table>';
            //add frozen, total withdrawable, total balance
            content+='<table class="table table-striped table-bordered table-hover">'
                +'<tr><th></th>'
                +'<th class="text-center"><?php echo lang('Total Balance');?></th>'
                +'<th class="warning text-center"><?php echo lang('Pending');?></th>'
                +'</tr>'
                +'<tr><td rowspan="2" style="vertical-align: middle;"><?php echo lang('Total');?></td>'
                +'<td class="text-right '+(beforeBal['total_balance']!=afterBal['total_balance'] ? 'danger' : '')+'">'+numeral(beforeBal['total_balance']).format()+'</td>'
                +'<td class="text-right '+(beforeBal['frozen']!=afterBal['frozen'] ? 'danger' : 'warning')+'">'+numeral(beforeBal['frozen']).format()+'</td>'
                +'</tr>'
                +'<tr><td class="text-right '+(beforeBal['total_balance']!=afterBal['total_balance'] ? 'danger' : '')+'">'+numeral(afterBal['total_balance']).format()+'</td>'
                +'<td class="text-right '+(beforeBal['frozen']!=afterBal['frozen'] ? 'danger' : 'warning')+'">'+numeral(afterBal['frozen']).format()+'</td>'
                +'</tr>';

            content+='</table>';

            content+='<div class="text-danger"><?php echo lang("Amount"); ?>: '+diffBigWallet['amount']+'</div>';

            content+='</div>';

            BootstrapDialog.show({
                title: '<?php echo lang("Transaction Details");?>',
                message: content,
                buttons: [{
                    label: '<?php echo lang("Close");?>',
                    action: function(dialogItself){
                        dialogItself.close();
                    }
                }]
            });
        };

        _pubutils.renderDiffBigWallet=function(diffBigWallet){
            if(typeof diffBigWallet['before']['big_wallet']=='undefined'){
                return _pubutils.renderDiffBalanceDetails(diffBigWallet);
            }

            var beforeBigWallet=diffBigWallet['before']['big_wallet'];
            var afterBigWallet=diffBigWallet['after']['big_wallet'];
            var content='<div class="table-responsive">'
                +'<table class="table table-striped table-bordered table-hover">'
                +'<tr><th class="text-center"><?php echo lang('Wallet');?></th>'
                +'<th class="text-center"><?php echo lang('Real Money');?></th>'
                +'<th class="text-center"><?php echo lang('Real Money For Bonus');?></th>'
                +'<th class="text-center"><?php echo lang('Winning').'('.lang('Real Money').')';?></th>'
                +'<th class="text-center"><?php echo lang('Bonus');?></th>'
                +'<th class="text-center"><?php echo lang('Winning').'('.lang('Bonus').')';?></th>'
                +'<th class="warning text-center"><?php echo lang('Pending');?></th>'
                +'<th class="success text-center"><?php echo lang('Withdrawable');?></th>'
                +'<th class="info text-center"><?php echo lang('Subtotal');?></th>'
                +'</tr>';

            //add main
            content+='<tr><td rowspan="2" style="vertical-align: middle;"><?php echo lang('Main Wallet');?></td>'
                +'<td class="text-right '+(beforeBigWallet['main']['real']!=afterBigWallet['main']['real'] ? 'danger' : '')+'">'+numeral(beforeBigWallet['main']['real']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['main']['real_for_bonus']!=afterBigWallet['main']['real_for_bonus'] ? 'danger' : '')+'">'+numeral(beforeBigWallet['main']['real_for_bonus']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['main']['win_real']!=afterBigWallet['main']['win_real'] ? 'danger' : '')+'">'+numeral(beforeBigWallet['main']['win_real']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['main']['bonus']!=afterBigWallet['main']['bonus'] ? 'danger' : '')+'">'+numeral(beforeBigWallet['main']['bonus']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['main']['win_bonus']!=afterBigWallet['main']['win_bonus'] ? 'danger' : '')+'">'+numeral(beforeBigWallet['main']['win_bonus']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['main']['frozen']!=afterBigWallet['main']['frozen'] ? 'danger' : 'warning')+'">'+numeral(beforeBigWallet['main']['frozen']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['main']['withdrawable']!=afterBigWallet['main']['withdrawable'] ? 'danger' : 'success')+'">'+numeral(beforeBigWallet['main']['withdrawable']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['main']['total']!=afterBigWallet['main']['total'] ? 'danger' : 'info')+'">'+numeral(beforeBigWallet['main']['total']).format()+'</td>'
                +'</tr>';

            content+='<tr>'
                +'<td class="text-right '+(beforeBigWallet['main']['real']!=afterBigWallet['main']['real'] ? 'danger' : '')+'">'+numeral(afterBigWallet['main']['real']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['main']['real_for_bonus']!=afterBigWallet['main']['real_for_bonus'] ? 'danger' : '')+'">'+numeral(afterBigWallet['main']['real_for_bonus']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['main']['win_real']!=afterBigWallet['main']['win_real'] ? 'danger' : '')+'">'+numeral(afterBigWallet['main']['win_real']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['main']['bonus']!=afterBigWallet['main']['bonus'] ? 'danger' : '')+'">'+numeral(afterBigWallet['main']['bonus']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['main']['win_bonus']!=afterBigWallet['main']['win_bonus'] ? 'danger' : '')+'">'+numeral(afterBigWallet['main']['win_bonus']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['main']['frozen']!=afterBigWallet['main']['frozen'] ? 'danger' : 'warning')+'">'+numeral(afterBigWallet['main']['frozen']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['main']['withdrawable']!=afterBigWallet['main']['withdrawable'] ? 'danger' : 'success')+'">'+numeral(afterBigWallet['main']['withdrawable']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['main']['total']!=afterBigWallet['main']['total'] ? 'danger' : 'info')+'">'+numeral(afterBigWallet['main']['total']).format()+'</td>'
                +'</tr>';

            //add sub
            $.each(beforeBigWallet['sub'], function(subWalletId, subWallet){
                var beforeSubWallet=subWallet;
                var afterSubWallet=afterBigWallet['sub'][subWalletId];
                content+='<tr><td rowspan="2" style="vertical-align: middle;">'+gameApiMap[subWallet['id']]+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['real']!=afterSubWallet['real'] ? 'danger' : '')+'">'+numeral(beforeSubWallet['real']).format()+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['real_for_bonus']!=afterSubWallet['real_for_bonus'] ? 'danger' : '')+'">'+numeral(beforeSubWallet['real_for_bonus']).format()+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['win_real']!=afterSubWallet['win_real'] ? 'danger' : '')+'">'+numeral(beforeSubWallet['win_real']).format()+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['bonus']!=afterSubWallet['bonus'] ? 'danger' : '')+'">'+numeral(beforeSubWallet['bonus']).format()+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['win_bonus']!=afterSubWallet['win_bonus'] ? 'danger' : '')+'">'+numeral(beforeSubWallet['win_bonus']).format()+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['frozen']!=afterSubWallet['frozen'] ? 'danger' : 'warning')+'">'+numeral(beforeSubWallet['frozen']).format()+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['withdrawable']!=afterSubWallet['withdrawable'] ? 'danger' : 'success')+'">'+numeral(beforeSubWallet['withdrawable']).format()+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['total']!=afterSubWallet['total'] ? 'danger' : 'info')+'">'+numeral(beforeSubWallet['total']).format()+'</td>'
                    +'</tr>';

                content+='<tr>'
                    +'<td class="text-right '+(beforeSubWallet['real']!=afterSubWallet['real'] ? 'danger' : '')+'">'+numeral(afterSubWallet['real']).format()+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['real_for_bonus']!=afterSubWallet['real_for_bonus'] ? 'danger' : '')+'">'+numeral(afterSubWallet['real_for_bonus']).format()+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['win_real']!=afterSubWallet['win_real'] ? 'danger' : '')+'">'+numeral(afterSubWallet['win_real']).format()+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['bonus']!=afterSubWallet['bonus'] ? 'danger' : '')+'">'+numeral(afterSubWallet['bonus']).format()+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['win_bonus']!=afterSubWallet['win_bonus'] ? 'danger' : '')+'">'+numeral(afterSubWallet['win_bonus']).format()+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['frozen']!=afterSubWallet['frozen'] ? 'danger' : 'warning')+'">'+numeral(afterSubWallet['frozen']).format()+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['withdrawable']!=afterSubWallet['withdrawable'] ? 'danger' : 'success')+'">'+numeral(afterSubWallet['withdrawable']).format()+'</td>'
                    +'<td class="text-right '+(beforeSubWallet['total']!=afterSubWallet['total'] ? 'danger' : 'info')+'">'+numeral(afterSubWallet['total']).format()+'</td>'
                    +'</tr>';
            });

            content+='</table>';

            //add frozen, total withdrawable, total balance
            content+='<table class="table table-striped table-bordered table-hover">'
                +'<tr><th></th>'
                +'<th class="text-center"><?php echo lang('Real Money');?></th>'
                +'<th class="text-center"><?php echo lang('Real Money For Bonus');?></th>'
                +'<th class="text-center"><?php echo lang('Winning').'('.lang('Real Money').')';?></th>'
                +'<th class="text-center"><?php echo lang('Bonus');?></th>'
                +'<th class="text-center"><?php echo lang('Winning').'('.lang('Bonus').')';?></th>'
                +'<th class="warning text-center"><?php echo lang('Total Pending');?></th>'
                +'<th class="success text-center"><?php echo lang('Total Withdrawable');?></th>'
                +'<th class="info text-center"><?php echo lang('Total Balance');?></th>'
                +'</tr>'
                +'<tr><td rowspan="2" style="vertical-align: middle;"><?php echo lang('Total');?></td>'
                +'<td class="text-right '+(beforeBigWallet['total_real']!=afterBigWallet['total_real'] ? 'danger' : '')+'">'+numeral(beforeBigWallet['total_real']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['total_real_for_bonus']!=afterBigWallet['total_real_for_bonus'] ? 'danger' : '')+'">'+numeral(beforeBigWallet['total_real_for_bonus']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['total_win_real']!=afterBigWallet['total_win_real'] ? 'danger' : '')+'">'+numeral(beforeBigWallet['total_win_real']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['total_bonus']!=afterBigWallet['total_bonus'] ? 'danger' : '')+'">'+numeral(beforeBigWallet['total_bonus']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['total_win_bonus']!=afterBigWallet['total_win_bonus'] ? 'danger' : '')+'">'+numeral(beforeBigWallet['total_win_bonus']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['total_frozen']!=afterBigWallet['total_frozen'] ? 'danger' : 'warning')+'">'+numeral(beforeBigWallet['total_frozen']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['total_withdrawable']!=afterBigWallet['total_withdrawable'] ? 'danger' : 'success')+'">'+numeral(beforeBigWallet['total_withdrawable']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['total']!=afterBigWallet['total'] ? 'danger' : 'info')+'">'+numeral(beforeBigWallet['total']).format()+'</td>'
                +'</tr>'
                +'<tr><td class="text-right '+(beforeBigWallet['total_real']!=afterBigWallet['total_real'] ? 'danger' : '')+'">'+numeral(afterBigWallet['total_real']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['total_real_for_bonus']!=afterBigWallet['total_real_for_bonus'] ? 'danger' : '')+'">'+numeral(afterBigWallet['total_real_for_bonus']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['total_win_real']!=afterBigWallet['total_win_real'] ? 'danger' : '')+'">'+numeral(afterBigWallet['total_win_real']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['total_bonus']!=afterBigWallet['total_bonus'] ? 'danger' : '')+'">'+numeral(afterBigWallet['total_bonus']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['total_win_bonus']!=afterBigWallet['total_win_bonus'] ? 'danger' : '')+'">'+numeral(afterBigWallet['total_win_bonus']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['total_frozen']!=afterBigWallet['total_frozen'] ? 'danger' : 'warning')+'">'+numeral(afterBigWallet['total_frozen']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['total_withdrawable']!=afterBigWallet['total_withdrawable'] ? 'danger' : 'success')+'">'+numeral(afterBigWallet['total_withdrawable']).format()+'</td>'
                +'<td class="text-right '+(beforeBigWallet['total']!=afterBigWallet['total'] ? 'danger' : 'info')+'">'+numeral(afterBigWallet['total']).format()+'</td>'
                +'</tr>';

            content+='</table>';

            content+='<div class="text-danger"><?php echo lang("Amount"); ?>: '+diffBigWallet['amount']+'</div>';

            content+='</div>';

            BootstrapDialog.show({
                title: '<?php echo lang("Transaction Details");?>',
                size: BootstrapDialog.SIZE_WIDE,
                message: content,
                buttons: [{
                    label: '<?php echo lang("Close");?>',
                    action: function(dialogItself){
                        dialogItself.close();
                    }
                }]
            });
        };

        _pubutils.showDiffBigWallet=function(btn){
            var sel=$(btn).data('diffbigwallet');
            var json=$(sel).text();
            if(json!=''){
                var diffBigWallet=JSON.parse(json);
                //try build
                _pubutils.renderDiffBigWallet(diffBigWallet);
            }
        };

        _pubutils.attchBigWalletButton=function(){
            //default attach detail button
            $('.show_bigwallet_details').on('click',function(){

                var fetchtype=$(this).data('fetchtype');
                if(fetchtype=='local'){
                    var sel=$(this).data('bigwallet');
                    var bigWallet=JSON.parse($(sel).text());
                    //try build
                    _pubutils.renderBigWallet(bigWallet);
                }else{
                    //remote
                    var playerid=$(this).data('playerid');
                    if(playerid==''){
                        alert('<?php echo lang("Invalid player");?>');
                        return;
                    }

                    _pubutils.playerId=playerid;

                    var url="<?php echo site_url('player_management/big_wallet_details');?>/"+playerid;

                    $.getJSON(url, function(data){
                        if(data['success']){
                            _pubutils.renderBigWallet(data['bigWallet']);
                        }else{
                            alert(data['message']);
                        }

                    }).fail(function(){
                        alert('<?php echo lang("Sorry, load wallet info failed");?>');
                    });
                }
            });

            $('.diff_bigwallet').on('click',function(){
                var sel=$(this).data('diffbigwallet');
                var diffBigWallet=JSON.parse($(sel).text());
                //try build
                _pubutils.renderDiffBigWallet(diffBigWallet);
            });
        };

        _pubutils.attchBigWalletButton();
    });
</script>