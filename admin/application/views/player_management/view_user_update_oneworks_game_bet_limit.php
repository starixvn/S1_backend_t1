<style type="text/css">
#bet_table tbody tr.active {
    color: #f18973;
    font-weight: bold;
}

#bet_table tbody  td.recent_update {
    color: white;
    background-color: hsl(89, 43%, 51%);
}

#my_form .row{
    padding: 0;
    margin: 0;
}

#my_form>.table-responsive>#bet_table_wrapper{
    overflow: hidden;    
}


#my_form>.table-responsive>#bet_table_wrapper>.row,
#my_form>.table-responsive>#bet_table_wrapper>.row>.col-sm-6,
#my_form>.table-responsive>#bet_table_wrapper>.row>.col-sm-12,
#my_form>.table-responsive>#bet_table_wrapper>.row>.col-sm-5,
#my_form>.table-responsive>#bet_table_wrapper>.row>.col-sm-7{
    padding: 0;
}
</style>
<div class="row" id="bet-container">
    <div class="" id="toggleView" style="width: 50%;margin: 0 auto;">
        <div class="panel panel-primary">
            <div class="panel-heading custom-ph">
                <h3 class="panel-title custom-pt" >
                    <i class="icon-list"></i>
                    <?= $system_code ?> <?= lang('Bet Setting') ?>
                </h3>
            </div>
            <div class="panel-body">
                <div>
                    <button type="button" class="btn btn-primary btn-md new"><i class="fa fa-edit"></i> <?php echo lang('ADD/UPDATE ROW ENTRY'); ?></button>
                    <button type="button" class="btn btn-success btn-md upload_bet_setting"><i class="fa fa-upload"></i> <?php echo lang('Upload Bet Setting'); ?></button>
                </div>
                <br>
                <ul class="list-group" style="width:20%;">
                    <li class="list-group-item"><?php echo lang('Username'); ?> : <b><?= $username ?></b> </li>
                </ul>
                <br>
                <form  autocomplete="on" id="my_form">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover dataTable" style="width:100%" id="bet_table" >
                            <thead>
                                <tr>
                                    <th><?=lang('Sport Id');?></th> 
                                    <th><?=lang('Sport Type');?></th> 
                                    <th><?=lang("Minimum Bet");?></th> 
                                    <th><?=lang("Max Bet");?></th> 
                                    <th><?=lang("Max Bet Per Match");?></th> 
                                    <th><?=lang("Max Bet Per ball");?></th> 
                                    <th><?=lang("Required");?></th> 
                                </tr>
                            </thead>
                        </table>
                    </div>
                </form>
            </div>
            <div class="panel-footer"></div>
        </div>
    </div>
</div> 
<script type="text/javascript">
    var player_bet_setting = $.parseJSON('<?php echo $player_bet_setting; ?>');
    var sports = $.parseJSON('<?php echo $sports; ?>');
    var updated_sports = [];
    var response_success = '<?php echo $response_success; ?>';
    var table = $('#bet_table').DataTable( {
            "createdRow": function(row, data, dataIndex) {
                $(row).addClass('new_data');
            },
            data: player_bet_setting,
            "iDisplayLength": 25,
            "columns" : [
                { "data" : "id"},
                { "data" : "sport" },
                { "data" : "min_bet" },
                { "data" : "max_bet" },
                { "data" : "max_bet_per_match" },
                { "data" : "max_bet_per_ball" },
                { "data" : "required" },
            ],
            "order": [[ 1, "asc" ]]
        } );
    $(document).ready(function() {
        if(!response_success){
            var message = '<?php echo $response_message; ?>';
            $("#bet-container").find(".panel-body").prepend('<div id="number_alert" class="alert alert-warning">\
                                                      <strong><?php echo lang('Warning'); ?>!</strong> '+message+'\
                                                    </div>');
            $('.new, .upload_bet_setting').prop("disabled", true);
        }
        $('#bet_table tbody').on('click', 'tr', function () {
            if ( $(this).hasClass('active') ) {
                $(this).removeClass('active');
            }
            else {
                table.$('tr.active').removeClass('active');
                $(this).addClass('active');
            }
        } );
        
        $(document).on("click",".new",function(){
            var current_sport_ids = table.columns( 0 )
                            .data()
                            .eq( 0 )      // Reduce the 2D array into a 1D array of data
                            .unique();     // Reduce to unique values
            $("body").append('<div id="bet_setting_entry" class="modal fade" role="dialog">\
                                    <div class="modal-dialog">\
                                        <div class="modal-content">\
                                            <div class="modal-header">\
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>\
                                                <h4 class="modal-title">Create/Update entry</h4>\
                                            </div>\
                                            <div class="modal-body">\
                                                <div class="form-group">\
                                                    <label><?php echo lang('Sports'); ?></label>\
                                                    <select id="sports_select"class="form-control select2" style="width: 100%;">\
                                                    <option value = "0" class="selected"><?php echo lang('Please Select'); ?></option>\
                                                    </select>\
                                                </div>\
                                                <div class="row">\
                                                    <div class="col-md-6">\
                                                        <div class="form-group">\
                                                            <label for="minimum_bet"><?php echo lang('Minimum Bet'); ?></label>\
                                                            <input type="text" class="form-control" name="minimum_bet" id="minimum_bet" placeholder="<?php echo lang('Minimum Bet'); ?>" maxlength="10">\
                                                        </div>\
                                                    </div>\
                                                    <div class="col-md-6">\
                                                        <div class="form-group">\
                                                            <label for="max_bet"><?php echo lang('Maximum Bet'); ?></label>\
                                                            <input type="text" class="form-control" name="max_bet" id="max_bet" placeholder="<?php echo lang('Maximum Bet'); ?>" maxlength="10">\
                                                        </div>\
                                                    </div>\
                                                    <div class="col-md-6">\
                                                        <div class="form-group">\
                                                            <label for="max_bet_per_match"><?php echo lang('Max Bet Per Match'); ?></label>\
                                                            <input type="text" class="form-control" id="max_bet_per_match" placeholder="<?php echo lang('Max Bet Per Match'); ?>" maxlength="10">\
                                                        </div>\
                                                    </div>\
                                                    <div class="col-md-6">\
                                                        <div class="form-group">\
                                                            <label for="max_bet_per_ball"><?php echo lang('Max Bet Per Ball'); ?></label>\
                                                            <input type="text" class="form-control" id="max_bet_per_ball" placeholder="<?php echo lang('Max Bet Per Ball'); ?>" maxlength="10" disabled>\
                                                        </div>\
                                                    </div>\
                                                    <div class="col-md-6">\
                                                        <div class="form-group">\
                                                            <input type="hidden" class="form-control" id="required" placeholder="Required" disabled>\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="modal-footer">\
                                                <button type="button" id="add_update" class="btn btn-primary" disabled><?php echo lang('Add/Update'); ?></button>\
                                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang('Close'); ?></button>\
                                            </div>\
                                        </div>\
                                    </div>\
                                </div>');
            if ( table.rows( '.active' ).any() ){
                var selected_data = table.row('.active').data();
                $('.select2').select2({ data: sports }).val(selected_data.id).trigger('change'); 
            } else {
                $('.select2').select2({ data: sports }).val(0).trigger('change'); 
            }
            $("#bet_setting_entry").modal('show');
        });
    } );

    $(document).on("change","#sports_select",function(){
        var id = $(this).val();
        $("#minimum_bet").val("");
        $("#max_bet").val("");
        $("#max_bet_per_match").val("");
        $("#max_bet_per_ball").val("");
        $("#required").val("");
        $("#add_update").addClass( "add" ).removeClass( "update");
        if(id != 0){
            var names = table.rows( function ( idx, data, node ) {
                if(data.id == id)
                {
                    $("#minimum_bet").val(data.min_bet);
                    $("#max_bet").val(data.max_bet);
                    $("#max_bet_per_match").val(data.max_bet_per_match);
                    $("#max_bet_per_ball").val(data.max_bet_per_ball);
                    $("#required").val(data.required);
                    $("#add_update").addClass( "update" ).removeClass( "add");
                } 
            } )
            .data();
            $("#add_update").prop('disabled', false);
            $("#bet_setting_entry :input[type=text]").prop("disabled", false);
            $("#max_bet_per_ball").prop('disabled', true);
            if(id == 161){
                $("#max_bet_per_ball").prop('disabled', false);
            }
        } else{
            $("#bet_setting_entry :input[type=text]").prop("disabled", true);
            $("#add_update").prop('disabled', true);
        }
        
    });


    $(document).on("click","#add_update",function(){
        var update = $(this).hasClass( "update" );
        var sports_id = $("#sports_select").val();
        var sports = $("#sports_select option:selected").text();
        var minimum_bet = parseInt($("#minimum_bet").val());
        var max_bet = parseInt($("#max_bet").val());
        var max_bet_per_match = parseInt($("#max_bet_per_match").val());
        var max_bet_per_ball = parseInt($("#max_bet_per_ball").val());
        var required = $("#required").val();
        if(minimum_bet > max_bet || minimum_bet > max_bet_per_match){
            var message = '<?php echo lang('Minimum bet must be less than to all fields.'); ?>';
            show_alert(message);
            return false;
        }
        if(max_bet > max_bet_per_match ){
            var message = '<?php echo lang('Maximum bet must be less than per match.'); ?>';
            show_alert(message);
            return false;
        }
        if(sports_id == 161){
            if(minimum_bet > max_bet_per_ball  || max_bet > max_bet_per_ball ){
                var message = '<?php echo lang('Minimum/maximum bet must be less than max per ball.'); ?>';
                show_alert(message);
                return false;
            }
            if(max_bet_per_ball > max_bet_per_match ){
                var message = '<?php echo lang('Max bet per ball  must be less than per match.'); ?>';
                show_alert(message);
                return false;
            }
        }

        if(update){//remove row
            table
            .rows( function ( idx, data, node ) {
                return data.id == sports_id;
            } )
            .remove()
            .draw( false);
        }

        var rowNode = table.row.add( {
            "id":sports_id,
            "sport":sports,
            "min_bet":minimum_bet,
            "max_bet":max_bet,
            "max_bet_per_match":max_bet_per_match,
            "max_bet_per_ball":max_bet_per_ball,
            "required":required
        } ).draw()
        .node();
        for (i = 0; i < 7; i++) {
            $( rowNode ).find('td').eq(i).addClass('recent_update');
        }

        $('#bet_setting_entry').modal('hide');
    });

    $(document).on("hidden.bs.modal","#bet_setting_entry",function(){
        $("#bet_setting_entry").remove();//remove append modal after hide
    });

    $(document).on("click",".upload_bet_setting",function(){
        var array = [];
        table.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            var data = this.data();
            array.push(data);
        } );
        var gameId = "<?php echo $game_platform_id; ?>";
        var playerId = "<?php echo $playerId; ?>";
        var url = "/async/update_player_oneworks_bet_setting";
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: {
                data:JSON.stringify(array),
                playerId:playerId,
                gameId:gameId
            },
            success: function(data){
                if(data.success == true){
                    alert("<?php echo lang('Update success!'); ?>");
                    location.reload();
                } else {
                    alert("<?php echo lang('Try again!'); ?>");
                }
            }
        });
    });

    $(document).on("keypress","#minimum_bet,#max_bet,#max_bet_per_match,#max_bet_per_ball",function(e){
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            //display error message
            if (!$('#number_alert').length){
                var message = '<?php echo lang('Input number Only'); ?>';
                show_alert(message);    
            }
            return false;
        }
        show_alert();
    });

    $(document).on("keyup","#minimum_bet,#max_bet,#max_bet_per_match,#max_bet_per_ball",function(e){
        var input = this.value;
        if(input.length == 0){
            $("#add_update").prop('disabled', true);
            $(this).css("border","2px solid red");
        } else {
            $(this).removeAttr("style");
            $("#add_update").prop('disabled', false);
        }
    });

    function show_alert($message){
        if($message){
            $("#bet_setting_entry").find(".modal-body").append('<div id="number_alert" class="alert alert-warning">\
                                                      <strong><?php echo lang('Warning'); ?>!</strong>'+$message+'\
                                                    </div>');
        }
        else{
            $("#bet_setting_entry").find(".modal-body").find('.alert').fadeOut("slow").remove();
        }
    }

</script>



