<div class="row">
	<div class="col-md-offset-2 col-md-8">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt pull-left">
					<i class="icon-newspaper"></i> <?=lang('cms.smsactivityadd');?>
				</h4>
				<a href="<?=BASEURL . 'cms_management/sms_activity_views'?>" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info btn-xs' : 'btn-default btn-sm'?>">
					<span class="glyphicon glyphicon-remove"></span>
				</a>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body">
                <form class="form-horizontal" action="<?=BASEURL . 'cms_management/sms_activity_add'?>" method="POST">
					<div class="form-group">
						<div class="col-md-6 col-md-offset-3" style="padding-top:10px;">
							<label for="content"><?=lang('cms.content');?>:</label>
							<textarea name="content" id="content" class="form-control" maxlength="5000" style="resize: none; height: 180px;"><?=set_value('content')?></textarea>
							<span style="color: red;"><?=form_error('content')?></span>
						</div>
					</div>
					<center>
                        <input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>" value="<?=lang('lang.add');?>" />
					</center>
				</form>
			</div>

			<div class="panel-footer"></div>
		</div>
	</div>
</div>