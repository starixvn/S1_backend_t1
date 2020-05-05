<style type="text/css">
	table {table-layout:fixed;}
    table td {word-wrap:break-word;}
</style>

<form class="form-horizontal" method="get" role="form" action="/cms_management/viewNews">
	<div class="panel panel-primary hidden">
		<div class="panel-heading">
			<h4 class="panel-title">
				<i class="fa fa-search"></i> <?=lang("lang.search")?>
				<span class="pull-right">
                <a data-toggle="collapse" href="#collapseNewsSearch" class="btn btn-info btn-xs"></a>
            </span>
			</h4>
		</div>
		<div id="collapseNewsSearch" class="panel-collapse">
			<div class="panel-body">
				<div class="col-md-3">
					<label class="control-label" for="flag"><?=lang('cms.categoryname');?></label>
					<select class="form-control input-sm" name="categoryId">
						<option value=""></option>
						<?php foreach ($newsCategoryList as $list) : ?>
							<option value="<?= $list['id'] ?>" <?php echo (isset($condition['categoryId']) && $condition['categoryId'] == $list['id']) ? "selected" : "" ;?>><?= $list['name'] ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="panel-footer text-right">
				<input type="submit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" id="btn-submit" value="<?php echo lang('Search'); ?>" >
			</div>
		</div>
	</div>

</form>

<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt pull-left">
					<i class="icon-newspaper"></i> <?=lang('cms.news');?>
				</h4>
				<a href="<?=BASEURL . 'cms_management/addNews'?>" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs btn-info' : 'btn-sm btn-default'?>" id="add_news">
					<i class="fa fa-plus-circle"></i> <?=lang('cms.addnews');?>
				</a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="newsList">
				<!-- <div class="row">
					<div class="col-md-12">
						<div class="btn-group">
		                    <button class="btn btn-info"><?=lang('lang.sort');?></button>
		                    <button class="btn btn-info dropdown-toggle" style="height: 34px;" data-toggle="dropdown">
		                        <span class="caret"></span>
		                    </button>

		                    <ul class="dropdown-menu">
		                        <li onclick="sortNews('language')"><?=lang('player.62');?></li>
		                    </ul>
		                </div>
					</div>
				</div>

				<br/> -->

				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-striped">
								<tr>
									<th width="15%"><?=lang('cms.categoryname');?></th>
									<th width="15%"><?=lang('cms.title');?></th>
									<th width="40%"><?=lang('cms.content');?></th>
									<th width="10%"><?=lang('cms.creator');?></th>
									<th width="10%"><?=lang('lang.date');?></th>
									<th width="10%"><?=lang('lang.action');?></th>
								</tr>
                                <?php if (count($news) > 0) : ?>
								<?php foreach ($news as $key => $value) { ?>
									<tr>
										<td><?=$value['name']?></td>
										<td><?=$value['title']?></td>
										<td width="45%"><?=$value['content']?></td>

											<td width="10%"><?=$value['username']?></td>
										<td width="20%"><?=$value['date']?></td>
										<td width="10%">
											<a href="<?=BASEURL . 'cms_management/editNews/' . $value['newsId']?>" data-toggle="tooltip" title="<?=lang('tool.cms06');?>" class="blue"><span class="glyphicon glyphicon-pencil"></span></a>
											<a href="#" data-toggle="tooltip" title="<?=lang('tool.cms07');?>" class="blue" onclick="deleteNews(<?=$value['newsId']?>, '<?=$value['title']?>')"><span class="glyphicon glyphicon-trash"></span></a>
										</td>
									</tr>
								<?php } ?>
								<?php else : ?>
                                    <tr>
                                        <td colspan="6" align="center"><?= lang('lang.norecord'); ?></td>
                                    </tr>
                                <?php endif; ?>
							</table>
						</div>
					</div>
				</div>

				<br>

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