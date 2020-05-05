<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt"><i class="icon-newspaper"></i> <?=lang('cms.editnews');?>
					<a href="<?=BASEURL . 'cms_management/viewNews'?>" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info btn-xs' : 'btn-default btn-sm'?>" id="add_news">
						<span class="glyphicon glyphicon-remove"></span>
					</a>
				</h4>
			</div>

			<div class="panel-body" id="affiliate_panel_body">
				<form class="form-horizontal" action="<?=BASEURL . 'cms_management/verifyEditNews/' . $news['newsId']?>" method="POST">
					<div class="form-group">
						<div class="col-md-6 col-md-offset-3">
							<label for="categoryId"><?=lang('cms.categoryname');?>:</label>
							<select name="categoryId" id="categoryId" class="form-control">
								<option value="" ><?=lang('cms.categoryoption');?></option>
								<?php foreach ($newsCategoryList as $list) : ?>
								<option value="<?= $list['id'] ?>" <?= ($news['categoryId'] == $list['id']) ? "selected" : ""?>><?= $list['name'] ?></option>
								<?php endforeach; ?>
							</select>
							<span style="color: red;"><?=form_error('categoryId')?></span>
						</div>
                        <div class="col-md-6 col-md-offset-3" style="padding-top:10px;">
                            <label><?=lang('cms.date');?> :</label><br>
                            <input type="checkbox" name="is_daterange" value="1" <?php echo ($news['is_daterange']) ? 'checked' : '' ?>> <label><?=lang('cms.setdaterange');?></label>
                            <input type="text" id="datetime_range" name="datetime" class="form-control dateInput" value="<?=set_value('datetime')?>"
                                   style="<?php echo ($news['is_daterange']) ? '12' : 'display:none' ?>"
                                   data-start="#start_date" data-end="#end_date" data-time="true" data-empty="true" data-future="TRUE"
                            />
                            <input type="hidden" id="start_date" name="start_date" value="<?php echo strtotime($news['start_date']) ? $news['start_date'] : '' ?>">
                            <input type="hidden" id="end_date"   name="end_date"   value="<?php echo  strtotime($news['end_date'])   ? $news['end_date']   : '' ?>">
                            <span style="color: red;"><?=form_error('datetime')?></span>
                        </div>
						<div class="col-md-6 col-md-offset-3">
							<label for="title"><?=lang('cms.title');?>:</label>
							<input type="text" name="title" id="title" maxlength="100" class="form-control" value="<?=(set_value('title') != null) ? set_value('title') : $news['title']?>"/>
							<span style="color: red;"><?=form_error('title')?></span>
						</div>
						<div class="col-md-6 col-md-offset-3" style="padding-top:10px;">
							<label for="content"><?=lang('cms.content');?>:</label>
							<?php $content = preg_replace('/<br\\s*?\/??>/i', '', $news['content']); ?>
							<textarea name="content" id="content" class="form-control" maxlength="3000" style="resize: none; height: 150px;"><?=(set_value('content') != null) ? set_value('content') : $content?></textarea>
							<span style="color: red;"><?=form_error('content')?></span>
						</div>
						<!-- <div class="col-md-6 col-md-offset-3" style="padding-top:10px;">
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
									default:break;
									}
								} else {
									switch ($news['language']) {
									case 'en':
										$language = 'en';
										break;
									case 'ch':
										$language = 'ch';
										break;
									default:break;
									}
								}
								?>
							<select name="language" id="language" class="form-control">
							   	<option value="" ><?=lang('system.word3');?></option>
								<option value="en" <?=($language == 'en') ? 'selected' : ''?> > English</option>
								<option value="ch" <?=($language == 'ch') ? 'selected' : ''?> > Chinese (中文)</option>
							</select>
							<span style="color: red;"><?=form_error('language')?></span>
						</div> -->

					</div>
					<center>
						<input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>" value="<?=lang('lang.save');?>" />
					</center>
					<?php
						$player = null;
						$affiliate = null;
						$admin = null;

						if ($news['category'] == null) {
							$player = true;
							$affiliate = true;
							$admin = true;
						} else if ($news['category'] == '0') {
							$player = true;
							$affiliate = false;
							$admin = false;
						} else if ($news['category'] == '1') {
							$player = false;
							$affiliate = true;
							$admin = false;
						} else if ($news['category'] == '2') {
							$player = false;
							$affiliate = false;
							$admin = true;
						} else if ($news['category'] == '3') {
							$player = true;
							$affiliate = true;
							$admin = false;
						} else if ($news['category'] == '4') {
							$player = true;
							$affiliate = false;
							$admin = true;
						} else if ($news['category'] == '5') {
							$player = false;
							$affiliate = true;
							$admin = true;
						}
						?>
				</form>
			</div>
			<div class="panel-footer"></div>
		</div>
	</div>
</div>

<script>
    let isDaterange = $("input[name='is_daterange']"),
        daterange   = $("input[name='datetime']")

    isDaterange.change(function(){
        if (this.checked) {
            daterange.show()
        } else {
            daterange.hide()
        }
    })
</script>