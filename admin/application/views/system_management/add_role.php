<div class="row">
	<div id="container" class="col-md-12">
		<div class="panel panel-primary">

			<div class="panel-heading custom-ph">
				<h3 class="panel-title custom-pt"><i class="glyphicon glyphicon-plus-sign"></i> <?=lang('sys.ar1');?> </h3>
			</div>

			<form class="form-inline" method="post" action="<?=site_url('role_management/verifyAddRole')?>" autocomplete="off">
			<div class="panel-body" id="add_panel_body">
					<div class="row">
						<div class="col-md-12">
						  	<div class="form-group">
							<label for="role_name"><?=lang('sys.ar2');?></label>
							<input class="form-control" class="form-control input-sm" type="text" name="role_name" id="role_name" value="<?php echo set_value('role_name'); ?>">
							<?php if($this->permissions->canAssignSuperAdmin()){ ?>
							<input id="isAdmin" type="checkbox" name="isAdmin" value="1" />
							<label for="isAdmin"><?=lang('isAdmin');?></label>
							<?php }?>
							<input type='button' class="btn btn-primary form-control btn_enable_giving" value="<?php echo lang('Enable Giving Permission'); ?>">
							<?php echo form_error('role_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							<?php echo form_error('functions', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							<?php echo form_error('functions_giving', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							</div>
						</div>
					</div>
                <input type="text" name="functions_parent" value="" id="parent_with_child_selected" class="hidden"/>
                <input type="text" name="functions_parent_giving" value="" id="parent_give_child_selected" class="hidden"/>
					<hr/>

					<div class="row">
						<div class="col-md-6">
							<label><?=lang('sys.ar3');?></label>
							<br/><br/>
							<input type="checkbox" onclick="checkAll(this.id);" id="all_use"/> <?=lang('sys.ar4');?>
							<br/><br/>

							<?php
$cnt = 0;
foreach ($functions as $value) {
	//echo 'test: '.$value['funcName'];
	if ($value['parentId'] == 0) {
		if ($cnt != 0) {
			?>
											</table>
							<?php
}
		?>
										<input type="checkbox" onclick="checkAll(this.id);" id="use_<?=$value['funcId']?>" class="all_use"/>
										<span style="font-size:16px; font-weight:bold;"><?=lang('role.' . $value['funcId']);?></span>
									  	<hr/>
										<table class="table table-bordered">

							<?php
} else {
		?>
										<tr>
											<td>
												<div class="checkbox">
													<label>
														<input type="checkbox" name="functions[]" class="all_use use_<?=$value['parentId']?>" parent_id="<?=$value['parentId']?>"
                                                               parent="use_<?=$value['parentId']?>" value="<?=$value['funcId']?>" id="use_<?=$value['funcId']?>" onclick="uncheckRole(this.id);"> <?=lang('role.' . $value['funcId']);?>
													</label>
												</div>
										  	</td>
									  	</tr>
							<?php
}

	$cnt++;
}
?>

							</table>

						</div>

						<div id="functions_giving" style="display:none;" class="col-md-6">
							<label><?=lang('sys.ar5');?></label>
							<br/><br/>
							<input type="checkbox" onclick="checkAll(this.id);" id="all_give"/> <?=lang('sys.ar6');?>
							<br/><br/>

							<?php
$cnt = 0;

foreach ($functions as $value) {
	if ($value['parentId'] == 0) {
		if ($cnt != 0) {
			?>
											</table>
							<?php
}
		?>
										<input type="checkbox" onclick="checkAll(this.id);" id="give_<?=$value['funcId']?>" class="all_give"/>
										<span style="font-size:16px; font-weight:bold;"><?=lang('role.' . $value['funcId']);?></span>
										<hr/>

										<table class="table table-bordered">
							<?php
} else {

		?>
										<tr>
											<td>
												<div class="checkbox">
													<label>
									      				<input type="checkbox" name="functions_giving[]" class="all_give give_<?=$value['parentId']?>" parent_id="<?=$value['parentId']?>"
                                                               parent="give_<?=$value['parentId']?>" value="<?=$value['funcId']?>" id="give_<?=$value['funcId']?>" onclick="uncheckRole(this.id);"> <?=lang('role.' . $value['funcId']);?>
										  			</label>
												</div>
										  	</td>
									  	</tr>
							<?php
}

	$cnt++;
}
?>

							</table>
						</div>
					</div>
			</div>

			<div class="panel-footer">
				<div class="pull-right">
					<?php echo '<input class="btn btn-info save_roles" type="submit" value="' . lang('lang.save') . '">'; ?>
					<?php echo '<input type="button" value="' . lang('lang.cancel') . '" class="btn btn-default" onclick="history.back();" />'; ?>
				</div>
				<div class="clearfix"></div>
			</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
$('.btn_enable_giving').click(function(e){
	if($('#use_2').is(':checked')){
		//change text
		$('.btn_enable_giving').val("<?php echo lang('Enable Giving Permission'); ?>");
	}else{
		$('.btn_enable_giving').val("<?php echo lang('Disable Giving Permission'); ?>");
	}
	$('#use_2').click();

});

$(document).ready(function(){
    $('.save_roles').click(function(){
        var selected = [];
        $('.all_use:checked').each(function() {
            if($.inArray($(this).attr('parent_id'), selected) === -1) selected.push($(this).attr('parent_id'));
        });
        $('#parent_with_child_selected').val(selected);

        var selected_give = [];
        $('.all_give:checked').each(function() {
            if($.inArray($(this).attr('parent_id'), selected_give) === -1) selected_give.push($(this).attr('parent_id'));
        });
        $('#parent_give_child_selected').val(selected_give);
    });

});
</script>

