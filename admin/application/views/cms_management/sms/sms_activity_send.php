<div class="row">
	<div class="col-md-offset-2 col-md-8">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt pull-left">
					<i class="icon-newspaper"></i> <?=lang('cms.smsactivitysend');?>
				</h4>
				<a href="<?=BASEURL . 'cms_management/sms_activity_views'?>" class="btn btn-default btn-sm pull-right">
					<span class="glyphicon glyphicon-remove"></span>
				</a>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body">
                <form class="form-horizontal" action="<?=BASEURL . "cms_management/sms_activity_send/$id"?>" method="POST">
					<div class="form-group">
						<div class="col-md-6 col-md-offset-3" style="padding-top:10px;">
                            <label for="content"><?=lang('cms.content');?>:</label>
                            <input type="hidden" name="id" value="<?= $id ?>"/>
							<textarea name="content" class="form-control" maxlength="5000" style="resize: none; height: 180px;" readonly><?= $content ?></textarea>
						</div>
					</div>
					<center>
                        <input type="submit" class="btn btn-info" value="<?=lang('cms.send');?>" />
					</center>
				</form>
			</div>

			<div class="panel-footer"></div>
		</div>
	</div>
</div>