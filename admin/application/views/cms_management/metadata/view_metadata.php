<style type="text/css">
	table {table-layout:fixed;}
    table td {word-wrap:break-word;}
</style>

<form class="form-horizontal" method="get" role="form" action="/cms_management/viewMetaData">
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
					<label class="control-label" for="flag"><?=lang('cms.uri_string');?></label>
					<input class="form-control"type="text" name="uri_string" value="<?= ($uri_string) ? : ''; ?>">
				</div>
			</div>
			<div class="footer-padding text-right">
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
					<i class="icon-newspaper"></i> <?=lang('cms.metadata');?>
				</h4>
				<a href="<?=BASEURL . 'cms_management/addMetaData'?>" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs btn-info' : 'btn-sm btn-default'?>" id="add_news">
					<i class="fa fa-plus-circle"></i> <?=lang('cms.addmetadata');?>
				</a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="newsList">
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-striped">
								<tr>
									<th width="15%"><?=lang('cms.uri_string');?></th>
									<th width="15%"><?=lang('cms.title');?></th>
									<th width="40%"><?=lang('cms.keyword');?></th>
									<th width="40%"><?=lang('cms.description');?></th>
									<th width="10%"><?=lang('cms.updater');?></th>
									<th width="10%"><?=lang('lang.date');?></th>
									<th width="10%"><?=lang('lang.action');?></th>
								</tr>
                                <?php if (count($list) > 0) : ?>
								<?php foreach ($list as $key => $value) { ?>
									<tr>
										<td>
											<a href="<?= $this->utils->getSystemUrl('player') . '/' . $value['uri_string'] ?>"><?=$this->utils->getSystemUrl('player') . '/' . $value['uri_string']?></a>
										</td>
										<td><?=$value['title']?></td>
										<td><?=$value['keyword']?></td>
										<td><?=$value['description']?></td>
										<td><?=$value['username']?></td>
										<td><?=$value['updated_at']?></td>
										<td>
											<a href="<?=BASEURL . 'cms_management/editMetaData/' . $value['id']?>" data-toggle="tooltip" title="<?=lang('cms.edit');?>" class="blue"><span class="glyphicon glyphicon-pencil"></span></a>
											<a href="#" data-toggle="tooltip" title="<?=lang('cms.delete');?>" class="blue" onclick="deleteMetaData(<?=$value['id']?>, '<?=$value['title']?>')"><span class="glyphicon glyphicon-trash"></span></a>
										</td>
									</tr>
								<?php } ?>
								<?php else : ?>
                                    <tr>
                                        <td colspan="7" align="center"><?= lang('lang.norecord'); ?></td>
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
		</div>
	</div>
</div>