<?php include APPPATH . "/views/includes/popup_promorules_info.php";?>

<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-bullhorn"></i> <?=lang('cms.promoRuleSettings');?>
            <div class="clearfix"></div>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12">
            <a href="<?=site_url('marketing_management/add_new_promo_by_template')?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-success' ?>" data-toggle="tooltip" data-placement="left">
                <i class="fa fa-plus"></i> <?=lang('Add New Promo By Template')?>
            </a>
            <a href="<?=site_url('marketing_management/addNewPromo')?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info' ?>" data-toggle="tooltip" title="<?=lang('cms.addNewPromo');?>" data-placement="left">
                <span id="addDepositPromoGlyhicon" class="glyphicon glyphicon-plus-sign"></span> <?=lang('cms.addNewPromo')?>
            </a>

                <hr class="hr_between_table"/>
                <div id="promorule_table" class="table-responsive" style="overflow: hidden;">
                    <table class="table table-bordered table-hover dataTable" id="myTable" style="width:100%;">

                        <thead>
                            <tr>
                                <th></th>
                                <th class="tableHeaderFont"><?=lang('cms.promoname');?></th>
                                <th class="tableHeaderFont"><?=lang('cms.promoCat');?></th>
                                <th class="tableHeaderFont"><?=lang('cms.promoType');?></th>
                                <th class="tableHeaderFont"><?=lang('cms.createdon');?></th>
                                <th class="tableHeaderFont"><?=lang('cms.createdby');?></th>
                                <th class="tableHeaderFont"><?=lang('cms.updatedon');?></th>
                                <th class="tableHeaderFont"><?=lang('cms.updatedby');?></th>
                                <th class="tableHeaderFont"><?=lang('Auto/Manual');?></th>
                                <th class="tableHeaderFont"><?=lang('Expire Date');?></th>
                                <th class="tableHeaderFont"><?=lang('lang.status');?></th>
                                <th class="tableHeaderFont"><?=lang('cms.bonusRelease')?></th>
                                <th class="tableHeaderFont"><?=lang('lang.action');?></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
//var_dump($promorules);exit();
if (!empty($promorules)) {
	$cnt = 1;
	foreach ($promorules as $row) {
		?>
                                            <tr class="<?php echo $row['hide_date']<$this->utils->getNowForMysql() || $row['status'] != 0 ? "danger" : "" ; ?>" >
                                                <td></td>
                                                <td class='tableContent'><?=$row['promoName'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : '<a href="'.site_url('marketing_management/editPromoRule/'.$row['promorulesId']).'">'.$row['promoName'].'</a>';?></td>
                                                <td class='tableContent'><?=$row['promoTypeName'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : lang($row['promoTypeName'])?></td>
                                                <td class='tableContent'><?=$row['promoType'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['promoType'] == '0' ? 'Deposit' : 'Non-Deposit'?></td>
                                                <td class='tableContent'><?=$row['createdOn'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['createdOn']?></td>
                                                <td class='tableContent'><?=$row['createdBy'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['createdBy']?></td>
                                                <td class='tableContent'><?=$row['updatedOn'] == null ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['updatedOn']?></td>
                                                <td class='tableContent'><?=$row['updatedBy'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['updatedBy']?></td>
                                                <td class='tableContent <?php echo $row['bonusReleaseToPlayer'] == 0 ? "success" : "" ;?>'><?=$row['bonusReleaseToPlayer'] == 0 ? lang("Automatic") : lang("Manual");?></td>
                                                <td class='tableContent'><?=$row['hide_date']?></td>
                                                <td class='tableContent'><?=$row['status'] == 0 ? lang("pay.active") : lang("pay.inactive");?></td>
                                                <td class='tableContent'><?= !empty($row['bonusCount']) ? $row['bonusCount'] : 0 ?></td>
                                                <td class='tableContent'>
                                                    <div class="actionDepositPromoGroup" align="center">
                                                        <?php if ($row['status'] == 0 ) :?>
                                                            <a href="<?=site_url('marketing_management/activatePromoRule/' . $row['promorulesId'] . '/1')?>">
                                                                <span data-toggle="tooltip" title="<?=lang('lang.deactivate');?>" class="glyphicon glyphicon-ok-sign" data-placement="top">
                                                                </span>
                                                            </a>
                                                        <?php elseif($row['status'] == 1) :?>
                                                            <a href="<?=site_url('marketing_management/activatePromoRule/' . $row['promorulesId'] . '/0')?>">
                                                                <span data-toggle="tooltip" title="<?=lang('lang.activate');?>" class="glyphicon glyphicon-remove-circle" data-placement="top">
                                                                </span>
                                                            </a>
                                                        <?php endif;?>

                                                        <?php if ($this->permissions->checkPermissions('allow_to_enable_edit_promo_rules')) : ?>
                                                            <?php if ($row['enable_edit'] == 1  ) :?>
                                                                <a href="<?=site_url('marketing_management/enableEditPromoRule/' . $row['promorulesId'] . '/0')?>">
                                                                    <span data-toggle="tooltip" title="<?=lang('Locked');?>" class="fa fa-unlock" data-placement="top">
                                                                    </span>
                                                                </a>
                                                                <a href="javascript:void(0)" class="deletePromoruleItem" data-id="<?=$row['promorulesId']?>">
                                                                    <span data-toggle="tooltip" title="<?=lang('lang.delete');?>" class="glyphicon glyphicon-trash" data-placement="top">
                                                                    </span>
                                                                </a>
                                                            <?php elseif($row['enable_edit'] == 0) :?>
                                                                 <a href="<?=site_url('marketing_management/enableEditPromoRule/' . $row['promorulesId'] . '/1')?>">
                                                                    <span data-toggle="tooltip" title="<?=lang('system.word56');?>" class="fa fa-lock" data-placement="top">
                                                                    </span>
                                                                </a>
                                                            <?php endif;?>
                                                        <?php endif;?>
                                                        <!-- <a href="<?=site_url('marketing_management/editPromoRule/' . $row['promorulesId'])?>">
                                                            <span data-toggle="tooltip" title="<?=lang('lang.edit');?>" class="glyphicon glyphicon-edit" data-placement="top">
                                                            </span>
                                                        </a> -->
                                                    </div>
                                                </td>
                                            </tr>
                            <?php
$cnt++;
	}
}
?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div><div class="panel-footer"></div>
</div>

<!-- Delete Promorule Modal Start -->
<div class="modal fade" id="deletePromoruleModal" tabindex="-1" role="dialog" aria-labelledby="deletePromoruleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="deletePromoruleModalLabel"><?=lang('cms.deletePromoRule')?></h4>
            </div>
            <div class="modal-body deletePromoruleModalBody">
                <?=lang('cms.deletePromoruleMsg')?>
            </div>
            <div class="modal-footer">
                <input type="hidden" class="deleteSelectedPromoruleId">
                <button type="button" class="btn btn-primary delete-func" data-dismiss="modal"><?=lang('Confirm')?></button>
            </div>
        </div>
    </div>
</div>
<!-- Delete Promorule Modal End -->

<script type="text/javascript">
    $(document).ready(function() {
        $('#myTable').DataTable( {
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
            },{
                orderable: false,
                targets:   1
            } ],
            "order": [ 4, 'desc' ]
        } );

        //check delete successful or not, then prompt different msg
        $('.deletePromoruleItem').on('click', function(){
            var del_id = $(this).attr('data-id');
            var url = "<?=site_url('marketing_management/deletePromorule')?>" + "/" + del_id;
            $.post(url).done(function(data){
                if(data && data.success){
                    $('.deletePromoruleModalBody').html('<?=lang('cms.deletePromoruleSuccMsg');?>');
                    $('#deletePromoruleModal button').hide();
                    $('#deletePromoruleModal').modal('show');
                    window.location.reload();
                }else{
                    $('.deletePromoruleModalBody').html('<?=lang('cms.deletePromoruleFailMsg');?>');
                    $('#deletePromoruleModal').modal('show');
                }
            }).fail(function(){
                alert('Delete failed');
            });

        });
    } );
</script>