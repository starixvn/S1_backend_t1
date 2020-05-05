<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt"><i class="icon-spinner4"></i> <?= lang('cms.logosettings'); ?> </h4>
				<!-- <a href="#" class="btn btn-primary btn-sm pull-right" id="add_cmslogo_sec"><span id="addLogoCmsGlyhicon" class="glyphicon glyphicon-plus-sign"></span></a> -->
			</div>

			<div class="panel-body" id="logo_panel_body">

				<!-- Add Logo -->
<!-- 				<div class="row add_cmslogo_sec">
		            <div class="col-md-12">		            	
		                <div class="well" style="overflow: auto">
		                    <form action="<?= BASEURL . 'cmslogo_management/addLogoCms' ?>" method="post"  role="form" id="form-cmspromo" accept-charset="utf-8" enctype="multipart/form-data">
		                        <div class="row">
		                            <div class="col-md-5">
		                                <h6><label for="category"><?= lang('cms.category'); ?>: </label></h6>
		                                <select id="category" name="category" class="form-control input-sm" required>
		                                	<option value="">-- <?= lang('lang.select'); ?> --</option>
		                                	<option value="1"><?= lang('cms.headerlogo'); ?> (127 x 95)</option>
		                                </select>
		                                <span class='uploadNote'><?= lang('cms.uploadNote'); ?>"</span>
		                            </div>

		                            <div class="col-md-5">
		                              <h6><label for="groupDescription"><?= lang('cms.uploadLogo'); ?>: </label></h6>
		                              	<input type="hidden" name="logo_url" id="logo_url" class="form-control" readonly>
		                                <input type="file" id="userfile" name="userfile" class="form-control input-sm" onchange="setURL(this.value);" value="<?= set_value('userfile'); ?>" required>
		                            </div>

		                            <div class="col-md-2 pull-left">
		                                <br/><br/>
		                                <input type="submit" value="<?= lang('lang.add'); ?>" class="btn btn-sm btn-primary" />
		                                <span class="btn btn-sm btn-danger addlogo-cancel-btn" /><?= lang('lang.cancel'); ?></span>
		                            </div>
		                        </div>
		                    </form>
		                </div>
		            </div>
		        </div> -->
		        
		        <!-- edit cms logo -->
		        <div class="row edit_cmslogo_sec">
		            <div class="col-md-12">
		                <div class="well" style="overflow: auto;">
		                    <form class="form-horizontal" action="<?= BASEURL . 'cmslogo_management/addLogoCms' ?>" method="post" role="form" id="form-editcmspromo"  accept-charset="utf-8" enctype="multipart/form-data">
			                    <div class="form-group">   
			                        <div class="col-md-2">
			                        	<h6><label for="editLogoCmsImg" class="control-label"><?= lang('cms.headerlogo'); ?>: </label></h6>
			                        	<img id="editLogoCmsImg" src="" style="align: left; valign= middle; width: 150px; height: 150px; margin: 0 1px 0 0; display:block;"/>
			                        	<input type="hidden" name="editLogoCms" id="editLogoCms" >
		                              	<input type="hidden" name="logo_url" id="logo_url" class="form-control" readonly>
			                        </div>
			                        <div class="col-md-4">
			                        	<h6><label class="control-label"><?= lang('cms.uploadLogo'); ?>: </label></h6>
			                        	<input type="file" name="userfile" id="userfile" class="form-control input-sm" onchange="setURLEditLogoCms(this,this.value);" value="<?= set_value('editPromoThumbnailImg'); ?>">
			                        </div>
			                        <div class="col-md-4">
			                        	<h6><label for="category" class="control-label"><?= lang('cms.category'); ?>: </label></h6>
			                        	<input type="hidden" id="editLogocmsId" name="logocmsId" class="form-control input-sm" required>
		                                <select id="editCategory" name="category" class="form-control input-sm" required>
		                                	<option value="">-- <?= lang('lang.select'); ?> --</option>
		                                	<option value="1"><?= lang('cms.headerlogo'); ?> (127 x 95)</option>
		                                </select>
		                                <span class='uploadNote' style="color:#888;"><?= lang('cms.uploadNote'); ?></span>
			                        </div>
			                        <div class="col-md-2" style="text-align:center;margin-top:21px;">
			                        	<br/>
		                                <input type="submit" value="<?= lang('lang.save'); ?>" class="btn btn-sm btn-info" />
		                                <span class="btn btn-sm btn-default editlogocms-cancel-btn" /><?= lang('lang.cancel'); ?></span>
		                            </div>
			                    </div>
		                    </form>
		                </div>
		            </div>
		        </div>
				<div id="logoList" class="table-responsive">
	                <form action="<?= BASEURL . 'cmslogo_management/deleteSelectedLogoCms'?>" method="post" role="form">                    
	                    <div id="cmslogo_table" class="table-responsive">
							<table class="table table-striped table-hover" id="myTable" style="width:100%;">
								<thead>
									<tr>
										<!-- <th>Logo Name</th> -->
										<th></th>
										<th><?= lang('cms.category'); ?></th>
										<th><?= lang('cms.image'); ?></th>
										<th><?= lang('cms.createdon'); ?></th>
										<th><?= lang('cms.createdby'); ?></th>
										<th><?= lang('cms.updatedon'); ?></th>
										<th><?= lang('cms.updatedby'); ?></th>
										<th><?= lang('lang.status'); ?></th>
										<th><?= lang('lang.action'); ?></th>
									</tr>
								</thead>

								<tbody>
									<?php if(!empty($logo)) { ?>
										<?php foreach ($logo as $value) { 
														switch ($value['category']) {
															case 1:
																$value['category'] = 'Header Logo';
																break;
															
															default:
																# code...
																break;
														}?>
											<tr>
												<!-- <td><?= $value['logoName'] ?></td> -->
												<td></td>
												<td><?= $value['category'] == '' ? '<i class="help-block"><?= lang("lang.norecord"); ?><i/>' : $value['category'] ?></td>
												<td><?= $value['logoName'] == '' ? '<i class="help-block"><?= lang("lang.norecord"); ?><i/>' : '<img id="logo_name" src="'.APPPATH.'../resources/images/cmslogo/' .$value['logoName'].'" >' ?></td>
												<td><?= $value['createdOn'] ?></td>	
												<td><?= $value['createdBy'] ?></td>	
												<td><?= $value['updatedOn'] == '' ? '<i class="help-block"><?= lang("lang.norecord"); ?><i/>' : $value['updatedOn'] ?></td>	
												<td><?= $value['updatedBy'] == '' ? '<i class="help-block"><?= lang("lang.norecord"); ?><i/>' : $value['updatedBy'] ?></td>	
												<td><?= $value['status'] ?></td>					
												<td>
													<div class="actionCmsLogoGroup">
		                                                    <span style="cursor:pointer;" class="glyphicon glyphicon-edit editLogoCmsBtn" data-toggle="tooltip" title="<?= lang('lang.edit'); ?>" onclick="CMSLogoManagementProcess.getLogoCmsDetails(<?= $value['cmsLogoId'] ?>)" data-placement="top">
		                                                    </span>
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
						</div>
					</form>
				</div>
			</div>
			<div class="panel-footer"></div>
		</div>
	</div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<br/><br/><br/>
	<center>
		<img src="" id="logo_img"/>
	</center>
</div>


<script type="text/javascript">
    $(document).ready(function(){
        $('#myTable').DataTable({
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
</script>
