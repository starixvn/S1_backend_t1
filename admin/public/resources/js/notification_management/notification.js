var NotificationManagement = {

	$path: '',

	init: function(path){

		var self = this;

		self.$path = path;

		$('#notif-sound').on('change', function(){

			var notifId = $(this).val();
			var sound = $('input[name="notif_' + notifId + '"]').val();

			self.notificationPlay( sound );

		});

	},

	delete: function(id, msg){

        var self = this;

        if (confirm(msg)) {
            window.location = base_url + "notification_management/delete/" + id;
        }

    },

    deleteSelected: function(msg){

        var self = this,
            ids = '',
            fld = $('input[name="item_id"]:checked');

        if( fld.length == 0 ) return false;

        if (confirm(msg)) {

            fld.each(function(){

                if( ids == '' ){

                    ids = $(this).val();

                }else{

                    ids += ',' + $(this).val();

                }

            });

            $.ajax({
                type: 'POST',
                url: base_url + 'notification_management/delete_multiple',
                data: {
                    ids:ids
                },
                async: false,
                success: function(){
                    window.location = base_url + 'notification_management';
                }
            });

        }

    },

    setSoundNotif: function( id ){

        var self = this;

        $('.notif_form').removeClass('hide');
        $('#notif_id').val(id);

        $('.set-notif').on('click', function(){

            if( $('#notif-sound').val() == "" ) return false;

            var form = $('form[id="set_notif"]').serialize();

            $.ajax({
                url: base_url + 'notification_management/set_notification',
                type: 'POST',
                data: form,
                success: function(){
                    window.location = base_url + "notification_management/settings";
                }
            });

        });

    },

    removeNotification: function( msg, id ){

        var self = this;

        if( confirm(msg) ){

            window.location = base_url + 'notification_management/remove_notification/' + id;

        }

    },

    notificationPlay: function( file, currencyKeyOnMDB){

        var self = this;
        console.log(self.$path + '/upload/notifications/' + file);
        if( ringSound == "undefined" || ringSound == undefined ) var ringSound = new Audio();
        if( currencyKeyOnMDB == "undefined" || currencyKeyOnMDB == undefined || currencyKeyOnMDB == ''){
            ringSound.src = self.$path + 'upload/notifications/' + file;
        }else{
            ringSound.src = self.$path + 'upload/notifications/' + currencyKeyOnMDB + '/' + file;
            console.log(ringSound.src);
        }

        ringSound.play();

    }
}