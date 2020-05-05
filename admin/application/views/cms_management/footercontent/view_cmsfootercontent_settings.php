
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-file-text"></i> <?= lang('cms.createfooter'); ?>
            <a href="#" class="btn btn-default pull-right" id="add_cmsfootercontent_sec">
                <span id="addFootercontentCmsGlyhicon" class="glyphicon glyphicon-plus-sign"></span>
            </a>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <!-- add vip group -->
        <div class="row add_cmsfootercontent_sec">
            <div class="col-md-12">
                <div class="well" style="overflow: auto;">
                    <form action="<?= BASEURL . 'cmsfootercontent_management/addNewFootercontent' ?>" method="post" role="form" id="form-cmsfootercontent"  accept-charset="utf-8" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">                            
                                <h6><label for="footercontentName"><?= lang('cms.footertitle'); ?>: </label></h6>
                                <input type="text" id="footercontentName" name="title" class="form-control input-sm" required>                                
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-12">                            
                                <h6><label for="language"><?= lang('cms.language'); ?>: </label></h6>
                                <select id="addFootercontentLanguage" name="language" class="form-control input-sm" required>   
                                    <option value="en">English</option> 
                                    <option value="ch">Chinese</option>
                                </select>                             
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-12" >
                                <h6><label for="footercontentDetails"><?= lang('cms.footercontent'); ?>: </label></h6>                    
                                
                                <div style="background-color:#fff;">
                                    <input name="content" type="hidden" class="footercontentContent" value="" required/>
                                    <div id="summernote" required><?= isset($form['content']) ? $form['content'] : '' ?></div>
                                </div>                                
                            </div>
                        </div>
                        <center>
                            <br/>
                            <input type="submit" value="<?= lang('lang.add'); ?>" class="btn btn-sm btn-info review-btn custom-btn-size" data-toggle="modal" />
                            <span class="btn btn-sm btn-default addfootercontent-cancel-btn" data-toggle="modal" /><?= lang('lang.cancel'); ?></span>  
                        </center>
                    </form>
                </div>
            </div>
        </div>

        <!-- edit cms footercontent -->
        <div class="row edit_cmsfootercontent_sec">
            <div class="col-md-12">
                <div class="well" style="overflow: auto;">
                    <form action="<?= BASEURL . 'cmsfootercontent_management/addNewFootercontent' ?>" method="post" role="form" id="form-editcmsfootercontent"  accept-charset="utf-8" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">                            
                                <h6><label for="footercontentName"><?= lang('cms.footertitle'); ?>: </label></h6>
                                <input type="hidden" id="editFootercontentcmsId" name="footercontentcmsId" class="form-control input-sm" required>
                                <input type="text" id="editFootercontentName" name="title" class="form-control input-sm" required>                                
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-12">                            
                                <h6><label for="language"><?= lang('cms.language'); ?>: </label></h6>
                                <select id="editFootercontentLanguage" name="language" class="form-control input-sm" required>   
                                    <option value="en">English</option> 
                                    <option value="ch">Chinese</option>
                                </select>                                  
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-12" >
                                <h6><label for="footercontentDetails"><?= lang('cms.footercontent'); ?>: </label></h6>
                                
                                <div style="background-color:#fff;">
                                    <input name="content" type="hidden" class="footercontentContent" required/>
                                    <div class="summernote" id="editFootercontentDetails"></div>
                                </div>                                
                            </div>
                        </div>
                        <center>
                            <br/>
                            <input type="submit" value="<?= lang('lang.save'); ?>" class="btn btn-sm btn-info review-btn custom-btn-size" data-toggle="modal" />
                            <span class="btn btn-sm btn-default editfootercontentcms-cancel-btn custom-btn-size" data-toggle="modal" /><?= lang('lang.cancel'); ?></span>
                        </center>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form action="<?= BASEURL . 'cmsfootercontent_management/deleteSelectedFootercontentCms'?>" method="post" role="form">
                    <?php if($export_report_permission){ ?>
                        <!-- <a href="<?= BASEURL . 'cms_management/exportToExcel/cmsfootercontentlist' ?>" >
                            <span data-toggle="tooltip" title="<?= lang('lang.exporttitle'); ?>" class="btn btn-sm btn-success" data-placement="top"><?= lang('lang.export'); ?>
                            </span>
                        </a> -->                    
                    <?php } ?>

                    <div id="footercontentcms_table" class="table-responsive">
                        <table class="table table-striped table-hover " id="my_table" style="width:100%;">
                            <button type="submit" class="btn btn-danger btn-sm btn-action" data-toggle="tooltip" data-placement="top" title="<?= lang('cms.deletesel'); ?>">
                                <i class="glyphicon glyphicon-trash" style="color:white;"></i> 
                            </button>
                            <thead>
                                <tr>
                                    <th></th>
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
                                                    <td></td>
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
                                                            
                                                            <span style="cursor:pointer" class="glyphicon glyphicon-edit editFootercontentCmsBtn" data-toggle="tooltip" title="<?= lang('lang.edit'); ?>" onclick="CMSFootercontentManagementProcess.getFootercontentCmsDetails(<?= $data['footercontentId'] ?>)" data-placement="top">
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
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </form>                
            </div>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#my_table').DataTable({
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
            "order": [ 1, 'asc' ],
            "dom": '<"top"fl>rt<"bottom"ip>',
            "fnDrawCallback": function(oSettings) {
                $('.btn-action').prependTo($('.top'));
            }
        });
    });
</script>