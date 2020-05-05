<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt pull-left">
					<i class="icon-newspaper"></i> <?=lang('cms.addmetadata');?>
				</h4>
				<a href="<?=BASEURL . 'cms_management/viewNews'?>" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info btn-xs' : 'btn-default btn-sm'?>">
					<span class="glyphicon glyphicon-remove"></span>
				</a>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body" id="affiliate_panel_body">
				<form class="form-horizontal" action="<?=BASEURL . 'cms_management/addMetaData'?>" method="POST">
					<div class="form-group">
						<div class="col-md-6 col-md-offset-3" style="padding-top:10px;">
							<label for="uri_string"><?=lang('cms.uri_string');?>:</label>
							<div class="input-group">
								<div class="input-group-addon">
									<span><?= $this->utils->getSystemUrl('player') . '/' ?></span>
								</div>
								<input type="text" name="uri_string" maxlength="100" id="uri_string" class="form-control" value="<?=set_value('uri_string')?>" required/>
							<span style="color: red;"><?=form_error('uri_string')?></span>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-3" style="padding-top:10px;">
							<label for="title">Title:</label>
							<input type="text" name="title" maxlength="100" id="title" class="form-control" value="<?=set_value('title')?>"/>
							<span style="color: red;"><?=form_error('title')?></span>
						</div>
						<div class="col-md-6 col-md-offset-3" style="padding-top:10px;">
							<label for="keyword">Keyword:</label>
							<textarea name="keyword" id="keyword" class="form-control" maxlength="5000" style="resize: none; height: 180px;"><?=set_value('keyword')?></textarea>
							<span style="color: red;"><?=form_error('keyword')?></span>
						</div>
						<div class="col-md-6 col-md-offset-3" style="padding-top:10px;">
							<label for="description">Description:</label>
							<textarea name="description" id="description" class="form-control" maxlength="5000" style="resize: none; height: 180px;"><?=set_value('description')?></textarea>
							<span style="color: red;"><?=form_error('description')?></span>
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