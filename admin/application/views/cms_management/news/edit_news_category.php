<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt"><i class="icon-newspaper"></i> <?=lang('cms.editnewscategory');?>
					<a href="<?=BASEURL . 'cms_management/viewNewsCategory'?>" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info btn-xs' : 'btn-default btn-sm'?>">
						<span class="glyphicon glyphicon-remove"></span>
					</a>
				</h4>
			</div>

			<div class="panel-body" id="affiliate_panel_body">
				<form class="form-horizontal" action="<?=BASEURL . 'cms_management/verifyEditNewsCategory/' . $newscategory['id']?>" method="POST">
					<div class="form-group">
						<div class="col-md-6 col-md-offset-3">
							<label for="name"><?=lang('cms.categoryname');?>:</label>
							<input type="text" name="name" id="name" maxlength="100" class="form-control" value="<?=(set_value('name') != null) ? set_value('name') : $newscategory['name']?>"/>
							<span style="color: red;"><?=form_error('name')?></span>
						</div>
						<div class="col-md-6 col-md-offset-3" style="padding-top:10px;">
							<label for="language"><?=lang('player.62');?>:</label>
							<?php
								$language = null;
								if (set_value('language') != null) {
									switch (set_value('language')) {
									case 'en':
										$language = 'en';
										break;
									case 'ch':
										$language = 'ch';
										break;
									case 'id':
										$language = 'id';
										break;
									case 'vn':
										$language = 'vn';
										break;
									case 'kr':
										$language = 'kr';
										break;
									case 'th':
										$language = 'th';
										break;

									default:break;
									}
								} else {
									switch ($newscategory['language']) {
									case 'en':
										$language = 'en';
										break;
									case 'ch':
										$language = 'ch';
										break;
									case 'id':
										$language = 'id';
										break;
									case 'vn':
										$language = 'vn';
										break;
									case 'kr':
										$language = 'kr';
										break;
									case 'th':
										$language = 'th';
										break;
									default:break;
									}
								}
							?>
							<select name="language" id="language" class="form-control">
							   	<option value="" ><?=lang('system.word3');?></option>
								<option value="en" <?=($language == 'en') ? 'selected' : ''?> > English</option>
								<option value="ch" <?=($language == 'ch') ? 'selected' : ''?> > Chinese (中文)</option>
								<option value="id" <?=($language == 'id') ? 'selected' : ''?> >Indonesian</option>
								<option value="vn" <?=($language == 'vn') ? 'selected' : ''?> >Vietnamese</option>
								<option value="kr" <?=($language == 'kr') ? 'selected' : ''?> >Korean</option>
								<option value="th" <?=($language == 'th') ? 'selected' : ''?> >Thai</option>
							</select>
							<span style="color: red;"><?=form_error('language')?></span>
						</div>

					</div>
					<center>
						<input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>" value="<?=lang('Save');?>" />
					</center>
				</form>
			</div>

			<div class="panel-footer"></div>
		</div>
	</div>
</div>