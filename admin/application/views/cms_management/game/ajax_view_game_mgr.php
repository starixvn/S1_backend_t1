<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th><?= lang('cms.gamename'); ?></th>
			<th><?= lang('cms.gametype'); ?></th>
			<th><?= lang('cms.progressive'); ?></th>
			<th><?= lang('cms.branded'); ?></th>
			<th><?= lang('cms.gamecode'); ?></th>
			<th><?= lang('cms.flash'); ?></th>
			<th><?= lang('cms.downloadclient'); ?></th>
			<th><?= lang('cms.mobile'); ?></th>
			<th><?= lang('lang.status'); ?></th>
			<th><?= lang('lang.action'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php 
			if(!empty($games)) {
				foreach($games as $game) {
		?>										
						<tr>																								
							<td><?= $game['gameName'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $game['gameName'] ?></td>
							<td><?= $game['gameType'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $game['gameType'] ?></td>
							<td><?= $game['progressive'] == '' ? '<?= lang("cms.nonprogressive"); ?>' : $game['progressive'] ?></td>
							<td><?= $game['branded'] == 0 ? '<?= lang("lang.no"); ?>' : '<?= lang("lang.yes"); ?>' ?></td>
							<td><?= $game['gameCode'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $game['gameCode'] ?></td>
							<td><?= $game['flashGamePlatform'] == 0 ? '<?= lang("lang.no"); ?>' : '<?= lang("lang.yes"); ?>' ?></td>
							<td><?= $game['dlcGamePlatform'] == 0 ? '<?= lang("lang.no"); ?>' : '<?= lang("lang.yes"); ?>' ?></td>
							<td><?= $game['mobileGamePlatform'] == 0 ? '<?= lang("lang.no"); ?>' : '<?= lang("lang.yes"); ?>' ?></td>
							<td><?= $game['status'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $game['status'] ?></td>
							<td>	
								<div class="btn-group" role="group">
									<?php  if($game['status'] == 'deactivated'){ ?>
										
											<!-- <span class="btn-sm btn-info promoActionBtn" onClick="CMSManagementProcess.showPromoActivateFormSettings('<?= $promo['promoId'] ?>','<?= isset($promo['promoCode']) ? $promo['promoCode'] :'' ?>','<?= $promo['promoName'] ?>')">
												Activate
											</span> -->
											<a class="btn promoActionBtn" href="<?= BASEURL . 'cms_management/activateGame/'.$game['cmsGameId'] ?>">
												<span class="btn-sm btn-success review-btn">
													<?= lang('lang.activate'); ?>
												</span>
											</a>
									&nbsp;
									<?php }elseif($game['status'] == 'activated'){ ?>
												<a class="btn promoActionBtn" href="<?= BASEURL . 'cms_management/deactivateGame/'.$game['cmsGameId'] ?>">
													<span class="btn-sm btn-danger review-btn">
														<?= lang('lang.deactivate'); ?>
													</span>
												</a>
									&nbsp;
									<?php } ?>

									<a href="#" data-toggle="modal" data-target="#category_<?= $game['cmsGameId']?>" class="btn promoActionBtn">
										<span class="btn-sm btn-warning review-btn">
											<?= lang('cms.category'); ?>
										</span>
									</a>
											
									<div class="modal fade bs-example-modal-sm" id="category_<?= $game['cmsGameId']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog modal-sm">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
													<h4 class="modal-title" id="myModalLabel" style="margin: 0 10px;"><?= lang('cms.categgame'); ?>: <?= $game['gameName'] ?></h4>
												</div>

												<form action="<?= BASEURL . 'cms_management/gameCategory/' . $game['cmsGameId'] ?>" method="POST">
													<div class="modal-body">
														<?php foreach ($level as $levelvalue) { ?>
															<input type="checkbox" name="category[]" value="<?= $levelvalue['rankingLevelSettingId'] ?>" <?= ($this->cms_model->checkGameCategory($game['cmsGameId'], $levelvalue['rankingLevelSettingId'])) ? 'checked':''?> /> <?= $levelvalue['rankingLevelGroup'] . " " . $levelvalue['rankingLevel'] ?><br/>
														<?php } ?>
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
					<td colspan="8" style="text-align:center"><?= lang('lang.norec'); ?>
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