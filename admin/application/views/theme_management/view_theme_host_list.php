<div id="theme_main_content">
	<h1 class="page-header">
		<span id="spnpPageTitle"><?=lang('Theme Host Template')?></span>
		<a type="button" class="btn btn-primary btn-sm pull-right add-item" onclick="addItem(this);"><i class="glyphicon glyphicon-plus" style="color:white;" data-placement="bottom"></i>
			<?= lang('Add Theme') ?>
		</a>
	</h1>
	<form id="form-theme-host" action="/theme_management/saveThemeHost" method="POST">
		<div class="row">
			<div class="col-md-offset-1 col-md-10">
				<table class="table">
					<thead>
						<th>HOSTNAME</th>
						<th>PC-THEME</th>
						<th>PC-HEADER</th>
						<th>PC-FOOTER</th>
						<th>MB-Custom-Css-File</th>
						<th>FUNCTION</th>
					</thead>
					<tbody>
						<?php if ($themeList) : ?>
						<?php foreach($themeList as $_theme) : ?>
						<tr>
							<td>
								<input type="text" class="form-control" name="hostname[]" value="<?= $_theme['hostname'] ?>" required>
							</td>
							<td>
								<select class="form-control" name="theme_template[]" required>
									<option value=""><?= lang('please_select') ?></option>
									<?php foreach ($themes as $theme) : ?>
									<option value="<?= $theme ?>" <?= ($_theme['theme_template'] == $theme) ? 'selected' : '' ?>><?= $theme ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td>
								<select class="form-control" name="header_template[]">
									<option value=""><?= lang('please_select') ?></option>
									<?php foreach ($headers as $header) : ?>
									<option value="<?= $header ?>" <?= ($_theme['header_template'] == $header) ? 'selected' : '' ?>><?= $header ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td>
								<select class="form-control" name="footer_template[]">
									<option value=""><?= lang('please_select') ?></option>
									<?php foreach ($footers as $footer) : ?>
									<option value="<?= $footer ?>" <?= ($_theme['footer_template'] == $footer) ? 'selected' : '' ?>><?= $footer ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon">
											<span>style-mobile-</span>
										</div>
										<input type="text" class="form-control" name="custom_css_file[]" value="<?= $_theme['custom_css_file'] ?>">
										<div class="input-group-addon">
											<span>.css</span>
										</div>
									</div>
								</div>
							</td>
							<td>
								<button type="button" class="btn btn-danger minus-item" onclick="minusItem(this);"><i class="fa fa-minus"></i></button>
							</td> 
						</tr>
						<?php endforeach; ?>
						<?php else : ?>
						<tr>
							<td>
								<input type="text" class="form-control" name="hostname[]" required>
							</td>
							<td>
								<select class="form-control" name="theme_template[]" required>
									<option value=""><?= lang('please_select') ?></option>
									<?php foreach ($themes as $theme) : ?>
									<option value="<?= $theme ?>"><?= $theme ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td>
								<select class="form-control" name="header_template[]">
									<option value=""><?= lang('please_select') ?></option>
									<?php foreach ($headers as $header) : ?>
									<option value="<?= $header ?>"><?= $header ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td>
								<select class="form-control" name="footer_template[]">
									<option value=""><?= lang('please_select') ?></option>
									<?php foreach ($footers as $footer) : ?>
									<option value="<?= $footer ?>"><?= $footer ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon">
											<span>style-mobile-</span>
										</div>
										<input type="text" class="form-control" name="custom_css_file[]" required>
										<div class="input-group-addon">
											<span>.css</span>
										</div>
									</div>
								</div>
							</td>
							<td>
								<button type="button" class="btn btn-danger minus-item" onclick="minusItem(this);"><i class="fa fa-minus"></i></button>
							</td>  
						</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="well text-center">
			<button type="submit" id="btn-save" class="btn btn-default"><?=lang('Save')?></button>
		</div>
		<input type="hidden" class="form-control" name="post">
	</form>
</div>

<script>

	function addItem(element) {
		var form = $("#form-theme-host"),
			item = form.find('tbody tr:first').clone();

		item.find('input').val("");
		item.find('select').each(function(index){
			$(this).val("");
		});

		form.find('tbody').append(item);
	}

	function minusItem(element) {
		$(element).parents('tr').remove();
	}

</script>