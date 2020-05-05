var Promotions = {
	site_url : document.location.origin,
	currency_symbol : '',

	viewPromoDetailsAll : function (objCalling, playerId) {
		var self = this;
		var first_child = 0;
		var promoId = objCalling.id;

		$('#promoMsg').hide();
		$('#promoMsgSec').hide();
		$('#promoMsg').html("");
		$("#promoItemPreviewImg").attr("src",self.site_url + "/resources/images/promothumbnails/loader.gif");
		$("#promoItemPreviewImg").css({"padding" : "100px 0", "margin" : "0 auto", "display" : "block"});

		$("#dateApplied").hide();
		$("#closeModal").hide();
		$(".applyBtn").show();
		$('.applyBtn').hide();
		$('.reject-mesg').text('').hide();
		$('#promo-bonus-amount').text(self.currency_symbol + " ---");

		$.ajax({
			// 'url' : self.site_url + "/player_center/getPromoCmsItemDetailsByPlayerId/allpromo/" + playerId + "/" + promoId,
			'url' : self.site_url + "/player_center/getPromoCmsItemDetailsByPlayerId/promojoint/" + playerId + "/" + promoId,
			'type' : 'GET',
			'dataType' : "json",
			'success' : function (data) {
				var fpromo = data.promo_list[first_child];
				var bonusAmount = 0;

				if (fpromo.player_promo != undefined ) {
					bonusAmount = fpromo.player_promo.bonusAmount;
				}
				else if(fpromo.promorule.bonusAmount == 0){
					var depositPercentage = fpromo.promorule.depositPercentage;
					var nonfixedDepositMinAmount = fpromo.promorule.nonfixedDepositMinAmount;
					bonusAmount = nonfixedDepositMinAmount * (depositPercentage/100);
				}else{
					bonusAmount = fpromo.promorule.bonusAmount;
				}

				if(typeof fpromo.promorule != 'undefined' && fpromo.promorule){
					$('#promoCmsPromoTypeModal').html(fpromo.promorule.promoTypeName);
					// $('#promoCmsBonusAmountModal').html("<i class='fa fa-cubes' aria-hidden='true'></i>" + self.currency_symbol + " " + bonusAmount);
					$('#promo-bonus-amount').text(self.currency_symbol + " " + bonusAmount);
				}

				$('#promoCmsTitleModal').text(fpromo.promoName);
				$('#promoCmsPromoDetailsModal').html(_export_sbe_t1t.utils.decodeHtmlEntities(fpromo.promoDetails));
				$("#badgeNew").hide();

				var flag_true = '1';
				if(fpromo.tag_as_new_flag == flag_true){
					$("#badgeNew").show();
				}else{
					$("#badgeNew").hide();
				}

				var is_promo_disabled = fpromo.disabled;

				if(is_promo_disabled){
					$("#requestPromoBtn").prop("disabled",true);
				}else{
					$("#requestPromoBtn").prop("disabled",false);
				}

				var is_disabled_pre_application = fpromo.disabled_pre_application;
				if(is_disabled_pre_application){
					$("#preApplicationPromoBtn").prop("disabled",true);
				}else{
					$("#preApplicationPromoBtn").prop("disabled",false);
				}

                $('.applyBtn').off('click');
                if(!!parseInt(fpromo.allow_claim_promo_in_promo_page)){
                	$('.claimLinkBtn').hide();
                    if (fpromo.player_allowed_for_promo == false) {
                        $('.applyBtn').hide();
                        if(fpromo.hasOwnProperty('error_redirect_url')){
                            $('.reject-mesg').html('<a href="'+fpromo.error_redirect_url+'">'+fpromo.player_allowed_for_promo_mesg+'</a>').show();
                        }else{
                            $('.reject-mesg').html(fpromo.player_allowed_for_promo_mesg).show();
                        }

                        $('#closeModal').show();
                    }else{
                        // Allow to apply
						if(fpromo.hasOwnProperty('contact_live_chat_to_apply')){
							$('.apply-mesg').html(fpromo.contact_live_chat_to_apply).show();
							$('#closeModal').show();
						}else{
							$('.applyBtn').show();
							$('.reject-mesg').text('').hide();
							$('#closeModal').hide();
						}
                    }
                }else{
                    $('.claimLinkBtn').show();
                    $('.claimLinkBtn a').text(fpromo.claim_button_name);
                    $('.claimLinkBtn a').attr('href', fpromo.claim_button_url);

                    $('.reject-mesg').text('').hide();
                    $('#closeModal').hide();
				}

				$('#itemDetailsId').val(fpromo.promoCmsSettingId);

				$('#promoItemPreviewImg').removeAttr('style');
				if(fpromo.promoThumbnail == null || fpromo.promoThumbnail == ""){
					$("#promoItemPreviewImg").attr("src",self.site_url + "/resources/images/promothumbnails/default_promo_cms_1.jpg");
				}else{
					var imageUrl = self.site_url + fpromo.promoThumbnailPath + fpromo.promoThumbnail;
					self.imageExists(imageUrl, function(exists) {
						if(exists) {
							$("#promoItemPreviewImg").attr("src",imageUrl);
						} else {
							$("#promoItemPreviewImg").attr("src", self.site_url + "/resources/images/promothumbnails/" + fpromo.promoThumbnail);
						}
					});

				}
			}
		});
	},

	viewMyPromoDetails : function (objCalling, playerId) {
		var self = this;
		var first_child = 0;
		var promoId = objCalling.id;

		/** Initialize dialog */
		$('#promoMsg').hide();
		$('#promoMsgSec').hide();
		$('#promoMsg').html("");
		$("#promoItemPreviewImg").attr("src",self.site_url + "/resources/images/promothumbnails/loader.gif");
		$("#promoItemPreviewImg").css({"padding" : "100px 0", "margin" : "0 auto", "display" : "block"});
		$("#dateApplied").show();
		$("#closeModal").show();
		$(".applyBtn").hide();
		$('.reject-mesg').text('').hide();
		$('#promo-bonus-amount').text(self.currency_symbol + " ---");

		$.ajax({
			'url' : self.site_url + "/player_center/getPromoCmsItemDetailsByPlayerId/mypromo/" + playerId + "/" + promoId,
			'type' : 'GET',
			'dataType' : "json",
			'success' : function(data){
				$('#promoCmsPromoTypeModal').html(data[first_child].promoTypeName);
				// $('#promoCmsBonusAmountModal').html("<i class='fa fa-cubes' aria-hidden='true'></i>" + self.currency_symbol + " " +data[first_child].bonusAmount);
				$('#promo-bonus-amount').text(self.currency_symbol + " " + data[first_child].bonusAmount);

				$('#dateAppliedTxt').html(data[first_child].dateApply);
				$('#promoCmsTitleModal').text(data[first_child].promoName);
				$('#promoCmsPromoDetailsModal').html(_export_sbe_t1t.utils.decodeHtmlEntities(data[first_child].promoDetails));
				$("#badgeNew").hide();

				var flag_true = '1';
				if(data[first_child].tag_as_new_flag == flag_true){
					$("#badgeNew").show();
				}else{
					$("#badgeNew").hide();
				}


				var is_promo_disabled = data[first_child].disabled;

				if(is_promo_disabled){
					$("#requestPromoBtn").prop("disabled",true);
				}else{
					$("#requestPromoBtn").prop("disabled",false);
				}

				var is_disabled_pre_application = data[first_child].disabled_pre_application;

				if(is_disabled_pre_application){
					$("#preApplicationPromoBtn").prop("disabled",true);
				}else{
					$("#preApplicationPromoBtn").prop("disabled",false);
				}

				$('#itemDetailsId').val(data[first_child].promoCmsSettingId);

				$('#promoItemPreviewImg').removeAttr('style');
				if(data[first_child].promoThumbnail == null || data[first_child].promoThumbnail == "") {
					$("#promoItemPreviewImg").attr("src",self.site_url + "/resources/images/promothumbnails/default_promo_cms_1.jpg");
				} else {
					var imageUrl = self.site_url + data[first_child].promoThumbnailPath + data[first_child].promoThumbnail;

					self.imageExists(imageUrl, function(exists) {
						if(exists) {
							$("#promoItemPreviewImg").attr("src",imageUrl);
						} else {
							$("#promoItemPreviewImg").attr("src", self.site_url + "/resources/images/promothumbnails/" + data[first_child].promoThumbnail);
						}
					});
				}
			}
		});
	},

	requestPromoNow : function (promoCmsSettingId, callback) {
		var self = this;
		promoCmsSettingId = (promoCmsSettingId === undefined) ? $("#itemDetailsId").val() : promoCmsSettingId;
		show_loading();

		var response_data = null;
		$.getJSON(self.site_url + '/player_center/request_promo/' + promoCmsSettingId, function(data){
			$('#promoMsgSec').show();
			$('#promoMsg').show();
			$('#promoMsg').html("");
			$('#promoMsg').html("<b>"+data.msg+"</b>");
			response_data = data;
		}).always(function(){
			$(".applyBtn").hide();
			stop_loading();
			if(typeof callback === "function"){
                callback.apply(null, [response_data]);
			}
		});
	},

	preapplicationPromo : function (promoCmsSettingId, callback){
		var self = this;
        promoCmsSettingId = (promoCmsSettingId === undefined) ? $("#itemDetailsId").val() : promoCmsSettingId;
		show_loading();

        var response_data = null;
		$.getJSON(self.site_url + '/player_center/request_promo/' + promoCmsSettingId, function(data){
			$('#promoMsgSec').show();
			$('#promoMsg').show();
			$('#promoMsg').html("");
			$('#promoMsg').html("<b>"+data.msg+"</b>");
            response_data = data;
		}).always(function(){
			$(".applyBtn").hide();
			stop_loading();
            if(typeof callback === "function"){
                callback.apply(null, [response_data]);
            }
		});
	},

	imageExists : function (url, callback) {
		var img = new Image();
		img.onload = function() { callback(true); };
		img.onerror = function() { callback(false); };
		img.src = url;
	}
}