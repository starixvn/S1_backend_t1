<style type="text/css">
.main_content .row{
	padding-top: 4px;
	padding-bottom: 4px;
}
</style>
<form action="/player_management/post_import_players/start" method="POST">
<input type="hidden" name="import_player_csv_file" value="<?=$import_player_csv_file?>">
<input type="hidden" name="import_aff_csv_file" value="<?=$import_aff_csv_file?>">
<input type="hidden" name="import_aff_contact_csv_file" value="<?=$import_aff_contact_csv_file?>">
<input type="hidden" name="import_player_contact_csv_file" value="<?=$import_player_contact_csv_file?>">
<input type="hidden" name="import_player_bank_csv_file" value="<?=$import_player_bank_csv_file?>">
<input type="hidden" name="import_agency_csv_file" value="<?=$import_agency_csv_file?>">
<input type="hidden" name="import_agency_contact_csv_file" value="<?=$import_agency_contact_csv_file?>">
<input type="hidden" name="importer_formatter" value="<?=$importer_formatter?>">
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
					<div class="col-md-3">
						<?=lang('Player Info')?>
					</div>
					<div class="col-md-9">
						<?php if(!empty($validPlayerCSV)){ ?>
						<div>
							<?=lang('Count of Column')?>: <?=$validPlayerCSV['column_count']?>
						</div>
						<div>
							<?=lang('Count of Row')?>: <?=$validPlayerCSV['row_count']?>
						</div>
						<?php }else{ ?>
						<div>
							<?=lang('NONE')?>
						</div>
						<?php } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<?=lang('Affiliate Info')?>
					</div>
					<div class="col-md-9">
						<?php if(!empty($validAffCSV)){ ?>
						<div>
							<?=lang('Count of Column')?>: <?=$validAffCSV['column_count']?>
						</div>
						<div>
							<?=lang('Count of Row')?>: <?=$validAffCSV['row_count']?>
						</div>
						<?php }else{ ?>
						<div>
							<?=lang('NONE')?>
						</div>
						<?php } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<?=lang('Affiliate Contact Info')?>
					</div>
					<div class="col-md-9">
						<?php if(!empty($validAffContactCSV)){ ?>
						<div>
							<?=lang('Count of Column')?>: <?=$validAffContactCSV['column_count']?>
						</div>
						<div>
							<?=lang('Count of Row')?>: <?=$validAffContactCSV['row_count']?>
						</div>
						<?php }else{ ?>
						<div>
							<?=lang('NONE')?>
						</div>
						<?php } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<?=lang('Player Contact Info')?>
					</div>
					<div class="col-md-9">
						<?php if(!empty($validPlayerContactCSV)){ ?>
						<div>
							<?=lang('Count of Column')?>: <?=$validPlayerContactCSV['column_count']?>
						</div>
						<div>
							<?=lang('Count of Row')?>: <?=$validPlayerContactCSV['row_count']?>
						</div>
						<?php }else{ ?>
						<div>
							<?=lang('NONE')?>
						</div>
						<?php } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<?=lang('Player Bank Info')?>
					</div>
					<div class="col-md-9">
						<?php if(!empty($validPlayerBankCSV)){ ?>
						<div>
							<?=lang('Count of Column')?>: <?=$validPlayerBankCSV['column_count']?>
						</div>
						<div>
							<?=lang('Count of Row')?>: <?=$validPlayerBankCSV['row_count']?>
						</div>
						<?php }else{ ?>
						<div>
							<?=lang('NONE')?>
						</div>
						<?php } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<?=lang('Agency Info')?>
					</div>
					<div class="col-md-9">
						<?php if(!empty($validAgencyCSV)){ ?>
						<div>
							<?=lang('Count of Column')?>: <?=$validAgencyCSV['column_count']?>
						</div>
						<div>
							<?=lang('Count of Row')?>: <?=$validAgencyCSV['row_count']?>
						</div>
						<?php }else{ ?>
						<div>
							<?=lang('NONE')?>
						</div>
						<?php } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<?=lang('Agency Contact Info')?>
					</div>
					<div class="col-md-9">
						<?php if(!empty($validAgencyContactCSV)){ ?>
						<div>
							<?=lang('Count of Column')?>: <?=$validAgencyContactCSV['column_count']?>
						</div>
						<div>
							<?=lang('Count of Row')?>: <?=$validAgencyContactCSV['row_count']?>
						</div>
						<?php }else{ ?>
						<div>
							<?=lang('NONE')?>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="panel-footer">
				<input type="submit" class="btn btn-sm btn-primary" value="<?=lang('Next Step')?>" >

				<a href="javascript:window.history.back()" class="btn btn-sm btn-danger pull-right"><?=lang('Cancel')?></a>
			</div>
		</div>
	</div>

</div>
</form>
<script type="text/javascript">
</script>
