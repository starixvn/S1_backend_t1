<?php if($this->utils->isEnabledMDB()){?>
<div class="row">
    <div class="col-md-12" style="margin: 10px">
    <a href="<?=site_url('/system_management/sync_aff_reg_setting_to_mdb')?>" class="btn btn-success btn-sm">
        <i class="fa fa-refresh"></i> <?=lang('Sync To Currency')?>
    </a>
    </div>
</div>
<?php }?>
<div class="row">
<div class="col-md-12">
	<form action="<?=BASEURL . 'marketing_management/saveRegistrationSettings/2'?>" method="POST">
    	<table class="table table-striped table-hover table-bordered" style="width: 100%; float: left;">
    		<thead>
    			<tr>
    				<th style="text-align: left;"><?=lang('mark.fields');?></th>
                    <th style="text-align: center;"><input type="checkbox" id="visible" onclick="checkAll('visible');"/> <?=lang('mark.visible');?></th>
                    <th style="text-align: center;"><input type="checkbox" id="required" onclick="checkAll('required');"/> <?=lang('mark.required');?></th>
    			</tr>
    		</thead>

    		<tbody style="text-align: center;">
    			<?php foreach ($registration_fields as $key => $value) {?>
        			<tr>
                        <td style="text-align: left;"><?=lang('a_reg.' . $value['registrationFieldId']);?></td>
                        <td>
                            <input type="checkbox" name="<?=$value['registrationFieldId'] . '_visible';?>" id="<?=$value['registrationFieldId'] . '_visible';?>" <?=($value['visible'] == 0) ? 'checked' : ''?>  class="visible" onclick="uncheckAll(this.id);"/>
                        </td>
                        <td>
                            <?php if ($value['can_be_required'] == '0') {?>
                                <input type="checkbox" name="<?=$value['registrationFieldId'] . '_required';?>" id="<?=$value['registrationFieldId'] . '_required';?>" <?=($value['required'] == 0) ? 'checked' : ''?> class="required" onclick="uncheckAll(this.id);" <?=($this->marketing_manager->checkRegisteredFieldsIfVisible($value['field_name'], 2) == 0) ? '' : 'disabled'?> />
                            <?php }?>
                        </td>
        			</tr>
    			<?php }?>
    		</tbody>
		</table>

		<div class="col-md-4 col-md-offset-5">
			<input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-primary' ?> input-xs" value="<?=lang('lang.save');?>"/>
			<input type="reset" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary' ?> input-xs" value="<?=lang('lang.reset');?>"/>
		</div>
	</form>
</div>
</div>
