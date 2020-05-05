<style type="text/css">
	table {table-layout:fixed;}
    table td {word-wrap:break-word;}
</style>
<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt pull-left">
					<i class="icon-newspaper"></i> <?=lang('cms.newscategory');?>
				</h4>
				<a href="<?=BASEURL . 'cms_management/addNewsCategory'?>" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info btn-xs' : 'btn-default btn-sm'?>">
					<i class="fa fa-plus-circle"></i> <?=lang('cms.addnewscategory');?>
				</a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body">
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-striped">
								<tr>
									<th width="30%"><?=lang('cms.categoryname');?></th>
                                    <th width="15%"><?=lang('player.62');?></th>
									<th width="15%"><?=lang('cms.creator');?></th>
									<th width="20%"><?=lang('lang.date');?></th>
									<th width="20%"><?=lang('lang.action');?></th>
								</tr>
                                <?php if (count($news_category) > 0) : ?>
								<?php foreach ($news_category as $key => $value) { ?>
									<tr>
										<td><?=$value['name']?></td>

										<?php if ($value['language'] == 'en') {?>
											<td width="45%">English</td>
										<?php } else if ($value['language'] == 'ch') {?>
											<td width="45%">中文</td>
										<?php } else if ($value['language'] == 'id') {?>
											<td width="45%">Indonesian</td>
										<?php } else if ($value['language'] == 'vn') {?>
											<td width="45%">Vietnamese</td>
										<?php } else if ($value['language'] == 'kr') {?>
											<td width="45%">Korean</td>
										<?php } else if ($value['language'] == 'th') {?>
											<td width="45%">Thai</td>
										<?php } ?>

										<td width="10%"><?=$value['username']?></td>
										<td width="20%"><?=$value['date']?></td>
										<td width="10%">
											<a href="<?=BASEURL . 'cms_management/editNewsCategory/' . $value['id']?>" data-toggle="tooltip" title="<?=lang('tool.cms06');?>" class="blue"><span class="glyphicon glyphicon-pencil"></span></a>
											<a href="#" data-toggle="tooltip" title="<?=lang('tool.cms07');?>" class="blue" onclick="deleteNewsCategory(<?=$value['id']?>, '<?=$value['name']?>')"><span class="glyphicon glyphicon-trash"></span></a>
										</td>
									</tr>
								<?php } ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5" align="center"><?= lang('lang.norecord'); ?></td>
                                    </tr>
                                <?php endif; ?>
							</table>
						</div>
					</div>
				</div>

				<!-- <br> -->

				<div class="row">
					<div class="col-md-12">
						<ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
					</div>
				</div>
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
</div>