//general
var base_url = "/";
var imgloader = "/resources/images/ajax-loader.gif";

$(document).ready(function(){
    var url = document.location.pathname;
    var res = url.split("/");
    CMSBannerManagementProcess.initialize();
     for (i = 0; i < res.length; i++) {
        switch(res[i]){
            case 'viewBannerManager':
                $("a#view_banner_mgr").addClass("active");
                break;
            default:
                break;
        }
    }
});

//cms management module
var CMSBannerManagementProcess = {

    initialize: function(){
        // console.log("initialized now!");
        //tooltip
        $('body').tooltip({
            selector: '[data-toggle="tooltip"]'
        });

        $('.edit_cmsbanner_sec').hide();

        //show hide add vip group panel
        var is_editPanelVisible = false;
        $("#add_cmsbanner_sec").click(function(){
            if(!is_editPanelVisible){
                is_editPanelVisible = true;
                $('.edit_cmsbanner_sec').show();
                CMSBannerManagementProcess.showBannerCmsForm();
            }else{
                is_editPanelVisible = false;
                $('.edit_cmsbanner_sec').hide();
            }
        });

        //show hide edit vip group panel
        $(".editBannerCmsBtn").click(function(){
            is_editPanelVisible = true;
            $('.edit_cmsbanner_sec').show();
            CMSBannerManagementProcess.showBannerCmsForm();
        });

        //cancel add promo
        $(".addbanner-cancel-btn").click(function(){
            is_editPanelVisible = false;
            $('.edit_cmsbanner_sec').hide();
        });

        //cancel edit promo
        $(".editbannercms-cancel-btn").click(function(){
            is_editPanelVisible = false;
            $('.edit_cmsbanner_sec').hide();
        });

    },

    showBannerCmsForm: function(data){
        if("object" === typeof data){
            $('#editBannercmsId').val(data.bannerId);

            $('#userfile').val('');
            $('#editCategory').val(data.category);
            $('#editlanguage').val(data.language);

            $('#editBannerCmsImg').attr('src', (data.banner_img_url)).show();

            $('#cmsbanner_title').val(data.title);
            $('#cmsbanner_summary').val(data.summary);
            $('#cmsbanner_link').val(data.link);
            $('#cmsbanner_link_target').val(data.link_target);
        }else{
            $('#editBannercmsId').val('');

            $('#userfile').val('');
            $('#editCategory').val($($('#editCategory option')[1]).attr('value'));
            $('#editlanguage').val($($('#editlanguage option')[1]).attr('value'));

            $('#editBannerCmsImg').attr('src', 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs=').hide();

            $('#cmsbanner_title').val('');
            $('#cmsbanner_summary').val('');
            $('#cmsbanner_link').val('');
            $('#cmsbanner_link_target').val($($('#cmsbanner_link_target option')[1]).attr('value'));
        }
    },

    getBannerCmsDetail: function(bannercmsId){
        is_editPanelVisible = true;
        $('.edit_cmsbanner_sec').show();
        $.ajax({
            'url': base_url + 'cmsbanner_management/getBannerCmsDetails/' + bannercmsId,
            'type': 'GET',
            'dataType': "json",
            'success': function(response){
                if(response.status === 'success'){
                    CMSBannerManagementProcess.showBannerCmsForm(response.data);
                }
            }
        }, 'json');
        return false;
    },

    delBannerCmsDetail: function(bannercmsId){
        var url = base_url + 'cmsbanner_management/deleteBannerCmsItem/' + bannercmsId;

        var modal = $('.del_cms_banner_modal').modal('hide');

        $('.del_cms_banner_modal .btn-danger').off('click').on('click', function(){
            window.location.href = url;
            modal.modal('hide');
        });
        $('.del_cms_banner_modal .btn-close').off('click').on('click', function(){
            modal.modal('hide');
        });
        modal.modal('show');
    },

    deleteSelected: function(form){
        var modal = $('.del_cms_banner_modal').modal('hide');

        $('.del_cms_banner_modal .btn-danger').off('click').on('click', function(){
            form.submit();
            modal.modal('hide');
        });
        $('.del_cms_banner_modal .btn-close').off('click').on('click', function(){
            modal.modal('hide');
        });
        modal.modal('show');
    }
};

function setURLEditBannerCms(input){
    if(input.files && input.files[0]){
        var reader = new FileReader();

        reader.onload = function(e){
            // console.log(e.target.result);
            $('#editBannerCmsImg').attr('src', e.target.result);
            $('#editBannerCmsImg').show();
        };

        reader.readAsDataURL(input.files[0]);
    }
}

//for multi select
function checkAll(id){
    var list = document.getElementsByClassName(id);
    var all = document.getElementById(id);

    if(all.checked){
        for(i = 0; i < list.length; i++){
            list[i].checked = 1;
        }
    }else{
        all.checked;

        for(i = 0; i < list.length; i++){
            list[i].checked = 0;
        }
    }
}

function uncheckAll(id){
    var list = document.getElementById(id).className;
    var all = document.getElementById(list);

    var item = document.getElementById(id);
    var allitems = document.getElementsByClassName(list);
    var cnt = 0;

    if(item.checked){
        for(i = 0; i < allitems.length; i++){
            if(allitems[i].checked){
                cnt++;
            }
        }

        if(cnt == allitems.length){
            all.checked = 1;
        }
    }else{
        all.checked = 0;
    }
}