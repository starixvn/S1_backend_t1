<table class="table table-striped table-hover" id="my_table">
    <thead>
        <tr>
            <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
            <!-- <th>Banner Name</th> -->
            <th><?= lang('cms.category'); ?></th>
            <th><?= lang('cms.size'); ?></th>
            <th><?= lang('cms.image'); ?></th>
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
        <?php if(!empty($banner)) { ?>
            <?php foreach ($banner as $value) { 
                            switch ($value['category']) {
                                case 1:
                                    $value['category'] = lang('cms.home');
                                    $value['dimension'] = '797 x 320';
                                    break;
                                case 2:
                                    $value['category'] = lang('cms.casino');
                                    $value['dimension'] = '960 x 335';
                                    break;
                                case 3:
                                    $value['category'] = lang('cms.home');
                                    $value['dimension'] = '244 x 309';
                                    break;
                                case 4:
                                    $value['category'] = lang('cms.home');
                                    $value['dimension'] = '145 x 222';
                                    break;
                                default:
                                    break;
                            }?>
                <tr>
                    <td><input type="checkbox" class="checkWhite" id="<?= $value['bannerId']?>" name="bannercms[]" value="<?= $value['bannerId']?>" onclick="uncheckAll(this.id)"/></td>
                    <!-- <td><?= $value['bannerName'] ?></td> -->
                    <td><?= $value['category'] == '' ? '<i class="help-block">'. lang('lang.norecord') .'<i/>' : $value['category'] ?></td>
                    <td><?= $value['dimension'] == '' ? '<i class="help-block">'. lang('lang.norecord') .'<i/>' : $value['dimension'] ?></td>
                    <td><?= $value['bannerName'] == '' ? '<i class="help-block">'. lang('lang.norecord') .'<i/>' : '<img id="banner_name" src="'.APPPATH.'../resources/images/cmsbanner/' .$value['bannerName'].'" >' ?></td>
                    
                    <?php if($value['language'] == 'en') { ?>
                        <td>English</td>
                    <?php } else if($value['language'] == 'ch') { ?>
                        <td>中文</td>
                    <?php } ?>

                    <td><?= $value['createdOn'] ?></td> 
                    <td><?= $value['createdBy'] ?></td> 
                    <td><?= $value['updatedOn'] == '' ? '<i class="help-block">'. lang('lang.norecord') .'<i/>' : $value['updatedOn'] ?></td> 
                    <td><?= $value['updatedBy'] == '' ? '<i class="help-block">'. lang('lang.norecord') .'<i/>' : $value['updatedBy'] ?></td> 
                    <td><?= $value['status'] ?></td>                    
                    <td>
                        <div class="actionCmsBannerGroup">
                                <?php if($value['status'] == 'active'){ ?>
                                    <a href="<?= BASEURL . 'cmsbanner_management/activateBannerCms/'.$value['bannerId'].'/'.'inactive' ?>">
                                    <span data-toggle="tooltip" title="<?= lang('tool.cms01'); ?>" class="glyphicon glyphicon-ok-sign" data-placement="top">
                                    </span>
                                </a>
                                <?php } else{ ?>
                                    <a href="<?= BASEURL . 'cmsbanner_management/activateBannerCms/'.$value['bannerId'].'/'.'active' ?>">
                                    <span data-toggle="tooltip" title="<?= lang('tool.cms02'); ?>" class="glyphicon glyphicon-remove-circle" data-placement="top">
                                    </span>
                                    </a>
                                <?php }  ?>

                                <!-- <a href="<?= BASEURL . 'cmsbanner_management/bankAccountBackupManager/'.$row['bannerId'].'/'.$row['bankName'] ?>">
                                    <span data-toggle="tooltip" title="Add Backup" class="glyphicon glyphicon-hdd" data-toggle="tooltip" data-placement="top">
                                    </span>
                                </a> -->

                                <span class="glyphicon glyphicon-edit editBannerCmsBtn" data-toggle="tooltip" title="<?= lang('lang.edit'); ?>" onclick="CMSBannerManagementProcess.getBannerCmsDetails(<?= $value['bannerId'] ?>)" data-placement="top">
                                </span>
                                <?php //if($value['dimension'] != '244 x 309' && $value['dimension'] != '145 x 222'){ ?>
                                    <a href="<?= BASEURL . 'cmsbanner_management/deleteBannerCmsItem/'.$value['bannerId'] ?>">
                                        <span data-toggle="tooltip" title="<?= lang('lang.delete'); ?>" class="glyphicon glyphicon-trash" data-placement="top">
                                        </span>
                                    </a>
                                <?php //}  ?>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
                <tr>
                    <td colspan="9" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
                </tr>
        <?php } ?>
    </tbody>
</table>
<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>