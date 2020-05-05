<div class="panel panel-primary">
     <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-hand-paper-o"></i> <?php echo lang('Shop Request List'); ?>
        </h4>
    </div>
    <div class="panel-body">
        <form id="search-form">

            <div class="row">
                <label style="display: block; cursor: pointer; margin-bottom: 0;">
                    <div class="col-md-4">
                        <input type="radio" name="status" value="<?php echo Shopper_list::REQUEST; ?>" class="dwStatus hidden"
                        <?php echo $conditions['status'] == Shopper_list::REQUEST ? 'checked="checked"' : '' ?> />
                        <div class="dashboard-stat blue panel_<?php echo Shopper_list::REQUEST; ?> <?php echo $conditions['status'] == Shopper_list::REQUEST ? 'checked' : '' ?>">
                            <div class="visual">
                                <i class="fa fa-square-o"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span><?php echo $countAllStatus[Shopper_list::REQUEST]; ?></span>
                                </div>
                                <div class="desc"> <?=lang('Request')?> </div>
                            </div>
                            <span class="more" href="#">&nbsp;</span>
                        </div>
                    </div>
                </label>

                <label style="display: block; cursor: pointer; margin-bottom: 0;">
                    <div class="col-md-4">
                        <input type="radio" name="status" value="<?php echo Shopper_list::APPROVED; ?>" class="dwStatus hidden"
                        <?php echo $conditions['status'] == Shopper_list::APPROVED ? 'checked="checked"' : '' ?> />
                        <div class="dashboard-stat green panel_<?php echo Shopper_list::APPROVED; ?> <?php echo $conditions['status'] == Shopper_list::APPROVED ? 'checked' : '' ?>">
                            <div class="visual">
                                <i class="fa fa-square-o"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span><?php echo $countAllStatus[Shopper_list::APPROVED]; ?></span>
                                </div>
                                <div class="desc"> <?=lang('Approved')?> </div>
                            </div>
                            <span class="more" href="#">&nbsp;</span>
                        </div>
                    </div>
                </label>

                <label style="display: block; cursor: pointer; margin-bottom: 0;">
                    <div class="col-md-4">
                        <input type="radio" name="status" value="<?php echo Shopper_list::DECLINED; ?>" class="dwStatus hidden"
                        <?php echo $conditions['status'] == Shopper_list::DECLINED ? 'checked="checked"' : '' ?>  />
                        <div class="dashboard-stat red panel_<?php echo Shopper_list::DECLINED; ?> <?php echo $conditions['status'] == Shopper_list::DECLINED ? 'checked' : '' ?>">
                            <div class="visual">
                                <i class="fa fa-check-square-o"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span><?php echo $countAllStatus[Shopper_list::DECLINED]; ?></span>
                                </div>
                                <div class="desc"> <?=lang('Declined')?> </div>
                            </div>
                            <span class="more" href="#">&nbsp;</span>
                        </div>
                    </div>
                </label>

            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label" for="search_deposit_date"><?=lang('pay.transperd')?></label>
                        <input id="search_deposit_date" class="form-control input-sm dateInput" data-time="true" data-start="#request_date_from" data-end="#request_date_to"/>
                        <input type="hidden" id="request_date_from" name="request_date_from" value="<?=$conditions['request_date_from'];?>" />
                        <input type="hidden" id="request_date_to" name="request_date_to" value="<?=$conditions['request_date_to'];?>" />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label" for="username"><?=lang('pay.username')?></label>
                        <input id="username" type="text" name="username" class="form-control input-sm" value="<?=$conditions['username'];?>" />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="vipsettingcashbackruleId" class="control-label"><?=lang('player.07')?></label>
                        <select name="vipsettingcashbackruleId" id="vipsettingcashbackruleId" class="form-control input-sm">
                            <option value=""><?=lang('player.08')?></option>
                            <?php foreach ($allLevels as $level) {?>

                                     <?php if ($conditions['vipsettingcashbackruleId'] == $level['vipsettingcashbackruleId']): ?>
                                      <option selected value="<?=$level['vipsettingcashbackruleId']?>"><?=$level['groupName'] . ' - ' . $level['vipLevelName']?></option>
                                     <?php else: ?>
                                      <option value="<?=$level['vipsettingcashbackruleId']?>"><?=$level['groupName'] . ' - ' . $level['vipLevelName']?></option>
                                     <?php endif;?>

                            <?php }?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="shoppingItemId" class="control-label"><?=lang('Shopping Item')?></label>
                        <select name="shoppingItemId" id="shoppingItemId" class="form-control input-sm">
                            <option value=""><?=lang('lang.selectall')?></option>
                            <?php foreach ($allShoppingItem as $shoppingItem) {?>
                                    <option selected value="<?=$shoppingItem['id']?>"><?=$shoppingItem['title']?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
                <?php if ($conditions['status'] != '0'): ?>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" for="processed_by"><?=lang('pay.procssby')?></label>
                            <select class="form-control input-sm" name="processed_by">
                                <option value =""  ><?=lang("lang.selectall")?> </option>
                                <?php foreach ($users as $u): ?>
                                    <option value ="<?php echo $u['userId'] ?>" <?php echo $conditions['processed_by'] == $u['userId'] ? 'selected' : '' ?> ><?php echo $u['username'] ?> </option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                <?php endif;?>
            </div>
            <div class="row">
                <div class="col-md-offset-9 col-md-3 text-right">
                    <button type="submit" class="btn btn-sm btn-primary"><?=lang('lang.search')?></button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover dataTable" id="shopper-list-application-table" style="margin: 0px 0 0 0; width: 100%;">
                    <thead>
                        <tr>
                            <th><?=lang('system.word85');?></th>
                            <th><?=lang('player.01');?></th>
                            <th><?=lang('player.07');?></th>
                            <th><?=lang('Item Title');?></th>
                            <th><?=lang('Player Available Points');?></th>
                            <th><?=lang('Required Points');?></th>
                            <th><?=lang('Total Points After Converted');?></th>
                            <th><?=lang('How Many Available');?></th>

                            <!-- <th><?=lang('cms.dateApplyRequest');?></th>
                            <th><?=lang('cms.dateProcessed');?></th>
                            <th><?=lang('pay.procssby');?></th>
                            <th><?=lang('Status');?></th> -->
                            <th><?=lang('Note');?></th>
                            <th><?=lang('Transaction History');?></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </form>
    </div>

    <div class="panel-footer"></div>
</div>

<!-- Modal for Decline Reason Start -->
<div class="modal fade" id="declineShoppingRequestModal" tabindex="-1" role="dialog" aria-labelledby="declineShoppingRequestModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="declineShoppingRequestModalLabel"><?php echo lang("Decline Reason") ?></h4>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="message-text" class="control-label"><?php echo lang("Reason") ?>:</label>
            <textarea class="form-control" id="modalShoppingItemDeclineReason"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("Cancel") ?></button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="declineShoppingItemClaimRequestWithReason()"><?php echo lang("Save") ?></button>
      </div>
    </div>
  </div>
</div>
<!-- Modal for Decline Reason End -->

<!-- Modal for Transaction History Start -->
<div class="modal modal-lg fade" id="shoppingTransactionHistoryModal" tabindex="-1" role="dialog" aria-labelledby="showShoppingTransactionHistoryLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="showShoppingTransactionHistoryLabel"><?php echo lang("Transaction History") ?></h4>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group" id="shoppingTransactionHistoryTableModal">

          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("Close") ?></button>
      </div>
    </div>
  </div>
</div>
<!-- Modal for Transaction History End -->

<style>
    @media screen and (min-width: 992px) {
        .modal-lg {
            width: 100%; /* New width for large modal */
        }
        @-moz-document url-prefix() {
            .modal-lg {
                width: 100%; /* Firefox New width for large modal */
            }
        }
    }
</style>

<script type="text/javascript">

    var status = '<?=$conditions['status'];?>';
    var playerId = '';
    var status = '';
    var itemId = '';

    $(document).ready( function() {
       var dataTable = $('#shopper-list-application-table').DataTable({

            autoWidth: false,
            searching: false,
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
           <?php if ($conditions['status'] != 0) {?>
               "columnDefs": [
                   {
                       // "targets": [ 0 ],
                       // "visible": false
                   }
               ],
           <?php }?>
           buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                     className:'btn  btn-sm'
                },
                <?php

if ($this->permissions->checkPermissions('export_shopping_request_list')) {

	?>
                {
                    text: '<?php echo lang("lang.export_excel"); ?>',
                    className:'btn  btn-sm btn-primary export_excel',
                    action: function ( e, dt, node, config ) {
                        var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};


                       // utils.safelog(d);
                        $.post(site_url('/export_data/shoppingItemList'), d, function(data){
                            // utils.safelog(data);

                            //create iframe and set link
                            if(data && data.success){
                                $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                            }else{
                                alert('export failed');
                            }
                        });
                    }
                },

                    {
                        text: "<?php echo lang('CSV Export'); ?>",
                        className:'btn btn-sm btn-primary',
                        action: function ( e, dt, node, config ) {
                            var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};


                            <?php if ($this->utils->isEnabledFeature('export_excel_on_queue')) {?>
                                $("#_export_excel_queue_form").attr('action', site_url('/export_data/shoppingItemList/true'));
                                $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                $("#_export_excel_queue_form").submit();
                            <?php } else {?>

                            $.post(site_url('/export_data/shoppingItemList/true'), d, function(data){
                                // utils.safelog(data);

                                //create iframe and set link
                                if(data && data.success){
                                    $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                                }else{
                                    alert('export failed');
                                }
                            });
                            <?php }?>

                        }
                    }

                <?php
}
?>
            ],
            order: [[7, 'desc']],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post("/api/shoppingItemClaimList", data, function(data) {
                    callback(data);
                },'json');
            },

        });

        // $('#search-form').submit( function(e) {
        //     e.preventDefault();
        //     dataTable.ajax.reload();
        // });

        $('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').trigger('submit');
            }
        });

        $('input[type="radio"].dwStatus').change( function() {
            var dwStatus = $(this).val();

            //clear all checked and check current
            $('.dashboard-stat').removeClass('checked');
            $('.dashboard-stat.panel_'+dwStatus).addClass('checked');

            $('#search-form').trigger('submit');
        });
    });


    function approveOrDeclinedShopItemClaimRequest(playerId,itemId,status){
        var msg = "";
        if(status == "<?php echo Shopper_list::APPROVED ?>"){
            msg = "<?php echo lang('confirm.approve'); ?>";
        }else{
            msg = "<?php echo lang('confirm.decline'); ?>";
        }
        if(confirm(msg)){
            window.location.href="<?php echo site_url('marketing_management/approveOrDeclinedShopItemClaimRequest') ?>/"+playerId+"/"+itemId+"/"+status;
        }
    }

    function showShoppinItemDeclineReasonModal(shoppingItemPlayerId,shoppingItemId,shoppingItemStatus){
        itemId = shoppingItemId;
        playerId = shoppingItemPlayerId;
        status = shoppingItemStatus;
        // $('#modalShoppingItemId').val(itemId+','+playerId+','+status);
    }

    function declineShoppingItemClaimRequestWithReason(){
        console.log("decline now");
        var reason = $('#modalShoppingItemDeclineReason').val().replace("%20", " ");
        $.ajax({
            'url' : '<?php echo site_url('marketing_management/approveOrDeclinedShopItemClaimRequest') ?>/' +playerId + '/' + itemId + '/' + status,
            'type' : 'POST',
            'dataType' : "json",
            'data': {'reason':reason},
            'success' : function(data){
                window.location.href="<?php echo site_url('marketing_management/shoppingClaimRequestList') ?>";
            }
        });
    }




    function showShoppingTransactionHistoryModal(playerId){
        $.ajax({
            'url' :'<?php echo site_url('marketing_management/getShoppingTransactionHistory') ?>/'+playerId,
            'type' : 'GET',
            'dataType' : "html",
            'success' : function(data){
                data = JSON.parse(data);
                  $('#shoppingTransactionHistoryTableModal').html("");
                  $('#shoppingTransactionHistoryTableModal').append("<table class='table table-bordered table-hover dataTable'><th><?php echo lang('Player') ?></th><th><?php echo lang('Date & Time') ?></th><th><?php echo lang('Status') ?></th><th><?php echo lang('Reason') ?></th><th><?php echo lang('Processed By') ?></th></table>");

                  var table = $('#shoppingTransactionHistoryTableModal').children();

                  for (i = 0; i < data.length; i++){
                    playerName = data[i].player_username ? data[i].player_username : "N/A";
                    processedDatetime = data[i].processed_datetime ? data[i].processed_datetime : "N/A";
                    status = data[i].status;
                    if(status == "<?php echo Shopper_list::REQUEST ?>"){
                        status = "<?php echo lang('Request') ?>";
                    }else if(status == "<?php echo Shopper_list::APPROVED ?>"){
                        status = "<?php echo lang('Approved') ?>";
                    }else if(status == "<?php echo Shopper_list::DECLINED ?>"){
                        status = "<?php echo lang('Declined') ?>";
                    }
                    reason = data[i].notes ? data[i].notes : "<?php echo lang('N/A') ?>";
                    processedBy = data[i].processed_by ? data[i].processed_by : "<?php echo lang('N/A') ?>";

                    table.append("<tr><td>"+playerName+"</td><td>"+processedDatetime+"</td><td>"+status+"</td><td>"+reason+"</td><td>"+processedBy+"</td></tr>");
                  }
            }
        });
    }
</script>