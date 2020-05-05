<script type="text/javascript">
    /*
    function autogrow(textarea){
        var adjustedHeight = textarea.clientHeight;

        adjustedHeight = Math.max(textarea.scrollHeight,adjustedHeight);
        if (adjustedHeight>textarea.clientHeight){
            textarea.style.height = adjustedHeight + 'px';
        }
    }
    */

    $(document).ready(function(){
        var dataTable = $('#linkedAccountsTable').DataTable({
            dom: "<'row'<'col-md-12'<'pull-right'B><'pull-right progress-container'>l<'dt-information-summary2 text-info pull-left' i>>><'table-responsive't><'row'<'col-md-12'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>>",
        	autoWidth: false,
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: ['btn-linkwater']
                },
            ],
        	order: [ 0, 'asc' ]
        });

        linkedAccountOnReady();
    });

    function showEditLinkedAccountModal(id){
    	$('#edit-remarks-modal').modal('show');
    	$('.usernameInputTxt').val("Loading data..");
    	$('#remarksTxtArea').val("Loading data..");

    	var url = "/player_management/getPlayerLinkedAccountDetailsById/"+id;
    	$.ajax({
			'url' : url,
			'type' : 'GET',
			'dataType' : "json",
			'success' : function(data){
				if(data.success){
					$("#messageTxt").text("");
					$('#linkAccountId').val(data.id);
					$('.usernameInputTxt').val(data.username);
					$('#remarksTxtArea').val(data.remarks);
				}
			}
		});
    }

    function showAddLinkedAccountModal(){
    	$('#add-link-account-modal').modal('show');
    }

    $("#linkDateTimeCbx").change(function() {
        if(this.checked) {
            $('#linkDatetimeInputTxt').prop('disabled',false);
            $('#linkDatetimeInputTxt').prop('required',true);
        }else{
            $('#linkDatetimeInputTxt').val("");
            $('#linkDatetimeInputTxt').prop('disabled',true);
            $('#linkDatetimeInputTxt').prop('required',false);
        }
    }).trigger('change');


    /**
     * Handle events of elements about linked account.
     *
     * @return void
     */
    function linkedAccountOnReady(){

        // ADD PLAYER LINKED ACCOUNT
        $('body').on('click', "#saveAddLinkAccountBtn", function(e){
            var data = {
                remarks : $("#addRemarksTxtArea").val(),
                linkedAccountsId : $("#addedLinkedAccounts").val(),
                username : $("#username").val()
            };

            var url = "/player_management/addPlayerLinkedAccount";
            $.post(url, data, function(data){
                $("#addCloseBtn").hide();
                $("#saveAddLinkAccountBtn").hide();
                $("#addMessageTxt").text("<?=lang('Saving..') ?>");

                setTimeout( function(){
                    $('#add-link-account-modal').modal('hide');
                    location.reload();
                },2000);
            });
        });
        // UPDATE PLAYER LINKED ACCOUNT REMARKS
        $('body').on('click', "#saveRemarksBtn", function(e){
            var data = {
                remarks : $("#remarksTxtArea").val(),
                id : $("#linkAccountId").val()
            };

            var url = "/player_management/updatePlayerLinkedAccountRemarks";
            $.post(url, data, function(data){

                $("#closeBtn").hide();
                $("#saveRemarksBtn").hide();
                $("#messageTxt").text("<?=lang('Sending..') ?>");

                setTimeout( function(){
                    $('#edit-remarks-modal').modal('hide');
                    location.reload();
                },2000);
            });
        });

        $('.linkAccountExportList').click(function(){
            var data = {
                username : $("#playerUsernameInputTxt").val(),
                link_datetime : "",
                search_type : 1, //exact
            };

            var url = "/player_management/exportLinkedAccountsListInPlayerInfo";
            $.post(url, data, function(data){

                //create iframe and set link
                if(data && data.success){
                    $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                }else{
                    alert('export failed');
                }
            });
        });
    }

	function deleteLinkedAccountById(id,playerName){
        if(confirm("Are you sure you want to delete linked account: "+playerName+"?")){
        	var url = "/player_management/deletePlayerLinkedAccountById/"+id;
	    	$.ajax({
				'url' : url,
				'type' : 'GET',
				'dataType' : "json",
				'success' : function(data){
					if(data.success){
						location.reload();
					}
				}
			});
        } else {
            return false;
        }
    };

    /**
     * initial select2 plugin with getNonLinkedAccountPlayers URI
     * @param {string} selectorStr The select string
     * @param {string} username The select string
     */
    function initSelect2WithGetNonLinkedAccountPlayers(selectorStr, username){
        $(selectorStr).select2({
            placeholder: '<?=lang('Select players to link')?>',
            ajax: {
                url: '/player_management/getNonLinkedAccountPlayers/'+ username,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.items,
                    };
                },
                cache: true
            },
            templateResult: function (option) {
                return option.text;
            },
            templateSelection: function (option) {
                return option.text;
            },
            minimumInputLength: 3,
        });
    }

    $(function() {
        // ADD LINK ACCOUNT SELECT 2, RETRIEVING PLAYER LIST
        var username = $("#username").val();
        initSelect2WithGetNonLinkedAccountPlayers('.js-data-example-ajax', username);
    });

    $('.export_excel').click(function(){
        var data = {
            username : $("#usernameInputTxt").val(),
            link_datetime : $("#linkDatetimeInputTxt").val(),
            search_type : $("input[name='searchType']:checked").val()
        };

        var url = "/player_management/exportLinkedAccountsListBySearch";
        $.post(url, data, function(data){

            //create iframe and set link
            if(data && data.success){
                $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
            }else{
                alert('export failed');
            }
        });
    });
</script>

