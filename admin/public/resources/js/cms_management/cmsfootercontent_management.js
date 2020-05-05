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
  	CMSFootercontentManagementProcess.initialize();
});

//cms management module
var CMSFootercontentManagementProcess = {

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

          $("#add_cmsfootercontent_sec").tooltip({
              placement: "left",
              title: "Add new footercontent",
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

                  case 'viewContentManager':
                      $("a#view_footercontent_mgr").addClass("active");
                      break;

                  default:
                      break;
              }
          }

          //$('.summernote').summernote();
          $('#summernote').summernote({
            height: 300,   //set editable area's height
          });

          $('#editFootercontentDetails').summernote({
            height: 300,   //set editable area's height
          });

          //add cms promo details
          $('#form-cmsfootercontent').submit( function(e) {
              var code = $('#summernote').code();
              // console.log(code);
              if(code != '<p><br></p>'){
                $('.footercontentContent').val(code);
                return true;
              }
          });
          $('#form-cmsfootercontent').submit( function(e) {
              var code = $('#summernoteCN').code();
              // console.log(code);
              if(code != '<p><br></p>'){
                $('.footercontentContent').val(code);
                return true;
              }
          });

          $('#form-editcmsfootercontent').submit( function(e) {
              var code = $('#editFootercontentDetails').code();
              // console.log(code);
              if(code != '<p><br></p>'){
                $('.footercontentContent').val(code);
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

          //for add vip group panel
          var is_addPanelVisible = false;

          //for ranking level edit form
          var is_editPanelVisible = false;

          if(!is_addPanelVisible){
              $('.add_cmsfootercontent_sec').hide();
          }else{
              $('.add_cmsfootercontent_sec').show();
          }

          if(!is_editPanelVisible){
              $('.edit_cmsfootercontent_sec').hide();
          }else{
              $('.edit_cmsfootercontent_sec').show();
          }

          //show hide add vip group panel
          $("#add_cmsfootercontent_sec").click(function () {
              if(!is_addPanelVisible){
                  is_addPanelVisible = true;
                  $('.add_cmsfootercontent_sec').show();
                  $('.edit_cmsfootercontent_sec').hide();
                  $('#addFootercontentCmsGlyhicon').removeClass('glyphicon glyphicon-plus-sign');
                  $('#addFootercontentCmsGlyhicon').addClass('glyphicon glyphicon-minus-sign');
              }else{
                  is_addPanelVisible = false;
                  $('.add_cmsfootercontent_sec').hide();
                  $('#addFootercontentCmsGlyhicon').removeClass('glyphicon glyphicon-minus-sign');
                  $('#addFootercontentCmsGlyhicon').addClass('glyphicon glyphicon-plus-sign');
              }
          });

          //show hide edit vip group panel
          $(".editFootercontentCmsBtn").click(function () {
                  is_editPanelVisible = true;
                  $('.add_cmsfootercontent_sec').hide();
                  $('.edit_cmsfootercontent_sec').show();
          });

          //cancel add promo
          $(".addfootercontent-cancel-btn").click(function () {
                  is_addPanelVisible = false;
                  $('.add_cmsfootercontent_sec').hide();
                  $('#addFootercontentCmsGlyhicon').removeClass('glyphicon glyphicon-minus-sign');
                  $('#addFootercontentCmsGlyhicon').addClass('glyphicon glyphicon-plus-sign');
          });

          //cancel edit promo
          $(".editfootercontentcms-cancel-btn").click(function () {
                  is_editPanelVisible = false;
                  $('.edit_cmsfootercontent_sec').hide();
          });

    },

    getFootercontentCmsDetails : function(footercontentcmsId) {
      is_editPanelVisible = true;
      $('.add_cmsfootercontent_sec').hide();
      $('.edit_cmsfootercontent_sec').show();
      $.ajax({
          'url' : base_url + 'cmsfootercontent_management/getFootercontentCmsDetails/' + footercontentcmsId,
          'type' : 'GET',
          'dataType' : "json",
          'success' : function(data){
                   // console.log(data[0]);
                   $('#editFootercontentcmsId').val(data[0].footercontentId);
                   $('#editFootercontentDetails').code(data[0].content);
                   $('#editFootercontentName').val(data[0].footercontentName);
                   $('#editFootercontentLanguage').val(data[0].language);
                   //$('#editPromoDetails').code(data[0].promoDetails);

                   //$('#userfile').val(data[0].promoThumbnail);
                   $('#editFootercontentCmsImg').attr('src',(base_url+'resources/images/cmsfootercontent/'+data[0].footercontentName));
                  }
      },'json');
      return false;
    },
};

// ----------------------------------------------------------------------------------------------------------------------------- //

// footercontent_settings.php
function setURL(value) {
    var val = value;
    var res = val.split("\\");

    document.getElementById('footercontent_url').value = base_url + 'resources/images/footercontent/' + res[2];
}

function setURLEditFootercontentCms(input) {
    // var val = value;
    // var res = val.split("\\");

    // document.getElementById('footercontent_url').value = base_url + 'resources/images/promothumbnails/' + res[2];

    // $('#editPromoThumbnailImg').attr('src',(base_url+'resources/images/promothumbnails/'+res[2]));
    // $('#editPromoThumbnailUrl').val(base_url+'resources/images/promothumbnails/'+res[2]);

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            // console.log(e.target.result);
            $('#editFootercontentCmsImg').attr('src', e.target.result);
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

function searchFootercontentCms() {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var search = document.getElementById('search').value;

    url = base_url + "cmsfootercontent_management/searchFootercontentCms/" + search;

    var div = document.getElementById("cmsfootercontent_table");

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

function get_footercontentcms_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "cmsfootercontent_management/get_promosetting_pages/" + segment;

    var div = document.getElementById("footercontentcms_table");

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

function sortFootercontentCms(sort) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "cms_management/sortFootercontentCms/" + sort;

    var div = document.getElementById("footercontentcms_table");

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