<style type="text/css">
#timezone, input, select{
    font-weight: bold;
}
#timezone{
    height: 36px;
}

.select2-container--default .select2-selection--single{
    padding-top: 2px;
    height: 35px;
    font-size: 1.2em;
    position: relative;
    border-radius: 0;
    font-size:12px;;
}
.removeLink {
    text-decoration: none;
    color : #222222;
}
.removeLink:hover {
    text-decoration:none;
    color : #222222;
    cursor:text;
}
</style>
<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseGamesReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
            <?php include __DIR__ . "/../../includes/report_tools.php"?>
        </h4>
    </div>

    <div id="collapseGamesReport" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
            <form id="form-filter" class="form-horizontal" method="GET" onsubmit="return validateForm();">
                <div class="row">

                    <div class="col-md-4 col-lg-4">
                       <label class="control-label" for="group_by"><?=lang('Date')?> </label>
                        <input class="form-control dateInput" id="datetime_range" data-start="#datetime_from" data-end="#datetime_to" data-time="false"/>
                        <input type="hidden" id="datetime_from" name="datetime_from" value="<?=$conditions['datetime_from'];?>"/>
                        <input type="hidden" id="datetime_to" name="datetime_to" value="<?=$conditions['datetime_to'];?>"/>
                     </div>
                    <div class="col-md-3 col-lg-3" style="display: none">
                        <label class="control-label" for="group_by"><?=lang('Timezone')?></label>
                        <!-- <input type="number" id="timezone" name="timezone" class="form-control input-sm " value="<?=$conditions['timezone'];?>" min="-12" max="12"/> -->
                        <?php
                        $default_timezone = $this->utils->getTimezoneOffset(new DateTime());
                        $timezone_offsets = $this->utils->getConfig('timezone_offsets');
                        $timezone_location = $this->utils->getConfig('current_php_timezone');
                        ?>
                        <select id="timezone" name="timezone"  class="form-control input-sm">
                        <?php for($i = 12;  $i >= -12; $i--): ?>
                        <?php if($conditions['timezone'] || $conditions['timezone'] == '0' ): ?>
                        <option value="<?php echo $i > 0 ? "+{$i}" : $i ;?>" <?php echo ($i == $conditions['timezone']) ? 'selected' : ''?>> <?php echo $i > 0 ? "+{$i}" : $i ;?>:00</option>
                        <?php else: ?>
                        <option value="<?php echo $i > 0 ? "+{$i}" : $i ;?>" <?php echo ($i==$default_timezone) ? 'selected' : ''?>> <?php echo $i >= 0 ? "+{$i}" : $i ;?></option>
                        <?php endif;?>
                        <?php endfor;?>
                        </select>
                        <i class="text-info" style="font-size:10px;font-weight: bold;"><?php echo lang('System Timezone') ?>: (GMT <?php echo ( $default_timezone >= 0) ? '+'. $default_timezone  : $default_timezone; ?>) <?php echo $timezone_location ;?></i>
                        </div>
                    <div class="col-md-3">
                        <label class="control-label" for="group_by"><?=lang('report.g14')?> </label>
                        <select name="group_by" id="group_by" class="form-control input-sm">
                            <option value="game_platform_id" <?php echo $conditions["group_by"] == 'game_platform_id' ? "selected=selected" : ''; ?> ><?php echo lang('Game Platform'); ?></option>
                            <option value="game_type_id" <?php echo $conditions["group_by"] == 'game_type_id' ? "selected=selected" : ''; ?>><?php echo lang('Game Type'); ?></option>
                            <option value="game_description_id" <?php echo $conditions["group_by"] == 'game_description_id' ? "selected=selected" : ''; ?>><?php echo lang('Game'); ?></option>
                            <option value="player_id" <?php echo $conditions["group_by"] == 'player_id' ? "selected=selected" : ''; ?> ><?php echo lang('Player'); ?></option>

                            <option value="game_platform_and_player" <?php echo $conditions["group_by"] == 'game_platform_and_player' ? "selected=selected" : ''; ?> ><?php echo lang('Player And Game Platform'); ?></option>
                            <option value="game_type_and_player" <?php echo $conditions["group_by"] == 'game_type_and_player' ? "selected=selected" : ''; ?> ><?php echo lang('Player And Game Type'); ?></option>
                            <option value="game_description_and_player" <?php echo $conditions["group_by"] == 'game_description_and_player' ? "selected=selected" : ''; ?> ><?php echo lang('Player And Game'); ?></option>

                            <option value="aff_id" <?php echo $conditions["group_by"] == 'aff_id' ? "selected=selected" : ''; ?> ><?php echo lang('Affiliate'); ?></option>
                            <option value="agent_id" <?php echo $conditions["group_by"] == 'agent_id' ? "selected=selected" : ''; ?> ><?php echo lang('Agency'); ?></option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label" for="username"><?=lang('Player Username')?> </label>
                        <input type="text" name="username" id="username" class="form-control input-sm"
                            value='<?php echo $conditions["username"]; ?>'/>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-1" style="text-align:center;padding-top:24px;">
                        <input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="btn col-md-12 btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="panel panel-primary" >
    <div class="panel-heading">
        <h4 class="panel-title"><i class="icon-dice"></i> <?= $platform_name ?> <?=lang('report.s07')?> </h4>
    </div>
    <div class="panel-body" >
        <table class="table table-bordered table-hover " id="myTable">
            <thead>
                <tr>
                    <th><?=lang('Game Platform')?></th>
                    <th><?=lang('Game Type')?></th>
                    <th><?=lang('Game')?></th>
                    <th><?=lang('Player Username')?></th>
                    <th><?=lang('Player Level')?></th>
                    <th><?=lang('aff.as03')?></th>
                    <th><?=lang('aff.as24')?></th>
                    <th><?=lang('report.g09')?></th>
                    <th><?=lang('report.g10')?></th>
                    <th><?=lang('report.g11')?></th>
                    <th><?=lang('Payout')?></th>
                    <th><?=lang('sys.payoutrate')?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th class="text-primary"><?=lang('Total')?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="text-right text-primary"><span class="total-player">0</span></th>
                    <th class="text-right text-primary"><span class="total-bet">0</span></th>
                    <th class="text-right text-primary"><span class="total-win">0.00</span></th>
                    <th class="text-right text-primary"><span class="total-loss">0.00</span></th>
                    <th class="text-right text-primary"><span class="total-payout">0.00</span></th>
                    <th class="text-left text-primary"><span class="total-payout-rate">0%</span></th>
                </tr>

            </tfoot>
        </table>
       <div style="min-height:400px;">
            <div id="player-bets-per-game-container" style="overflow-x: auto;">
                <table class="table table-hover table-bordered table-condensed " id="player-bets-per-game" >
                   <thead>
                    <tr ></tr>
                   </thead>
                   <tbody>
                   </tbody>
                    <tfoot id="player-bets-per-game-totals" >
                    </tfoot>
              </table>
            </div>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
<input name='json_search' type="hidden">
</form>
<form id="_export_csv_form" class="hidden" method="POST" target="_blank">
<input name='json_search' id = "json_csv_search" type="hidden">
</form>
<?php }?>

<script type="text/javascript">

    var baseUrl =  '<?= base_url(); ?>';
    var loadParams = {
        platformId      : '<?= $conditions["external_system"] ?  $conditions["external_system"] : 0 ?>',
        gameTypeId      : '<?= $conditions["game_type"] ? $conditions["game_type"] : 0 ?>',
        gameTypeIdMultiple      : turnToArrayGametype('<?= $conditions["game_type_multiple"] ? $conditions["game_type_multiple"] : 0 ?>'),
        showMultiSelectFilter      : '<?= $conditions["show_multiselect_filter"] ?>',
    };
    var gamePlatformId;
    var message = {
        gameType         : '<?= lang('Select Game Type'); ?>',
        distinctPlayers  : '<?= lang('Distinct Players'); ?>',
        totalBets       : '<?=lang('report.g09')?>',
        totalWins       : '<?=lang('report.g10')?>',
        totalLosses       : '<?=lang('report.g11')?>',
        totalPayouts     : '<?=lang('Payout')?>',
        Wins     : '<?=lang('player.ui27')?>',
        Loss    : '<?=lang('player.ui28')?>',
        Payout    : '<?=lang('Payout')?>',
        playerId         : '<?=lang('Player Id')?>',
        playerUsername   : '<?=lang('Player Username')?>',
        All  : '<?=lang('lang.all')?>' ,
        total: '<?=lang('This Total')?>',
        subTotal: '<?=lang('Subtotal')?>',
        count: '<?=lang('Count')?>',
    };


    var gameApisMapObj = <?php echo json_encode($game_apis_map) ?>;

    var gameTypeParam = [];

    var jsonObjForExportToCSV = {data:[], header_data:[]};

    function turnToArrayGametype(gameTypeString){

        if (typeof gameTypeString === undefined || !gameTypeString) {
            return 0;
        }
        return gameTypeString.split('+');

    }

    function validateForm(){
        // $('#form-filter')

        // utils.safelog($('#datetime_from').val());
        // utils.safelog($('#datetime_to').val());

        // utils.safelog($('#datetime_from').val().substr(14,5));
        // utils.safelog($('#datetime_to').val().substr(14,5));

        // if($('#datetime_from').val().substr(14,5)!='00:00'){
        //     alert('<?php echo lang("Please donot change minute and second, minimum level is hour")?>');
        //     $('#datetime_range').focus();
        //     return false;
        // }

        // if($('#datetime_to').val().substr(14,5)!='59:59'){
        //     alert('<?php echo lang("Please donot change minute and second, minimum level is hour")?>');
        //     $('#datetime_range').focus();
        //     return false;
        // }

        //check user name
        var checked = true;
        $.ajax({
            async: false,
            url : '/api/checkUserName',
            type: 'POST',
            dataType: 'json',
            data: {
                username: $('#form-filter #username').val(),
                agent_name : $('#form-filter #agent_name').val(),
                affiliate_username: $('#form-filter #affiliate_username').val(),
                admin_username: '',
            },
            success: function(response) {
                var message = '';
                if (!response.username) {
                    message += '<?=lang('Username') . ' ' .lang('not found')?>' + "\r\n";
                }
                if (!response.agent_name) {
                    message += '<?=lang('Agent Username') . ' ' .lang('not found')?>' + "\r\n";
                }
                if (!response.affiliate_username) {
                    message += '<?=lang('Affiliate Username') . ' ' .lang('not found')?>' + "\r\n";
                }
                if (!response.admin_username) {
                    message += '<?=lang('Admin Username') . ' ' .lang('not found')?>' + "\r\n";
                }
                if (message.length > 0) {
                    alert(message);
                    checked = false;
                }
            }
        });
        if (!checked)
            return false;
        return true;
    }

     function addCommas(nStr){
        nStr += '';
        var x = nStr.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    function renderGameTable(data){
        //---------------------Bets Per Game Table start----------------------------
        var playerTotalBetsPerGame = data.player_total_bets_per_game,
        tableHeaders =  data.game_platform_header_map,
         th = "<th>"+message.playerId+"</th><th>"+message.playerUsername+"</th>",
         rowHeaders=[];

         rowHeaders.push(message.playerId);
         rowHeaders.push(message.playerUsername);

        if ( $.fn.DataTable.isDataTable('#player-bets-per-game') ) {
           $('#player-bets-per-game').DataTable().destroy();
        }

       existTableHeaders = [];

       for (var key in tableHeaders) {

           rowHeaders.push(gameApisMapObj[key]);
           rowHeaders.push(message.Wins+gameApisMapObj[key]);
           rowHeaders.push(message.Loss+gameApisMapObj[key]);
           rowHeaders.push(message.Payout+gameApisMapObj[key]);

           th +=  '<th>'+gameApisMapObj[key]+'</th><th class="text-success">'+message.Wins+'<i style="font-size:9px">('+gameApisMapObj[key]+')</i></th><th class="text-danger">'+message.Loss+'<i style="font-size:9px">('+gameApisMapObj[key]+')</i></th><th class="text-warning">'+message.Payout+'<i style="font-size:9px">('+gameApisMapObj[key]+')</i></th>';
           existTableHeaders.push(key);
       }

       rowHeaders.push(message.totalBets)
       rowHeaders.push(message.totalWins);
       rowHeaders.push(message.totalLosses);
       rowHeaders.push(message.totalPayouts);

       th += "<th>"+message.totalBets+"</th><th class='text-success'>"+message.totalWins+"</th><th class='text-danger'>"+message.totalLosses+"</th><th class='text-warning' >"+message.totalPayouts+"</th>";
       $('#player-bets-per-game tr:first').html("");
       $('#player-bets-per-game tr:first').append(th);

       //return;

       var tbody = '';

        //use the fastest way https://jsperf.com/fastest-way-to-iterate-object
        var keys = Object.keys(playerTotalBetsPerGame);
        var len = keys.length;


        for (var i = 0; i < len; i++) {
            var rowObj = {};
            var tr = '<tr>', tds = '', playerRow = playerTotalBetsPerGame[keys[i]], //rows of game
            betDetails = playerRow.bet_details,
            hlen = existTableHeaders.length,
            bdArrkeys = Object.keys(betDetails);


            rowObj['playerId']= playerRow.player_id;
            rowObj['username'] = playerRow.username;

            tds +='<td>'+playerRow.player_id+'</td>';
            tds +='<td>'+playerRow.username+'</td>';

               for (var n=0; n < hlen;  n++) {
                    if(bdArrkeys.indexOf(existTableHeaders[n]) > -1){
                   //  console.log(betDetails[existTableHeaders[n]])

                        rowObj['total_bet'+n] = betDetails[existTableHeaders[n]].total_bet.replace( /<.*?>/g, '' );
                        rowObj['total_win'+n]= betDetails[existTableHeaders[n]].total_win.replace( /<.*?>/g, '' );
                        rowObj['total_loss'+n] = betDetails[existTableHeaders[n]].total_loss.replace( /<.*?>/g, '' );
                        rowObj['total_payout'+n] = betDetails[existTableHeaders[n]].total_payout.replace( /<.*?>/g, '' );

                        tds += '<td class ="text-right" >' +betDetails[existTableHeaders[n]].total_bet+ '</td><td class ="text-right text-success" >' +betDetails[existTableHeaders[n]].total_win+ '</td><td class ="text-right text-danger" >' +betDetails[existTableHeaders[n]].total_loss+ '</td><td class ="text-right text-warning" >' +betDetails[existTableHeaders[n]].total_payout+ '</td>'
                    }else{

                        rowObj['total_bet'+n] = '0.00';
                        rowObj['total_win'+n] = '0.00';
                        rowObj['total_loss'+n] = '0.00';
                        rowObj['total_payout'+n] = '0.00';

                        tds += '<td class ="text-right text-muted" >0.00</td><td class ="text-right text-muted" >0.00</td><td class ="text-right text-muted" >0.00</td><td class ="text-right text-muted" >0.00</td>';
                    }
                }

            rowObj['sum_total_bets'] = playerRow.sum_total_bets.replace( /<.*?>/g, '' );
            rowObj['sum_total_wins'] = playerRow.sum_total_wins.replace( /<.*?>/g, '' );
            rowObj['sum_total_loss'] = playerRow.sum_total_loss.replace( /<.*?>/g, '' );
            rowObj['sum_total_payout'] = playerRow.sum_total_payout.replace( /<.*?>/g, '' );

            tds +='<td class ="text-right text-primary">'+playerRow.sum_total_bets+'</td><td class ="text-right text-primary">'+playerRow.sum_total_wins+'</td><td class ="text-right text-primary">'+playerRow.sum_total_loss+'</td><td class ="text-right text-primary">'+playerRow.sum_total_payout+'</td>';
            tr += tds+'</tr>';
            tbody += tr;
            jsonObjForExportToCSV['data'].push(rowObj);
            jsonObjForExportToCSV['header_data'] = rowHeaders;
             $("#json_csv_search").val(JSON.stringify(jsonObjForExportToCSV));
        }
        // var myJSON = JSON.stringify(jsonObjForExportToCSV);
        // console.log(myJSON)

        $('#player-bets-per-game tbody').html(tbody);


        //Prepare footer totals
        var footerCount = jsonObjForExportToCSV.header_data.length ;

            var  footerTotals = '';
            for(var i=1; i<=footerCount; i++){
              if(i==1){
                 footerTotals += '<th class="text-right "><span style="font-weight:bold;">'+message.subTotal+'</span><br><span style="font-weight:bold;" class="text-primary">'+message.total+'</span></th>';
              }else{
                 footerTotals += '<th class="text-right "></th>';
              }
            }
              footerTotals = '<tr >'+footerTotals+'</tr>';

           $('#player-bets-per-game-totals').html(footerTotals);

        if (data.recordsTotal == 0) {
            $('#player-bets-per-game-container').hide();
            return;
         } else {
            $('#player-bets-per-game-container').show()
         }
        $('#player-bets-per-game').DataTable({
            "pageLength": 25,
            "lengthMenu": [[25, 50, 100], [25, 50, 100]],
            searching: true,
            autoWidth: false,
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>f t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",

             buttons: [
            // {
            //     extend: 'colvis',
            //     postfixButtons: [ 'colvisRestore' ]
            // }
            <?php if ($export_report_permission) {?>
            //,
            {
                text: "<?php echo lang('CSV Export'); ?>",
                className:'btn btn-sm btn-primary _export_csv_btn',
                action: function ( e, dt, node, config ) {

                          $(this).attr('disabled', 'disabled');
                         $.ajax({
                            url:  site_url('/export_data/game_report_results/true'),
                            type: 'POST',
                             data: {json_search: $("#json_csv_search").val() }
                            }).done(function(data) {

                            if(data && data.success){
                               $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                                $('._export_csv_btn').removeAttr("disabled");
                            }else{
                               $('._export_csv_btn').removeAttr("disabled");
                               alert('export failed');
                            }
                            }).fail(function(){
                                $('._export_csv_btn').removeAttr("disabled");
                                alert('export failed');
                          });
                           }
            }
            <?php } ?>
            ],


            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;

                var intVal = function ( a,b ) {
                    if ( typeof a === 'string' ) {
                            a = a.replace(/[^\d.-]/g, '') * 1;
                        }
                        if ( typeof b === 'string' ) {
                            b = b.replace(/[^\d.-]/g, '') * 1;
                        }
                       return a + b;
                };

                var count_ = jsonObjForExportToCSV.header_data.length  ;

                for(var i=2; i<count_; i++){

                var  total = api.column(i).data().reduce( function( a, b ){return intVal(a,b);}, 0 ).toFixed(2);
                    $( api.column( i ).footer() ).html('<span>'+ addCommas(api.column( i, {page:'current'} ).data().sum().toFixed(2)) +'</span><br><span class="text-right text-primary ">'+addCommas(total)+'</span>');
                }
            }


         }).draw(false);
         $('#player-bets-per-game-container').hide();
         $('#player-bets-per-game-container').fadeIn(1000);
         //---------------------Bets Per Game Table end----------------------------
    }

    $(document).ready(function(){

        var DEFAULT_TIMEZONE = <?php echo $default_timezone ?>;
        var timezoneVal = Number($('#timezone').val());

          if(timezoneVal != DEFAULT_TIMEZONE){
            $('#datetime_range').css({color:'red'});
          }

            //This is for front side without referesh page , because sometimes we sort the table(will also get the search form), if the search has changed value
            $('#timezone').change(function(){
              var timezone = Number($(this).val());

             if (timezone != DEFAULT_TIMEZONE) {
               $('#datetime_range').css({color:'red'});
             } else {
                $('#datetime_range').css({color:''});
              }

            });
        jQuery.fn.dataTable.Api.register( 'sum()', function ( ) {
            return this.flatten().reduce( function ( a, b ) {
                if ( typeof a === 'string' ) {
                    a = a.replace(/[^\d.-]/g, '') * 1;
                }
                if ( typeof b === 'string' ) {
                    b = b.replace(/[^\d.-]/g, '') * 1;
                }

                return a + b;
            }, 0 );
        } );


        $('#playerList').select2();

//---------------------Multi select search start----------------------------



   if(loadParams.showMultiSelectFilter == 'true'){
         $('#show-multiselect-filter').val('true');
         $('#show-multiselect-filter').attr("checked", true);
         $('#gamePlatform').attr('disabled', 'disabled');
         $('#gameType').attr('disabled', 'disabled');
         $('#gameTypeCheckBoxes').show();

     }else{
      $('#game-type-multiple').val("");
      $('#show-multiselect-filter').val('false');
      $('#show-multiselect-filter').attr("checked", false);
      $('#gameTypeCheckBoxes').hide();
      // $('#gamePlatform').removeAttr('disabled');
      // $('#gameType').removeAttr('disabled');
    }

  $('#show-multiselect-filter').click(function(){
      if(this.checked) {
         $(this).val('true');
         $(this).attr("checked", true);
         // $('#gamePlatform').attr('disabled', 'disabled');
         // $('#gameType').attr('disabled', 'disabled');
         $('#gameTypeCheckBoxes').show();
     }else{
      $('.game-types-all').attr("checked", false);
      $(this).val('false');
      $(this).attr("checked", false);
      $('#game-type-multiple').val("");
      $('.game-types').attr("checked", false);
      $('#gameTypeCheckBoxes').hide();
      // $('#gamePlatform').removeAttr('disabled');
      // $('#gameType').removeAttr('disabled');
      loadParams.gameTypeIdMultiple =0;
      gameTypeParam = [];
    }

   });

    $('.game-types-all').each(function(index, value) {
        $(this).click(function(){
             var id =  $(this).attr('platform_id_class');
              if (this.checked) {
                 $('.game-platfom-class-'+id).each(function(i, v) {
                     if(!this.checked){
                          $(this).trigger('click');
                     }
                 });
              }else{
                $('.game-platfom-class-'+id).each(function(i, v) {
                       $(this).trigger('click');
                });
              }
        });
     });


   //loop through checkboxes and attach each event listener
    $('.game-types').each(function(index, value) {
          // on first load of page gameTypeId is always zero bec no url parameter
          if (loadParams.gameTypeIdMultiple != 0) {
           if (loadParams.gameTypeIdMultiple.indexOf($(this).val()) > -1) {
               $(this).attr("checked", true);
           }
       }
        //onchecked make setting and string  for game_type value as single string
        $(this).change(function() {
           if (this.checked) {
               gameTypeParam.push($(this).val());
               gameTypeString = gameTypeParam.join('+');
               $('#game-type-multiple').val(gameTypeString);
           } else {
               var gameTypeIndex = gameTypeParam.indexOf($(this).val());
               gameTypeParam.splice(gameTypeIndex, 1);
               gameTypeString = gameTypeParam.join('+');
               $('#game-type-multiple').val(gameTypeString);
               var platform_id =  $(this).attr('platform_id');
               $('#game-types-all-'+platform_id).attr("checked", false);

           }
       });
    });
     // on succeeding page load make string param for exising game_type values
     if (loadParams.gameTypeIdMultiple!= 0) {
       $('#game-type-multiple').val(loadParams.gameTypeIdMultiple.join('+'));
       gameTypeParam = loadParams.gameTypeIdMultiple;

   }
     //---------------------Multi select search  end----------------------------

       var existTableHeaders = [];

       var dataTable = $('#myTable').DataTable({
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            autoWidth: false,
            searching: false,

            <?php if ($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>

            //dom: "<'panel-body'l>t<'text-center'r><'panel-body'<'pull-right'p>i>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
                }
                <?php if ($export_report_permission) {?>
                ,{
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                    action: function ( e, dt, node, config ) {
                        var d = {'extra_search':$('#form-filter').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                        // utils.safelog(d);

                        <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                                    $("#_export_excel_queue_form").attr('action', site_url('/export_data/game_report/null/true'));
                                    $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                    $("#_export_excel_queue_form").submit();
                        <?php }else{?>

                        $.post(site_url('/export_data/game_report/null/true'), d, function(data){
                            // utils.safelog(data);

                            //create iframe and set link
                            if(data && data.success){
                                $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                            }else{
                                alert('export failed');
                            }
                        }).fail(function(){
                            alert('export failed');
                        });

                        <?php }?>
                    }
                }
                <?php } ?>
            ],
            columnDefs: [
                { className: 'text-right', targets: [6,7,8,9,10,11] },
                { visible: false, targets: [ <?php echo implode(',', $this->config->item('game_reports_hidden_cols')); ?>  ] }
            ],
            "order": [ 0, 'asc' ],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {

                var formData = $('#form-filter').serializeArray();
                data.extra_search = formData;
                $.post(base_url + "api/oneworksGameReports", data, function(data) {
                 if(data.recordsTotal > 0){
                    $('.total-player').html('<i class="text-success" style="font-size:9;padding-right:10px;">('+message.distinctPlayers+')</i>'+data.summary[0].total_player);
                    $('.total-bet').text(addCommas(parseFloat(data.summary[0].total_bet).toFixed(2)));
                    $('.total-ave-bet').html(addCommas(parseFloat(data.summary[0].total_ave_bet).toFixed(2)) +'<br><i class="text-success" style="font-size:10px;">('+message.count+': '+data.summary[0].total_ave_count+')</i>' );
                    $('.total-win').text(addCommas(parseFloat(data.summary[0].total_win).toFixed(2)));
                    $('.total-loss').text(addCommas(parseFloat(data.summary[0].total_loss).toFixed(2)));
                    $('.total-payout').text(addCommas(parseFloat(data.summary[0].total_payout).toFixed(2)));
                    $('.total-payout-rate').text(data.summary[0].total_payout_rate + '%');

                 <?php if($this->utils->isEnabledFeature('display_player_bets_per_game')){?>
                    renderGameTable(data);
                 <?php } ?>

                }else{
                    if ( $.fn.DataTable.isDataTable('#player-bets-per-game') ) {
                       $('#player-bets-per-game').DataTable().destroy();
                    }
                    $('#player-bets-per-game-container').hide();
                 }

                    callback(data);
                    if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                        dataTable.buttons().disable();
                    }
                    else {
                        dataTable.buttons().enable();
                    }
                }, 'json');
           }
           ,
           drawCallback : function(data, type, row) {
           },
           "rowCallback": function( row, data, index ) {
           },




        });
    });

</script>