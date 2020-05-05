var KycSettings = {
	msgSubmitConfirmation : '',
	msgDeleteConfirmation : '',
	/**
	 * Get KYC Details
	 * @param  id = kyc id
	 */
	getKycDetails : function(id) {
		var self = this;
		$.post('/player_management/getKycDetails',{'id' : id},function(data){
			if(data) {
				$("#id").val(data.id);
				$("#rate_code").val(data.rate_code);
				$("#description_english").val(data.description_english);
				$("#description_chinese").val(data.description_chinese);
				$("#description_indonesian").val(data.description_indonesian);
				$("#description_vietnamese").val(data.description_vietnamese);
				$("#target_function").val(data.target_function);
				$("#kyc_lvl").val(data.kyc_lvl);
				self.viewPanel();
			}
		});
	},
	getCancel : function() {
		var self = this;
		$("input[type=text],input[type=hidden]").val(""); 
		$("#kyc-panel").hide();
	},
	viewPanel : function() {
		$("#kyc-panel").show();
	},
	viewAddPanel : function() {
		$("input[type=text],input[type=hidden]").val("");
		$("#kyc-panel").show();
	},
	submitEntry : function() {
		var self = this;
		if (confirm(self.msgSubmitConfirmation)) {
		    $("#form_kyc_settings").submit();
		}
	},
	removeDetails : function(id) {
		var self = this;
		if (confirm(self.msgDeleteConfirmation)) {
		    $.post('/player_management/removeKycEntry',{'id' : id},function(data){
				if(data.status == "success") {
					location.reload();
				} else {
					location.reload();
				}
			});
		}
	}
}

$(document).on('change', '.chartTag', function(){
    var id = $(this).attr('id');
    var riskLvl = $(this).data('risklvl');
    var kycLvl = $(this).data('kyclvl');
    var current_val = $("option:selected", $("#"+$(this).attr('id'))).text();
    $('#'+id).removeClass('chart-Y chart-X');
    $('#'+id).addClass('chart-'+current_val);
    $.post('/player_management/updateKycRiskScoreChart', { 'riskLvl' : riskLvl,'kycLvl' : kycLvl, 'tag' : current_val  },function(data){
       if(data.status == "success"){
            $('#'+id).next().fadeIn();
            $('#'+id).next().fadeOut(4000);
       } else {
            alert(data.msg);
       }
    });
});