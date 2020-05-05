<?php

$loggedPlayerId=$this->utils->getLoggedPlayerId();

$bigWallet=$this->utils->getBigWalletByPlayerId($loggedPlayerId);

$subwalletsBalance = array();

foreach ($bigWallet['sub'] as $apiId=>$subWallet) {
    $subwalletsBalance[$apiId]=$subWallet['total_nofrozen'];
}

?>

<script type="text/javascript">

if( typeof _smartbackend =='undefined'){
    _smartbackend={};
}

    <?php if($this->utils->isEnabledFeature('auto_refresh_balance_on_cashier')){ ?>
        $(function(){
            refreshBalance();
        });
    <?php }?>

    function refreshBalance(){
        $('.refreshBalanceButton').prop('disabled',true);
        //call refresh balance
        $.ajax({
            url: '<?=site_url("/async/player_query_all_balance");?>',
            type: 'GET',
            dataType: 'json',
            cache: false,
            xhrFields: {
                withCredentials: true
            }
        }).done(function(data){
            // utils.safelog(data);
            if(data){
                // frozen=data['frozen'];
                mainwallet=data['mainwallet'];
                subwallets=data['subwallets'];
                // $(data['subwallets']).each(function(idx,val){
                //     safelog(val);
                //     $('.wallet_'+val['gamePlatformId']).html(val['balance']);
                //     subwallets[val['gamePlatformId']]=val['balance'];
                // });

                totalBalance();
            }
        }).fail(function(){
            // alert("<?=lang('refresh.failed');?>");
        }).always(function(){
            $('.refreshBalanceButton').prop('disabled',false);
        });
    }

    var mainwallet=<?php echo $this->utils->formatCurrencyNumber($bigWallet['main']['total_nofrozen']); ?>;
    var subwallets=<?=json_encode($subwalletsBalance);?>;
    var frozen=<?php echo $this->utils->formatCurrencyNumber($bigWallet['main']['frozen']); ?>;

    function totalBalance(){
        var sumSubwallet=0;
        $(".mainwallet_value").html(mainwallet);
        $.each(subwallets, function(k,v){
            sumSubwallet=sumSubwallet+parseFloat(v);
            $('.wallet_'+k).html(v);
        });
        // safelog(mainwallet+sumSubwallet);
        // var total=parseFloat(mainwallet)+sumSubwallet+parseFloat(frozen);
        var total=parseFloat(mainwallet)+sumSubwallet;

        total = Math.round(total*100)/100;

        $("#playerTotalBalance, .playerTotalBalance").html(total.toFixed(2).replace(/./g, function(c, i, a) {
            return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
        }));
    }

    window['recalcTotalBalance']=function(jobResult, jobInfo, state){
        var systemId=jobInfo['system_id'];
        if(jobResult['balance']){
            subwallets[systemId]=jobResult['balance'];
        }
        totalBalance();
    }

_smartbackend.refreshBalance=refreshBalance;
_smartbackend.totalBalance=totalBalance;
_smartbackend.cancelAllPendingWithdrawals=cancelAllPendingWithdrawals;
_smartbackend.recalcTotalBalance=window['recalcTotalBalance'];
_smartbackend.mainwallet=mainwallet;
_smartbackend.subwallets=subwallets;
_smartbackend.frozen=frozen;

</script>
