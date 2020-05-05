var AccountInformation = {
	site_url: document.location.origin,

	/** Error messages */
	msgWait		: '',
	msgSmsSent	: '',
	msgSmsFailed: '',
	uploadLabel: '',

	DOBoption: {},

	/**
	 * Send SMS verification
	 * @param  {[type]} callingObject [description]
	 * @return {[type]}               [description]
	 */
	sendSmsVerification : function(callingObject) {
		var mobileNumber = $('#contact_number').val();
		var objMessageLabel = $('#sms_verification_msg');
		var self = this;

		if(!mobileNumber || mobileNumber == '') {
			mobileNumber='';
		}

		$('#send_sms_verification').prop('disabled',true);
		objMessageLabel.text(self.msgWait);
		objMessageLabel.show();

		$.ajax({
			url: self.site_url + '/iframe_module/iframe_register_send_sms_verification/'  + mobileNumber,
			type: "POST",
			dataType: 'json',
			success: function(response) {
				// console.log(response);
				if (response.success) {
					// alert('test');
					objMessageLabel.text(self.msgSmsSent);
				} else {
					objMessageLabel.text(self.msgSmsFailed);
				}
			},
			always : function() {
				$('#'+callingObject.id).prop('disabled',false);
			}
		});

		setTimeout(function(){objMessageLabel.fadeOut('slow')}, 5000);
	},

	previewProfilePicture : function(callingObject) {
		var fullPath = $('#'+callingObject.id).val();
		var nFIleName = fullPath.replace(/^.*[\\\/]/, '');
		var self = this;

		$('#fileNameToUpload').text(self.uploadLabel + nFIleName);
		if (callingObject.files && callingObject.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#imgProfilePicture').attr('src', e.target.result);
			}

			reader.readAsDataURL(callingObject.files[0]);
			$('input[name="has_profileToUpload"]').val('1');
		}
	},

	emptyIMField : function(imTypeSelectionObject, imImputFieldObject) {
		var objImSelection = $('#'+imTypeSelectionObject.id);
		var objImField = $('#'+imImputFieldObject.id);

		if (!objImSelection.val()) {
			objImField.val("");
		}
	},

	initDOB : function(options) {

		var defaults = {
			yearSelector: "#year",
			monthSelector: "#month",
			daySelector: "#day",
			inputDOB: "",
			legal_age: "18",
		};

		self.DOBoption = $.extend({}, defaults, options);

		var date = new Date(),
			DateY = date.getFullYear(),
			DateM = date.getMonth(),
			DateD = date.getDate(),
			minDOBDate = new Date(DateY - self.DOBoption.legal_age, DateM, DateD);

			console.log(minDOBDate);

		var $yearSelector = $(self.DOBoption.yearSelector),
			$monthSelector = $(self.DOBoption.monthSelector),
			$daySelector = $(self.DOBoption.daySelector),
			$dayDefaultOption = $daySelector.find("option:first").clone(),
			$monthDefaultOption = $monthSelector.find("option:first").clone(),
			changeDay = function () {
				var _year = $yearSelector.val(),
					_month = $monthSelector.val();

				// OGP-8450: Keep old value of daySelector befo re destruction
				var date_val_old = $daySelector.val();

				$daySelector.html($dayDefaultOption);

				if (_year == "" || _month == "") {
					return;
				}
				var _curentDayOfMonth = new Date(_year, _month, 0).getDate(),
					_isSameYear  = (Number(_year) == minDOBDate.getFullYear()),
					_isSameMonth = (Number(_month) == (minDOBDate.getMonth() + 1)),
					_isLessMonth = (Number(_month) > (minDOBDate.getMonth() + 1)),
					_minDay 	 = minDOBDate.getDate();

				for (var i = 1; i <= _curentDayOfMonth; i++) {
					var dV = i.toString(),
						d = (dV.length > 1) ? dV : "0" + dV ;
					if ((_isSameYear && _isSameMonth && i > _minDay) || (_isSameYear && _isLessMonth)) {
						$daySelector.append($("<option>").attr('disabled', 'disabled').val(d).text(d));
					} else {
						$daySelector.append($("<option>").val(d).text(d));
					}
				}

				// OGP-8450: Restore the old value of daySelector if possible
				var date_var_old_v = parseInt(date_val_old);
				if (date_var_old_v > 0 && date_var_old_v <= _curentDayOfMonth) {
					var date_option_old = $daySelector.find('option[value='+ date_val_old +']');
					if (!date_option_old.is(':disabled')) {
						$daySelector.val(date_val_old);
					}
				}

			},changeMonth = function () {
				var _year = $yearSelector.val(),
					_month = $monthSelector.val();

				// $daySelector.html($dayDefaultOption);
				$monthSelector.html($monthDefaultOption);

				var _isSameYear = (Number(_year) == minDOBDate.getFullYear()),
					_minMonth   = minDOBDate.getMonth() + 1;

				for (var i = 1; i <= 12; i++) {
					var mV = i.toString(),
						m = (mV.length > 1) ? mV : "0" + mV;
					if ((_isSameYear && i > _minMonth)) {
						$monthSelector.append($("<option>").attr('disabled', 'disabled').val(m).text(m));
					} else {
						if (Number(_month) == i) {
							$monthSelector.append($("<option>").attr('selected', 'selected').val(m).text(m));
						} else {
							$monthSelector.append($("<option>").val(m).text(m));
						}
					}
				}

				changeDay();
			},
			init = function () {
				var fullYear = new Date().getFullYear(),
					maxY = fullYear - self.DOBoption.legal_age,
					countY = 100 - self.DOBoption.legal_age,
					countM = 12;

				for (var i = maxY; i >= (maxY - countY); i--) {
					$yearSelector.append($("<option>").val(i).text(i));
				}

				for (var i = 1; i <= countM; i++) {
					var mV = i.toString(),
						m = (mV.length > 1) ? mV : "0" + mV;
					$monthSelector.append($("<option>").val(m).text(m));
				}

			};

			init();

		$monthSelector.change(changeDay);
		// $yearSelector.change(changeDay);
		$yearSelector.change(changeMonth);
	},

	validateDOB: function () {

		var cdv = $(self.DOBoption.daySelector).val(),
			cmv = $(self.DOBoption.monthSelector).val(),
			cyv = $(self.DOBoption.yearSelector).val(),
			DOB = cyv + '-' + cmv + '-' + cdv;

		if (cdv != "" && cmv != "" && cyv != "" && AccountInformation.validateDOBProcess(DOB)) {
			self.DOBoption.inputDOB.val(DOB);
		} else {
			self.DOBoption.inputDOB.val("");
		}
	},

	validateDOBProcess: function (DOB) {

		var today = new Date(),
			birthDate = new Date(DOB),
			age = today.getFullYear() - birthDate.getFullYear(),
			mon = today.getMonth() - birthDate.getMonth(),
			day = today.getDate() < birthDate.getDate();

		if (mon < 0 || (mon === 0 && day)) {
			age--;
		}

		return age >= self.DOBoption.legal_age;
	},
	/**
	 * Client-side invalid chars check for name fields, OGP-12268
	 * Checks first name, and last name if available
	 * @param	object	form        reference to the form
	 * @param	bool	block_emoji	Also match emoji if true.  Default: false
	 * @return	object	{ result:(bool), field:(string) }
	 */
	validateNames: function (form, block_emoji) {
		var field_name = $(form).find('input#name'), field_last_name = $(form).find('input#lname');
		var field_sels = [
			{ sel: 'input#name'		, name: 'first_name' },
			{ sel: 'input#lname'	, name: 'last_name' }
		];

		for (var i in field_sels) {
			var field = $(form).find(field_sels[i].sel);
			if (field.length > 0) {
				console.log('sel', field_sels[i].sel, 'val', $(field).val());
				if (this.str_check_names($(field).val(), block_emoji) == true) {
					$(field_sels[i].sel).focus();
					return { result: false, field: field_sels[i].name };
				}
			}
		}
		return { result: true, field: null };
	} ,
	/**
	 * Checks names against invalid chars (and emojis if enabled), OGP-12268
	 * @param	string	s			String to test
	 * @param	bool	block_emoji	Also match emoji if true.  Default: false
	 * @return	bool	true if s contains invalid chars or emojis, otherwise false
	 */
	str_check_names: function(s, block_emoji){
		if (typeof(block_emoji) == 'undefined') {
			block_emoji = false;
		}

		if (block_emoji) {
			var emoji_regex = /(\u00a9|\u00ae|[\u2000-\u3300]|\ud83c[\ud000-\udfff]|\ud83d[\ud000-\udfff]|\ud83e[\ud000-\udfff])/;
			if (emoji_regex.test(s)) {
				return true;
			}
		}

		var invalid_chars =  '~!@#$%^&*():<>{}();+-_0123456789[]/，、！：；？（）…｛｝‧．。\\.|\'"=?,`';
		if (s.length !=0 ){
			for (var i = 0; i < s.length; i++){
				if (invalid_chars.indexOf(s[i]) >= 0) {
					return true;
				}
			}
		}

		return false;
	}

}
