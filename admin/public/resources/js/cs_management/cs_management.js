// general
var base_url = "/";
var imgloader = "/resources/images/ajax-loader.gif";

function GetXmlHttpObject()
{
    if (window.XMLHttpRequest)
    {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        return new XMLHttpRequest();
    }
    if (window.ActiveXObject)
    {
        // code for IE6, IE5
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
}

function closeDetails() {
    $('#toggleView').removeClass('col-md-5');
    $('#toggleView').addClass('col-md-12');

    if ($('#toggleView').hasClass('col-md-5')) {
        $('table#myTable td#visible').hide();
        $('table#myTable th#visible').hide();
    } else {
        $('table#myTable td#visible').show();
        $('table#myTable th#visible').show();
    }
    document.getElementById('cs_details').style.display = "none";
}
// end of general

// ----------------------------------------------------------------------------------------------------------------------------- //

// sidebar.php
$(document).ready(function() {

    //tooltip
    $('body').tooltip({
    selector: '[data-toggle="tooltip"]'
    });

    var url = document.location.pathname;
    var res = url.split("/");

    for (i = 0; i < res.length; i++) {
        switch(res[i]){
            case 'messages':
                $("a#view_messages").addClass("active");
                break;

            default:
                break;
        }
    }

});
// end of sidebar.php

// ----------------------------------------------------------------------------------------------------------------------------- //

function viewChatHistoryDetails(chat_id) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "cs_management/viewChatHistoryDetails/" + chat_id;

    var div = document.getElementById("cs_details");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
            $('#myModal2').modal('show');
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function get_messages_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "cs_management/getChatPages/" + segment;

    var div = document.getElementById("chatHistoryList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }
        if (xmlhttp.readyState != 4) {
            /*div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';*/
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function markAsClosed(chat_id) {
    if (confirm('Are you sure you want to mark this message as closed?')) {
        window.location = base_url + "cs_management/markAsClose/" + chat_id;
    }
}

function verifyReply(chat_id) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var message = document.getElementById('message').value;
    var recipient = document.getElementById('recipient').value;
    var error = 0;

    if(message == '') {
        $('#error_message').html("Message is required!");
        error = 1;
    }

    if (error == 0) {
        url = base_url + "cs_management/reply/" + chat_id;

        var poststr =
        "&recipient=" + encodeURI(recipient) +
        "&message=" + encodeURI(message);

        /*var div = document.getElementById("cs_details");*/

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                /*div.innerHTML = xmlhttp.responseText;*/
                window.location.assign(base_url + 'cs_management/messages');
            }

            if (xmlhttp.readyState != 4) {
                /*div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';*/
            }
        }

        xmlhttp.open("POST", url, true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(poststr);
    }
}
// end of view_chat_history.php
