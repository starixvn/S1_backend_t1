<?php
$isEdit = isset($bankTypeId);
$saveType = isset($bankTypeId) ? 'editBankType' : 'newBankType';
?>
<div class="row">
	<div class="col-md-offset-4 col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo $isEdit ? lang('pay.bt.edit.banktype') : lang('pay.bt.add.banktype'); ?>
				</h3>
			</div>
			<div class="panel-body">
				<?php if ($errors = validation_errors()): ?>
					<div class="alert alert-danger"><?=$errors?></div>
				<?php endif?>
				<form id="form" class="form" action="<?php echo site_url('/payment_management/'.$saveType.'/1'); ?>" method="post" enctype="multipart/form-data" >
					<?php if ($isEdit): ?>
						<input type="hidden" name="bankTypeId" value=<?=$bankTypeId?> />
					<?php endif;?>
					<?php $bank_name_language_fields = $this->config->item('bank_name_language_fields'); ?>
					<?php if(!empty($bank_name_language_fields)) : ?>
                        <?php foreach ($bank_name_language_fields as $key => $value) : ?>
                            <div class="form-group required">
							<label for="bankname_<?=$key?>" class="control-label"><?=lang('lang.'.$value.'.name')?> </label>
							<input type="text" name="bankname[<?=$key?>]" id="bankname_<?=$key?>" class="form-control" required="required" placeholder="<?=lang('lang.'.$value.'.name')?>" value="<?=isset($bankName) ? lang($bankName, $key) : ''?>" />
						</div>
                        <?php endforeach; ?>
                    <?php else: ?>
						<div class="form-group required">
							<label for="bankname_1" class="control-label"><?=lang('lang.english.name')?> </label>
							<input type="text" name="bankname[1]" id="bankname_1" class="form-control" required="required" placeholder="<?=lang('lang.english.name')?>" value="<?=isset($bankName) ? lang($bankName, 1) : ''?>" />
						</div>
						<div class="form-group required">
							<label for="bankname_2" class="control-label"><?=lang('lang.chinese.name')?> </label>
							<input type="text" name="bankname[2]" id="bankname_2" class="form-control" required="required" placeholder="<?=lang('lang.chinese.name')?>" value="<?=isset($bankName) ? lang($bankName, 2) : ''?>" />
						</div>
						<div class="form-group required">
							<label for="bankname_5" class="control-label"><?=lang('lang.korean.name')?> </label>
							<input type="text" name="bankname[5]" id="bankname_5" class="form-control" required="required" placeholder="<?=lang('lang.korean.name')?>" value="<?=isset($bankName) ? lang($bankName, 5) : ''?>" />
						</div>
					<?php endif; ?>
					<div class="form-group">
						<label for="external_system_id" class="control-label"><?=lang('pay.bt.payment_api_id')?> </label>
						<input name="external_system_id" id="external_system_id" class="form-control" type="text" value="<?= isset($external_system_id) && $external_system_id ? $external_system_id : ''?>" />
					</div>
					<div class="form-group">
						<label for="bank_code" class="control-label"><?=lang('Bank Code')?> </label>
						<input name="bank_code" id="bank_code" class="form-control" type="text" value="<?= isset($bank_code) && $bank_code ? $bank_code : ''?>" />
					</div>
                    <div class="form-group required">
                        <label for="payment_type_flag" class="control-label"><?=lang('Bank/3rd Payment Type')?> </label>
                        <select name="payment_type_flag" class="form-control input-sm" required="required" >
                            <?php foreach ($payment_type_flags as $id => $val): ?>
                            <option value="<?=$id?>" <?=(isset($payment_type_flag) && $id==$payment_type_flag) ? 'selected="selected"' : ''?>><?=$val?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
					<div class="form-group">
						<label for="bank_icon" class="control-label"><?=lang('Bank Icon')?> </label>
						<hr>
						<div class="col-md-8">
							<input type="file" name="filBankIcon[]" id="filBankIcon">
							<br><br>
							<input type="checkbox" name="chkUseDefaultIcon" id="chkUseDefaultIcon" value="1" <?=empty($bank_icon) ? "checked" : "" ?>> <label>Use default bank icon</label>
						</div>
						<div class="col-md-2"></div>
						<div class="corkol-md-8">
							<img id="imgBankIcon" src="<?= isset($bank_icon)?$this->utils->getBankIcon($bank_icon):$this->utils->imageUrl('no.png')?>" class="img-thumbnail" width="125" height="125" alt="<?=lang('Bank Icon')?>">
						</div>
						<div class="col-md-2"></div>
					</div>
				</form>
			</div>
			<div class="panel-footer">
				<div class="text-right">
					<a href="/payment_management/bank3rdPaymentList" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>"><?=lang('lang.cancel')?></a>
					<button type="submit" form="form" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>">
						<i class="fa fa-floppy-o"></i> <?php echo lang('Save'); ?>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(function(){
		$('#collapseSubmenu').addClass('in');
		$('#view_payment_settings').addClass('active');
		$('#bank3rdPaymentList').addClass('active');

		var fillEmptyBankNameFields = function() {
			var bankNameFieldIds = ['bankname_2', 'bankname_5'];
			$.each(bankNameFieldIds, function(index, value) {
				if($('#'+value).val() == '') {
					$('#'+value).val($('#bankname_1').val());
				}
			});
		};
		$('#bankname_1').on('blur', fillEmptyBankNameFields);
	});

	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#imgBankIcon').attr('src', e.target.result);
			}
			reader.readAsDataURL(input.files[0]);
		}
	}

	$("#filBankIcon").change(function(){
		readURL(this);
		$('#chkUseDefaultIcon').attr('checked', false);
	});
</script>
