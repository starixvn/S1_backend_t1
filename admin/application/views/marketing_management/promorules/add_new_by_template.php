<style type="text/css">
	.form-horizontal .control-label{
		text-align: right;
	}
</style>
<form id="form_template" class="form-horizontal" method="POST" action="<?php echo site_url('/marketing_management/create_promo_from_template'); ?>">

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-book"></i> <span id="title_promo_rule"><?php echo lang('Add New Promo By Template'); ?></span>
            <a href="<?=site_url('marketing_management/promoRuleManager/')?>" class="btn btn-danger btn-xs pull-right" data-toggle="tooltip" title="Close" data-placement="left">
                <span class="glyphicon glyphicon-remove"></span>
            </a>
        </h4>
    </div>
    <div class="panel-body">
    	<div class="row form-group">
    		<div class="col-md-2 col-md-offset-1">
    		<label class="control-label">
    		<?php echo lang('Promotion Category'); ?>
    		</label>
    		</div>
    		<div class="col-md-4">
				<?php echo form_dropdown('promoCategory', $promoCategoryList, null, 'class="form-control" '); ?>
    		</div>
    	</div>
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover">
				<tr>
					<th class="col-md-4"><?php echo lang('Template Name'); ?></th>
					<th class="col-md-8"><?php echo lang('Template Parameters'); ?></th>
				</tr>
				<?php

foreach ($template_list as $tmpl) {
	$current_row_id = $tmpl['id'];
	$input_params = json_decode($tmpl['template_parameters'], true);
	?>
				<tr>
					<td><input type="radio" name="template_id" value="<?php echo $current_row_id; ?>"> <?php echo lang($tmpl['template_name']); ?></td>
					<td><?php

	include APPPATH . 'views/includes/make_input_ui.php';

	?></td>
				</tr>
				<?php

}
?>
			</table>
		</div>
	</div>
    <div class="panel-footer">
    	<div class="row">
    		<div class="col-md-3"><button class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary' ?>" id="create_btn"><?php echo lang('Create Promo Template'); ?></button></div>
    	</div>
	</div>

</div>
</form>
<script type="text/javascript">
$(document).ready(function(){

    $('input:radio[name="template_id"]').change(function(){
        var temp_id = $(this).val();
        $('input[name$=_'+temp_id+']').attr('required','required');
        $(':not(input[name$=_'+temp_id+']), input[type=checkbox]').removeAttr('required');
    });

	$('#form_template').submit(function(e){
		//check
		var tmpl_id=$('input[name=template_id]:checked').val();
		// console.log(tmpl_id);
		if(typeof tmpl_id == "undefined" || tmpl_id==''){
			alert("<?php echo lang('Please choose one template'); ?>");
			e.preventDefault();
		}

	});
});

</script>