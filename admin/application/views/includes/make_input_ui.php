<?php

if (!empty($input_params)) {
	foreach ($input_params as $input_param) {

		$label = isset($input_param['label']) ? $input_param['label'] : (isset($input_param['label_lang']) ? lang($input_param['label_lang']) : '');
		$value = isset($input_param['value']) ? $input_param['value'] : '';
		$ctrl_name = $input_param['name'] . '_' . $current_row_id;
		?>

<?php

		if ($input_param['type'] == 'float_amount') {
			?>
							<div class="form-group">
								<label class="col-sm-3 col-sm-offset-1 control-label"><?php echo lang($label); ?></label>
								<div class="col-sm-7">
 									<input type="number" name="<?php echo $ctrl_name; ?>" id="<?php echo $ctrl_name; ?>"
 									class="form-control amount_only input-sm" value="<?php echo $value; ?>" step="any" />
								</div>
								<div class="col-sm-1">
								</div>
							</div>
					<?php

		} else if ($input_param['type'] == 'checkbox') {
			?>
							<div class="form-group">
								<label class="col-sm-3 col-sm-offset-1 control-label"><?php echo lang($label); ?></label>
								<div class="col-sm-7">
									<label class="control-label">
 									<input type="checkbox" name="<?php echo $ctrl_name; ?>"  id="<?php echo $ctrl_name; ?>"
 									value="true" <?php echo $value ? 'checked="checked"' : ''; ?>/>
 									</label>
								</div>
								<div class="col-sm-1">
								</div>
							</div>

					<?php

		} else if ($input_param['type'] == 'text') {
			?>
							<div class="form-group">
								<label class="col-sm-3 col-sm-offset-1 control-label"><?php echo lang($label); ?></label>
								<div class="col-sm-7">
 									<input type="text" name="<?php echo $ctrl_name; ?>" id="<?php echo $ctrl_name; ?>"
 									class="form-control input-sm" value="<?php echo $value; ?>"/>
								</div>
								<div class="col-sm-1">
								</div>
							</div>
					<?php

		} else if ($input_param['type'] == 'html') {
			?>
							<div class="form-group">
								<label class="col-sm-3 col-sm-offset-1 control-label"><?php echo lang($label); ?></label>
								<div class="col-sm-7">
								<?php echo $value; ?>
								</div>
								<div class="col-sm-1">
								</div>
							</div>
<?php

		}

		?>

<?php

	}
}
?>