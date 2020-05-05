<style type="text/css">
.main_content .row{
	padding-top: 4px;
	padding-bottom: 4px;
}
</style>
<form action="/player_management/post_import_players/preview" method="POST" enctype="multipart/form-data">
<div class="row form-group main_content">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left">
					<i class="icon-user-plus"></i> <?=lang('Import Players by Upload CSV');?>
				</h4>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body" id="player_panel_body">
				<div class="row">
					<div class="col-md-2">
						<?=lang('Importer Formatter')?>
					</div>
					<div class="col-md-6">
						<select id="importer_formatter" name="importer_formatter" class="form-control">
							<option value='importer_standard' ><?=lang('Standard')?></option>
							<option value='importer_ole' <?=$default_importer=='importer_ole' ? "selected" : "" ?>><?=lang('OLE')?></option>
							<option value='importer_newrainbow' <?=$default_importer=='importer_ole' ? "selected" : "" ?>><?=lang('NEWRAINBOW')?></option>
							<option value='importer_lequ' <?=$default_importer=='importer_lequ' ? "selected" : "" ?>><?=lang('LEQU')?></option>
						</select>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
					<?=lang('Player Info')?> <span class="text-danger">*</span>
					<input type="file" class="form-control" name="import_player_csv_file">
					<a class="importer_sample" href='/sample_import_player.csv' target="_blank"><?=lang('Download Sample')?></a>
					</div>
					<div class="col-md-6">
						<label><?=lang('or Copy Text')?></label>
						<textarea name="import_player_text" class="form-control" cols="40" rows="10"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
					<?=lang('Affiliate Info')?>
					<input type="file" class="form-control" name="import_aff_csv_file">
					<a class="importer_sample" href='/sample_import_aff.csv' target="_blank"><?=lang('Download Sample')?></a>
					</div>
					<div class="col-md-6">
						<label><?=lang('or Copy Text')?></label>
						<textarea name="import_aff_text" class="form-control" cols="40" rows="10"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
					<?=lang('Affiliate Contact Info')?>
					<input type="file" class="form-control" name="import_aff_contact_csv_file">
					<a class="importer_sample" href='/sample_import_aff_contact.csv' target="_blank"><?=lang('Download Sample')?></a>
					</div>
					<div class="col-md-6">
						<label><?=lang('or Copy Text')?></label>
						<textarea name="import_aff_contact_text" class="form-control" cols="40" rows="10"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
					<?=lang('Player Contact Info')?>
					<input type="file" class="form-control" name="import_player_contact_csv_file">
					<a class="importer_sample" href='/sample_import_player_contact.csv' target="_blank"><?=lang('Download Sample')?></a>
					</div>
					<div class="col-md-6">
						<label><?=lang('or Copy Text')?></label>
						<textarea name="import_player_contact_text" class="form-control" cols="40" rows="10"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
					<?=lang('Player Bank Info')?>
					<input type="file" class="form-control" name="import_player_bank_csv_file">
					<a class="importer_sample" href='/sample_import_player_bank.csv' target="_blank"><?=lang('Download Sample')?></a>
					</div>
					<div class="col-md-6">
						<label><?=lang('or Copy Text')?></label>
						<textarea name="import_player_bank_text" class="form-control" cols="40" rows="10"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
					<?=lang('Agency Info')?>
					<input type="file" class="form-control" name="import_agency_csv_file">
					<a class="importer_sample" href='/sample_import_agency.csv' target="_blank"><?=lang('Download Sample')?></a>
					</div>
					<div class="col-md-6">
						<label><?=lang('or Copy Text')?></label>
						<textarea name="import_agency_text" class="form-control" cols="40" rows="10"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
					<?=lang('Agency Contact Info')?>
					<input type="file" class="form-control" name="import_agency_contact_csv_file">
					<a class="importer_sample" href='/sample_import_agency_contact.csv' target="_blank"><?=lang('Download Sample')?></a>
					</div>
					<div class="col-md-6">
						<label><?=lang('or Copy Text')?></label>
						<textarea name="import_agency_contact_text" class="form-control" cols="40" rows="10"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
					<input type="submit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" value="<?=lang('Upload and Preview')?>" >
					</div>
				</div>
				</form>
			</div>

			<div class="panel-footer"></div>
		</div>
	</div>

</div>
</form>
<script type="text/javascript">
	(function(){
		$("#importer_formatter").change(function(e){
			if($(this).val()!='importer_standard'){
				$(".importer_sample").hide();
			}else{
				$(".importer_sample").show();
			}
		});
	})();
</script>