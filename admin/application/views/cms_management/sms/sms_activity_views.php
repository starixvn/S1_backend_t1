<form class="form-horizontal" method="get" role="form" action="/cms_management/sms_activity_views">
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
					<label class="control-label" for="flag"><?=lang('Status');?></label>
					<select class="form-control input-sm" name="status">
						<option value=""  ><?=lang('lang.selectall');?></option>
						<option value="1" <?php echo (isset($condition['sms_activity_msg.status']) && $condition['sms_activity_msg.status'] == 0) ? "selected" : "" ;?>><?=lang('cms.sendyet')?></option>
						<option value="2" <?php echo (isset($condition['sms_activity_msg.status']) && $condition['sms_activity_msg.status'] == 1) ? "selected" : "" ;?>><?=lang('cms.sent')?></option>
					</select>
				</div>
			</div>
			<div class="panel-footer text-right">
				<input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage btn-sm' : 'btn-primary btn-sm'?>" id="btn-submit" value="<?php echo lang('Search'); ?>" >
			</div>
		</div>
	</div>

</form>

<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt pull-left">
					<i class="icon-newspaper"></i> <?=lang('cms.smsactivityMsg');?>
				</h4>
				<a href="<?=BASEURL . 'cms_management/sms_activity_add'?>" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info btn-xs' : 'btn-sm btn-default'?>">
					<i class="fa fa-plus-circle"></i> <?=lang('cms.smsactivityadd');?>
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
									<th width="40%"><?=lang('cms.content');?></th>
									<th width="10%"><?=lang('lang.date');?></th>
									<th width="10%"><?=lang('lang.status');?></th>
									<th width="10%"><?=lang('lang.action');?></th>
								</tr>
                                <?php if (count($data) > 0) : ?>
								<?php foreach ($data as $key => $value) { ?>
									<tr>
										<td><?=$value['username']?></td>
										<td><?=$value['content']?></td>
										<td><?=$value['update_time']?></td>
										<td>
											<?php if ($value['status'] == 1) : ?>
												<span class="btn-xs btn-danger"><?=lang('cms.sent')?></span>
											<?php elseif ($value['status'] == 0) : ?>
												<span class="btn-xs btn-primary"><?=lang('cms.sendyet')?></span>
											<?php endif; ?>
										</td>
										<td>
											<a href="<?=BASEURL . 'cms_management/sms_activity_edit/' . $value['id']?>" data-toggle="tooltip" title="<?=lang('tool.cms03');?>" class="blue"><span class="glyphicon glyphicon-pencil"></span></a>
											<a href="#" data-toggle="tooltip" title="<?=lang('tool.cms04');?>" class="blue" onclick="deleteActivityMsg(<?=$value['id']?>)"><span class="glyphicon glyphicon-trash"></span></a>
											<?php if ($value['status'] == 1) : ?>
											<a href="javascript:;" data-toggle="tooltip" title="<?=lang('tool.cms11');?>" class="red" style="cursor:not-allowed"><span class="glyphicon glyphicon-send"></span></a>
											<?php else : ?>
											<a href="<?=BASEURL . 'cms_management/sms_activity_send/' . $value['id']?>" data-toggle="tooltip" title="<?=lang('tool.cms11');?>" class="blue"><span class="glyphicon glyphicon-send"></span></a>
											<?php endif; ?>
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