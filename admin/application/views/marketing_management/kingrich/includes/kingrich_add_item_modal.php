<style type="text/css">
	.add-schedule button{
		height: 36px; 
		width: 100%;
		font-size: 12px;
	}
	.add-schedule .col-md-3{
		padding: 0;
	}
	.modal-footer{
		background-color: #e5e5e5;
	}
</style>
<div class="clearfix">	
	<form action="/marketing_management/kingrich_submit_add_scheduler/<?=$schedule_id?>" method="post" enctype="multipart/form-data" class="row add-schedule">
		<div class="col-md-7">
			<label class="control-label" for="date_range"><?=lang('report.sum02');?></label>
			<input type="text" id="date_range" class="form-control input-sm dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
			<input type="hidden" id="date_from" name="date_from" value="<?php echo $conditions['date_from']; ?>"/>
			<input type="hidden" id="date_to" name="date_to" value="<?php echo $conditions['date_to']; ?>"/>
		</div>
		<?php if( !empty($kingrich_currency_branding) && $this->config->item('multiple_currency_enabled') ) :?>
		<div class="col-md-3">
			<label class="control-label" for="by_game_type_globalcom"><?=lang('Status');?> </label>
			<select class="form-control input-sm" name="currency" id="currency">
				<option value="" ><?=lang('All');?></option>
				<?php if( !empty($kingrich_currency_branding)) :?>
					<?php foreach ($kingrich_currency_branding as $key => $value) : ?>
						<option value="<?=$key?>" <?php echo $conditions['currency']==$key ? 'selected="selected"' : '' ; ?>><?= $key ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>
	<?php endif; ?>
		<div class="col-md-2">
	    	<span class="input-group-btn" style="padding-top: 25px;">
				<button type="submit" class="btn btn-primary" id="btn-submit" style="height: 40px; width: 100%;"><?= lang('Submit') ?></button>
			</span>
		</div>
	</form>
</div>

<script type="text/javascript">
	var date_from = "<?= $conditions['date_from'] ?>";
	var date_to = "<?= $conditions['date_to'] ?>";
	
	$("#date_range").daterangepicker({
	    parentEl: "#mainModal  .modal",
	    timePicker: true,
	    timePickerSeconds: true,
	    timePicker24Hour: true,
	    startDate: date_from,
	    endDate: date_to,
	    autoUpdateInput: true,
	    locale: {
		      format: 'YYYY-MM-DD HH:mm:ss'
		}
	},function(start, end, label) {
    	$('#date_from').val(start.format('YYYY-MM-DD HH:mm:ss'));
    	$('#date_to').val(end.format('YYYY-MM-DD HH:mm:ss'));
	});

</script>