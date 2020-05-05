<style type="text/css">
</style>

<form action="<?php echo site_url('system_management/save_system_settings'); ?>" method="POST">
<div class="panel panel-primary panel_main">

	<div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-university"></i> &nbsp;<?php echo $title; ?>
		<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		</h4>
	</div>

	<div id="main_panel" class="panel-collapse collapse in ">

	<div class="panel-body">

		<?php if(!empty($bankList)){ ?>
		<table class="table table-hover table-striped table-bordered">
			<tr>
			<th class="col-md-1"><?php echo lang('Action');?></th>
			<th class="col-md-2"><?=lang('Bank Name')?></th>
			<th class="col-md-1"><?=lang('Bank Code')?></th>
			<th class="col-md-1"><?=lang('Card Number/Account')?></th>
			<th class="col-md-1"><?=lang('Account Name')?></th>
			<th class="col-md-6"><?=lang('Player Level')?></th>
			</tr>
			<?php foreach($bankList as $bankIndex => $bankInfo){ ?>
			<tr>
				<td>
					<a href="javascript:void(0);" onclick="editBankCode('<?php echo $bankIndex;?>')">
						<span title="<?=lang('Edit')?>" class="glyphicon glyphicon-edit"></span></a>
					<a href="javascript:void(0);" onclick="deleteBankCode('<?php echo $bankIndex;?>')">
						<span title="<?=lang('Delete')?>" class="glyphicon glyphicon-remove-circle"></span></a>
					<?php if(array_key_exists('enabled', $bankInfo) && !$bankInfo['enabled']) : ?>
					<a href="javascript:void(0);" onclick="enableBankCode('<?php echo $bankIndex;?>');">
						<span title="<?=lang('Deactivated, click to activate')?>" class="fa fa-toggle-off"></span></a>
					<?php else : ?>
					<a href="javascript:void(0);" onclick="disableBankCode('<?php echo $bankIndex;?>');">
						<span title="<?=lang('Activated, click to deactivate')?>" class="fa fa-toggle-on"></span></a>
					<?php endif; ?>
				</td>
				<td><?php echo lang($bankInfo['bank_name']); ?></td>
				<td><?php echo $bankInfo['bank_code']; ?></td>
				<td><?php echo $bankInfo['card_number']; ?></td>
				<td><?php echo @$bankInfo['name']; ?></td>
				<td><?php if(!empty($bankInfo['playerLevels'])) {
						$levelNames = array();
						foreach($bankInfo['playerLevels'] as $aLevel) {
							$levelNames[] = $levels[$aLevel];
						}
						echo join(', ', $levelNames);
					}
				?></td>
			</tr>
			<?php } ?>
		</table>
		<?php } ?>

	</div>

	<div class="panel-footer">
		<input type="button" class="btn btn-primary" onclick="addBankInfo()" value="<?php echo lang('Add'); ?>">
	</div>

	</div>

</div>
</form>

<?php
$frm_open=form_open('/payment_account_management/save_api_bank_info/'.$apiId, ['class'=>'frm_edit_bank_info']);
$frm_close=form_close();
$lang_bank_name=lang('Bank Name');
$lang_address=lang('Bank Address');
$lang_card_number=lang('Card Number/Account');
$lang_account_name=lang('Account Name');
$lang_player_list = lang('Player Level');
$bank_dropdown=form_dropdown('bank_id', $bankDropdown, '', 'class="bank_dropdown form-control"');
$player_list_select = form_multiselect('playerLevels[]',is_array( $levels) ?  $levels : array(), $form['playerLevels'], ' class="form-control input-sm chosen-select playerLevels" id="addPlayerLevels" data-placeholder="' . lang("cms.selectnewlevel") . '" data-untoggle="checkbox" data-target="#form_add .toggle-checkbox .playerLevels" ');
$form_bank_info=<<<EOD
<div class="row">
$frm_open
<input type="hidden" name="bank_index" class="bank_index">
<div class="col-md-12">
$lang_bank_name $bank_dropdown
</div>
<div class="col-md-12" style="margin-top: 10px">
$lang_address <textarea name="address" class="address form-control"></textarea>
</div>
<div class="col-md-12" style="margin-top: 10px">
$lang_card_number <input type="text" name="card_number" required="required" class="card_number form-control">
</div>
<div class="col-md-12" style="margin-top: 10px">
$lang_account_name <input type="text" name="name" required="required" class="name form-control">
</div>
<div class="col-md-12" style="margin-top: 10px">
$lang_player_list $player_list_select
</div>
$frm_close
</div>
EOD;
?>

<script type="text/javascript">

var bankList=<?php echo $this->utils->encodeJson($bankList); ?>;

function addBankInfo(){

	BootstrapDialog.show({
		title: '<?php echo lang('Add Bank Info');?>',
		message: <?php echo json_encode($form_bank_info);?>,
		nl2br: false,
		onshow: function(dialogRef){
			dialogRef.getModalBody().find('.bank_index').val('');
			dialogRef.getModalBody().find('.bank_dropdown').val('');
			dialogRef.getModalBody().find('.card_number').val('');
			dialogRef.getModalBody().find('.name').val('');
			dialogRef.getModalBody().find('.address').val('');
		},
		buttons: [{
			label: "<?php echo lang('Save'); ?>",
			cssClass: 'btn-primary',
			action: function(dialogRef) {
				//submit form
				dialogRef.getModalBody().find('.frm_edit_bank_info').submit();
				// dialogRef.close();
			}
		},{
			label: "<?php echo lang('Cancel'); ?>",
			cssClass: 'btn-danger',
			action: function(dialogRef) {
				dialogRef.close();
			}
		}
		]
	});

}

function disableBankCode(bankIndex){
	window.location.href="<?php echo site_url('/payment_account_management/enable_api_bank_info/'.$apiId); ?>/"+bankIndex + "/0";
}

function enableBankCode(bankIndex){
	window.location.href="<?php echo site_url('/payment_account_management/enable_api_bank_info/'.$apiId); ?>/"+bankIndex + "/1";
}

function deleteBankCode(bankIndex){
	if(confirm("<?php echo lang('Do you want delete this bank account'); ?>")){
		window.location.href="<?php echo site_url('/payment_account_management/delete_api_bank_info/'.$apiId); ?>/"+bankIndex;
	}
}

function editBankCode(bankIndex){

	var bankInfo=bankList[bankIndex];

	BootstrapDialog.show({
		title: '<?php echo lang('Edit Bank Info');?>',
		message: <?php echo json_encode($form_bank_info);?>,
		nl2br: false,
		onhide: function(dialogRef){},
		onshow: function(dialogRef){
			dialogRef.getModalBody().find('.bank_index').val(bankIndex);
			dialogRef.getModalBody().find('.bank_dropdown').val(bankInfo['db_bank_id']);
			dialogRef.getModalBody().find('.card_number').val(bankInfo['card_number']);
			dialogRef.getModalBody().find('.name').val(bankInfo['name']);
			dialogRef.getModalBody().find('.address').val(bankInfo['address']);
			dialogRef.getModalBody().find('.playerLevels').val(bankInfo['playerLevels']);
			dialogRef.getModalBody().find('.chosen-select').chosen({
				disable_search: true,
			}); // trigger chosen on dynamic element
		},
		buttons: [{
			label: "<?php echo lang('Save'); ?>",
			cssClass: 'btn-primary',
			action: function(dialogRef) {
				dialogRef.getModalBody().find('.frm_edit_bank_info').submit();
			}
		},{
			label: "<?php echo lang('Cancel'); ?>",
			cssClass: 'btn-danger',
			action: function(dialogRef) {
				dialogRef.close();
			}
		}
		]
	});

}

$(function(){
	//jquery choosen
	$(".chosen-select").chosen({
		disable_search: true,
	});

	$('input[data-toggle="checkbox"]').click(function() {

		var element = $(this);
		var target  = element.data('target');

		$(target).prop('checked', this.checked).prop('selected', this.checked);
		$(target).parent().trigger('chosen:updated');
		$(target).parent().trigger('change');

	});

	$('[data-untoggle="checkbox"]').on('change', function() {

		var element = $(this);
		var target  = element.data('target');
		if (element.is('select')) {
			$(target).prop('checked', element.children('option').length == element.children('option:selected').length);
		} else {
			$(target).prop('checked', this.checked);
		}

	});
});

</script>
