var PlayerAttachDocument = {
	current_kyc_status: '',
	allowed_withdrawal_status: '',
	confirmed_delete: '',
    confirmed_visible: '',
    confirmed_not_visible: '',
    txt_comment:'',

	init: function() {
		var self = this;
		$('#kyc_status').text(self.current_kyc_status);
	    $('#allowed_withdrawal_status').text(self.allowed_withdrawal_status);
	    self.initTextarea();
	},

	openNav: function() {
		var self = this;
        document.getElementById("overlay").style.width = "100%";
    },

    closeNav: function() {
		var self = this;
        document.getElementById("overlay").style.width = "0%";
    },

    getFileData: function(myFile) {
		var self = this;
		var file = myFile.files[0];  
		var filename = file.name;
		$(myFile).parent().find("span").text(filename);
    },

    removeImage: function( picID , playerId , tag ) {
		var self = this;
        if (confirm(self.confirmed_delete)) {
        	$.post('/player_management/delKYCPlayerImage/', {'picId' : picID , 'playerId' : playerId, 'tag' : tag} ,function(data){
                alert(data.msg);
                modal('/player_management/player_attach_document/'+playerId);
	        });
        } else {
            event.preventDefault();
        }
    },

    submit_form: function() {
		var self = this;
    },

    InitRemarks: function(fields) {
		var self = this;
		$(fields).parent().find('.hidden-remarks').val($(fields).val());
    },

    InitComments: function(fields) {
		var self = this;
		$(fields).parent().find('.hidden-comments').val($(fields).val());
    },

    initTextarea: function() {
	    var self = this;
        var one_row_imgs = 4;
        var default_rows = 2;
        $.each(self.txt_comment, function (i, val){
            var total_imgs = $('#form_' + val + ' .image-container .multi-img').size();
            var need_generate = false;
            var total_rows = default_rows;

            if(total_imgs > 0){
                total_rows = Math.ceil(total_imgs / one_row_imgs);
                need_generate = total_rows > default_rows;
            }

            if(need_generate){
                var height = (total_rows*50-30) + 'px'; //50 id for img height, 30 is for non textarea
                $('#txt_comments_'+val).css('height', height);
            }
        });
    },

    updateVisibilitystatus: function(action, picId, playerId, tag) {
        var self = this;
        if(action === "visible"){
            var confirmation = self.confirmed_visible;
        } else {
            var confirmation = self.confirmed_not_visible;
        }
        if (confirm(confirmation)) {
            $.post('/player_management/updateVisibilitystatus/', {'action' : action , 'playerId' : playerId, 'picId' : picId, 'tag' : tag} ,function(data){
                alert(data.msg);
                modal('/player_management/player_attach_document/'+playerId);
            });
        }
    },
}

$(document).on("click",".img_thumbnail",function() {
    $src = $(this).find('img').attr("src");
    $(".overlay").find(".img_container").find("img").attr("src",$src);
    $uploadedBy = $(this).parent().find(".uploaded-by").val();
    $(".overlay").find(".img-info").find(".overlay-uploaded-by").html($uploadedBy);
    $dateUploaded = $(this).parent().find(".timestamp").val();
    $(".overlay").find(".img-info").find(".overlay-timestamp").html($dateUploaded);
    
    $visibleToPlayer = $(this).parent().find(".visible_to_player").val();

    $picid = $(this).data('picid');
    $tag = $(this).data('tag');
    $playerid = $(this).data('playerid');
    $(".overlay").find(".image-on-hover").find(".delete-btn").attr('onclick',"return PlayerAttachDocument.removeImage("+$picid+","+$playerid+","+"'"+$tag+"'"+");");

    if($visibleToPlayer === '1'){
        $('.not-visible-to-player').hide();
        $('.visible-to-player').show();
        $(".overlay").find(".image-on-hover").find(".visible-to-player").attr('onclick',"return PlayerAttachDocument.updateVisibilitystatus('not_visible',"+$picid+","+$playerid+","+"'"+$tag+"'"+");");
    } else {
        $('.visible-to-player').hide();
        $('.not-visible-to-player').show();
        $(".overlay").find(".image-on-hover").find(".not-visible-to-player").attr('onclick',"return PlayerAttachDocument.updateVisibilitystatus('visible',"+$picid+","+$playerid+","+"'"+$tag+"'"+");");

    }

    PlayerAttachDocument.openNav();
});

$(document).on("click",".add-image-btn",function() {
    $(this).closest("form").find('.custom-file-upload').click();
});
