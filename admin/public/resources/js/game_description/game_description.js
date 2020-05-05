$(document).ready(function () {
    

	var gameNameVal,
	currentMode;
	//forDeleteIds = Array();
	//Selections for delete
	// $(".id_selector").click(function () {
	// 	var id = $(this).attr("id").split('-')[1];
	// 	currentMode = "delete";
	// 	forDeleteIds.push(id);
	// });
	//Confirmation Delete
	// $("#cancel-delete").click(function () {
	// 	resetTableCheckoxes();
	// });
	//Confirmation Delete
	
	// $("#confirm-delete").click(function () {

	// 	var data = {
	// 		forDeletes : forDeleteIds
	// 	};
	// 	$.ajax({
	// 		url : DELETE_GAME_DESCRIPTION_URL,
	// 		type : 'POST',
	// 		data : data,
	// 		dataType : "json",
	// 	}).done(function (data) {
	// 		if (data.status == "success") {
	// 			window.location.href = REFRESH_PAGE_URL;
	// 		} else {
	// 			window.location.href = REFRESH_PAGE_URL;
	// 		}

	// 	}).fail(function (jqXHR, textStatus) {
	// 		window.location.href = REFRESH_PAGE_URL;
	// 	});
	// });

	/*Gets the game and game types options for adding and open Form*/
	$("#addGameDesc").on('click', function () {
		disableDeleteButton();
		resetTableCheckoxes();
		currentMode = 'add';
		//setGameNameSample();
		addPanelNamesAndButtons("add");
		getGamesAndGameTypes();
		addEditFormPanelOpen();
		setTimeout(function () {
			// setGameCode();
		}, 500);
	});
	// $(".trash-gd").on('click', function () {
	// 	$('#modal_column2').modal('show');
	// });

	/*Open Game Description Details */
	$("body").on('click', '.edit-gd', function () {
		disableDeleteButton();
		resetTableCheckoxes();
		currentMode = 'edit';
		var aTagId = $(this).attr('id'),
		id = aTagId.split('-')[1];

		addPanelNamesAndButtons("edit");
		editGameDescription(id);
		//wait sometime to set data in game name field
		// setTimeout(function () {
			//setGameCode()
			addEditFormPanelOpen();
		// }, 200);

	});


	/*Open Game Description Details */
	$("body").on('click', '.delete-gd', function () {
		console.log(this)
		var aTagId = $(this).attr('id'),
		id = aTagId.split('-')[1];
		if (confirm("Are you sure you want to delete this game?")) {
			deleteGameDescription(id);
		}
	});

	/*Disable game description */
	$("body").on('click', '.deactivate-gd', function () {
		console.log(this)
		var aTagId = $(this).attr('id'),
		id = aTagId.split('-')[1];
		if (confirm("Are you sure you want to deactivate this game?")) {
			updateGameDescription(id,'status',0);
		}
	});

	$("body").on('click', '.activate-gd', function () {
		console.log(this)
		var aTagId = $(this).attr('id'),
		id = aTagId.split('-')[1];
		if (confirm("Are you sure you want to activate this game?")) {
			updateGameDescription(id,'status',1);
		}
	});

	$("body").on('click', '.hideinsite-gd', function () {
		console.log(this)
		var aTagId = $(this).attr('id'),
		id = aTagId.split('-')[1];
		if (confirm("Are you sure you want to hide in site this game?")) {
			updateGameDescription(id,'flag_show_in_site',0);
		}
	});

	$("body").on('click', '.showinsite-gd', function () {
		console.log(this)
		var aTagId = $(this).attr('id'),
		id = aTagId.split('-')[1];
		if (confirm("Are you sure you want to show in site this game?")) {
			updateGameDescription(id,'flag_show_in_site',1);
		}
	});

	function updateGameDescription(game_description_id,type,status){
		$.post(UPDATE_GAME_DESCRIPTION_STATUS_URL,{forDeactivate:game_description_id,type:type,status:status},function(data){
			if (data.status == "success") {
				location.reload(true);
			}
		},'json');
	}

	/*Close Game Description Details */
	$("#closeDetails").on('click', function () {
		closeDetails();
	});
	function disableDeleteButton() {
		$("#delete-items").prop('disabled', true);
		$(".id_selector").prop('disabled', true);
	}
	function acivateDeleteButton() {
		$("#delete-items").prop('disabled', false);
		$(".id_selector").prop('disabled', false);
	}
	function resetTableCheckoxes() {
		forDeleteIds = Array();
		$(".id_selector").prop('checked', false);
	}

	function addPanelNamesAndButtons(type) {
		switch (type) {
		case "add":
			$("#add-edit-panel-title").html(LANG.ADD_PANEL_TITLE);
			$("#add-update-button").html(LANG.ADD_BUTTON_TITLE);
			break;

		case "edit":
			$("#add-edit-panel-title").html(LANG.EDIT_PANEL_TITLE);
			$("#add-update-button").html(LANG.UPDATE_BUTTON_TITLE);
			break;

		}
	}

	/*Gets Game and Game Type options*/
	function getGamesAndGameTypes() {

		$.ajax({
			url : GET_GAMES_AND_GET_TYPES_URL,
			type : 'GET',
			dataType : "json",
		}).done(function (data) {

			removeOptions();

			var gameApis = data.data.game_apis,
			gameTypeOptions = data.data.gameTypes,
			gameNameOptions = data.data.gameNames;
			$('#game_platform_id').append('<option value="N/A"" selected>Select Game Platform</option>');
			$('#game_type_id').append('<option value="N/A"" selected>Select Game Type</option>');

			gameNamesLength = gameNameOptions.length;
			for (var i = 0; i < gameApis.length; i++) {
				$('#game_platform_id').append('<option value="' + gameApis[i].id + '" >' + gameApis[i].system_code + '</option>');
			}
			// for (var j = 0; j < gameTypeOptions.length; j++) {
			// 	$('#game_type_id').append('<option value="' + gameTypeOptions[j].id + '" >' + gameTypeOptions[j].game_type + '</option>');
			// }

			// for (var n = 0; n < gameNamesLength; n++) {
			// 	var gn = gameNameOptions[n].game_name;
			// 	if(gn && gn != '0'){
			// 	 $('#game_name').append('<option value="' + gn + '" >' + gn + '</option>');
			//     }
			// }

			$('#gd_id').val("");
			//Note:Do not empty gamenameS
			$('#game_code').val("");
			$('#game_code_view').val("");
			$('#external_game_id').val("");
			$('#english_name').val("");
			$('#progressive').prop('checked', false);
			$('#dlc_enabled').prop('checked', false);
			$('#flash_enabled').prop('checked', false);
			$('#offline_enabled').prop('checked', false);
			$('#mobile_enabled').prop('checked', false);
			$('#flag_new_game').prop('checked', true);
			$('#enabled_on_android').prop('checked', false);
			$('#enabled_on_ios').prop('checked', false);
			$('#html_five_enabled').prop('checked', false);
			$('#status').prop('checked', true);
			$('#note').val("");
			$('#flag_show_in_site').prop('checked', true);
			$('#no_cash_back').val("");
			$('#void_bet').val("");
			$('#game_order').val("");

		}).fail(function (jqXHR, textStatus) {
			window.location.href = REFRESH_PAGE_URL;
		});
	}

	function removeOptions() {
		$('#game_platform_id').html("");
		$('#game_type_id').html("");
		// $('#game_name').html("");

	}

	/*Gets Game Description row for editing*/
	function editGameDescription(gameDeskId) {
		var input_fields = ['game_name','game_code','external_game_id','english_name','progressive','game_attributes','note','no_cash_back','void_bet','game_order'];
		var checkbox_fields = ['status', 'flag_show_in_site', 'dlc_enabled', 'flash_enabled', 'offline_enabled', 'mobile_enabled', 
			'html_five_enabled', 'enabled_on_ios', 'enabled_on_android', 'flag_new_game'];
				
		$.ajax({
			url : GET_GAME_DESCRIPTION_URL + gameDeskId,
			type : 'GET',
			dataType : "json",
		}).done(function (data) {
			removeOptions();
			var gameApis = data.data.game_apis,
			gameTypeOptions = data.data.gameTypes,
			gameNameOptions = data.data.gameNames,
			gameDescription = data.data.gameDescription;
			// gameNamesLength = gameNameOptions.length;
            $('#game_type_id').prop('disabled', false);

			for (var i = 0; i < gameApis.length; i++) {
				if (gameApis[i].id == gameDescription['game_platform_id']) {
					$('#game_platform_id').append('<option value="' + gameApis[i].id + '" selected >' + gameApis[i].system_code + '</option>');
				} else {
					$('#game_platform_id').append('<option value="' + gameApis[i].id + '" >' + gameApis[i].system_code + '</option>');
				}

			}
			for (var j = 0; j < gameTypeOptions.length; j++) {
				if (gameTypeOptions[j].id == gameDescription['game_type_id']) {
					$('#game_type_id').append('<option value="' + gameTypeOptions[j].id + '" selected >' + gameTypeOptions[j].game_type + '</option>');
				} else {
					$('#game_type_id').append('<option value="' + gameTypeOptions[j].id + '" >' + gameTypeOptions[j].game_type + '</option>');
				}
			}

			// for (var n = 0; n < gameNamesLength; n++) {
			// 	if(gameNameOptions[n].game_name && gameNameOptions[n].game_name != '0'){
			// 		if (gameNameOptions[n].game_name == gameDescription['game_name']) {
			// 		$('#game_name').append('<option value="' + gameNameOptions[n].game_name + '" selected >' + gameNameOptions[n].game_name + '</option>');
			// 		} else {
			// 			$('#game_name').append('<option value="' + gameNameOptions[n].game_name + '" >' + gameNameOptions[n].game_name + '</option>');
			// 		}
			// 	}

			$('#gd_id').val(gameDescription['id']);
			
			$.each(gameDescription,function(key,value) {

				if ($.inArray(key,checkbox_fields) !== "-1") {
					$('#' + key).prop('checked', value === "1");					
				}

				if ($.inArray(key,input_fields) !== "-1") {
					$('#' + key).val(value);					
				}
			});

			// Based on the game_name value prefix, decide whether to show the JSON editor
			if(gameDescription['game_name'].startsWith('_json')){
                gameNameEditor.setValue(gameDescription['game_name'].substring(6));
                $('#game_name_use_json').prop('checked', true);
                $('#game_name_use_json').triggerHandler('click');
            }
            else{
                gameNameEditor.setValue(gameDescription['game_name']);
                $('#game_name_use_json').prop('checked', false);
                $('#game_name_use_json').triggerHandler('click');
            }

            if(gameDescription['attributes'] && gameDescription['attributes'].startsWith('{')){
				gameAttributesEditor.setValue(gameDescription['attributes']);
				$('#game_attributes_use_json').prop('checked', true);
				$('#game_attributes_use_json').triggerHandler('click');
			}
            else{
                gameAttributesEditor.setValue(gameDescription['attributes']);
                $('#game_attributes_use_json').prop('checked', false);
                $('#game_attributes_use_json').triggerHandler('click');
            }

		}).fail(function (jqXHR, textStatus) {
			// window.location.href = REFRESH_PAGE_URL;
		});
	}

	/*Submit update Game Description*/
	$("#add-update-button").on('click', function () {

		$("#game-description-form").submit(function () {
			$(this).prop('disabled', true);
			var url;
			if (currentMode === "add") {
				url = ADD_GAME_DESCRIPTION_URL;
			} else {
				url = UPDATE_GAME_DESCRIPTION_URL;
			}

			var formData = $(this).serializeArray();
			// replace 'game_name' field with JSON string from aceEditor
			if($('#game_name_use_json').prop('checked')) {
				var gameNameIndex = -1;
				for(var i = 0; i < formData.length; i++) {
					if(formData[i].name == "game_name") {
						gameNameIndex = i;
						break;
					}
				}
				if(gameNameIndex >= 0) {
					formData.splice(gameNameIndex, 1);
				}
                formData.push({name: "game_name", value: '_json:' + gameNameEditor.getValue()})
				formData.push({name: "game_attributes", value: gameAttributesEditor.getValue()})
			}

			$.ajax({
				url : url,
				type : 'POST',
				data : $.param(formData),
				dataType : "json",
				cache : false,
			}).done(function (data) {
				if (data.status == "success") {
					location.reload(true);
				}
			}).fail(function (jqXHR, textStatus) {
				location.reload(true);
			});

			return false;
		});

	});

	/*Select Game option to display in game name field*/
	// $('#game_name').change(function () {
	// 	setGameCode();
	// });

	// function setGameCode() {
	// 	var gameName = $("#game_name option:selected").text(),

	// 	extension="";

	// 	if(gameName !== 'null' ){
	// 		extension = gameName.split('.')[1]//.toUpperCase();
	// 	}

	//     $("#game_code").val(extension);

	// }
	/*Toggles panels for editing*/

	function addEditFormPanelOpen() {
		$('#toggleView').removeClass('col-md-12');
		$('#toggleView').addClass('col-md-7');

		$('#edit_game_description_details').css({
			display : "block"
		})

		if ($('#toggleView').hasClass('col-md-5')) {
			$('table#myTable td#visible').hide();
			$('table#myTable th#visible').hide();
		} else {
			$('table#myTable td#visible').show();
			$('table#myTable th#visible').show();

		}

	}
	function closeDetails() {
		acivateDeleteButton();
		$('#toggleView').removeClass('col-md-7');
		$('#toggleView').addClass('col-md-12');

		if ($('#toggleView').hasClass('col-md-7')) {
			$('table#myTable td#visible').hide();
			$('table#myTable th#visible').hide();
		} else {
			$('table#myTable td#visible').show();
			$('table#myTable th#visible').show();
		}
		$('#edit_game_description_details').css({
			display : "none"
		});
	}

	//tooltips
	$(".id_selector").tooltip({
		placement : "right",
		title : LANG.EDIT,
	});
	$(".edit-gd").tooltip({
		placement : "right",
	});

	$("#edit_column").tooltip({
		placement : "right",
		title : LANG.EDIT_COLUMN,
	});
	$("#delete-items").tooltip({
		placement : "right",
		title : LANG.DELETE_ITEMS,
	});
	// $("#addGameDesc").tooltip({
	// 	placement : "right",
	// 	title : LANG.ADD_GAME_DESC,
	// });

	$(".deactivate-no-cashback").tooltip({
		placement : "right",

	});
	$(".activate-no-cashback").tooltip({
		placement : "right",

	});
	$(".deactivate-void-bet").tooltip({
		placement : "right",

	});
	$(".activate-void-bet").tooltip({
		placement : "right",

	});

	// sidebar.php
	var url = document.location.pathname;
	var res = url.split("/");
	for (i = 0; i < res.length; i++) {

		switch (res[i]) {

		case 'viewGameDescription':
			$("a#viewGameDescription").addClass("active");
			break;

		default:
			break;
		}
	}
	// end of sidebar.php

	function deleteGameDescription(game_description_id){
		$.post(DELETE_GAME_DESCRIPTION_URL,{forDeletes:game_description_id},function(data){
			if (data.status == "success") {
				location.reload(true);
			}
		},'json');
	}

	$('#game_platform_checkbox').on('change',function(){
		if ($('#game_platform_checkbox').is(':checked')) {
			$('#game_platform_id_select').prop('disabled', true);
			$('#game_platform_id_num').prop('disabled', false);
		}else{
			$('#game_platform_id_select').prop('disabled', false);
			$('#game_platform_id_num').prop('disabled', true);
		}
	});

	$('#batchAddUpdateModalSubmit').on('click',function(){
		$('#game-settings-form').submit();
	});

	$('#batchUpdateModalSubmit').on('click',function(){
		$('#game-update-active-form').submit();
	});


});
