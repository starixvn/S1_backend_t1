//general
var base_url = "/";

// sidebar.php
$(document).ready(function() {
    $('#collapseSubmenu').addClass('in');
    $('#view_payment_settings').addClass('active');
    $('#viewDispatchAccount').addClass('active');
});
// end of sidebar.php

// ----------------------------------------------------------------------------------------------------------------------------- //

//player management module
var DispatchAccountManagementProcess = {

    initializeGroupList : function() {
        //tooltip
        $('body').tooltip({
            selector: '[data-toggle="tooltip"]'
        });

        //for add dispatch group panel
        var is_addPanelVisible = false;

        //for edit dispatch group panel
        var is_editPanelVisible = false;

        if(!is_addPanelVisible){
            $('.add_dispatch_group_sec').hide();
        }else{
            $('.add_dispatch_group_sec').show();
        }

        if(!is_editPanelVisible){
            $('.edit_dispatch_group_sec').hide();
        }else{
            $('.edit_dispatch_group_sec').show();
        }

        //show hide add dispatch group panel
        $("#add_dispatch_group").click(function () {
            if(!is_addPanelVisible){
                is_addPanelVisible = true;
                $('.add_dispatch_group_sec').show();
                $('.edit_dispatch_group_sec').hide();
                $('#add_dispatch_account_group_glyhicon').removeClass('glyphicon glyphicon-plus-sign');
                $('#add_dispatch_account_group_glyhicon').addClass('glyphicon glyphicon-minus-sign');
            }else{
                is_addPanelVisible = false;
                $('.add_dispatch_group_sec').hide();
                $('#add_dispatch_account_group_glyhicon').removeClass('glyphicon glyphicon-minus-sign');
                $('#add_dispatch_account_group_glyhicon').addClass('glyphicon glyphicon-plus-sign');
            }
        });

        //cancel add dispatch group
        $(".add_dispatch_group_cancel_btn").click(function () {
                is_addPanelVisible = false;
                $('.add_dispatch_group_sec').hide();
                $('#add_dispatch_account_group_glyhicon').removeClass('glyphicon glyphicon-minus-sign');
                $('#add_dispatch_account_group_glyhicon').addClass('glyphicon glyphicon-plus-sign');
        });

        //show/hide edit dispatch group panel
        $(".edit_dispatch_group_btn").click(function () {
                is_editPanelVisible = true;
                $('.add_dispatch_group_sec').hide();
                $('.edit_dispatch_group_sec').show();
        });

        //cancel edit dispatch group
        $(".edit_dispatch_group_cancel_btn").click(function () {
                is_editPanelVisible = false;
                $('.edit_dispatch_group_sec').hide();
        });
    },

    getDispatchAccountGroupDetails : function(group_id,currentLang) {
        is_editPanelVisible = true;
        $('.add_dispatch_group_sec').hide();
        $('.edit_dispatch_group_sec').show();
        $.ajax({
            'url' : base_url + 'dispatch_account_management/getDispatchAccountGroupDetails/' + group_id,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                console.log(data);
                $('#edit_group_id').val(group_id);
                $('#edit_group_level_count').val(data.group_level_count);
                if (data.group_name.toLowerCase().indexOf("_json:") >= 0){
                    var langConvert = jQuery.parseJSON(data.group_name.substring(6));
                    $('#editGroupName').val(data.group_name);
                } else {
                    $('#editGroupName').val(data.group_name);
                }
                $('#editGroupDescription').val(data.group_description);
            }
        },'json');
        return false;
    },
};

function refreshPlayerBelongLevel() {
    $('#refresh-loader').show();
    $("#refresh_player_belong_level").prop("onclick", null).off("click");

    $.ajax({
        'url' : base_url + 'dispatch_account_management/refreshPlayersDispatchAccountLevel',
        'type' : 'GET',
        'dataType' : "json",
        'success' : function(result_data){
            if(result_data.success == true) {
                BootstrapDialog.show({
                    type: BootstrapDialog.TYPE_SUCCESS,
                    title: 'Successfully Refreshed',
                    message: result_data.msg,
                    buttons: [{
                        label: 'Close',
                        cssClass: 'btn-linkwater',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            }
            else {
                BootstrapDialog.show({
                    type: BootstrapDialog.TYPE_DANGER,
                    title: 'Refreshed Failed',
                    message: result_data.msg,
                    buttons: [{
                        label: 'Close',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            }
            $('#refresh-loader').hide();
            $("#refresh_player_belong_level").attr("onclick", 'refreshPlayerBelongLevel()');
        }
    },'json');
}