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
	$("body").on('click', '.delete_gpa', function () {
		console.log(this)
		var aTagId = $(this).attr('id'),
		id = aTagId.split('-')[1];
		var bulletChar = "-";
		var list = ["Deleting this duplicate prefix will transfer the sub wallet back to main wallet"];

		var text = "Are you sure you want to delete this game account username ?\n";

		if (confirm(text + "\n" + createUnorderedList(list, bulletChar))) {
			deleteAndCreateGameProviderAuth(id);
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

	function deleteAndCreateGameProviderAuth(game_provider_auth_id){
		$.post(DELETE_AND_CREATE_GAME_PROVIDER_AUTH_URL,{id:game_provider_auth_id},function(data){
			if (data.status == 'failed' || data.status == 'success') {
				location.reload(true);
			}
		},'json');
	}

	// Alert modeification
	function createUnorderedList(list, bulletChar) {
	  var result = "";
	  for (var i = 0; i<list.length; ++i) {
	    result += bulletChar + " " + list[i] + "\n";
	  }
	  return result;
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
