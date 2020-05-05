//general
var base_url = "/";
var imgloader = "/resources/images/ajax-loader.gif";

function GetXmlHttpObject() {
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        return new XMLHttpRequest();
    }
    if (window.ActiveXObject) {
        // code for IE6, IE5
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
}

function closeDetails() {
    $("#toggleView").show();
    // $('#toggleView').removeClass('col-md-5');
    // $('#toggleView').addClass('col-md-12');

    // if ($('#toggleView').hasClass('col-md-5')) {
    //     $('table#myTable td#visible').hide();
    //     $('table#myTable th#visible').hide();
    // } else {
    //     $('table#myTable td#visible').show();
    //     $('table#myTable th#visible').show();
    // }
    $(".modal .close").click();
    // document.getElementById('player_details').style.display = "none";
}


function viewPlayer(playerId, path) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/" + path + "/" + playerId;

    // $("#toggleView").hide();
    // $('#toggleView').removeClass('col-md-12');
    // $('#toggleView').addClass('col-md-5');

    document.getElementById('player_details').style.display = "block";

    if ($('#toggleView').hasClass('col-md-5')) {
        $('table#myTable td#visible').hide();
        $('table#myTable th#visible').hide();
    } else {
        $('table#myTable td#visible').show();
        $('table#myTable th#visible').show();
    }

    var div = document.getElementById("player_details");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
            $(".edit_note").tooltip({
                placement: "top",
                title: "Edit this Note",
            });

            $(".delete_note").tooltip({
                placement: "top",
                title: "Delete this Note",
            });

            //$('html, body').animate({scrollTop: $("#toggleView").offset().top}, 1500);
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function adjustPlayerLevel(playerId, path) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/" + path + "/" + playerId;

    $('#toggleView').removeClass('col-md-12');
    $('#toggleView').addClass('col-md-5');

    document.getElementById('player_details').style.display = "block";

    if ($('#toggleView').hasClass('col-md-5')) {
        $('table#myTable td#visible').hide();
        $('table#myTable th#visible').hide();
    } else {
        $('table#myTable td#visible').show();
        $('table#myTable th#visible').show();
    }

    var div = document.getElementById("player_details");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
            $(".edit_note").tooltip({
                placement: "top",
                title: "Edit this Note",
            });

            $(".delete_note").tooltip({
                placement: "top",
                title: "Delete this Note",
            });

            //$('html, body').animate({scrollTop: $("#toggleView").offset().top}, 1500);
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function viewPlayerTagOptions(path) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/" + path;

    $('#toggleView').removeClass('col-md-12');
    $('#toggleView').addClass('col-md-5');

    document.getElementById('player_details').style.display = "block";

    // if ($('#toggleView').hasClass('col-md-5')) {
    //     $('table#myTable td#visible').hide();
    //     $('table#myTable th#visible').hide();
    // } else {
    //     $('table#myTable td#visible').show();
    //     $('table#myTable th#visible').show();
    // }

    var div = document.getElementById("player_details");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function viewGameDetails(playerId) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/gameDetails/" + playerId;

    document.getElementById('player_details').style.display = "block";

    var div = document.getElementById("player_details");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
            $(".edit_note").tooltip({
                placement: "top",
                title: "Edit this Note",
            });

            $(".delete_note").tooltip({
                placement: "top",
                title: "Delete this Note",
            });

            //$('html, body').animate({scrollTop: $("#toggleView").offset().top}, 1500);
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function get_gameDetails_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var gameHistoryId = document.getElementById('gameHistoryId').value;

    url = base_url + "player_management/getGameDetailsPages/" + gameHistoryId + "/" + segment;

    var div = document.getElementById("gameHistoryDetailsList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function get_tag_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/get_tag_pages/" + segment;

    var div = document.getElementById("tag_table");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function get_vipgroupsetting_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "vipsetting_management/get_vipgroupsetting_pages/" + segment;

    var div = document.getElementById("tag_table");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function sortTag(sort) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/sortTag/" + sort;

    var div = document.getElementById("tag_table");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function searchTag() {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var search = document.getElementById('search').value;

    url = base_url + "player_management/searchTag/" + search;

    var div = document.getElementById("tag_table");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function sortVipGroup(sort) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/sortVipGroup/" + sort;

    var div = document.getElementById("tag_table");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function viewPlayerWithCurrentPage(playerId, path, page) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/" + path + "/" + playerId + "/" + page;

    // $('#toggleView').removeClass('col-md-12');
    // $('#toggleView').addClass('col-md-5');

    document.getElementById('player_details').style.display = "block";

    if ($('#toggleView').hasClass('col-md-5')) {
        $('table#myTable td#visible').hide();
        $('table#myTable th#visible').hide();
    } else {
        $('table#myTable td#visible').show();
        $('table#myTable th#visible').show();
    }
    //$("#toggleView").hide();
    var div = document.getElementById("player_details");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {

            div.innerHTML = xmlhttp.responseText;
            $(".edit_note").tooltip({
                placement: "top",
                title: "Edit this Note",
            });

            $(".delete_note").tooltip({
                placement: "top",
                title: "Delete this Note",
            });

            //$('html, body').animate({scrollTop: $("#toggleView").offset().top}, 1500);
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function viewPlayerWithCurrentPageBlocked(playerId, gameId, path, page) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/" + path + "/" + playerId + "/" + gameId + "/" + page;

    $('#toggleView').removeClass('col-md-12');
    $('#toggleView').addClass('col-md-5');

    document.getElementById('player_details').style.display = "block";

    if ($('#toggleView').hasClass('col-md-5')) {
        $('table#myTable td#visible').hide();
        $('table#myTable th#visible').hide();
    } else {
        $('table#myTable td#visible').show();
        $('table#myTable th#visible').show();
    }

    var div = document.getElementById("player_details");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
            $(".edit_note").tooltip({
                placement: "top",
                title: "Edit this Note",
            });

            $(".delete_note").tooltip({
                placement: "top",
                title: "Delete this Note",
            });

            //$('html, body').animate({scrollTop: $("#toggleView").offset().top}, 1500);
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function get_player_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/getPlayerPages/" + segment;

    var div = document.getElementById("playerList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;

            if ($('#toggleView').hasClass('col-md-5')) {
                $('table#myTable td#visible').hide();
                $('table#myTable th#visible').hide();
            } else {
                $('table#myTable td#visible').show();
                $('table#myTable th#visible').show();
            }
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function get_vip_player_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/getVIPPlayerPages/" + segment;

    var div = document.getElementById("playerList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;

            if ($('#toggleView').hasClass('col-md-5')) {
                $('table#myTable td#visible').hide();
                $('table#myTable th#visible').hide();
            } else {
                $('table#myTable td#visible').show();
                $('table#myTable th#visible').show();
            }
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function get_blacklist_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/getBlacklistPages/" + segment;

    var div = document.getElementById("playerList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;

            if ($('#toggleView').hasClass('col-md-5')) {
                $('table#myTable td#visible').hide();
                $('table#myTable th#visible').hide();
            } else {
                $('table#myTable td#visible').show();
                $('table#myTable th#visible').show();
            }
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function searchPlayerList(type) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var search = document.getElementById('search').value;

    url = base_url + "player_management/searchPlayerList/" + search + "/" + type;

    var div = document.getElementById("playerList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;

            if ($('#toggleView').hasClass('col-md-5')) {
                $('table#myTable td#visible').hide();
                $('table#myTable th#visible').hide();
            } else {
                $('table#myTable td#visible').show();
                $('table#myTable th#visible').show();
            }
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function sortPlayerList(sort, type) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/sortPlayerList/" + sort + "/" + type;

    var div = document.getElementById("playerList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;

            if ($('#toggleView').hasClass('col-md-5')) {
                $('table#myTable td#visible').hide();
                $('table#myTable th#visible').hide();
            } else {
                $('table#myTable td#visible').show();
                $('table#myTable th#visible').show();
            }
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function get_gameHistory_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/getGameHistoryPages/" + segment;

    var div = document.getElementById("gameHistoryList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function actionType(action) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/actionType/" + action;

    var div = document.getElementById("action");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }

        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}
// end of general

// ----------------------------------------------------------------------------------------------------------------------------- //

// sidebar.php
$(document).ready(function() {
    var url = document.location.pathname;
    var res = url.split("/");
    for (i = 0; i < res.length; i++) {
        switch (res[i]) {
            case 'searchAllPlayer':
            case 'viewAllPlayer':
            case 'viewAddPlayer':
            case 'lockPlayer':
            case 'userInformation':
            case 'searchMain':
            case 'selectedPlayers':
            case 'resetPassword':
                $("a#view_player_list").addClass("active");
                break;

            case 'viewOnlinePlayerList':
                $("a#view_online_player_list").addClass("active");
                break;

            case 'vipGroupSettingList':
            case 'searchMainVIP':
                $("a#view_vipsetting_list").addClass("active");
                break;

            case 'taggedlist':
                $("a#view_taggedlist").addClass("active");
                break;

            case 'linkedAccount':
            case 'searchLinkedPlayer':
                $("a#view_linkedAccount").addClass("active");
                break;

            case 'playerTagManagement':
                $("a#view_player_tag_management").addClass("active");
                break;

            case 'viewRankingSettings':
                $("a#view_ranking_settings").addClass("active");
                break;

            case 'accountProcess':
                $("a#view_account_process").addClass("active");
                break;

            case 'friendReferral':
                $("a#view_friend_referral").addClass("active");
                break;

            case 'gameHistory':
                $("a#view_game_history").addClass("active");
                break;

            case 'chatHistory':
                $("a#view_chat_history").addClass("active");
                break;

            case 'viewRegistrationSettings':
                $("a#view_registration_setting").addClass("active");
                break;

            case 'responsibleGamingSetting':
                $("a#view_responsible_gaming_setting").addClass("active");
                break;

            case 'ManualSubtractBalanceTagManagement':
                $("a#view_manual_adjust_tag_management").addClass("active");
                break;

            case 'accountAutoProcess':
                $("a#view_upload_player").addClass("active");
                break;
            default:
                break;
        }
    }
});
// end of sidebar.php

// ----------------------------------------------------------------------------------------------------------------------------- //


//Tags
function showOthers(e) {
    if (e.value == 'Others') {
        document.getElementById('specify').style.display = "block";
    } else {
        document.getElementById('specify').style.display = "none";
    }
}

function showDescription(e) {
    if (e.value) {
        $.ajax({
            'url': base_url + 'player_management/getTagDescription/' + e.value,
            'type': 'GET',
            'dataType': "json",
            'success': function(data) {
                // console.log(data[0]);
                $('#tagDescription').html(data[0].tagDescription);
            }
        }, 'json');
        document.getElementById('description').style.display = "block";
        return false;
    } else {
        document.getElementById('description').style.display = "none";
    }
}
//end Tags

//Telephone call
function makeTeleCall(getUrl) {
    $.ajax({
        url: getUrl,
        type: 'GET',
        dataType: "json"
    }).done(function(data) {
        // console.log(data);
        if(data.hasOwnProperty('redirect_url')) {
            window.open(data.redirect_url, '_blank');
        }
        else if(data.hasOwnProperty('success')) {
            if(data.success) {
                BootstrapDialog.show({
                    type: BootstrapDialog.TYPE_SUCCESS,
                    title: 'Success',
                    message: data.msg,
                    buttons: [{
                        label: 'Close',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            }
            else {
                BootstrapDialog.show({
                    type: BootstrapDialog.TYPE_DANGER,
                    title: 'Failed',
                    message: data.msg,
                    buttons: [{
                        label: 'Close',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            }
        }
        // no need to do anything so far
    }).fail(function(jqXHR, textStatus) {
        // no need to do anything so far
    });
}

function hangupTeleCall(getUrl) {
    $.ajax({
        url: getUrl,
        type: 'GET',
        dataType: "text"
    }).done(function(data) {
        console.log(data);
        if(data == "200") {
            BootstrapDialog.closeAll();
        }
    }).fail(function(jqXHR, textStatus) {
        // no need to do anything so far
    });
}
//end Telephone call


//Specfiy
function specify(e) {
    if (e.value == 'specify') {
        $("#start_date").attr("disabled", false);
        $("#end_date").attr("disabled", false);
    } else {
        $("#start_date").attr("disabled", true);
        $("#end_date").attr("disabled", true);
    }
}
//end Specfiy

//Locked
function specifyLocked(e) {
    if (e.value == 'specify') {
        $("#start_date_locked").attr("disabled", false);
        $("#end_date_locked").attr("disabled", false);
    } else {
        $("#start_date_locked").attr("disabled", true);
        $("#end_date_locked").attr("disabled", true);
    }
}
//end Locked

//Blocked
function specifyBlocked(e) {
    if (e.value == 'frozen') {
        $("#start_date_block").attr("disabled", false);
        $("#end_date_block").attr("disabled", false);
        $("#start_date_block").attr("required", true);
        $("#end_date_block").attr("required", true);
    } else {
        $("#start_date_block").attr("disabled", true);
        $("#end_date_block").attr("disabled", true);
        $("#start_date_block").attr("required", false);
        $("#end_date_block").attr("required", false);
    }
}
//end Blocked

//showGame
function showDivs(e) {
    // if (e.value == 'blocked') {
    //     document.getElementById('block_lock').style.display = "block";
    //     document.getElementById('game').style.display = "block";
    //     document.getElementById('tag').style.display = "none";
    //     document.getElementById('level').style.display = "none";
    // } else
    if (e.value == 'tag') {
        document.getElementById('tag').style.display = "block";
        // document.getElementById('block_lock').style.display = "none";
        // document.getElementById('game').style.display = "none";
        document.getElementById('level').style.display = "none";
    }
    // else if(e.value == 'locked') {
    //     document.getElementById('block_lock').style.display = "block";
    //     document.getElementById('tag').style.display = "none";
    //     document.getElementById('game').style.display = "none";
    //     document.getElementById('level').style.display = "none";
    // }
    else {
        // document.getElementById('block_lock').style.display = "none";
        document.getElementById('tag').style.display = "none";
        // document.getElementById('game').style.display = "none";
        document.getElementById('level').style.display = "block";
    }
}
//end showGame

//Blocked
function specifyDate(e) {
    if (e.value == 'frozen') {
        $("#start_date").attr("disabled", false);
        $("#end_date").attr("disabled", false);
    } else {
        $("#start_date").attr("disabled", true);
        $("#end_date").attr("disabled", true);
    }
}
//end Blocked

// ----------------------------------------------------------------------------------------------------------------------------- //


// ----------------------------------------------------------------------------------------------------------------------------- //

//check if passwords match

function checkPass() {
    //Store the password field objects into variables ...
    var pass1 = document.getElementById('password');
    var pass2 = document.getElementById('cpassword');
    //Store the Confimation Message Object ...
    var message = document.getElementById('lcpassword');
    //Set the colors we will be using ...
    var goodColor = "#66cc66";
    var badColor = "#ff6666";
    //Compare the values in the password field
    //and the confirmation field
    if (pass1.value == "" || pass2.value == "") {
        pass2.style.backgroundColor = "";
        message.innerHTML = "";
    } else if (pass1.value == pass2.value) {
        //The passwords match.
        //Set the color to the good color and inform
        //the user that they have entered the correct password
        //pass2.style.backgroundColor = goodColor;
        message.style.color = goodColor;
        message.innerHTML = "Passwords Match!";
    } else {
        //The passwords do not match.
        //Set the color to the bad color and
        //notify the user.
        //pass2.style.backgroundColor = badColor;
        message.style.color = badColor;
        message.innerHTML = "Passwords Do Not Match!";
    }

}
//end of check if passwords match

// ----------------------------------------------------------------------------------------------------------------------------- //


// ----------------------------------------------------------------------------------------------------------------------------- //

// view_account_process_list.php
function get_account_process_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/getAccountProcessPages/" + segment;

    var div = document.getElementById("accountProcessList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function searchAccountProcessList() {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var search = document.getElementById('search').value;

    url = base_url + "player_management/searchAccountProcessList/" + search;

    var div = document.getElementById("accountProcessList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function sortAccountProcessList(sort) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/sortAccountProcessList/" + sort;

    var div = document.getElementById("accountProcessList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function addAccountProcess() {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/addAccountProcess";

    $('#toggleView').removeClass('col-md-12');
    $('#toggleView').addClass('col-md-6 col-lg-6');
    $('#account_process_details').show();

    var div = document.getElementById("account_process_details");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
            $("#password").strength();
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}



function viewAccountProcess(id) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/viewAccountProcess/" + id;

    $('#toggleView').removeClass('col-md-12');
    $('#toggleView').addClass('col-md-6 col-lg-6');

    var div = document.getElementById("account_process_details");

    $('#account_process_details').show();

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
            $(".edit_account").tooltip({
                placement: "top",
                title: "Edit this Account",
            });

            $(".delete_account").tooltip({
                placement: "top",
                title: "Delete this Account",
            });
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function get_account_process_list_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var id = document.getElementById('batch_id').value;

    url = base_url + "player_management/getAccountProcessListPages/" + segment + "/" + id;

    var div = document.getElementById("viewAccountProcess");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function verifyEditAccountProcess(player_affiliate_id) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var name = document.getElementById('name').value;
    var description = document.getElementById('description').value;

    var error = true;
    $('#error').html('');
    $('#error').hide();

    if (!name) {
        $('#error').html('Name field is required.');
        $('#error').show();
        error = false;
    }

    if (error == true) {
        url = base_url + "player_management/verifyEditAccountProcess/" + player_affiliate_id;

        var poststr =
            "&name=" + encodeURI(name) +
            "&description=" + encodeURI(description);

        var div = document.getElementById("account_process_details");

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                //div.innerHTML = xmlhttp.responseText;
                window.location = base_url + 'player_management/accountProcess';
            }
            if (xmlhttp.readyState != 4) {
                div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
            }
        }

        xmlhttp.open("POST", url, true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(poststr);
    }
}

function editAccountProcessDetails(id, type) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/editAccountProcessDetails/" + id + "/" + type;

    var div = document.getElementById("account_process_details");

    $('#account_process_details').show();

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function verifyEditAccountProcessDetails(player_id, type) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;

    var error = true;
    $('#error').html('');
    $('#error').hide();

    if (!username || !password) {
        $('#error').html('All fields are required.');
        $('#error').show();
        error = false;
    }

    if (error == true) {
        url = base_url + "player_management/verifyEditAccountProcessDetails/" + player_id + "/" + type;

        var poststr =
            "&username=" + encodeURI(username) +
            "&password=" + encodeURI(password);

        var div = document.getElementById("account_process_details");

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                //div.innerHTML = xmlhttp.responseText;
                window.location = base_url + 'player_management/accountProcess';
            }
            if (xmlhttp.readyState != 4) {
                div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
            }
        }

        xmlhttp.open("POST", url, true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(poststr);
    }
}
// end of view_account_process_list.php

// ----------------------------------------------------------------------------------------------------------------------------- //

// view_chat_history.php
function viewChatHistoryDetails(session) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/viewChatHistoryDetails/" + session;

    $('#toggleView').removeClass('col-md-12');
    $('#toggleView').addClass('col-md-5');

    var div = document.getElementById("player_details");

    $('#player_details').show();

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function get_chat_history_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/getChatHistoryPages/" + segment;

    var div = document.getElementById("chatHistoryList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function searchChatHistoryList() {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var search = document.getElementById('search').value;

    url = base_url + "player_management/searchChatHistoryList/" + search;

    var div = document.getElementById("chatHistoryList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function sortChatHistoryList(sort) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/sortChatHistoryList/" + sort;

    var div = document.getElementById("chatHistoryList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}
// end of view_chat_history.php

function sortGameHistory(sort) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/sortGameHistory/" + sort;

    var div = document.getElementById("gameHistoryList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode

    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

    return true;
}

function get_user_information_chat_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var player_id = $('#player_id').val();

    url = base_url + "player_management/chatSessionPages/" + segment + "/" + player_id;

    var div = document.getElementById("player_info");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;

            if ($('#toggleView').hasClass('col-md-5')) {
                $('table#myTable td#visible').hide();
                $('table#myTable th#visible').hide();
            } else {
                $('table#myTable td#visible').show();
                $('table#myTable th#visible').show();
            }
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function get_user_information_nonawr_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var player_id = $('#player_id').val();

    url = base_url + "player_management/nonAWRPages/" + segment + "/" + player_id;

    var div = document.getElementById("player_info");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function information(info, segment) {
    if (info == null)
        return;

    var xmlhttp = GetXmlHttpObject();
    var player_id = $('#player_id').val();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    $('#toggleView').removeClass('col-md-5');
    $('#toggleView').addClass('col-md-12');
    document.getElementById('player_details').style.display = "none";

    if (info == "signup") {
        url = base_url + "player_management/viewSignupInformation/" + player_id + "/" + segment;
    }

    if (info == "personal") {
        url = base_url + "player_management/viewPersonalInformation/" + player_id + "/" + segment;
    }

    if (info == "balance_i") {
        url = base_url + "player_management/viewBalanceInformation/" + player_id + "/" + segment;
    }

    if (info == "bonus") {
        url = base_url + "player_management/viewBonusInformation/" + player_id + "/" + segment;
    }

    if (info == "account") {
        url = base_url + "player_management/viewAccountInformation/" + player_id + "/" + segment;
    }

    if (info == "other") {
        url = base_url + "player_management/viewOtherInformation/" + player_id + "/" + segment;
    }

    if (info == "sent") {
        url = base_url + "player_management/viewSentMessages/" + player_id + "/" + segment;
    }

    if (info == "chat") {
        url = base_url + "player_management/viewChatSessions/" + player_id + "/" + segment;
    }

    if (info == "user_pma") {
        url = base_url + "player_management/viewUserPMA/" + player_id + "/" + segment;
    }

    if (info == "user_cc") {
        url = base_url + "player_management/viewUserCC/" + player_id + "/" + segment;
    }

    if (info == "non") {
        url = base_url + "player_management/viewNonAWR/" + player_id + "/" + segment;
    }

    if (info == "transactions") {
        url = base_url + "player_management/viewTransactions/" + player_id + "/" + segment;
    }

    if (info == "game_session") {
        url = base_url + "player_management/viewGameSession/" + player_id + "/" + segment;
    }

    if (info == "unfinished") {
        url = base_url + "player_management/viewUnfinishedGames/" + player_id + "/" + segment;
    }

    if (info == "saved") {
        url = base_url + "player_management/viewSavedGS/" + player_id + "/" + segment;
    }

    if (info == "auto") {
        url = base_url + "player_management/viewAutoBC/" + player_id + "/" + segment;
    }

    if (info == "pending") {
        url = base_url + "player_management/viewPendingBonuses/" + player_id + "/" + segment;
    }

    if (info == "game") {
        url = base_url + "player_management/viewGameTransactions/" + player_id + "/" + segment;
    }

    if (info == "balance_c") {
        url = base_url + "player_management/viewBalanceCorrection/" + player_id + "/" + segment;
    }

    if (info == "session") {
        url = base_url + "player_management/viewSessionTimer/" + player_id + "/" + segment;
    }

    if (info == "deposit") {
        url = base_url + "player_management/viewDepositLimits/" + player_id + "/" + segment;
    }

    var div = document.getElementById("player_info");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
            $('#randomPassword').click(function() {
                if ($(this).attr("value") == "randomPassword") {
                    $("#passwordField").toggle();
                    $("#hiddenField").toggle();
                }
            });
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function ageText() {
    $("#age_from").attr("disabled", true);
    $("#age_to").attr("disabled", true);
    $("#age_text").attr("disabled", false);
}

function ageDate() {
    $("#age_from").attr("disabled", false);
    $("#age_to").attr("disabled", false);
    $("#age_text").attr("disabled", true);
}

function checkAll(id) {
    var list = document.getElementsByClassName(id);
    var all = document.getElementById(id);

    if (all.checked) {
        for (i = 0; i < list.length; i++) {
            list[i].checked = 1;
        }
    } else {
        all.checked;

        for (i = 0; i < list.length; i++) {
            list[i].checked = 0;
        }
    }
}

function uncheckAll(id) {
    var list = document.getElementById(id).className;
    // var all = document.getElementById(list);

    var item = document.getElementById(id);
    var allitems = document.getElementsByClassName(list);
    var cnt = 0;

    if (item.checked) {
        for (i = 0; i < allitems.length; i++) {
            if (allitems[i].checked) {
                cnt++;
            }
        }

        if (cnt == allitems.length) {
            $("#visible").prop('checked', true);
            // all.checked = 1;
        }
    } else {
        $("#visible").prop('checked', false);
        // all.checked = 0;
    }
}

//--------DOCUMENT READY---------
//---------------
$(document).ready(function() {
    PlayerManagementProcess.initialize();
});


//player management module
var PlayerManagementProcess = {

    initialize: function() {
        // console.log("initialized now!");

        //tooltip
        $('body').tooltip({
            selector: '[data-toggle="tooltip"]'
        });

        $('#from_date').prop("disabled", true);
        $('#to_date').prop("disabled", true);

        //for ranking level add form
        var is_addRankingVisible = true;
        //for add new tag
        var is_playerTagMngmtVisible = false;
        //for add new manual subtract balance tag
        var is_ManualSubtractBalanceTagMngmtVisible = false;
        //for ranking level edit form
        var is_editRankingVisible = false;
        //for edit tag
        var is_editPlayerTagMngmtVisible = false;
        //for edit manual subtract balance tag
        var is_editManualSubtractBalanceTagMngmtVisible = false;

        if (!is_addRankingVisible) {
            $('#addRankingLevelSetting').hide();
        } else {
            $('#addRankingLevelSetting').show();
        }

        if (!is_playerTagMngmtVisible) {
            $('#addPlayerTagMngmt').hide();
        } else {
            $('#addPlayerTagMngmt').show();
        }

        if (!is_ManualSubtractBalanceTagMngmtVisible) {
            $('#addManualSubtractBalanceTagMngmt').hide();
        } else {
            $('#addManualSubtractBalanceTagMngmt').show();
        }

        $("#addPlayerTagMngmtBtn").click(function() {
            // console.log('Log'+is_playerTagMngmtVisible);
            if (!is_playerTagMngmtVisible) {
                is_playerTagMngmtVisible = true;
                $('#addPlayerTagMngmt').show();
                $('#is_playerTagMngmtVisible').hide();
            } else {
                is_playerTagMngmtVisible = false;
                $('#addPlayerTagMngmt').hide();
            }
        });

        $("#addManualSubtractBalanceTagMngmtBtn").click(function () {
            if(!is_ManualSubtractBalanceTagMngmtVisible){
                is_ManualSubtractBalanceTagMngmtVisible = true;
                $('#addManualSubtractBalanceTagMngmt').show();
                // $('#is_playerTagMngmtVisible').hide();
            }else{
                is_ManualSubtractBalanceTagMngmtVisible = false;
                $('#addManualSubtractBalanceTagMngmt').hide();
            }
        });

        $("#editTagMngmt").click(function() {
            is_editPlayerTagMngmtVisible = true;
            $('#addPlayerTagMngmt').show();
        });

        $("#editManualSubtractBalanceTagMngmt").click(function () {
            is_editManualSubtractBalanceTagMngmtVisible = true;
            $('#addManualSubtractBalanceTagMngmt').show();
        });

        //show hide
        $(".addRankingLevelSettingBtn").click(function() {
            // console.log('Log'+is_addRankingVisible);
            if (!is_addRankingVisible) {
                is_addRankingVisible = true;
                $('#addRankingLevelSetting').show();
                $('.addRankingLevelSettingBtn').removeClass('glyphicon glyphicon-plus-sign');
                $('.addRankingLevelSettingBtn').addClass('glyphicon glyphicon-minus-sign');
                $('#editRankingLevelSetting').hide();
            } else {
                is_addRankingVisible = false;
                $('#addRankingLevelSetting').hide();
                $('.addRankingLevelSettingBtn').removeClass('glyphicon glyphicon-minus-sign');
                $('.addRankingLevelSettingBtn').addClass('glyphicon glyphicon-plus-sign');
            }
        });

        if (!is_editRankingVisible) {
            $('#editRankingLevelSetting').hide();
        } else {
            $('#editRankingLevelSetting').show();
        }

        //show hide
        $(".editRankingLevelSettingBtn").click(function() {
            is_editRankingVisible = true;
            $('.addRankingLevelSettingBtn').removeClass('glyphicon glyphicon-minus-sign');
            $('.addRankingLevelSettingBtn').addClass('glyphicon glyphicon-plus-sign');

            $('#editRankingLevelSetting').show();
            $('#addRankingLevelSetting').hide();
        });

        //for add vip group panel
        var is_addPanelVisible = false;

        //for ranking level edit form
        var is_editPanelVisible = false;

        if (!is_addPanelVisible) {
            $('.add_new_tag_sec').hide();
        } else {
            $('.add_new_tag_sec').show();
        }

        if (!is_editPanelVisible) {
            $('.add_new_tag_sec').hide();
        } else {
            $('.add_new_tag_sec').show();
        }

        //show hide add vip group panel
        $("#add_new_tag").click(function() {
            if (!is_addPanelVisible) {
                is_addPanelVisible = true;
                $('#tagName').val('');
                $('#tagDescription').val('');
                $('.add_new_tag_sec').show();
                $('#addVipGroupGlyhicon').removeClass('glyphicon glyphicon-plus-sign');
                $('#addVipGroupGlyhicon').addClass('glyphicon glyphicon-minus-sign');
            } else {
                is_addPanelVisible = false;
                $('.add_new_tag_sec').hide();
                $('#addVipGroupGlyhicon').removeClass('glyphicon glyphicon-minus-sign');
                $('#addVipGroupGlyhicon').addClass('glyphicon glyphicon-plus-sign');
            }
        });

        //show hide edit vip group panel
        $(".editTag").click(function() {
            is_editPanelVisible = true;
            $('.add_vip_group_sec').hide();
            $('.edit_vip_group_sec').show();
        });

        //cancel add vip group
        $(".addvip-cancel-btn").click(function() {
            is_addPanelVisible = false;
            $('.add_vip_group_sec').hide();
            $('#addVipGroupGlyhicon').removeClass('glyphicon glyphicon-minus-sign');
            $('#addVipGroupGlyhicon').addClass('glyphicon glyphicon-plus-sign');
        });
    },

    getTagDetails: function(tag_id) {
        is_playerTagMngmtVisible = true;
        var $evidence = $('#addEvidence');
        var listArr = [];
        var list = [];
        $('#addPlayerTagMngmt').show();
        $.ajax({
            'url': base_url + 'player_management/getTagDetails/' + tag_id,
            'type': 'GET',
            'dataType': "json",
            'success': function(data) {
                $('#tagId').val(data[0].tagId).attr('value', data[0].tagId).trigger('change');
                $('#playerTagName').val(data[0].tagName).attr('value', data[0].tagName).trigger('change');
                $('#tagDescription').val(data[0].tagDescription).text(data[0].tagDescription).trigger('change');
                $('#tagColor').val(data[0].tagColor).attr('value', data[0].tagColor).trigger('change');

                if (data[0].blockedPlayerTag) {
                    $('#chkPlayerBlockTag').prop('checked', true);
                } else {
                    $('#chkPlayerBlockTag').prop('checked', false);
                }
            }
        }, 'json');
        return false;
    },

    getManualSubtractBalanceTagDetails: function(id) {
        is_ManualSubtractBalanceTagMngmtVisible = true;
        $('#addManualSubtractBalanceTagMngmt').show();
        $.ajax({
            'url' : base_url + 'player_management/getManualSubtractBalanceTagDetails/' + id,
            'type' : 'GET',
            'dataType' : 'json',
            'success' : function(data) {
                $('#id').val(data[0].id);
                $('#adjust_tag_name').val(data[0].adjust_tag_name);
                $('#adjust_tag_description').val(data[0].adjust_tag_description);
            }
        }, 'json');
        return false;
    },

    getRankingList: function(requestId) {
        $.ajax({
            'url': base_url + 'player_management/getRankingList',
            'type': 'GET',
            'dataType': "json",
            'success': function(data) {
                // console.log(data.length);
                for (var i = 0; i < data.length; i++) {
                    // console.log(data[i]);
                    html = '';
                    html += '<option value="' + data[i].levelId + '" >' + data[i].levelGroup + '-' + data[i].levelName + '</option>';
                    $('#addRankingLevel').append(html);
                };
            }
        }, 'json');
        return false;
    },

    getRankingLevelSettingsDetail: function(requestId) {
        $.ajax({
            'url': base_url + 'player_management/getRankingLevelSettingsDetail/' + requestId,
            'type': 'GET',
            'dataType': "json",
            'success': function(data) {
                // console.log(data[0]);
                $('#editRankingLevelGroup').val(data[0].rankingLevelGroup);
                $('#editRankingLevel').val(data[0].rankingLevel);
                $('#editMinRequiredDeposit').val(data[0].minDepositRequirement);
                $('#editCurrency').val(data[0].currency);
                $('#editPointRequirement').val(data[0].pointRequirement);
                $('#editMaxDepositAmount').val(data[0].maxDepositAmount);
                $('#editMinDepositAmount').val(data[0].minDepositAmount);
                $('#editDailyMaxWithdrawal').val(data[0].dailyMaxWithdrawal);
                $('#editRankingLevelId').val(data[0].rankingLevelSettingId);
            }
        }, 'json');
        return false;
    },

    clearPlayerBonus: function(requestId) {
        var playerDepositPromoId = $('#playerDepositPromoId').val();

        // console.log('playerDepositPromoId: '+playerDepositPromoId);
        $.ajax({
            'url': base_url + 'player_management/clearPlayerBonus/' + playerDepositPromoId,
            'type': 'GET',
            'success': function(data) {
                location.reload();
            }
        }, 'json');
        return false;
    }
};

$(document).ready(function() {
    var offset = 200;
    var duration = 500;
    jQuery(window).scroll(function() {
        if (jQuery(this).scrollTop() > offset) {
            jQuery('.custom-scroll-top').fadeIn(duration);
        } else {
            jQuery('.custom-scroll-top').fadeOut(duration);
        }
    });

    $('.custom-scroll-top').on('click', function(event) {
        event.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });

    //modal Tags
    $("#tags").change(function() {
        $("#tags option:selected").each(function() {
            if ($(this).attr("value") == "Others") {
                $("#specify").show();
            } else {
                $("#specify").hide();
            }
        });
    }).change();
    //end modal Tags

    //Use Random Password
    $('#randomPassword').click(function() {
        if ($(this).attr("value") == "randomPassword") {
            $("#passwordField").toggle();
            $("#hiddenField").toggle();
        }
    });
    //End of Use Random Password

    //password press
    $("#cpassword").keyup(checkPass);
    $("#password").keyup(checkPass);
    //end password press

    //tool tip
    $(".details").tooltip({
        placement: "top",
        title: "Show Details",
    });

    $(".playerlevel").tooltip({
        placement: "top",
        title: "Adjust Player Level",
    });

    $(".lock").tooltip({
        placement: "top",
        title: "Lock/Unlock Player",
    });

    $(".block").tooltip({
        placement: "top",
        title: "Block/Unblock Player",
    });

    $(".tags").tooltip({
        placement: "top",
        title: "Edit Tag",
    });

    $(".edit").tooltip({
        placement: "top",
        title: "Edit Details",
    });

    $(".edit_tag").tooltip({
        placement: "top",
        title: "Edit this tag",
    });

    $(".delete").tooltip({
        placement: "top",
        title: "Delete this item",
    });

    $(".delete_tag").tooltip({
        placement: "top",
        title: "Delete this tag",
    });


    $("#edit_column").tooltip({
        placement: "left",
        title: "Edit columns",
    });

    $("#show_advance_search").tooltip({
        placement: "top",
        title: "Advance search",
    });


    //end of tool tip

    // view_player_list.php MAIN
    // $("#main_panel_body").slideToggle();
    $("#hide_main").click(function() {
        $("#main_panel_body").slideToggle();
        $("#hide_main_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });
    // end of view_player_list.php MAIN

    // view_plist.php MAIN
    $("#hide_search_main").click(function() {
        $("#deposit_fieldset").slideToggle();
        $("#hide_search_main", this).toggleClass("glyphicon glyphicon-chevron-down glyphicon glyphicon-chevron-up");
    });
    // end of view_plist.php MAIN

    // view_player_list.php PERSONAL
    $("#hide_personal").click(function() {
        $("#personal_panel_body").slideToggle();
        $("#hide_personal_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-minus-sign");
    });
    // end of view_player_list.php PERSONAL

    // view_player_list.php TRACKING
    $("#hide_tracking").click(function() {
        $("#tracking_panel_body").slideToggle();
        $("#hide_tracking_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-minus-sign");
    });
    // end of view_player_list.php TRACKING

    // view_player_list.php PAYMENT
    $("#hide_payment").click(function() {
        $("#payment_panel_body").slideToggle();
        $("#hide_payment_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-minus-sign");
    });
    // end of view_player_list.php PAYMENT

    // view_player_list.php OTHER OPTIONS
    $("#hide_other_options").click(function() {
        $("#other_options_panel_body").slideToggle();
        $("#hide_other_options_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-minus-sign");
    });
    // end of view_player_list.php OTHER OPTIONS

    // view_player_list.php SECURITY TOKEN
    $("#hide_security_token").click(function() {
        $("#security_token_panel_body").slideToggle();
        $("#hide_security_token_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-minus-sign");
    });
    // end of view_player_list.php SECURITY TOKEN

    // view_player_list.php affiliate
    $("#hide_affiliate").click(function() {
        $("#affiliate_panel_body").slideToggle();
        $("#hide_affiliate_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-minus-sign");
    });
    // end of view_player_list.php SECURITY TOKEN

    // view_player_list.php SIGNUPINFO
    $("#hide_signupinfo").click(function() {
        $("#signupinfo_panel_body").slideToggle();
        $("#hide_signupinfo_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    // end of view_player_list.php SIGNUPINFO

    // view_player_list.php SIGNUPINFO
    $("#hide_sign_up").click(function() {
        $("#signupinfo_panel_body").slideToggle();
        $("#hide_si_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    // end of view_player_list.php SIGNUPINFO

    // view_player_list.php SIGNUPINFO
    $("#hide_personal_info").click(function() {
        $("#personal_panel_body").slideToggle();
        $("#hide_pi_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down")
        return false; // prevents from jumping view on the bottom
    });
    // end of view_player_list.php SIGNUPINFO

    // view_player_list.php SIGNUPINFO
    $("#hide_contact_info").click(function() {
        $("#contact_panel_body").slideToggle();
        $("#hide_pi_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    // end of view_player_list.php SIGNUPINFO

    // linked account in player's info
    $("#hide_linked_account_info").click(function() {
        $("#linked_account_info_panel_body").slideToggle();
        $("#hide_la_info_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    // end of linked account in player's info

    // view_player_list.php SIGNUPINFO
    $("#hide_balance_info").click(function() {
        $("#balance_panel_body").slideToggle();
        $("#hide_bi_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    // end of view_player_list.php SIGNUPINFO

    // view_player_list.php bank details
    $("#hide_bank_info").click(function() {
        $("#bank_panel_body").slideToggle();
        $("#hide_bank_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    // end of view_player_list.php SIGNUPINFO

    // view_player_list.php bank details
    $("#hide_game_info").click(function() {
        $("#game_panel_body").slideToggle();
        $("#hide_game_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    // end of view_player_list.php SIGNUPINFO

    // view_player_list.php bank details
    $("#hide_log_info").click(function() {
        $("#log_panel_body").slideToggle();
        $("#hide_log_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    // end of view_player_list.php SIGNUPINFO

    // view_player_list.php responsible gaming
    $("#hide_resp_info").click(function() {
        $("#resp_game_panel_body").slideToggle();
        $("#hide_resp_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });

    // view_player_list.php responsible gaming
    $("#hide_comm_pref_info").click(function() {
        $("#comm_pref_panel_body").slideToggle();
        $("#hide_comm_pref_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });

    // end of view_player_list.php SIGNUPINFO

    $("#hide_search").click(function() {
        $("#search_body").slideToggle();
        $("#hide_search_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    // view_player_list.php withdrawal info
    $("#hide_withrawal_info").click(function() {
        $("#withrawal_panel_body").slideToggle();
        $("#hide_withrawal_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    // transfer_condition_details.php transfer info
    $("#hide_transfer_condition_info").click(function() {
        $(".transfer_condition_panel_body").slideToggle();
        $("#hide_transfer_condition_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    $('#save_changes').on("click", function(e) {
        e.preventDefault();
        $("form#modal_column_form").submit();
    });

    // //scrolling down
    // $('html, body').animate({
    //     scrollTop: $("#toggleView").offset().top
    // }, 1500);

    $("#first_deposit_date").click(function() {
        if ($('#first_deposit_date').is(":checked") || $('#second_deposit_date').is(":checked") || $('#never_deposited').is(":checked")) {
            $('#from_date').prop("disabled", false);
            $('#to_date').prop("disabled", false);
        } else {
            $('#from_date').prop("disabled", true);
            $('#to_date').prop("disabled", true);
        }
    });

    $("#second_deposit_date").click(function() {
        if ($('#first_deposit_date').is(":checked") || $('#second_deposit_date').is(":checked") || $('#never_deposited').is(":checked")) {
            $('#from_date').prop("disabled", false);
            $('#to_date').prop("disabled", false);
        } else {
            $('#from_date').prop("disabled", true);
            $('#to_date').prop("disabled", true);
        }
    });

    $("#never_deposited").click(function() {
        if ($('#first_deposit_date').is(":checked") || $('#second_deposit_date').is(":checked") || $('#never_deposited').is(":checked")) {
            $('#from_date').prop("disabled", false);
            $('#to_date').prop("disabled", false);
        } else {
            $('#from_date').prop("disabled", true);
            $('#to_date').prop("disabled", true);
        }
    });

    $('#less_deposit_amount').focusout(function() {
        var check_greater = $('#less_deposit_amount').val();
        var check_less = $('#greater_deposit_amount').val();
        if (check_less == "" && check_greater == "") {
            $("#start_first_deposit_date").attr('disabled', true);
            $("#end_first_deposit_date").attr('disabled', true);
            $("#start_second_deposit_date").attr('disabled', true);
            $("#end_second_deposit_date").attr('disabled', true);
        } else {
            $("#start_first_deposit_date").attr('disabled', false);
            $("#end_first_deposit_date").attr('disabled', false);
            $("#start_second_deposit_date").attr('disabled', false);
            $("#end_second_deposit_date").attr('disabled', false);
        }
    });

    $('#greater_deposit_amount').focusout(function() {
        var check_greater = $('#greater_deposit_amount').val();
        var check_less = $('#less_deposit_amount').val();
        if (check_less == "" && check_greater == "") {
            $("#start_first_deposit_date").attr('disabled', true);
            $("#end_first_deposit_date").attr('disabled', true);
            $("#start_second_deposit_date").attr('disabled', true);
            $("#end_second_deposit_date").attr('disabled', true);
        } else {
            $("#start_first_deposit_date").attr('disabled', false);
            $("#end_first_deposit_date").attr('disabled', false);
            $("#start_second_deposit_date").attr('disabled', false);
            $("#end_second_deposit_date").attr('disabled', false);
        }
    });

    $(".number_only").keydown(function(e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13, 110]) !== -1 ||
            // Allow: Ctrl+A
            (code == 65 && e.ctrlKey === true) ||
            // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105)) {
            e.preventDefault();
        }
    });

    $('input[name="deposit_amount_type"]').change(function() {
        $('#deposit_amount').prop('disabled', $(this).val() == 3);
    });
});

function checkDepositCheckBox() {
    //console.log('test');
    var first_deposited = document.getElementById("first_deposited");
    var second_deposited = document.getElementById("second_deposited");
    var never_deposited = document.getElementById("never_deposited");
    var deposited = document.getElementById("deposited");

    if (first_deposited.checked == true || second_deposited.checked == true || deposited.checked == true) {
        $("#deposit_amount").attr('disabled', $('input[name="deposit_amount_type"]').val() == 3);
        $("#deposit_amount_type1").attr('disabled', false);
        $("#deposit_amount_type2").attr('disabled', false);
        $("#deposit_amount_type3").attr('disabled', false);
        $("#deposit_amount_type3").prop('checked', true);
        $("#deposit_date_from").attr('disabled', false);
        $("#deposit_date_to").attr('disabled', false);
    } else if (never_deposited.checked == true) {
        $("#deposit_amount").attr('disabled', true);
        $("#deposit_amount").val('');
        $("#deposit_amount_type1").attr('disabled', true);
        $("#deposit_amount_type2").attr('disabled', true);
        $("#deposit_amount_type3").attr('disabled', true);
        $("#deposit_amount_type3").attr('checked', false);
        $("#deposit_date_from").attr('disabled', true);
        $("#deposit_date_to").attr('disabled', true);
    }
}

function resetSearching() {
    $("#first_deposited").attr('checked', false);
    $("#second_deposited").attr('checked', false);
    $("#never_deposited").attr('checked', false);

    $("#deposit_amount_type1").attr('checked', false);
    $("#deposit_amount_type1").attr('disabled', true);
    $("#deposit_amount_type2").attr('disabled', true);

    $("#deposit_amount").attr('disabled', true);
    $("#deposit_amount").val('');

    $("#deposit_date_from").attr('required', false);
    $("#deposit_date_to").attr('required', false);
    $("#deposit_date_from").attr('disabled', true);
    $("#deposit_date_to").attr('disabled', true);
    $("#deposit_date_from").val('');
    $("#deposit_date_to").val('');


    $("#wallet_amount_type1").attr('checked', false);
    $("#wallet_amount_type2").attr('checked', false);
    $("#wallet_amount_type1").attr('disabled', true);
    $("#wallet_amount_type2").attr('disabled', true);


    $("#main_wallet").attr('checked', false);
    $("#pt_wallet").attr('checked', false);
    $("#ag_wallet").attr('checked', false);

    $("#wallet_amount").attr('disabled', true);
    $("#wallet_amount").val('');

    // $("#status").val('');
    // $("#tagged").val('');
}

function checkDepositAmount() {
    var deposit_amount_type1 = document.getElementById("deposit_amount_type1");
    var deposit_amount_type2 = document.getElementById("deposit_amount_type2");

    if (deposit_amount_type1.checked == true || deposit_amount_type2.checked == true) {
        $("#deposit_amount").attr('disabled', false);
    }
}

function checkWalletCheckBox() {
    var main_wallet = document.getElementById("main_wallet");
    var pt_wallet = document.getElementById("pt_wallet");
    var ag_wallet = document.getElementById("ag_wallet");

    if (main_wallet.checked == true || pt_wallet.checked == true || ag_wallet.checked == true) {
        $("#wallet_amount").attr('disabled', false);
        $("#wallet_amount").attr('required', true);
        $("#wallet_amount_type1").attr('checked', true);
        $("#wallet_amount_type1").attr('disabled', false);
        $("#wallet_amount_type2").attr('disabled', false);
    }
}

function searchGameHistory(username) {
    var xmlhttp = GetXmlHttpObject();

    var start_date = $('#dateRangeValueStart').val();
    var end_date = $('#dateRangeValueEnd').val();
    url = base_url + "marketing_management/searchGameLog/" + username;
    var div = document.getElementById('changeable_table');
    var posting = $.post(url, { 'start_date': start_date, 'end_date': end_date });

    posting.done(function(data) {
        div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        div.innerHTML = data;

        $('#gameTable').DataTable({
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "columnDefs": [{
                className: 'control',
                orderable: false,
                targets: [1],
                "visible": false
            }],
            "order": [1, 'desc'],
            "pageLength": 30,
            "dom": '<"top"f>rt<"bottom"ip>', // l = rows, f = search, i = # entries, p = prev and next btn
            "fnDrawCallback": function(oSettings) {
                $('.selected_date').prependTo($('.top'));
            }
        });
    });
}

function checkPlayerInfo(value) {
    var checkbox = document.getElementById(value);
    var all = document.getElementById('checkall');

    var list = document.getElementsByClassName('checkall');
    var cnt = 0;

    if (checkbox.checked) {
        $('#' + value + "_form").show();
    } else {
        $('#' + value + "_form").hide();

        all.checked = 0;
    }

    for (i = 0; i < list.length; i++) {
        if (list[i].checked) {
            cnt++;
        }
    }

    if (cnt == 6) {
        all.checked = 1;
    }
}

function checkSearchPlayerInfo(value) {
    var checkbox = document.getElementById(value);

    if (checkbox.checked) {
        $('#' + value + "_search").show();
    } else {
        $('#' + value + "_search").hide();
    }
}

function checkAllPlayerInfo(value) {
    var checkbox = document.getElementById(value);
    console.log("here"+value);
    var signup = document.getElementById('signup');
    var personal = document.getElementById('personal');
    var withdraw_condition = document.getElementById('withdraw_condition');
    var contact = document.getElementById('contact');
    var account = document.getElementById('account');
    var game = document.getElementById('game');
    var bank = document.getElementById('bank');
    var players = document.getElementById('players');
    var linked_accounts = document.getElementById('linked_accounts');

    console.log("test: "+checkbox.checked);

    if (checkbox.checked) {
        $("#signup_form").show();
        $("#personal_form").show();
        $("#contact_form").show();
        $("#account_form").show();
        $("#game_form").show();
        $("#bank_form").show();
        $("#players_form").show();
        $("#withdraw_condition_form").show();
        $("#linked_accounts_form").show();

        signup.checked = 1;
        personal.checked = 1;
        contact.checked = 1;
        account.checked = 1;
        game.checked = 1;
        bank.checked = 1;
        players.checked = 1;
        withdraw_condition.checked = 1;
        linked_accounts.checked = 1;
    } else {
        $("#signup_form").hide();
        $("#personal_form").hide();
        $("#contact_form").hide();
        $("#account_form").hide();
        $("#game_form").hide();
        $("#bank_form").hide();
        $("#players_form").hide();
        $("#withdraw_condition_form").hide();
        $("#linked_accounts_form").hide();

        withdraw_condition.checked = 0;
        signup.checked = 0;
        personal.checked = 0;
        contact.checked = 0;
        account.checked = 0;
        game.checked = 0;
        bank.checked = 0;
        players.checked = 0;
        linked_accounts.checked = 0;
    }
}

function changeOnlineList(type) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/changeOnlineList/" + type;

    var div = document.getElementById('nav_content');

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
            $('#playersTable').DataTable({
                "responsive": {
                    details: {
                        type: 'column'
                    }
                },
                "columnDefs": [{
                    className: 'control',
                    orderable: false,
                    targets: 0
                }],
                "order": [1, 'asc']
            });
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function blockPlayer(player_id) {
    var dst_url = "/player_management/load_block_player_view/" + player_id;
    open_modal('block_player', dst_url, 'Block Player');
}

function refreshAccountInfo(player_id) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "player_management/refreshAccountInfo/" + player_id;

    var div = document.getElementById('balance_panel_body');

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function player_notes(player_id) {
    var dst_url = "/player_management/player_notes/" + player_id;
    open_modal('player_notes', dst_url, 'Player Notes');
}

function add_player_notes(self, player_id) {
    var url = $(self).attr('action');
    var params = $(self).serializeArray();
    $.post(url, params, function(data) {
        if (data.success) {
            refresh_modal('player_notes', "/player_management/player_notes/" + player_id, 'Player Notes', true);
        }
    });
    return false;
}

function edit_player_note(self, note_id, player_id, action = 'edit') {
    var note_list    = $(self).closest('.note_list');
    var old_note     = $(note_list).find('.old_note').val();
    var display_note = $(note_list).find('.display_note');
    var edit_notes_button = $(note_list).find('.edit_notes');
    var save_notes_button = $(note_list).find('.save_notes');
    var cancel_notes_button = $(note_list).find('.cancel_notes');
    var dropdown_notes_button = $(note_list).find('.drop_notes');

    if(action == 'edit'){//Edit
        $(save_notes_button).removeClass("hidden");
        $(cancel_notes_button).removeClass("hidden");
        $(edit_notes_button).addClass("hidden");
        $(dropdown_notes_button).addClass("hidden");
        $(display_note).empty().append('<textarea class="form-control" style="min-width: 100%" name="new_notes" required></textarea>');
        $(display_note).find('textarea').val(old_note.replace(/<br ?\/?>/g, ""));

    }else if (action == 'save'){ //Save
        var url = $(self).attr('action');
        var params = $(self).serializeArray();
        $.post(url, params, function(data) {
            if (data.success) {
                $(save_notes_button).addClass("hidden");
                $(cancel_notes_button).addClass("hidden");
                $(edit_notes_button).removeClass("hidden");
                refresh_modal('player_notes', "/player_management/player_notes/" + player_id, 'Player Notes', true);
            }
        });

    }else{ //Cancel
        $(save_notes_button).addClass("hidden");
        $(cancel_notes_button).addClass("hidden");
        $(edit_notes_button).removeClass("hidden");
        $(dropdown_notes_button).removeClass("hidden");
        $(display_note).empty().append('<textarea class="form-control input-sm" rows="2" style="max-width: 100%; min-width: 100%;" disabled>' + old_note + '</textarea>');
    }

    return false;
}

function remove_player_note(note_id, player_id) {
    var confirm_val = confirm('Are you sure you want to delete this player note?');
    if (confirm_val) {
        var url = '/player_management/remove_player_note/' + note_id + '/' + player_id;
        // console.log(url);
        $.getJSON(url, function(data) {
            if (data.success) {
                refresh_modal('player_notes', "/player_management/player_notes/" + player_id, 'Player Notes', true);
            }
        });
    }
    return false;
}

function open_modal(name, dst_url, title) {
    var main_selector = '#' + name;

    var label_selector = '#label_' + name;
    $(label_selector).html(title);

    var body_selector = main_selector + ' .modal-body';
    var target = $(body_selector);
    target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(dst_url);

    $(main_selector).modal('show');
}

function refresh_modal(name, dst_url, title, loader = false) {
    var main_selector = '#' + name;
    var body_selector = main_selector + ' .modal-body';
    var target = $(body_selector);

    if(loader){
        target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(dst_url);
    }else{
        target.load(dst_url);
    }

}

function iframe_promotion() {
    var xmlhttp = GetXmlHttpObject();
    var player_id = $('#player_id').val();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "iframe_module/iframe_promotion";

    var div = document.getElementById('balance_adjustment_history');

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
            $('#adjustmentTable').DataTable({
                "responsive": {
                    details: {
                        type: 'column'
                    }
                },
                "columnDefs": [{
                    className: 'control',
                    orderable: false,
                    targets: 0
                }],
                "order": [1, 'asc']
            });
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function autogrow(textarea){
    var adjustedHeight = textarea.clientHeight;

    adjustedHeight = Math.max(textarea.scrollHeight,adjustedHeight);
    if (adjustedHeight>textarea.clientHeight){
        textarea.style.height = adjustedHeight + 'px';
    }
}
