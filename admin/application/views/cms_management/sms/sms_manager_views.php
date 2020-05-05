<form class="form-horizontal" method="get" role="form" action="/cms_management/sms_manager_views">
	<div class="panel panel-primary hidden">
		<div class="panel-heading">
			<h4 class="panel-title">
				<i class="fa fa-search"></i> <?=lang("lang.search")?>
				<span class="pull-right">
                <a data-toggle="collapse" href="#collapseSearch" class="btn btn-info btn-xs"></a>
            </span>
			</h4>
		</div>
		<div id="collapseSearch" class="panel-collapse">
			<div class="panel-body">
				<div class="col-md-3">
					<label class="control-label" for="flag"><?=lang('Category');?></label>
					<select class="form-control input-sm" name="category">
						<option value=""><?=lang('cms.categoryoption');?></option>
						<?php foreach ($categoryTpye as $val => $desc) : ?>
						<option value="<?= $val ?>" <?= (!empty($condition['sms_manager_msg.category']) == $val) ? "selected" : "" ?>><?=lang($desc);?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="footer-padding text-right">
				<input type="submit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" id="btn-submit" value="<?php echo lang('Search'); ?>" >
			</div>
		</div>
	</div>
</form>

<div class="row">
	<?php if ($currentData) : ?>
	<div class="col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('cms.currentmanagermsg')?>
				</h3>
			</div>
			<div class="panel-body">
				<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
					<?php foreach($currentData as $key => $val) : ?>
					<div class="panel panel-default" style="font-size:12px;">
						<div class="panel-heading" role="tab" id="<?= 'smsKey'. $key ?>">
						<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion" href="<?= '#smsVal'. $key ?>" aria-expanded="true" aria-controls="collapseOne">
								<?= lang($categoryTpye[$val['category']]) ?>
							</a>
						</h4>
						</div>
						<div id="<?= 'smsVal'. $key ?>" class="panel-collapse collapse <?= ($key === 0) ? "in" : "" ?>" role="tabpanel" aria-labelledby="headingOne">
						<div class="panel-body">
							<?= $val['content']; ?>
						</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<div class="<?= (!$currentData) ? "col-md-offset-2" : ""; ?> col-md-8">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt pull-left">
					<i class="icon-newspaper"></i> <?=lang('cms.smsmanagermsg');?>
				</h4>
				<a href="<?=BASEURL . 'cms_management/sms_manager_add'?>" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs btn-info' : 'btn-sm btn-default'?>">
					<i class="fa fa-plus-circle"></i> <?=lang('cms.smsmanageradd');?>
				</a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body">
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-striped">
								<tr>
									<th width="10%"><?=lang('cms.creator');?></th>
									<th width="20%"><?=lang('Category');?></th>
									<th width="30%"><?=lang('cms.content');?></th>
									<th width="20%"><?=lang('lang.date');?></th>
									<th width="10%"><?=lang('lang.status');?></th>
									<th width="10%"><?=lang('lang.action');?></th>
								</tr>
								<?php if (count($data) > 0) : ?>
								<?php foreach ($data as $key => $value) { ?>
									<tr>
										<td><?=$value['username']?></td>
										<td><?=lang($categoryTpye[$value['category']])?></td>
										<td><?=$value['content']?></td>
										<td><?=$value['update_time']?></td>
										<td>
											<?php if ($value['status'] == 1) : ?>
											<span class="glyphicon glyphicon-ok text-success"></span>
											<?php else : ?>
											<span class="glyphicon glyphicon-remove text-danger"></span>
											<?php endif; ?>
										</td>
										<td width="10%">
											<a href="<?=BASEURL . 'cms_management/sms_manager_edit/' . $value['id']?>" data-toggle="tooltip" title="<?=lang('tool.cms03');?>" class="blue"><span class="glyphicon glyphicon-pencil"></span></a>
											<a href="#" data-toggle="tooltip" title="<?=lang('tool.cms04');?>" class="blue" onclick="deleteManagerMsg(<?=$value['id']?>)"><span class="glyphicon glyphicon-trash"></span></a>
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
		</div>
	</div>
</div>