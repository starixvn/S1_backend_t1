var RegisteredAutoPlayProcedure = {
    /** tutorial params */
    is_tutorial_done : 0,

    /** vip params */
    playerId : "",
    joinVipUrl : "",
    vip_count : "",
    is_vip_show_done : "",
    is_vip_callback : 0,

    /** tutorial params */
    enable_registered_show_popup : 0,
    is_registered_popup_success_done : "",

    queueList: ['setIsTutorialDone', 'registeredPopUp', 'chooseVipGroup'],

    is_init : false,
    init : function() {
        if (!this.is_init) {

            if (this.is_vip_show_done) {
                this.queueList.splice($.inArray('chooseVipGroup', this.queueList), 1);
            }

            if (this.is_tutorial_done) {
                this.queueList.splice($.inArray('setIsTutorialDone', this.queueList), 1);
            }

            if (!this.enable_registered_show_popup || this.is_registered_popup_success_done) {
                this.queueList.splice($.inArray('registeredPopUp', this.queueList), 1);
            }

            this.is_init = true;
        }
    },
    
    execute : function() {
        this.init();

        var action = this.queueList.shift(),
            endPlay = this.queueList.length - 1;

        switch (action) {
            case 'chooseVipGroup':
                chooseVipGroup();
                break;
            case 'registeredPopUp':
                registeredSuccesePopUp();
                break;
            case 'setIsTutorialDone':
                setIsTutorialDone();
                break;
            default: return;
        }
    }, 
    complete : function (callback) {
        this.execute();
    }
}

function registeredSuccesePopUp() {
    $("#registered-modal").modal('show');
    $("#registered-modal").on('hidden.bs.modal', function (e) {
        setTimeout(RegisteredAutoPlayProcedure.complete(), 2000);
    });
    $.post(base_url + "player_center/setIsRegisterPopUpDone");
}

function chooseVipGroup () {
    $('#vip-group-modal').modal('show');
    $("#vip-group-modal").on('hidden.bs.modal', function (e) {
        if (!RegisteredAutoPlayProcedure.is_vip_callback) {
            setTimeout(RegisteredAutoPlayProcedure.complete(), 2000);
        }
    });
    $.post(base_url + "player_center/setIsVIPShowDone");
}

function setIsTutorialDone() {
    $.post(base_url + "player_center/setIsTutorialDone");
    setTimeout(RegisteredAutoPlayProcedure.complete(), 1000);
}
 
$(document).on("click", "#vip-group-modal .btn_join", function () {
    var vipId = $(this).data("id"),
        groupName = $(this).data("gname"),
        levelName = $(this).data("lname"),
        playerId = RegisteredAutoPlayProcedure.playerId;
        joinVipUrl = RegisteredAutoPlayProcedure.joinVipUrl;
        level = $(this).data("level");
        data = {
            newPlayerLevel: level,
            groupName: groupName,
            levelName: levelName,
            vipId: vipId,
            playerId: playerId
        };

    $.ajax({
        url: RegisteredAutoPlayProcedure.joinVipUrl,
        type: 'POST',
        data: data,
        dataType: "json",
        cache: false,
    }).done(function (data) {
        if (data.status == "success") {
            RegisteredAutoPlayProcedure.is_vip_callback = 1;
            $('#vip-group-modal').modal('hide');
            $('#vip-group-modal').on('hidden.bs.modal', function () {
                load_alert_message(data.status, data.msg, function() {
                    setTimeout(RegisteredAutoPlayProcedure.complete(), 2000);
                });
            });
            loadPlayerVipGroupDetails();
        }
        if (data.status == "error") {
            load_alert_message(data.status, data.msg);
        }
    }).fail(function (jqXHR, textStatus) {
        if (jqXHR.status >= 300 && jqXHR.status < 500) {
            location.reload();
        } else {
            load_alert_message("error", textStatus);
        }
    });
});