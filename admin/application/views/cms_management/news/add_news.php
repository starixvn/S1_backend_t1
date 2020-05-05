<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt pull-left">
					<i class="icon-newspaper"></i> <?=lang('cms.addnews');?>
				</h4>
				<a href="<?=BASEURL . 'cms_management/viewNews'?>" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info btn-xs' : 'btn-default btn-sm'?>" id="add_news">
					<span class="glyphicon glyphicon-remove"></span>
				</a>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body" id="affiliate_panel_body">
				<form class="form-horizontal" action="<?=BASEURL . 'cms_management/verifyAddNews'?>" method="POST">
					<div class="form-group">
						<div class="col-md-6 col-md-offset-3">
							<label for="categoryId"><?=lang('cms.categoryname');?>:</label>
							<select name="categoryId" id="categoryId" class="form-control">
								<option value="" ><?=lang('cms.categoryoption');?></option>
								<?php foreach ($newsCategoryList as $list) : ?>
								<option value="<?= $list['id'] ?>"><?= $list['name'] ?></option>
								<?php endforeach; ?>
							</select>
							<span style="color: red;"><?=form_error('categoryId')?></span>
						</div>
                        <div class="col-md-6 col-md-offset-3" style="padding-top:10px;">
                            <label><?=lang('cms.date');?>:</label><br>
                            <input type="checkbox" name="is_daterange" value="1"> <label><?=lang('cms.setdaterange');?></label>
                            <input type="text" id="datetime_range" name="datetime" class="form-control dateInput" value="<?=set_value('datetime')?>"
                                   style="display: none"
                                   data-start="#start_date" data-end="#end_date" data-time="true" data-empty="true" data-future="TRUE"
                            />
                            <input type="hidden" id="start_date" name="start_date" value="">
                            <input type="hidden" id="end_date"   name="end_date"   value="">
                            <span style="color: red;"><?=form_error('datetime')?></span>
                        </div>
						<div class="col-md-6 col-md-offset-3" style="padding-top:10px;">
							<label for="title"><?=lang('cms.title');?>:</label>
							<input type="text" name="title" maxlength="100" id="title" class="form-control" value="<?=set_value('title')?>"/>
							<span style="color: red;"><?=form_error('title')?></span>
						</div>
						<div class="col-md-6 col-md-offset-3" style="padding-top:10px;">
							<label for="content"><?=lang('cms.content');?>:</label>
							<textarea name="content" id="content" class="form-control" maxlength="5000" style="resize: none; height: 180px;"><?=set_value('content')?></textarea>
							<span style="color: red;"><?=form_error('content')?></span>
						</div>
						<!-- <div class="col-md-6 col-md-offset-3" style="padding-top:10px;">
							<label for="language"><?=lang('player.62');?>:</label>
							<select name="language" id="language" class="form-control">
							   <option value="" ><?=lang('system.word3');?></option>
								<option value="en">English</option>
								<option value="ch">Chinese (中文)</option>
							</select>
							<span style="color: red;"><?=form_error('language')?></span>
						</div> -->
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