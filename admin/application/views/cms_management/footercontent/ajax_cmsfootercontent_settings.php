<table class="table table-striped table-hover table-responsive" id="my_table">
    <thead>
        <tr>
            <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
            <th><?= lang('cms.title'); ?></th>
            <th><?= lang('player.62'); ?></th>
            <th><?= lang('cms.createdon'); ?></th>
            <th><?= lang('cms.createdby'); ?></th>
            <th><?= lang('cms.updatedon'); ?></th>
            <th><?= lang('cms.updatedby'); ?></th>                                    
            <th><?= lang('lang.status'); ?></th>
            <th><?= lang('lang.action'); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php   //var_dump($data);
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
                            <td>
                                <?php if($data['footercontentId'] != 1){ ?>
                                    <input type="checkbox" class="checkWhite" id="<?= $data['footercontentId']?>" name="footercontentcms[]" value="<?= $data['footercontentId']?>" onclick="uncheckAll(this.id)"/></td>
                                <?php } ?>
                            <td><?= $data['footercontentName'] == '' ? '<i class="help-block">'. lang('lang.norecord') .'<i/>' : '<span data-toggle="tooltip" title="'. lang('tool.cms05') .'" data-placement="top">'.anchor_popup(BASEURL . 'cmsfootercontent_management/viewFootercontentDetails/'.$data['footercontentId'],  $data['footercontentName'],$atts_popup).'</span>' ?></td>
                            <td><?= $data['language'] == '' ? '<i class="help-block">'. lang('lang.norecord') .'<i/>' : ($data['language'] == 'en') ? 'English':'中文' ?></td>
                            <td><?= $data['createdOn'] == '' ? '<i class="help-block">'. lang('lang.norecord') .'<i/>' : $data['createdOn'] ?></td>
                            <td><?= $data['createdBy'] == '' ? '<i class="help-block">'. lang('lang.norecord') .'<i/>' : $data['createdBy'] ?></td>
                            <td><?= $data['updatedOn'] == '' ? '<i class="help-block">'. lang('lang.norecord') .'<i/>' : $data['updatedOn'] ?></td>
                            <td><?= $data['updatedBy'] == '' ? '<i class="help-block">'. lang('lang.norecord') .'<i/>' : $data['updatedBy'] ?></td>                                                    
                            <td><?= $data['status'] == '' ? '<i class="help-block">'. lang('cms.nodailymaxwithdrawal') .'<i/>' : $data['status'] ?></td>                                                
                            <td>
                                <div class="actionFootercontentCMS">
                                    <?php if($data['status'] == 'active'){ ?>
                                        <a href="<?= BASEURL . 'cmsfootercontent_management/activateFootercontentCms/'.$data['footercontentId'].'/'.'inactive' ?>">
                                        <span data-toggle="tooltip" title="<?= lang('lang.deactivate'); ?>" class="glyphicon glyphicon-ok-sign" data-placement="top">
                                        </span>
                                    </a>
                                    <?php } else{ ?>
                                        <a href="<?= BASEURL . 'cmsfootercontent_management/activateFootercontentCms/'.$data['footercontentId'].'/'.'active' ?>">
                                        <span data-toggle="tooltip" title="<?= lang('lang.activate'); ?>" class="glyphicon glyphicon-remove-circle" data-placement="top">
                                        </span>
                                        </a>
                                    <?php }  ?>
                                    
                                    <span class="glyphicon glyphicon-edit editFootercontentCmsBtn" data-toggle="tooltip" title="<?= lang('lang.edit'); ?>" onclick="CMSFootercontentManagementProcess.getFootercontentCmsDetails(<?= $data['footercontentId'] ?>)" data-placement="top">
                                    </span>
                                    
                                    <?php if($data['footercontentId'] != 1){ ?>
                                    <a href="<?= BASEURL . 'cmsfootercontent_management/deleteFootercontentCmsItem/'.$data['footercontentId'] ?>">
                                        <span data-toggle="tooltip" title="<?= lang('lang.delete'); ?>" class="glyphicon glyphicon-trash" data-placement="top">
                                        </span>
                                    </a>
                                    <?php } ?>
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