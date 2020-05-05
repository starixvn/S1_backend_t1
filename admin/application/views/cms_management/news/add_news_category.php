<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt pull-left">
					<i class="icon-newspaper"></i> <?=lang('cms.addnewscategory');?>
				</h4>
				<a href="<?=BASEURL . 'cms_management/viewNewsCategory'?>" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info btn-xs' : 'btn-default btn-sm'?>">
					<span class="glyphicon glyphicon-remove"></span>
				</a>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body" id="affiliate_panel_body">
				<form class="form-horizontal" action="<?=BASEURL . 'cms_management/verifyAddNewsCategory'?>" method="POST">
					<div class="form-group">
						<div class="col-md-6 col-md-offset-3">
							<label for="name"><?=lang('cms.categoryname');?>:</label>
							<input type="text" name="name" maxlength="100" id="name" class="form-control" value="<?=set_value('name')?>"/>
							<span style="color: red;"><?=form_error('name')?></span>
						</div>
						<div class="col-md-6 col-md-offset-3" style="padding-top:10px;">
							<label for="language"><?=lang('player.62');?>:</label>
							<select name="language" id="language" class="form-control">
							   <option value="" ><?=lang('system.word3');?></option>
								<option value="en">English</option>
								<option value="ch">Chinese (中文)</option>
								<option value="id">Indonesian</option>
								<option value="vn">Vietnamese</option>
								<option value="kr">Korean</option>
								<option value="th">Thai</option>
							</select>
							<span style="color: red;"><?=form_error('language')?></span>
						</div>
					</div>
					<center>
						<input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>" value="<?=lang('lang.add');?>" />
					</center>
				</form>
			</div>

			<div class="panel-footer"></div>
		</div>
	</div>
</div>