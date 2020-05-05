<?php include APPPATH . "/views/includes/popup_promorules_info.php";?>

<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-note"></i> <?=lang('cms.promoReqCancelList');?>

        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">


        <div class="row">
            <div class="col-md-12">
                <form action="<?=site_url('marketing_management/deleteSelectedPromoType')?>" method="post" role="form">

                    <!-- <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="<?=lang('cms.deletesel');?>">
                        <i class="glyphicon glyphicon-trash" style="color:white;"></i>
                    </button> -->

                    <div id="tag_table" class="table-responsive" style="overflow:hidden">
                        <table class="table table-bordered table-hover dataTable" id="my_table" style="margin: 0px 0 0 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <!-- <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th> -->
                                    <th><?=lang('player.01');?></th>
                                    <th><?=lang('player.07');?></th>
                                    <th><?=lang('cms.promotitle');?></th>
                                    <th><?=lang('cms.promoRuleName');?></th>
                                    <th><?=lang('cms.bonusAmount');?></th>
                                    <th><?=lang('cms.dateCancelRequest');?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($promoAppList)) {
	foreach ($promoAppList as $row) {
		?>
                                    <tr>
                                        <td></td>
                                        <!-- <td><input type="checkbox" class="checkWhite" id="<?=$row['playerpromoId']?>" name="playerpromo[]" value="<?=$row['playerpromoId']?>" onclick="uncheckAll(this.id)"/></td> -->
                                        <td><a href="<?=BASEURL . 'player_management/userInformation/' . $row['playerId']?>" data-toggle='tooltip' data-original-title='<?=lang("cms.checkPlayerDetails")?>' data-placement="right"><?=$row['username']?></a></td>
                                        <td><?=$row['groupName'] . ' ' . $row['vipLevel']?></td>
                                        <td><?php $atts_popup = array(
			'width' => '1030',
			'height' => '600',
			'scrollbars' => 'yes',
			'status' => 'yes',
			'resizable' => 'no',
			'screenx' => '0',
			'screeny' => '0');?>
                                            <?='<span data-toggle="tooltip" title="' . lang('cms.checkCmsPromo') . '" data-placement="right">' . anchor_popup(BASEURL . 'cms_management/viewPromoDetails/' . $row['promoCmsSettingId'], $row['promoTitle'], $atts_popup) . '</span>'?>
                                        </td>
                                        <td>
                                            <?php echo createPromoDetailButton($row['promorulesId'], $row['promoName']); ?>
                                        </td>
                                        <td><?=$row['bonusAmount']?></td>
                                        <td><?=$row['cancelRequestDate']?></td>
                                    </tr>
                                <?php }
} else {
	?>
                                <?php }
?>
                            </tbody>
                        </table>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>

<div class="modal fade bs-example-modal-md" id="promoCancel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel" style="margin: 0 10px;"><?=lang('cms.declineCancelPromoRequest');?>: </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form method="post" action="<?=BASEURL . 'marketing_management/declinePromoCancellationRequest'?>">
                            <?=lang('cms.declinePromoCancelReason');?>
                            <input type="hidden" name="declinePlayerPromoId" id="decline_playerpromoId" class="form-control">
                            <textarea name="reasonToCancel" class="form-control" rows="7" required></textarea>
                            <br/>
                            <center>
                            <button class="btn btn-primary" style="width:30%"><?=lang('lang.submit');?></button>
                            </center>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#my_table').DataTable({
            dom: "<'panel-body' <'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ]
        });
    });
    $('#promotype_sec').hide();
    $('#editpromotype_sec').hide();
    var promoTypeFlag = false;
    $('#addPromoTypeBtn').click(function() {
        if(!promoTypeFlag){
            $('#promotype_sec').show();
            $('#editpromotype_sec').hide();
            $('#addPromoTypeGlyph').removeClass('glyphicon glyphicon-plus-sign');
            $('#addPromoTypeGlyph').addClass('glyphicon glyphicon-minus-sign');
            promoTypeFlag = true;
        }else{
            $('#promotype_sec').hide();
            $('#addPromoTypeGlyph').removeClass('glyphicon glyphicon-minus-sign');
            $('#addPromoTypeGlyph').addClass('glyphicon glyphicon-plus-sign');
            promoTypeFlag = false;
        }

    });

    function getPromotypeDetails(promotypeId){
        $('#editpromotype_sec').show();
        $('#promotype_sec').hide();
        $.ajax({
            'url' : base_url + 'marketing_management/getPromoTypeDetails/' + promotypeId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                     // console.log(data[0]);
                     $('#editpromoTypeName').val(data[0].promoTypeName);
                     $('#editpromoTypeDesc').val(data[0].promoTypeDesc);
                     $('#promoTypeId').val(data[0].promotypeId);

                 }
         });
    }

    function checkAll(id) {
        var list = document.getElementsByClassName(id);
        var all = document.getElementById(id);

        if (all.checked) {
            for (i = 0; i < list.length; i++) {
                list[i].checked = 1;
            }
        } else {
            all.checked;

            for (i = 0; i < list.length; i++) {
                list[i].checked = 0;
            }
        }
    }

</script>

<script type="text/javascript">
    function viewPromoDeclineForm(playerpromoId){
        $('#decline_playerpromoId').val(playerpromoId);
    }

</script>