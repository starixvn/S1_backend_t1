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

$(document).ready(function() {
    var url = document.location.pathname;
    var res = url.split("/");

    for (i = 0; i < res.length; i++) {
        switch(res[i]){
            case 'messages':
                /*window.onload = function() {
                    get_messages_pages();
                    window.setInterval(get_messages_pages, 5000)
                };*/
                break;

            default:
                break;
        }
    }
});
// end of general
 
// ----------------------------------------------------------------------------------------------------------------------------- //

// view_messages_history.php
function viewMessagesDetails(message_id) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "messages/viewMessagesDetails/" + message_id;

    $('#toggleView').removeClass('col-md-12');
    $('#toggleView').addClass('col-md-5');

    var div = document.getElementById("cs_details");

    $('#cs_details').show();

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

function get_messages_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "messages/getMessagesPages/" + segment;

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

function searchMessagesList() {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var search = document.getElementById('search').value;

    url = base_url + "messages/searchMessagesList/" + search;

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

function sortMessagesList(sort) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "messages/sortMessagesList/" + sort;

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

function addMessagesDetails() {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "messages/addMessagesDetails";

    $('#toggleView').removeClass('col-md-12');
    $('#toggleView').addClass('col-md-5');

    var div = document.getElementById("cs_details");

    $('#cs_details').show();

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

function verifyAddMessages() {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var subject = document.getElementById('subject').value;
    var message = document.getElementById('message').value;
    var error = 0;

    if(subject == '') {
        $('#error_subject').html("Subject is required!");
        error = 1;
    }

    if(message == '') {
        $('#error_message').html("Message is required!");
        error = 1;
    }

    if (error == 0) {
        url = base_url + "messages/verifyAddMessages";

        var poststr =
        "&subject=" + encodeURI(subject) +
        "&message=" + encodeURI(message);

        /*var div = document.getElementById("cs_details");*/

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                //div.innerHTML = xmlhttp.responseText;
                window.location.assign(base_url + 'messages');
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
        url = base_url + "messages/reply/" + chat_id;

        var poststr =
        "&recipient=" + encodeURI(recipient) +
        "&message=" + encodeURI(message);

        $('#toggleView').removeClass('col-md-12');
        $('#toggleView').addClass('col-md-5');

        var div = document.getElementById("cs_details");

        $('#cs_details').show();

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                var result = xmlhttp.responseText;

                if(result == "failed") {
                    window.location = base_url + 'messages';
                } else {
                    div.innerHTML = result;
                }
                
                //window.location.assign(base_url + 'messages');
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
// end of view_messages_history.php