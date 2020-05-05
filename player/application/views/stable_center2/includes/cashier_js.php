<?php
$loggedPlayerId = $this->utils->getLoggedPlayerId();

if(!empty($loggedPlayerId)){
    $bigWallet=$this->utils->getBigWalletByPlayerId($loggedPlayerId);

    $subwalletsBalance = array();

    $subWalletTotalBalance = 0;
    foreach ($bigWallet['sub'] as $apiId=>$subWallet) {
        $subwalletsBalance[$apiId] = $subWallet['total_nofrozen'];
        $subWalletTotalBalance += $subWallet['total_nofrozen'];
    }

    $walletData = [
        'total' => $bigWallet['main']['total'],
        'total_nofrozen' => $bigWallet['main']['total_nofrozen'],
        'total_sub' => $bigWallet['main']['total'] + $subWalletTotalBalance,
        'total_sub_nofrozen' => $bigWallet['main']['total_nofrozen'] + $subWalletTotalBalance,
        'frozen' => $bigWallet['main']['frozen'],
        'subwallets' => $subwalletsBalance
    ];
}else{
    $walletData = [
        'total' => 0,
        'total_nofrozen' => 0,
        'total_sub' => 0,
        'total_sub_nofrozen' => 0,
        'frozen' => 0,
        'subwallets' => []
    ];
}
?>
<script type="text/javascript">
(function(){
    var walletData = JSON.parse('<?=json_encode($walletData)?>');
    var has_login = parseInt('<?=$loggedPlayerId?>');
    var show_balance_container_select = '#playerTotalBalance, .playerTotalBalance';
    var on_refresh = false;

    var DEFAULT_TIMEOUT_TRANSFER = 30;
    var DEFAULT_TIMEOUT_BALREFRESH = 10;
    var DEFAULT_TIMEOUT_ERROR_RELOAD = 30;

    var mainwallet = <?php echo $this->utils->formatCurrencyNumber($walletData['total_nofrozen']); ?>;
    var frozen = <?php echo $this->utils->formatCurrencyNumber($walletData['frozen']); ?>;
    var subwallets = <?=json_encode($subwalletsBalance);?>;

    if( typeof _smartbackend =='undefined'){
        _smartbackend={};
    }

    function check_on_refresh(){
        if(!has_login){
            return false;
        }

        if(on_refresh){
            return false;
        }

        return true;
    }

    function refreshBalanceByTransfer(wallet_id, callback){
        if(!check_on_refresh()){
            return false;
        }

        on_refresh = true;

        var ajax_options = {};

        ajax_options['url'] = base_url + 'api/transferAllWallet';
        ajax_options['type'] = 'POST';
        ajax_options['data'] = {
            'transfer_to': wallet_id
        };
        ajax_options['timeout'] = DEFAULT_TIMEOUT_TRANSFER * 1000; // in milliseconds

        showProgressing();

        $.ajax(ajax_options).always(function(){
            on_refresh = false;
            refreshBalance(callback);
        });
    }

    function transferSubWalletToMain() {
        var buttonText = $('.transferAllToMainBtn').text();
        $('.transferAllToMainBtn').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw">');
        $('.transferAllToMainBtn').addClass("disabled");
        $.post('<?=site_url('api/retrieveAllSubWalletBalanceToMainBallance')?>', function(data){
            var status = data.status == 'success' ? 'success' : 'danger';
            refreshBalance(function(){
                if(status==='success'){
                    alert(data.msg);
                }else{
                    alert(data.msg);
                }
                if (data.failedTransferCount) {
                    $('.transferAllToMainBtn').removeClass("disabled");
                }
                $('.transferAllToMainBtn').text(buttonText);
            }, { dont_toggle_transferallback: 1, no_alert_when_complete: 1 });
        });
    }

    function isInActiveWindow(callback){

        if(typeof callback === 'undefined' || callback==='' || callback===null){
            callback=null;
        }

        var hidden = "hidden";

        // Standards:
        if (hidden in document)
            document.addEventListener("visibilitychange", onchange);
        else if ((hidden = "mozHidden") in document)
            document.addEventListener("mozvisibilitychange", onchange);
        else if ((hidden = "webkitHidden") in document)
            document.addEventListener("webkitvisibilitychange", onchange);
        else if ((hidden = "msHidden") in document)
            document.addEventListener("msvisibilitychange", onchange);
        // IE 9 and lower:
        else if ("onfocusin" in document)
            document.onfocusin = document.onfocusout = onchange;
        // All others:
        else
            window.onpageshow = window.onpagehide
            = window.onfocus = window.onblur = onchange;

        function onchange (evt) {
            var v = "visible", h = "hidden",
                evtMap = {
                  focus:v, focusin:v, pageshow:v, blur:h, focusout:h, pagehide:h
                };

            evt = evt || window.event;
            if (evt.type in evtMap){
                window._sbe_window_status=evtMap[evt.type];
            }else{
                window._sbe_window_status=this[hidden] ? h : v;
            }

            if(callback!==null){
                callback(window._sbe_window_status);
            }
        }

        // set the initial state (but only if browser supports the Page Visibility API)
        if( document[hidden] !== undefined ){
            onchange({type: document[hidden] ? "blur" : "focus"});
        }

    }

    function manuallyRefreshBalance(){
        refreshBalance(null, null, false);
    }

    function refreshBalance(callback, options, ignore_0){

        // isInActiveWindow();

        showBalance();

        $('.btn.refreshBalanceButton').addClass('disabled');
        if (!options || !options.dont_toggle_transferallback) {
            $('.btn.transferAllToMainBtn').addClass('disabled');
        }

        if(!check_on_refresh()){
            return false;
        }

        on_refresh = true;

        if(refreshBalance.hasOwnProperty('connect_to') && (typeof refreshBalance.connect_to == 'function')){
            refreshBalance.connect_to();
        }

        if(typeof ignore_0 === 'undefined' || ignore_0==='' || ignore_0===null){
            ignore_0='true';
            // console.log('undefined or empty ignore 0:'+ignore_0);
        }else{
            // console.log('ignore 0:'+ignore_0);
            ignore_0=ignore_0 ? 'true' : 'false';
        }

        if(typeof callback === 'undefined' || callback==='' || callback===null){
            callback=null;
        }

        // showProgressing();
        // var walletList = [];
        // for (var i in walletData.subwallets) {
        //     walletList.push(i);
        // }
        if(_sbe_window_status=='visible'){
            $.ajax({
                url: '<?=site_url('/async/available_subwallet_list')?>/false/'+ignore_0,
                type: 'GET',
                dataType: 'json',
                cache: false,
                success: function(data) {
                    // utils.safelog(data);
                    if(data['success']){
                        if(data['wallets'].length>0){
                            var walletList=data['wallets'];
                            getSubWalletData(walletList, callback, options);
                            // self.getSubWalletData(walletList, callback);
                        }else{
                            //refresh main wallet
                            totalBalance(data);
                            showBalance();
                            if (callback !== null){
                                callback();
                            }
                        }
                    }
                }
            });
        }else{
            // utils.safelog('not active: '+_sbe_window_status);
            if (callback !== null){
                callback();
            }
        }
    }

    function getSubWalletData(walletList, callback, options){
        var game_platform_id = walletList[0];
        $.ajax({
            url: '<?=site_url("/async/player_query_all_balance");?>' + '/' + game_platform_id,
            type: 'GET',
            dataType: 'json',
            cache: false,
            xhrFields: {
                withCredentials: true
            },
            success: function(subwallet) {
                walletList.shift();
                totalBalance(subwallet);
                if (walletList.length > 0) {
                    getSubWalletData(walletList, callback, options);
                } else {
                    on_refresh = false;
                    showBalance();
                    if (callback !== null) {
                        setTimeout(function() {
                            callback();
                        }, 5000);
                    }
                    setTimeout(function() {
                        $('.btn.refreshBalanceButton').removeClass('disabled');
                        if (!options || !options.no_alert_when_complete) {
                            alert('<?= lang('refresh complete') ?>');
                        }
                        if (!options || !options.dont_toggle_transferallback) {
                            $('.btn.transferAllToMainBtn').removeClass('disabled');
                        }
                    }, 500, options);
                }
            }
        });
    }

    function totalBalance(jsonData){
        if(!jsonData.hasOwnProperty('mainwallet') || !jsonData.hasOwnProperty('frozen') || !jsonData.hasOwnProperty('subwallets')){
            return false;
        }

        walletData = {
            "frozen": 0,
            "total_nofrozen": 0,
            "total": 0,
            "total_sub_nofrozen": 0,
            "total_sub": 0,
            "subwallets": []
        };

        var subWalletTotal = 0;
        $.each(jsonData.subwallets, function(index, value) {
            subWalletTotal += parseFloat(value);
        });

        walletData.frozen = parseFloat(jsonData.frozen);
        walletData.total_nofrozen = parseFloat(jsonData.mainwallet);
        walletData.total = walletData.total_nofrozen + walletData.frozen;
        walletData.total_sub_nofrozen = walletData.total_nofrozen + subWalletTotal;
        walletData.total_sub = walletData.total_nofrozen + walletData.frozen + subWalletTotal;
        walletData.subwallets = jsonData.subwallets;

        mainwallet = jsonData['mainwallet'];
        subwallets = jsonData['subwallets'];
    }

    function showProgressing(){
        var default_img = "/resources/images/ajax-loader-big2.gif";

        var container = showBalanceContainer();

        if(container.data('loading-img') && container.data('loading-img').length){
            default_img = container.data('loading-img');
        }

        container.html('<img class="loading" src="' + default_img + '" width="100%" height="100%">');

        return container;
    }

    function showBalanceContainer(container_select){
        var container = false;
        if (!container_select){
            container = $('#playerTotalBalance, .playerTotalBalance');
        }else{
            container = $(container_select);
            if ($(container_select).length <= 0) {
                container = $('#playerTotalBalance, .playerTotalBalance');
            }
        }

        if(container.data('show-type').length <= 0){
            container.data('show-type', 'total_sub');
        }

        return container;
    }

    function showBalance() {
        if(!check_on_refresh()){
            return false;
        }

        var container, totalbal;

        container = showBalanceContainer(show_balance_container_select);
        for (var i = 0; i < container.length; i++) {
            switch ($(container[i]).data('show-type')) {
                case 'total_sub_nofrozen':
                    totalbal = walletData.total_sub_nofrozen;
                break;
                case 'total_sub':
                    totalbal = walletData.total_sub;
                break;
                case 'total_nofrozen':
                    totalbal = walletData.total_nofrozen;
                break;
                case 'total':
                    totalbal = walletData.total;
                break;
                case 'total_subwallet':
                    totalbal = 0;
                    $.each(walletData.subwallets,function(index, value) {
                        totalbal += parseFloat(value);
                    });
                break;
                default:
                    totalbal = walletData.total_sub;
                break;
            }
            $(container[i]).html(totalbal.toFixed(2).replace(/./g, function (c, i, a) {
                return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
            }));
        }
    }

    window['recalcTotalBalance']=function(jobResult, jobInfo, state){
        var systemId = jobInfo['system_id'];
        if(jobResult['balance']){
            subwallets[systemId] = jobResult['balance'];
        }
        showBalance();
    }

    _smartbackend.refreshBalance = refreshBalance;
    _smartbackend.totalBalance = function(){
        showBalance();
    };
    _smartbackend.transferSubWalletToMain = transferSubWalletToMain;
    _smartbackend.cancelAllPendingWithdrawals = cancelAllPendingWithdrawals;
    _smartbackend.recalcTotalBalance = window['recalcTotalBalance'];
    _smartbackend.mainwallet = mainwallet;
    _smartbackend.subwallets = subwallets;
    _smartbackend.frozen = frozen;

    window.refreshBalanceByTransfer = refreshBalanceByTransfer;
    window.transferSubWalletToMain = transferSubWalletToMain;
    window.refreshBalance = refreshBalance;
    window.showBalanceContainer = showBalanceContainer;
    window.showBalance = showBalance;
    window.isInActiveWindow=isInActiveWindow;
})();

$(function(){

    window._sbe_window_status='visible';

    var auto_refresh_cold_down_time_milliseconds=<?=$this->utils->getConfig('auto_refresh_cold_down_time_milliseconds')?>;

    var last_time=(new Date()).getTime();

    isInActiveWindow(function(_sbe_window_status){
        if(_sbe_window_status=='visible'){

//            utils.safelog('cashier actived, refresh balance');

            var current_time=(new Date()).getTime();
            if(current_time-last_time>auto_refresh_cold_down_time_milliseconds){
                refreshBalance(null, { no_alert_when_complete: 1 });
                last_time=(new Date()).getTime();
            }else{
//                utils.safelog('cashier active too fast, current_time:'+current_time+', last_time:'+last_time);
            }

        }
    });

<?php if($this->utils->isEnabledFeature('auto_refresh_balance_on_cashier')): ?>
        refreshBalance(null, { no_alert_when_complete: 1 });
<?php else: ?>
        showBalance();
<?php endif ?>
});

</script>
