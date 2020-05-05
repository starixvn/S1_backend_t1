(function(window){
    function PlayerMessages(options){
        this.options = {
            "site_url": document.location.origin
        };

        this.options = $.extend({}, this.options, options);
    }

    PlayerMessages.prototype.init = function(){
        this.initEvent();
    };

    PlayerMessages.prototype.initEvent = function(){
        var self = this;

        $('.msg-btn').on('click', function(){
            $('#composeMsg').modal('show');
        });

        $('.sendMsg').on('click', function(){
            Loader.show();

            var callback = function(){
                window.location.reload();
            };

            self.sendNewMessage(function(data){
                Loader.hide();
                $("#composeMsg").modal("hide");

                if (data.status == 'success') {
                    MessageBox.success(data.message, null, callback);
                } else {
                    MessageBox.danger(data.message, null, callback);
                }
            });

            return false;
        });
    };

    PlayerMessages.prototype.sendNewMessage = function (callback) {
        var self = this;

        var subjectTitle = $("#subjectTitle").val();
        var message = $("#text-new-msg").val();

        $.post(self.options.site_url + '/player_center2/messages/addMessages', {'subject': subjectTitle, 'message': message}, function (data) {
            $("#subjectTitle").val("");
            $("#text-new-msg").val("");

            if(typeof callback === "function") callback(data);
        });
    };

    window.PlayerMessages = new PlayerMessages();
})(window);