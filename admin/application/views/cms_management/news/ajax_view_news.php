<!-- <div class="row">
	<div class="col-md-12">
		<div class="btn-group">
            <button class="btn btn-info"><?= lang('lang.sort'); ?></button>
            <button class="btn btn-info dropdown-toggle" style="height: 34px;" data-toggle="dropdown">
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu">
                <li onclick="sortNews('language')"><?= lang('player.62'); ?></li>
            </ul>
        </div>
	</div>
</div>

<br/> -->

<div class="row">
	<div class="col-md-12">
		<table class="table table-striped">
							<tr>
								<th width="15%"><?= lang('cms.title'); ?></th>
								<th width="45%"><?= lang('cms.content'); ?></th>
								<th width="15%"><?= lang('player.62'); ?></th>
								<th width="10%"><?= lang('cms.creator'); ?></th>
								<th width="10%"><?= lang('lang.date'); ?></th>
								<th width="20%"><?= lang('lang.action'); ?></th>
							</tr>

							<?php foreach ($news as $key => $value) { ?>
								<tr>
									<td width="15%"><?= $value['title'] ?></td>
									<td width="45%"><?= $value['content'] ?></td>

									<?php if($value['language'] == 'en') { ?>
										<td width="45%">English</td>
									<?php } else if($value['language'] == 'ch') { ?>
										<td width="45%">中文</td>
									<?php } ?>

									<td width="10%"><?= $value['username'] ?></td>
									<td width="20%"><?= $value['date'] ?></td>
									<td width="10%">
										<a href="<?= BASEURL . 'cms_management/editNews/' . $value['newsId']?>" data-toggle="tooltip" title="<?= lang('tool.cms06'); ?>" class="blue"><span class="glyphicon glyphicon-pencil"></span></a>
										<a href="#" data-toggle="tooltip" title="<?= lang('tool.cms07'); ?>" class="blue" onclick="deleteNews(<?= $value['newsId']?>, '<?= $value['title']?>')"><span class="glyphicon glyphicon-trash"></span></a>
										<?php if($value['status'] == 1) { ?>
											<a href="<?= BASEURL . 'cms_management/showNews/' . $value['newsId'] . '/' . rtrim(base64_encode($value['title']), '=')?>" data-toggle="tooltip" title="<?= lang('tool.cms08'); ?>" class="blue"><span class="glyphicon glyphicon-eye-open"></span></a>
										<?php } else { ?>
											<a href="<?= BASEURL . 'cms_management/hideNews/' . $value['newsId'] . '/' . rtrim(base64_encode($value['title']), '=')?>" data-toggle="tooltip" title="<?= lang('tool.cms09'); ?>" class="blue"><span class="glyphicon glyphicon-eye-close"></span></a>
										<?php } ?>
										<a href="#" data-toggle="modal" data-target="#category_<?= $value['newsId']?>" class="blue"><span class="glyphicon glyphicon-glass" data-toggle="tooltip" title="<?= lang('tool.cms10'); ?>"></span></a>
										
										<div class="modal fade bs-example-modal-sm" id="category_<?= $value['newsId']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
											<div class="modal-dialog modal-sm">
												<div class="modal-content">
													<div class="modal-header">
														<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
														<h4 class="modal-title" id="myModalLabel" style="margin: 0 10px;"><?= lang('cms.newsNote'); ?>: <?= $value['title']?></h4>
													</div>

													<?php 
														$player = null;
														$affiliate = null;
														$admin = null;

														if($value['category'] == '6') {
															$player = true;
															$affiliate = true;
															$admin = true;
														} else if ($value['category'] == '0') {
															$player = true;
															$affiliate = false;
															$admin = false;
														} else if ($value['category'] == '1') {
															$player = false;
															$affiliate = true;
															$admin = false;
														} else if ($value['category'] == '2') {
															$player = false;
															$affiliate = false;
															$admin = true;
														} else if ($value['category'] == '3') {
															$player = true;
															$affiliate = true;
															$admin = false;
														}  else if ($value['category'] == '4') {
															$player = true;
															$affiliate = false;
															$admin = true;
														}  else if ($value['category'] == '5') {
															$player = false;
															$affiliate = true;
															$admin = true;
														} 
													?>

													<!-- <form action="<?= BASEURL . 'cms_management/filterNews/' . $value['newsId'] . '/' . rtrim(base64_encode($value['title']), '=')?>" method="POST">
														<div class="modal-body">
															<input type="checkbox" name="web[]" value="player" <?= ($player) ? 'checked':'' ?> /> <?= lang('cms.playerend'); ?><br/>
															<input type="checkbox" name="web[]" value="affiliate" <?= ($affiliate) ? 'checked':'' ?> /> <?= lang('cms.affiliateend'); ?><br/>
															<input type="checkbox" name="web[]" value="admin" <?= ($admin) ? 'checked':'' ?> /> <?= lang('cms.adminend'); ?><br/>
														</div>

														<div class="modal-footer">
															<input type="submit" class="btn btn-primary" value="<?= lang('lang.save'); ?>"/>
														</div>
													</form> -->
												</div>
											</div>
										</div>
									</td>
								</tr>
							<?php } ?>
						</table>
	</div>
</div>

<br>

<div class="row">
	<div class="col-md-12">
		<ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
	</div>
</div>