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

$(document).ready(function() {
  	CMSLogoManagementProcess.initialize();
});

//cms management module
var CMSLogoManagementProcess = {

    initialize : function() {
          // console.log("initialized now!");

          //hide promo setting
          $('#addPromoSetting').hide();

          $(".sortby_panel_body").show();
          $(".hide_sortby").click(function() {
             $(".sortby_panel_body").slideToggle();
             $(".hide_sortby_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
          });

          //tooltip
          $('body').tooltip({
            selector: '[data-toggle="tooltip"]'
          });

          $("#add_cmslogo_sec").tooltip({
              placement: "right",
              title: "Add new logo",
          });

          //sidebar
          var url = document.location.pathname;
          var res = url.split("/");

          for (i = 0; i < res.length; i++) {
              switch(res[i]){

                  case 'viewPromoManager':
                      $("a#view_promo_mgr").addClass("active");
                      break;

                  case 'viewGameManager':
                      $("a#view_game_mgr").addClass("active");
                      break;

                  case 'viewLogoManager':
                      $("a#view_logo_mgr").addClass("active");
                      break;

                  default:
                      break;
              }
          }

          //for add vip group panel
          var is_addPanelVisible = false;

          //for ranking level edit form
          var is_editPanelVisible = false;

          if(!is_addPanelVisible){
              $('.add_cmslogo_sec').hide();
          }else{
              $('.add_cmslogo_sec').show();
          }

          if(!is_editPanelVisible){
              $('.edit_cmslogo_sec').hide();
          }else{
              $('.edit_cmslogo_sec').show();
          }

          //show hide add vip group panel
          $("#add_cmslogo_sec").click(function () {
              if(!is_addPanelVisible){
                  is_addPanelVisible = true;
                  $('.add_cmslogo_sec').show();
                  $('.edit_cmslogo_sec').hide();
                  $('#addLogoCmsGlyhicon').removeClass('glyphicon glyphicon-plus-sign');
                  $('#addLogoCmsGlyhicon').addClass('glyphicon glyphicon-minus-sign');
              }else{
                  is_addPanelVisible = false;
                  $('.add_cmslogo_sec').hide();
                  $('#addLogoCmsGlyhicon').removeClass('glyphicon glyphicon-minus-sign');
                  $('#addLogoCmsGlyhicon').addClass('glyphicon glyphicon-plus-sign');
              }
          });

          //show hide edit vip group panel
          $(".editLogoCmsBtn").click(function () {
                  is_editPanelVisible = true;
                  $('.add_cmslogo_sec').hide();
                  $('.edit_cmslogo_sec').show();
          });

          //cancel add promo
          $(".addlogo-cancel-btn").click(function () {
                  is_addPanelVisible = false;
                  $('.add_cmslogo_sec').hide();
                  $('#addLogoCmsGlyhicon').removeClass('glyphicon glyphicon-minus-sign');
                  $('#addLogoCmsGlyhicon').addClass('glyphicon glyphicon-plus-sign');
          });

          //cancel edit promo
          $(".editlogocms-cancel-btn").click(function () {
                  is_editPanelVisible = false;
                  $('.edit_cmslogo_sec').hide();
          });

    },

    getLogoCmsDetails : function(logocmsId) {
       is_editPanelVisible = true;
       $('.add_cmslogo_sec').hide();
       $('.edit_cmslogo_sec').show();
       $.ajax({
            'url' : base_url + 'cmslogo_management/getLogoCmsDetails/' + logocmsId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                     // console.log(data[0]);
                     $('#editLogocmsId').val(data[0].cmsLogoId);
                     $('#editCategory').val(data[0].category);
                     $('#editLogoCms').val(data[0].logoName);

                     //$('#userfile').val(data[0].promoThumbnail);
                     $('#editLogoCmsImg').attr('src',(base_url+'resources/images/cmslogo/'+data[0].logoName));
                    }
       },'json');
        return false;
    },
};

// ----------------------------------------------------------------------------------------------------------------------------- //

// logo_settings.php
function setURL(value) {
    var val = value;
    var res = val.split("\\");

    document.getElementById('logo_url').value = base_url + 'resources/images/logo/' + res[2];
}

function setURLEditLogoCms(input) {
    // var val = value;
    // var res = val.split("\\");

    // document.getElementById('logo_url').value = base_url + 'resources/images/promothumbnails/' + res[2];

    // $('#editPromoThumbnailImg').attr('src',(base_url+'resources/images/promothumbnails/'+res[2]));
    // $('#editPromoThumbnailUrl').val(base_url+'resources/images/promothumbnails/'+res[2]);

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            // console.log(e.target.result);
            $('#editLogoCmsImg').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

//for multi select
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
    var all = document.getElementById(list);

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
            all.checked = 1;
        }
    } else {
        all.checked = 0;
    }
}

function searchLogoCms() {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var search = document.getElementById('search').value;

    url = base_url + "cmslogo_management/searchLogoCms/" + search;

    var div = document.getElementById("cmslogo_table");

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

function get_logocms_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "cmslogo_management/get_promosetting_pages/" + segment;

    var div = document.getElementById("logocms_table");

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

function sortLogoCms(sort) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "cms_management/sortLogoCms/" + sort;

    var div = document.getElementById("logocms_table");

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