<table class="table table-striped table-hover table-responsive" id="my_table">
<thead>
    <tr>
        <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
        <th><?= lang('cms.promotitle'); ?></th>
        <th><?= lang('cms.promodesc'); ?></th>
        <th><?= lang('cms.promothumb'); ?></th>
        <th><?= lang('cms.promolink'); ?></th>
        <th><?= lang('cms.createdon'); ?></th>
        <th><?= lang('cms.createdby'); ?></th>
        <th><?= lang('cms.updatedon'); ?></th>
        <th><?= lang('cms.updatedby'); ?></th>                                    
        <th><?= lang('lang.status'); ?></th>
        <th><?= lang('lang.action'); ?></th>
    </tr>
</thead>

<tbody>
    <?php   //var_dump($data);exit();
            $atts_popup = array(
                                                  'width'      => '1030',
                                                  'height'     => '600',
                                                  'scrollbars' => 'yes',
                                                  'status'     => 'yes',
                                                  'resizable'  => 'no',
                                                  'screenx'    => '0',
                                                  'screeny'    => '0'
                                                );
            
            if(!empty($data)) {
                foreach($data as $data) {
    ?>
                    <tr>
                        <td><input type="checkbox" class="checkWhite" id="<?= $data['promoCmsSettingId']?>" name="promocms[]" value="<?= $data['promoCmsSettingId']?>" onclick="uncheckAll(this.id)"/></td>
                        <td><?= $data['promoName'] == '' ? '<i class="help-block"><?= lang("lang.norecord"); ?><i/>' : '<span data-toggle="tooltip" title="'. lang('tool.cms05') .'" data-placement="top">'.anchor_popup(BASEURL . 'cms_management/viewPromoDetails/'.$data['promoCmsSettingId'],  $data['promoName'],$atts_popup).'</span>' ?></td>
                        <td><?= $data['promoDescription'] == '' ? '<i class="help-block"><?= lang("lang.norecord"); ?><i/>' : $data['promoDescription'] ?></td>
                        <td><?= $data['promoThumbnail'] == '' ? '<i class="help-block"><?= lang("lang.norecord"); ?><i/>' : '<img id="banner_name" src="'.APPPATH.'../resources/images/promothumbnails/' .$data['promoThumbnail'].'" >' ?></td>
                        <td><?= $data['promoId'] == '' ? '<i class="help-block"><?= lang("lang.norecord"); ?><i/>' : $data['promoId'] ?></td>
                        <td><?= $data['createdOn'] == '' ? '<i class="help-block"><?= lang("lang.norecord"); ?><i/>' : $data['createdOn'] ?></td>
                        <td><?= $data['createdBy'] == '' ? '<i class="help-block"><?= lang("lang.norecord"); ?><i/>' : $data['createdBy'] ?></td>
                        <td><?= $data['updatedOn'] == '' ? '<i class="help-block"><?= lang("lang.norecord"); ?><i/>' : $data['updatedOn'] ?></td>
                        <td><?= $data['updatedBy'] == '' ? '<i class="help-block"><?= lang("lang.norecord"); ?><i/>' : $data['updatedBy'] ?></td>                                                    
                        <td><?= $data['status'] == '' ? '<i class="help-block"><?= lang("cms.nodailymaxwithdrawal"); ?><i/>' : $data['status'] ?></td>                                                
                        <td>
                            <div class="actionPromoCMS">
                                <?php if($data['status'] == 'active'){ ?>
                                    <a href="<?= BASEURL . 'cms_management/activatePromoCms/'.$data['promoCmsSettingId'].'/'.'inactive' ?>">
                                    <span data-toggle="tooltip" title="<?= lang('lang.deactivate'); ?>" class="glyphicon glyphicon-ok-sign" data-placement="top">
                                    </span>
                                </a>
                                <?php } else{ ?>
                                    <a href="<?= BASEURL . 'cms_management/activatePromoCms/'.$data['promoCmsSettingId'].'/'.'active' ?>">
                                    <span data-toggle="tooltip" title="<?= lang('lang.activate'); ?>" class="glyphicon glyphicon-remove-circle" data-placement="top">
                                    </span>
                                    </a>
                                <?php }  ?>
                                
                                <span class="glyphicon glyphicon-edit editCmsPromoBtn" data-toggle="tooltip" title="<?= lang('lang.edit'); ?>" onclick="CMSManagementProcess.getPromoCmsDetails(<?= $data['promoCmsSettingId'] ?>)" data-placement="top">
                                </span>
                                
                                <a href="<?= BASEURL . 'cms_management/deletePromoCmsItem/'.$data['promoCmsSettingId'] ?>">
                                    <span data-toggle="tooltip" title="<?= lang('lang.delete'); ?>" class="glyphicon glyphicon-trash" data-placement="top">
                                    </span>
                                </a>
                            </div>
                        </td>
                    </tr>
    <?php       }
            } else {
     ?>

        <tr>
            <td colspan="12" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
        </tr>
    <?php } ?>
</tbody>
</table>

<div class="col-md-12 col-offset-0">
<ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>