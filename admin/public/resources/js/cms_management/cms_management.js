//general
//var base_url = "/";
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

function get_deposit_list(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }
    alert("Browser does not support HTTP Request");
    url = _site_url + "payment_management/get_deposit_list/" + segment;

    var div = document.getElementById("paymentList");

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

function randomCode(len,isEdit) {
    var text = '';

    var charset = "abcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < len; i++ ) {
        text += charset.charAt(Math.floor(Math.random() * charset.length));
    }

    if(isEdit == 'true'){
      $('.editPromoCode').val(text);
      $('.editPromoCMSCode').text(text);
      $('.editPromoCodeLinkSec').show();
    }else{
      $('.promoCode').val(text);
      $('.promoCMSCode').text(text);
    }

}

$(function() {
    $('.promoCode').on('keypress', function(e) {
        if (e.which == 32)
            return false;
    });
});

$(document).ready(function() {
    CMSManagementProcess.initialize();
});

//cms management module
var CMSManagementProcess = {

    initialize : function() {
          // console.log("initialized now!");

          //hide promo setting
          $('#addPromoSetting').hide();

         // $(".sortby_panel_body").show();
          $(".hide_sortby").click(function() {
             $(".sortby_panel_body").slideToggle();
             $(".hide_sortby_up", this).toggleClass("glyphicon glyphicon-chevron-down glyphicon glyphicon-chevron-up");
          });

          //tooltip
          $('body').tooltip({
            selector: '[data-toggle="tooltip"]'
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

                  default:
                      break;
              }
          }
           //$('.summernote').summernote();
            $('#summernote').summernote({
                height: 300   //set editable area's height
            });

           $('#editPromoDetails').summernote({
              height: 300   //set editable area's height
            });

          //add cms promo details
          $('#form-cmspromo').submit( function(e) {
              var code = $('#summernote').code();
              // console.log(code);
              if(code != '<p><br></p>'){
                $('.promoDetails').val(code);
                return true;
              }
          });

          //edit cms promo details
          $('#form-editcmspromo').submit( function(e) {
              var code = $('#editPromoDetails').code();
              // console.log('editcmspromo: '+code);
              //if(code != '<p><br></p>'){
                $('.promoDetails').val(code);
                return true;
              //}
          });

          //show hide edit vip group panel
          $(".editCmsPromoBtn").click(function () {
              is_editPanelVisible = true;
              $('.add_promocms_sec').hide();
              $('.edit_promocms_sec').show();
          });

          //cancel add promo
          $(".addcmspromo-cancel-btn").click(function () {
                  is_addPanelVisible = false;
                  $('.add_promocms_sec').hide();
                  $('#addPromoCmsGlyhicon').removeClass('glyphicon glyphicon-minus-sign');
                  $('#addPromoCmsGlyhicon').addClass('glyphicon glyphicon-plus-sign');
          });

         $('#emailTemplateContent').summernote({
            height: 300   //set editable area's height
          });
        if($("#msgTemplateContent").length > 0) {
            $('#msgTemplateContent').summernote({
                height: 300   //set editable area's height
            });
        }

         $('#form-editcmsfootercontent').submit( function(e) {
              var code = $('#emailTemplateContent').code();
              // console.log(code);
              if(code != '<p><br></p>'){
                $('.footercontentContent').val(code);
                return true;
              }

          });

        $('#form-editMsgTplfootercontent').submit( function(e) {
            var code = $('#msgTemplateContent').code();
                if(code != '<p><br></p>'){
                    $('.footercontentContent').val(code);
                    return true;
                }
        });

         //cancel edit email
          $(".editfootercontentcms-cancel-btn").click(function () {
              is_editPanelVisible = false;
              $('.edit_cmsfootercontent_sec').hide();
              $('#email_promotion_template').hide();
              $('#email_aff_plyr_registration_template').hide();
              $('#email_aff_track_code_list_template').hide();
          });

    },

    showPromoActivateFormSettings : function(promoId,promoCode,promoName){
          // console.log(promoId+','+promoCode);
          $('#addPromoSetting').show();
          $('#promoName').val(promoName);
          $('#currentPromoCode').val(promoCode);
    },

    getPromoCmsDetails : function(promocmsId) {
      is_editPanelVisible = true;
      $('.add_promocms_sec').hide();
      $('.edit_promocms_sec').show();
      $.ajax({
          'url' : _site_url + 'cms_management/getPromoCmsDetails/' + promocmsId,
          'type' : 'GET',
          'dataType' : "json",
          'success' : function(data){

                // console.log(data);
                $('#editPromoCode').val(data.promo_code);
                $('#editPromocmsId').val(promocmsId);
                $('#editPromoName').val(data.promoName);
                $('#u_visit_limit').val(parseInt(data.visit_limit));
                $('#editPromoThumbnail').val(data.promoThumbnail);
                $('#editLanguage').val(data.language);
                $('.editPromoCMSCode').text(data.promo_code);

                if(data.hide_on_player=='1'){
                  $('.hide_on_player').prop('checked', true);
                }else{
                  $('.hide_on_player').prop('checked', false);
                }

                if(data.promo_code == null){
                  $('.editPromoCodeLinkSec').hide();
                }else{
                  $('.editPromoCodeLinkSec').show();
                }

                $('#editPromoDescription').val(data.promoDescription);
                $('#editPromoDetails').summernote({focus: true});
                $('#editPromoDetails').code(data.promoDetails);

                $('#editPromoLink').val(data.promoId);
                if(data.promoThumbnail){
                 $('#editPromoThumbnailImg').attr('src',(_site_url+'resources/images/promothumbnails/'+data.promoThumbnail));
                }

                // for(var i=0; i<data.promoCategory.length; i++){
                //   $('#editPromoCategory'+data.promoCategory[i].promoCmsCatId).prop('checked', true);
                // }
              }
      },'json');
      return false;
    },

    getMsgCmsDetails : function(emailcmsId) {
        is_editPanelVisible = true;
        $('.edit_cmsfootercontent_sec').show();
        $.ajax({
            'url' : _site_url + 'cms_management/getMsgCmsDetails/' + emailcmsId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                $('#editMsgTemplateId').val(data.id);
                $('#msgTemplateName').val(data.note);
                if($("#msgTemplateContent").length > 0) {
                    $('#msgTemplateContent').code(data.template);
                }


            }
        },'json');
        return false;
    },

};

// ----------------------------------------------------------------------------------------------------------------------------- //

// sidebar.php
$(document).ready(function() {
    var url = document.location.pathname;
    var res = url.split("/");

    for (i = 0; i < res.length; i++) {
        switch(res[i]){
            case 'viewGameManager':
            case 'sortGame':
                $("a#view_logs").addClass("active");
                break;

            case 'viewPromoManager':
                $("a#view_report").addClass("active");
                break;

            case 'viewNews':
            case 'addNews':
            case 'editNews':
                $("a#view_news").addClass("active");
                break;

            case 'viewNewsCategory':
            case 'addNewsCategory':
            case 'editNewsCategory':
                $("a#view_news_category").addClass("active");
                break;

            case 'sms_manager_views':
            case 'sms_manager_add':
            case 'sms_manager_edit':
            case 'sms_activity_views':
            case 'sms_activity_add':
            case 'sms_activity_edit':
                $("a#sms_manager").addClass("active");
                $("#collapseSubmenu").addClass("in");
                break;

            case 'viewBannerManager':
                $("a#view_report").addClass("active");
                break;

            case 'promoSettingList':
                $("a#view_promo_list").addClass("active");
                break;

            case 'viewEmailTemplateManager':
            case 'viewEmailTemplateManagerDetail':
                $("a#view_emailcms").addClass("active");
                break;

            case 'generateSites':
            case 'generateSitesNow':
                $("a#view_generate_sites").addClass("active");
                break;

            case 'smtp_setting':
                $("a#view_smtp_setting").addClass("active");
                break;

            case 'staticSites':
                $("a#view_static_site").addClass("active");
                break;

            case 'player_center_settings':
                $("a#view_player_center_settings").addClass("active");
                break;

            case 'viewMetaData':
                $("a#metadata_manager").addClass("active");
                break;

            default:
                break;
        }
    }

    $(".edit").tooltip({
        placement: "left",
        title: "Edit this News",
    });

    $(".delete").tooltip({
        placement: "top",
        title: "Delete this News",
    });

    $(".shows").tooltip({
        placement: "top",
        title: "Show this News",
    });

    $(".hides").tooltip({
        placement: "top",
        title: "Hide this News",
    });

    $(".filter").tooltip({
        placement: "left",
        title: "Where to show News",
    });
});
// end of sidebar.php

// ----------------------------------------------------------------------------------------------------------------------------- //

// view_news.php
function deleteNews(news_id, title) {
    if (confirm('Are you sure you want to delete this news: ' + title)) {
      window.location = _site_url + "cms_management/deleteNews/" + news_id + '/' + encode64(title);
    }
}

function deleteMetaData(id, title) {
    if (confirm('Are you sure you want to delete this MetaData: ' + title)) {
        window.location = _site_url + "cms_management/deleteMetaData/" + id + '/' + encode64(title);
    }
}

// view_news_category.php
function deleteNewsCategory(category_id, name) {
    if (confirm('Are you sure you want to delete this news category: ' + name)) {
      window.location = _site_url + "cms_management/deleteNewsCategory/" + category_id + '/' + encode64(name);
    }
}

// sms_manager_views.php
function deleteManagerMsg(id) {
    if (confirm('Are you sure you want to delete it?')) {
        window.location = _site_url + "cms_management/sms_manager_delete/" + id;
    }
}

// sms_activity_views.php
function deleteActivityMsg(id) {
    if (confirm('Are you sure you want to delete it?')) {
        window.location = _site_url + "cms_management/sms_activity_delete/" + id;
    }
}

function encode64(input) {
     input = escape(input);
     var output = "";
     var chr1, chr2, chr3 = "";
     var enc1, enc2, enc3, enc4 = "";
     var i = 0;

     var keyStr = "ABCDEFGHIJKLMNOP" +
               "QRSTUVWXYZabcdef" +
               "ghijklmnopqrstuv" +
               "wxyz0123456789";

     do {
        chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);

        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;

        if (isNaN(chr2)) {
           enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
           enc4 = 64;
        }

        output = output +
           keyStr.charAt(enc1) +
           keyStr.charAt(enc2) +
           keyStr.charAt(enc3) +
           keyStr.charAt(enc4);
        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";
     } while (i < input.length);

     return output;
  }

  function get_news_pages(segment) {
      var xmlhttp = GetXmlHttpObject();

      if (xmlhttp == null) {
          alert("Browser does not support HTTP Request");
          return;
      }

      url = _site_url + "cms_management/getNewsPages/" + segment + "/language";

      var div = document.getElementById("newsList");

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
// end of view_news.php

// ----------------------------------------------------------------------------------------------------------------------------- //
function get_game_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = _site_url + "cms_management/getGamePages/" + segment;

    var div = document.getElementById("paymentList");

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

function get_sort_game_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = _site_url + "cms_management/getSortGamePages/" + segment;

    var div = document.getElementById("paymentList");

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

//banner
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#' + input.name).attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

// banner_settings.php
function setURL(value) {
    var val = value;
    var res = val.split("\\");

    document.getElementById('banner_url').value = _site_url + 'resources/images/banner/' + res;
    // console.log(res[0]);
}

function setURLEditPromoThumbnail(input) {
    // var val = value;
    // var res = val.split("\\");

    // document.getElementById('banner_url').value = _site_url + 'resources/images/promothumbnails/' + res[2];

    // $('#editPromoThumbnailImg').attr('src',(_site_url+'resources/images/promothumbnails/'+res[2]));
    // $('#editPromoThumbnailUrl').val(_site_url+'resources/images/promothumbnails/'+res[2]);

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            // console.log(e.target.result);
            $('#editPromoThumbnailImg').attr('src', e.target.result);
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

function searchPromoCms() {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var search = document.getElementById('search').value;

    url = _site_url + "cms_management/searchPromoCms/" + search;

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

function get_promosetting_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = _site_url + "cms_management/get_promosetting_pages/" + segment;

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

function sortPromoCms(sort) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = _site_url + "cms_management/sortPromoCms/" + sort;

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

function sortNews(sort, segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = _site_url + "cms_management/getNewsPages/0/" + sort;

    var div = document.getElementById("newsList");

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