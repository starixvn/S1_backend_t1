$(document).ready(function (){
	var initGamePlatformsSelect = function(){
		if($("#game_platform option").length == 0 && typeof GET_GAME_PLATFORMS_URL !== 'undefined'){
			$.ajax({
				url : GET_GAME_PLATFORMS_URL,
				type : 'GET',
				dataType : 'json',
			}).done(populateGamePlatformSelect);
		}
	};

	var populateGamePlatformSelect = function(jsonData){
		var gameApis = jsonData.data.game_apis;
		$('#game_platform').append($("<option id = 'gamePlatformGray' disabled = 'disabled'>"+LANG.SELECT_GAME_PLATFORM+"</option>").prop('selected',true));
		$.each(gameApis, function(k,v){
			$('#game_platform').append($("<option></option>").attr("value",v.id).text(v.system_code));
		});
	};

	// To make sure checkbox fields' values get posted back in the form even if unchceked
	var linkCheckboxes = function(){
		$('#edit_game_type_details input[type="checkbox"]').change(function(){
			$(this).siblings('#' + $(this).data('link')).val($(this).is(":checked") ? 1 : 0);
		});
	}

	var showForm = function(createOrUpdate){
		$("#required_game_platform").empty();
		$("#required_game_type_name").empty();
		$("#required_game_type_lang").empty();
		$("#required_game_type_code").empty();

		if('create' == createOrUpdate){
			$("#add-edit-panel-title").html(LANG.ADD_PANEL_TITLE);
			$("#add-update-button").html(LANG.ADD_BUTTON_TITLE);
		}
		else if('update' == createOrUpdate){
			$("#add-edit-panel-title").html(LANG.EDIT_PANEL_TITLE);
			$("#add-update-button").html(LANG.UPDATE_BUTTON_TITLE);
		}

		if(!$('#edit_game_type_details').is(':visible')){
			// Change the main list width
			$('#toggleView').toggleClass('col-md-12 col-md-7');
			// Show the hidden edit form
			$('#edit_game_type_details').show();
		}
	};

	var showCreateForm = function(){
		showForm('create');
		$("#game-type-form #gt_id").val(-1); // New record
		$("#game_type_name").val(''); // New record
		$("#game_type_lang").val(''); // New record
		$("#game_type_code").val(''); // New record
		$("#game_type_code").val(''); // New record
		$("#note").val(''); // New record

		$("#auto_add_new_game").prop('checked',true);
		$("#auto_add_to_cashback").prop('checked',true);
		$("#status").prop('checked',true);
		$("#flag_show_in_site").prop('checked',true);
	};

	var showUpdateForm = function(){
		showForm('update');
		var rowId = $(this).data('row-id'); // ID of the row being updated
		$("#game-type-form #gt_id").val(rowId);
		$.ajax({
			url : CRUD_R_URL + "/" + rowId,
			type : 'GET',
			dataType : 'json',
		}).done(fillUpdateForm);
	};

	var deleteGameType = function(){
		if (confirm(LANG.GAME_TYPE_DELETE_LANG)) {
			var rowId = $(this).data('row-id'); // ID of the row being updated
			$("#game-type-form #gt_id").val(rowId);
			$.ajax({
				url : CRUD_D_URL + "/" + rowId,
				type : 'get',
				dataType : 'json',
			}).done(function(data){
				if(data.status=="success"){
					location.reload(true);
				}
			},'json');
		}
	};

	var fillUpdateForm = function(jsonData){
		if('success' == jsonData.status){
			$.each(jsonData.data, function(name,val){
				// Set the value to corresponding fields whose name matches DB column
				$('#game-type-form')
					.find('input[name="' + name + '"],select[name="' + name + '"],textarea[name="' + name + '"]')
					.val(val);
				$('#game-type-form #' + name).prop('checked', val>0);
			});
		}
	};

	function checkSaveForm() {
		var bContinue = true;

		var mGame_Platform = $('#game_platform').val();
		var mGame_Typename = $('#game_type_name').val();
		var mGame_Typelang = $('#game_type_lang').val();
		var mGame_Typecode = $('#game_type_code').val();
		$("#required_game_platform").empty();
		$("#required_game_type_name").empty();
		$("#required_game_type_lang").empty();
		$("#required_game_type_code").empty();

		if (mGame_Platform === null) {
			$("#required_game_platform").append("<font color='red'><b> Please choose Game Platform</b></font>");
			$(window).scrollTop(0);
			bContinue = false;
		}
		if ($.trim(mGame_Typename) === '') {
			$("#required_game_type_name").append("<font color='red'><b> Please enter Game Type Name</b></font>");
			$(window).scrollTop(0);
			bContinue = false;
		}
		if ($.trim(mGame_Typelang) === '') {
			$("#required_game_type_lang").append("<font color='red'><b> Please enter Language Code</b></font>");
			$(window).scrollTop(0);
			bContinue = false;
		}
		if ($.trim(mGame_Typecode) === '' ) {
			$("#required_game_type_code").append("<font color='red'><b> Please enter Game Type Code</b></font>");
			$(window).scrollTop(0);
			bContinue = false;
		}
		return bContinue;
	}

	var saveForm = function(){
		$("#game-type-form").submit(function(){

			$("#gamePlatformGray").css('color', 'gray');

			var bContinue = checkSaveForm();
			if (bContinue === false) {
				return false;
			}

			// console.log($(this).serialize());
			$.ajax({
				url : CRUD_U_URL,
				type : 'POST',
				data : $(this).serialize(),
				dataType : "json",
				cache : false,
			}).done(function (data){
				// console.log(data);
				window.location.href = REFRESH_PAGE_URL;
			}).fail(function (jqXHR, textStatus){
				window.location.href = REFRESH_PAGE_URL;
				// console.log("Ajax call failed, textStatus = " + textStatus + ", responseText = " + jqXHR.responseText);
			});
			return false;
		});
	};
	
	


	var closeForm = function(){
		if($('#edit_game_type_details').is(':visible')){
			// Change the main list width
			$('#toggleView').toggleClass('col-md-12 col-md-7');
			// Show the hidden edit form
			$('#edit_game_type_details').hide();
		}
	}

	var showGameTypeHistoryModal = function(){
		var rowId = $(this).data('row-id');
		
		$.ajax({
			url : GET_GAME_TYPE_HISTORY + "/" + rowId,
			type : 'GET',
			dataType : 'json',
		}).done(function(data){
			gameTypeHistoryTable.clear().draw();
			if (data.status == "success") {
				if (data.gameTypesHistory) {
					$.each(data.gameTypesHistory, function(key,gameTypeHistory){
						gameTypeHistoryTable.rows.add( [ {
					        "0":gameTypeHistory.game_type_id,
							"1":gameTypeHistory.game_platform_id,
							"2":gameTypeHistory.action,
							"3":gameTypeHistory.game_type,
							"4":gameTypeHistory.game_type_lang,
							"5":gameTypeHistory.note,
							"6":gameTypeHistory.status,
							"7":gameTypeHistory.flag_show_in_site,
							"8":gameTypeHistory.order_id,
							"9":gameTypeHistory.auto_add_new_game,
							"10":gameTypeHistory.related_game_type_id,
							"11":gameTypeHistory.auto_add_to_cashback,
							"12":gameTypeHistory.game_type_code,
							"13":gameTypeHistory.game_tag_id,
							"14":gameTypeHistory.created_on,
							"15":gameTypeHistory.updated_at,
							"16":gameTypeHistory.md5_fields,
							"17":gameTypeHistory.deleted_at
					    }] )
					    .draw();
					});
				}
			}
		});
	};

	initGamePlatformsSelect();
	linkCheckboxes();
	$("#addGameType").click(showCreateForm);
	$("#list_panel_body .edit-gt").click(showUpdateForm);
	$("#list_panel_body .delete-gt").click(deleteGameType);
	$("#add-update-button").click(saveForm);
	$("#closeDetails").click(closeForm);

	//For paging use delegate
	$('#my_table').delegate(".edit-gt", "click", showUpdateForm);
	$('#my_table').delegate(".delete-gt", "click", deleteGameType);

	//tooltips
	if(typeof LANG !== 'undefined'){
		$(".edit-gt").tooltip({
			placement : "right",
			title : LANG.EDIT,
		});
		$("#edit_column").tooltip({
			placement : "right",
			title : LANG.EDIT_COLUMN,
		});
		$("#delete-items").tooltip({
			placement : "right",
			title : LANG.DELETE_ITEMS,
		});
		$("#addGameDesc").tooltip({
			placement : "right",
			title : LANG.ADD_GAME_DESC,
		});
	}

	// sidebar.php
	var url = document.location.pathname;
	var res = url.split("/");
	for (i = 0; i < res.length; i++){
		switch (res[i]){
			case 'viewGameType':
				$("a#viewGameType").addClass("active");
				break;

			default:
				break;
		}
	}
	// end of sidebar.php
	$('#my_table').delegate(".viewGameTypeHistory-gt", "click", showGameTypeHistoryModal);
});
