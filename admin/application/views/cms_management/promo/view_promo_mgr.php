<!-- Sort Option -->
<form action="<?= BASEURL . 'cms_management/sortPromo' ?>" method="post" role="form" style="margin-top:-15px;">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a href="#personal" style="color: white;" class="btn btn-info btn-sm hide_sortby"> 
                            <i class="glyphicon glyphicon-chevron-down hide_sortby_up" id=""></i>
                        </a> 
                        <?= lang('lang.sort'); ?>
                    </h4>
                </div>

                <div class="panel panel-body sortby_panel_body" id="" style="display: none;">                    
                     <div class="row">
                        <div class="col-md-12">
                        	<div class="col-md-1" style="margin-top:5px;"><?= lang('lang.show'); ?></div>
                            <div class="col-md-3" style="margin-left:-30px;">
                                                        
                                  <select class="form-control" name="promoSort">                                    
                                    <option value="activated" <?= $this->session->userdata('promoSort') == 'activated' ? 'selected' : ''?>><?= lang('cms.activatedpromo'); ?></option>
                                    <option value="nonactivated" <?= $this->session->userdata('promoSort') == 'nonactivated' ? 'selected' : ''?>><?= lang('cms.nonactivatedpromo'); ?></option>                                                                                                                     
                                  </select>
                            </div>      
                            <div class="col-md-4" style="margin-left:-20px;">                            	
                                <input class="btn btn-sm btn-primary" type="submit" value="<?= lang('lang.submit'); ?>" />
                            </div>      
                        </div>
                    </div> 
                </div>                                
            </div>
        </div>
    </div>
</form>
<!--end of Sort Information-->

<!-- start promotions list -->
<div class="panel panel-primary
              " style="margin-top:-15px;">
	<div class="panel-heading">
		<div class="col-md-8 pull-left">
			<h4 class="panel-title "><i class="glyphicon glyphicon-list-alt"></i> <?= lang('cms.promotionslist'); ?><span class="choosenDateRange">&nbsp;<?= isset($choosenDateRange) ? ($choosenDateRange) : '' ?></span></h4>
		</div>					
		<div class="col-md-4"></div>
		<div class="clearfix"></div>
	</div>
	
	<!-- start data table -->
	<div class="panel panel-body" id="player_panel_body">
		<div id="paymentList" class="table-responsive">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th><?= lang('cms.promoname'); ?></th>
						<th><?= lang('cms.promocode'); ?></th>
						<th><?= lang('cms.promoperiod'); ?></th>
						<th><?= lang('lang.action'); ?></th>
					</tr>
				</thead>

				<tbody>
					<?php 
						if(!empty($promos)) {
							foreach($promos as $promo) {
					?>										
									<tr>																								
										<td><?= $promo['promoName'] == '' ? '<i class="help-block"><?= lang("lang.norecyet");?><i/>' : $promo['promoName'] ?></td>
										<td><?= isset($promo['promoCode']) ? $promo['promoCode'] == '' ? '<i class="help-block"><?= lang("lang.norecyet");?><i/>' : $promo['promoCode'] :'' ?></td>
										<td>
											<?= $promo['promoStartTimestamp'] == '' ? '<i class="help-block"><?= lang("lang.norecyet");?><i/>' : $promo['promoStartTimestamp'].' - '.$promo['promoEndTimestamp'] ?>																						
										</td>
										<td>
											<div class="btn-group" role="group">
												<a class="promoActionBtn" href="<?= BASEURL . 'marketing_management/promo_item/'.$promo['promoId'] ?>">
													<span class="btn-sm btn-info review-btn">
														<?= lang('cms.moredetail'); ?>
													</span>
												</a>
												&nbsp;
												<?php  if($this->session->userdata('promoSort') == 'nonactivated'){ ?>
													
														<!-- <span class="btn-sm btn-info promoActionBtn" onClick="CMSManagementProcess.showPromoActivateFormSettings('<?= $promo['promoId'] ?>','<?= isset($promo['promoCode']) ? $promo['promoCode'] :'' ?>','<?= $promo['promoName'] ?>')">
															Activate
														</span> -->
														<a class="promoActionBtn" href="<?= BASEURL . 'cms_management/activatePromo/'.$promo['promoId'] ?>">
															<span class="btn-sm btn-success review-btn">
																<?= lang('lang.activate'); ?>
															</span>
														</a>
												&nbsp;
												<?php }elseif($this->session->userdata('promoSort') == 'activated'){ ?>
															<a class="promoActionBtn" href="<?= BASEURL . 'cms_management/deactivatePromo/'.$promo['promoId'] ?>">
																<span class="btn-sm btn-danger review-btn">
																	<?= lang('lang.deactivate'); ?>
																</span>
															</a>
												&nbsp;
												<?php } ?>

												<a href="#" data-toggle="modal" data-target="#category_<?= $promo['promoId']?>" class="promoActionBtn">
													<span class="btn-sm btn-warning review-btn">
														<?= lang('cms.category'); ?>
													</span>
												</a>

												<?php 
													$category = $this->cms_manager->getPromoCategory($promo['promoId']);
													
													$featured = false;
													$new = false;
													$all = false;
													$vip = false;

													if($category != null) {
														foreach ($category as $category) {
															if($category['category'] == 'featured') {
																$featured = true;
															} else if($category['category'] == 'new') {
																$new = true;
															} else if($category['category'] == 'all') {
																$all = true;
															} else if($category['category'] == 'vip') {
																$vip = true;
															}
														}
													}
												?>
														
												<div class="modal fade bs-example-modal-sm" id="category_<?= $promo['promoId']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
													<div class="modal-dialog modal-sm">
														<div class="modal-content">
															<div class="modal-header">
																<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
																<h4 class="modal-title" id="myModalLabel" style="margin: 0 10px;"><?= lang('cms.promoNote'); ?>: <?= $promo['promoName']?></h4>
															</div>

															<form action="<?= BASEURL . 'cms_management/promoCategory/' . $promo['promoId'] ?>" method="POST">
																<div class="modal-body">
																	<input type="checkbox" name="category[]" value="featured" <?= ($featured) ? 'checked':'' ?> /> <?= lang('cms.featured'); ?><br/>
																	<input type="checkbox" name="category[]" value="new" <?= ($new) ? 'checked':'' ?> /> <?= lang('cms.new'); ?><br/>
																	<input type="checkbox" name="category[]" value="all" <?= ($all) ? 'checked':'' ?> /> <?= lang('cms.all'); ?><br/>
																	<input type="checkbox" name="category[]" value="vip" <?= ($vip) ? 'checked':'' ?> /> <?= lang('cms.vip'); ?><br/>
																</div>

																<div class="modal-footer">
																	<input type="submit" class="btn btn-primary" value="<?= lang('lang.save'); ?>"/>
																</div>
															</form>
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr>
					<?php 		
							}
						}
						else{ ?>
							<tr>
								<td colspan="4" style="text-align:center"><?= lang('lang.norec'); ?>
								</td>
							</tr>
					<?php	}
					?>
				</tbody>
			</table>

			<br>

			<div class="row">
				<div class="col-md-12">
					<ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
				</div>
			</div>
		</div>
	</div>
	<!-- end data table -->

	<div class="panel-footer">
	</div>
</div>
<!-- end promostions list -->
	

