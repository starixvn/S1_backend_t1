var variables={
    debugLog: true
};

var utils={
    safelog:function(msg){
        // check exists console.log
        if(variables.debugLog && typeof(console)!='undefined' && console.log){
            console.log(msg);
        }
    }
};

// Part1 - Below are the scripts originally hard-coded in template
// Part1 <editor-fold>{{{

function loadPlayerVipGroupDetails(){
    $.ajax({
        url: '/api/getPlayerVipGroupDetails',
        type: 'GET',
        success: function(data){
            //utils.safelog(data);
            if (!data) {
                return;
            }

            var nextLvlPercentageDeposit = false;
            var nextLvlPercentageBet = false;
            var nextLvlPercentage = 0;
            var formula;
            var lang_lvl_name;
            var lang_group_name;
            var currentLang = _export_sbe_t1t.variables.currentLang;
            var _divide = 0;
            var isMaxLevel = (data.current_vip_level.maxLevel == data.current_vip_level.vip_group_lvl_number) ? true : false;

            if (!isMaxLevel) {

                if(data.current_vip_level.upgrade_deposit_amt_req == 0){
                    $('#currentVipGroupDepAmtSec').hide();
                }else{
                    var percentDeposit = data.current_vip_level.next_level_percentage_deposit;
                    nextLvlPercentageDeposit = percentDeposit ? percentDeposit : 0
                }

                if(data.current_vip_level.upgrade_bet_amt_req == 0){
                    $('#currentVipGroupBettingAmtSec').hide();
                }else{
                    var percentBet = data.current_vip_level.next_level_percentage_bet;
                    nextLvlPercentageBet = percentBet ? percentBet : 0
                }

                if (typeof nextLvlPercentageDeposit !== 'boolean') {
                    _divide++;
                    nextLvlPercentage += nextLvlPercentageDeposit;
                }

                if (typeof nextLvlPercentageBet !== 'boolean') {
                    _divide++;
                    nextLvlPercentage += nextLvlPercentageBet;
                }

                if (_divide > 0) {
                    nextLvlPercentage /= _divide;
                }

            } else {
                nextLvlPercentage = 100;
            }

            //overview info
            $('#vip-icon').attr("src",data.current_vip_level.vip_group_lvl_badge).width(30).height(30);

            if (data.current_vip_level.vip_lvl_name.toLowerCase().indexOf("_json:") >= 0){
                var langLvlConvert = jQuery.parseJSON(data.current_vip_level.vip_lvl_name.substring(6));
                var lang_lvl_name = langLvlConvert[currentLang];
            } else {
                var lang_lvl_name = data.current_vip_level.vip_lvl_name;
            }
            $('#vipLvlName').html(lang_lvl_name);
            if (data.current_vip_level.vip_group_name.toLowerCase().indexOf("_json:") >= 0){
                var langGroupConvert = jQuery.parseJSON(data.current_vip_level.vip_group_name.substring(6));
                var lang_group_name = langGroupConvert[currentLang];
            } else {
                var lang_group_name =data.current_vip_level.vip_group_name;
            }
            $('#vipGroupLvl').html(lang_group_name+' - '+lang_lvl_name);


            // progress info
            $('#vipGroupNextLvlPercentage').css("width", nextLvlPercentage+"%");
            $('#vipGroupNextLvlPercentageTxt').text(nextLvlPercentage+"%");
            if (isMaxLevel) {
                $(".vip_detail").hide();
                $(".vip_max_detail").show();
            } else {
                $('#currentVipGroupDepAmtTxt').text(data.current_vip_level.current_lvl_deposit_amt);
                $('#currentVipGroupBettingAmtTxt').text(data.current_vip_level.current_lvl_bet_amt);
                $('#vipUpradeDepAmtReqTxt').text(data.current_vip_level.upgrade_deposit_amt_req);
                $('#vipUpradeBetAmtReqTxt').text(data.current_vip_level.upgrade_bet_amt_req);
            }

            if(data.current_vip_level.player_upgrade_progress.length) {
                var playerProgress = data.current_vip_level.player_upgrade_progress;
                utils.safelog(playerProgress);
                var html = '';
                for(var i in playerProgress) {
                    html += '<div class="col-xs-3 vip-progress-circle-content">';
                    html +=   '<div class="circle">';
                    html +=     '<div class="pie_left"><div class="left"></div></div>';
                    html +=     '<div class="pie_right"> <div class="right"></div></div>';
                    html +=     '<div class="mask"><span class="percent">'+playerProgress[i].percentage_rate+'</span>%</div>';
                    html +=   '</div>';
                    html +=   '<p>'+playerProgress[i].name+'</p>';
                    html += '</div>';
                }
                $('#player-upgrade-progress').html(html);
            }

            if(data.current_vip_level.formula) {
                var tooltipInfo = lang('Upgrade Condition') + ' : ' + data.current_vip_level.formula;
                //$('.formula').html(data.current_vip_level.formula + ' <i class="fa fa-info-circle" data-toggle="tooltip" title="'+lang('Upgrade Condition')+'" aria-hidden="true"></i>');
                $('.formula').html('<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="'+tooltipInfo+'" aria-hidden="true"></i>');

                var upgrade_notice = data.current_vip_level.schedule;
                $('.upgrade_notice').html('<i class="fa fa-calendar" data-toggle="tooltip" data-placement="right" title="'+upgrade_notice+'" aria-hidden="true"></i>');
                $('[data-toggle="tooltip"]').tooltip();
            }

            $('#vipLvlPercentageTxtInSidebar').text(nextLvlPercentage+"%");

            $('#vipLevelNameTxt').text(data.current_vip_level.vip_group_lvl_name);
            $('#lastLogintimeTxt').text(data.others.player_last_login_time);
            $('#lastLogintimezoneTxt').text(data.others.player_last_login_timezone + ' ');
            $('.overview .total_bet_amount_result').text(data.others.player_today_total_betting_amount);
            $('#promoBonusAmtTxt').text(data.others.player_total_bonus);

            // $('#birthdayBonusDaysLeftTxt').text("0");
            $('#birthdayBonusDaysLeftTxt').text(data.others.player_days_left_before_bday_bonus);
            $('#overviewPageTotalCashbackAmtReceivedTxt').text(data.others.player_available_cashback_amount);

            // $('#availablePointsBalanceTxt').text(data.others.player_available_points);
            // $('#availablePointsOverviewTxt').text(data.others.player_available_points);
            // $('#totalPointsOverviewTxt').text(data.others.player_total_points);

            //player profile pic
            //$('#playerCenterPlayerProfilePic').attr("src",data.others.player_profile_pic);

            if(data.current_vip_level.bonus_mode_birthday>0 && data.others.player_birthdate_exists){
            }else{
                // $('#birthdayBonusDaysLeftTxt').text("0");
                $('#vipBirthdayBonusSec').hide();
            }

            //vip rewards page
            $('#vipGroupPageBirthdayBonusAmtTxt').text(data.current_vip_level.vip_group_lvl_bday_bonus_amt);
            $('#vipGroupPageVipGroupNameTxt').text(lang_group_name);
            $('#vipGroupPageVipGroupLvlNameTxt').text(lang_lvl_name);
            $('#vipGroupPagePlayerTotalCashbackAmtTxt').text(data.others.player_total_cashback_amount_received);
            $('#vipGroupPageAvailableCashbackAmtTxt').text(data.others.player_available_cashback_amount);

            // console.log(data.others);
            if(data.current_vip_level.bonus_mode_birthday >0 ){
                // console.log("h0");
                if(data.others.player_days_left_before_bday_bonus){
                    // console.log("h1");
                    $('#vipBirthdayBonusSec').show();
                    $('#daysLeftTxt').text(data.others.player_days_left_before_bday_bonus);
                }else{
                    // console.log("h2");
                    if(data.others.player_days_left_before_bday_bonus == "0"){
                        // console.log("h3");
                        $('#vipBirthdayBonusSec').show();
                        $('#daysLeftTxt').text(data.others.player_days_left_before_bday_bonus);
                    }else{
                        // console.log("h4");
                        $('#vipBirthdayBonusSec').hide();
                        $('#daysLeftTxt').text("0");
                    }
                }
            } else {
                $('#vipBirthdayBonusSec').hide();
                $('#bdayReleaseNow').val("false");
            }

            if(!data.others.player_birthdate_exists || data.others.player_birthdate_exists == 'false'){
                $('#vipBirthdayBonusSec').hide();
            }
        }
    });
}

function changeLanguage(language) {
    $.getJSON('/async/set_language/' + language, function(data) {
        if (data.status == 'success') {
            location.reload();
        }
    })
}

function load_alert_message(result, message, callback) {
    var is_callback = 0;
    $("#alert_message").empty();
    if(result.length != 0 && message.length != 0) {
        $("#alert_message").append($('<div />', {'class' : 'player-alert-container'}).append($('<div />', {'class' : 'alert player-alert  alert-'+result, 'role' : 'alert'}).append(
            $('<button />', {'type' : 'button' , 'class' : 'close close-alert-msg' , 'data-dismiss' : 'alert' , 'aria-label' : 'Close'}).append($('<span />', {'aria-hidden' : 'true' , 'html' : 'x'})),
            $('<p />', {'html' : message})
        )));
        setTimeout(function(){
            $("#alert_message").empty();
            if (typeof callback == 'function' && is_callback++) callback();
        }, 7000);
        $(".close-alert-msg").click(function(e) {
            $("#alert_message").empty();
            if(typeof callback == 'function' && is_callback++) callback();
        });
    }
}

function smButtonLoadStart(button) {
    button.html('<i class="fa fa-spinner fa-spin fa-1x fa-fw">');
}

function buttonLoadEnd(button,text) {
    button.text(text);
}

// }}}}</editor-fold>

// Part 3 - Player Center - Show tab by url hashtag
// <editor-fold>{{{
function change_url_hash_on_menu_click(e) {
    var url = $(this).find('a').attr('href');
    var newhash = url.split('#')[1];
    window.location.hash = newhash;
}

function show_tab_by_url_hash(){
    // Remove leading '#'
    var hashtag_str = window.location.hash.substr(1);
    if(hashtag_str.length <= 0){
        return;
    }

    try{
        $('.main-menu-nav li.' + hashtag_str + ' a').tab('show');
    }catch(e){

    }

    $('body').trigger($.Event('t1t.member-center.load'));
}

$(function () {
    $('.main-menu-nav li a').removeAttr('data-toggle');

    $('ul.main-menu-nav li').on('click', change_url_hash_on_menu_click);

    show_tab_by_url_hash();
    $(window).on('hashchange', show_tab_by_url_hash);

    $('.dashboar-container').removeClass('hidden');
});

// }}}</editor-fold>

$(document).ready(function() {
    loadPlayerVipGroupDetails();

    window.show_loading = function(){
        Loader.show();
    };

    window.stop_loading = function(){
        Loader.hide();
    };
});