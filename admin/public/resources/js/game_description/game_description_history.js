$(document).ready(function () {
	var gameDescriptionHistoryTable = $("#gameDescriptionHistoryTable").DataTable({
		autoWidth: false,
		dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i><'dt-information-summary2 pull-right' f>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
	        buttons: [
	            {
	                extend: 'colvis',
	                collectionLayout: 'two-column',
					className: 'btn-linkwater',
	            }
	        ],

		columnDefs: [
			{ sortable: false, targets: [ 0 ] },
			{ visible: false, targets: [ 0,1,12,13,14,17,18,19,20,21,22,23,24,26,27,32,33,34 ] }
		],
		searching: true,
	});

	var showGameDescriptionHistoryModal = function(){
		var rowId = $(this).data('row-id');
		console.log(rowId)
		$.ajax({
			url : GET_GAME_DESCRIPTION_HISTORY + "/" + rowId,
			type : 'GET',
			dataType : 'json',
		}).done(function(data){
			gameDescriptionHistoryTable.clear().draw();
			if (data.status == "success") {
				if (data.gameDescriptionHistory) {
					$.each(data.gameDescriptionHistory, function(key,gameDescriptionHistory){
						gameDescriptionHistoryTable.rows.add( [ {
					        "0":gameDescriptionHistory.id,
							"1":gameDescriptionHistory.game_description_id,
							"2":gameDescriptionHistory.game_platform_id,
							"3":gameDescriptionHistory.action,
							"4":gameDescriptionHistory.game_type,
							"5":gameDescriptionHistory.game_name,
							"6":gameDescriptionHistory.game_code,
							"7":gameDescriptionHistory.attributes,
							"8":gameDescriptionHistory.note,
							"9":gameDescriptionHistory.english_name,
							"10":gameDescriptionHistory.external_game_id,
							"11":gameDescriptionHistory.clientid,
							"12":gameDescriptionHistory.moduleid,
							"13":gameDescriptionHistory.sub_game_provider,
							"14":gameDescriptionHistory.flash_enabled,
							"15":gameDescriptionHistory.status,
							"16":gameDescriptionHistory.flag_show_in_site,
							"17":gameDescriptionHistory.no_cash_back,
							"18":gameDescriptionHistory.void_bet,
							"19":gameDescriptionHistory.game_order,
							"20":gameDescriptionHistory.related_game_desc_id,
							"21":gameDescriptionHistory.dlc_enabled,
							"22":gameDescriptionHistory.progressive,
							"23":gameDescriptionHistory.enabled_freespin,
							"24":gameDescriptionHistory.offline_enabled,
							"25":gameDescriptionHistory.mobile_enabled,
							"26":gameDescriptionHistory.enabled_on_android,
							"27":gameDescriptionHistory.enabled_on_ios,
							"28":gameDescriptionHistory.flag_new_game,
							"29":gameDescriptionHistory.html_five_enabled,
							"30":gameDescriptionHistory.demo_link,
							"31":gameDescriptionHistory.md5_fields,
							"32":gameDescriptionHistory.deleted_at,
							"33":gameDescriptionHistory.created_on,
							"34":gameDescriptionHistory.updated_at
					    }] )
					    .draw();
					});
				}
			}
		});
	};

    $('#my_table').delegate(".viewGameDescriptionHistory",'click',showGameDescriptionHistoryModal);


	var gamePlatform = $("#gamePlatform"),
		gamePlatformId = gamePlatform.val();

	$('.multi-select-filter').select2();

	if(show_non_active_game_api_game_list==0 && gameCodes){
		processFilters(gameCodes,'selectGameCode');
	}

	if(filters){
		processFilters(filters,'filters');
	}

	gamePlatform.on("change", function (){
		LoadGameTypesByGamePlatformId($("#gamePlatform :selected").val(),'#gameType');

	});

	$( window ).on( "load", function(){
 		if (gamePlatformId) {
		 	LoadGameTypesByGamePlatformId(gamePlatformId,'#gameType');
 		}
	});

	$('#game_platform_id').on("change", function (){
		LoadGameTypesByGamePlatformId($("#game_platform_id :selected").val(),'#game_type_id');

	});

    function LoadGameTypesByGamePlatformId(gamePlatformId,id) {
    	var option = '';

        if(gamePlatformId != "N/A") {
            $.post(baseUrl + 'game_type/getGameTypeByPlatformId/' + gamePlatformId,function(data){
                var dataList = data;
                option += '<option value="">'+message.gameType+'</option>';

                for(var i in dataList) {
                    // console.log(data[i].id);
                    var selected = '';
                    if(gameTypeId == data[i].id) {
                        selected = 'selected';
                    }
                    option += '<option value="'+data[i].id+'" '+selected+'>'+data[i].game_type+'</option>';
                }
                $(id).html(option);
        		$(id).removeAttr('disabled');
            },'json');
        }else{
        	$(id).attr('disabled',true);
        }
    }

    function processFilters(filters,$id){
        $('#'+$id+' option').prop('selected', false);
        if ($.isArray(filters)) {
			$.each(filters,function(key,filter){
	            $('#'+$id+' option[value="'+filter+'"]').prop('selected', true);
			});
        }else{
	        $('#'+$id+' option[value="'+filters+'"]').prop('selected', true);
        }
        $('#'+$id+'').trigger('chosen:updated');
    	$('#'+$id+'').trigger('change');
    }

    $("#game-update-active-form input").change(function() {
        var fileExtension = ['csv'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $(this).val(null);
            alert("Only format are allowed : "+fileExtension.join(', '));
        }
    });

    $('#batchUpdateModal').on('hidden.bs.modal', function () {
    	//reset form once modal is hide
	    $('#game-update-active-form').trigger("reset");
	});

	$( "#game-update-active-form" ).after( "<a id='download_csv'><button onclick='download_csv()'>Download Sample CSV</button>");

});

function download_csv() {
    var data = [
       ['gamecode1'],
       ['gamecode2'],
       ['gamecode3']
    ];
    var csv = 'Game Code\n';
    data.forEach(function(row) {
            csv += row.join(',');
            csv += "\n";
    });

    console.log(csv);
    var hiddenElement = document.getElementById('download_csv');
    hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
    hiddenElement.target = '_blank';
    hiddenElement.download = 'sample.csv';
    hiddenElement.click();
}