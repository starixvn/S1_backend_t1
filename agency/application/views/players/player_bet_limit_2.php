<style type="text/css">
.tab-content{
	margin-top: 10px;
}
</style>
<div class="content-container">
	<?php if(isset($player_id)){ ?>
	<form action="/agency/updatePlayerBetLimit/<?=$player_id?>" method="POST">
	<?php }else{?>
	<form action="/agency/post_bet_limit_template/<?=@$template_id?>" method="POST">
	<?php }?>
	<div class="panel panel-primary">

		<div class="panel-heading">
			<h4 class="panel-title"><i class="fa fa-hand-stop-o"></i> <?=lang('Bet Limits')?></h4>
		</div>

		<div class="panel-body">
			<?php if(!isset($player_id)){ ?>
			<div class="row">
				<div class="col-md-4 form-group">
					<label class="control-label"><?=lang('Template Name')?></label>
					<input type="text" class="form-control input-sm" name="template_name" value="<?= @$template_name; ?>">
				</div>
			</div>
			<?php }?>

			<div class="row">
				<div class="col-md-4 form-group">
					<label class="control-label"><?=lang('Player Username')?></label>
					<span class="form-control input-sm"><?=$player_username?></span>
				</div>
			</div>

			<ul class="nav nav-tabs">
				<?php foreach (array_keys($limit) as $game_name): ?>
					<li role="presentation" <?php if ($game_name == array_keys($limit)[0]) echo 'class="active"'?>><a href="#limit_<?=$game_name?>" aria-controls="limit_<?=$game_name?>" role="tab" data-toggle="tab"><?=lang($game_name)?></a></li>
				<?php endforeach ?>
			</ul>

			<div class="tab-content">

				<?php foreach ($limit as $game_name => $game_tables): ?>
					<div role="tabpanel" class="tab-pane<?php if ($game_name == array_keys($limit)[0]) echo ' active'?>" id="limit_<?=$game_name?>">
						<div class="row">
							<div class="col-md-2">
								<ul class="nav nav-pills nav-stacked" >
									<?php foreach (array_keys($game_tables) as $game_table_id): ?>
										<li role="presentation" <?php if ($game_table_id == array_keys($game_tables)[0]) echo 'class="active"'?>><a href="#limit_<?=$game_table_id?>" aria-controls="limit_<?=$game_table_id?>" role="tab" data-toggle="tab"><?=$game_table_id?></a></li>
									<?php endforeach ?>
								</ul>
							</div>

							<div class="col-md-10">
								<div class="tab-content">
									<?php foreach ($game_tables as $game_table_id => $game_table): ?>
										<div role="tabpanel" class="tab-pane<?php if ($game_table_id == array_keys($game_tables)[0]) echo ' active'?>" id="limit_<?=$game_table_id?>">
											<div class="form-horizontal">
												<?php foreach ($game_table as $key => $value): ?>
													<?php if (substr($key, -3) == 'Min'): ?>
														<label class="control-label col-md-2"><?=lang(substr($key, strpos($key, '.') + 1, -3))?></label>
														<div class="col-md-2">
															<input type="hidden" name="old[<?=$game_table_id?>][<?=$key?>]" value="<?=$value?>"/>
															<input type="number" name="new[<?=$game_table_id?>][<?=$key?>]" class="form-control" placeholder="<?=lang('lang.min')?>" min="0" value="<?=$value?>" min="0" max="5000" step="any"/>
														</div>
														<div class="col-md-2">
															<input type="hidden" name="old[<?=$game_table_id?>][<?=str_replace('Min', 'Max', $key)?>]" value="<?=$game_table[str_replace('Min', 'Max', $key)]?>"/>
															<input type="number" name="new[<?=$game_table_id?>][<?=str_replace('Min', 'Max', $key)?>]" class="form-control" placeholder="<?=lang('lang.max')?>" value="<?=$game_table[str_replace('Min', 'Max', $key)]?>" min="0" max="5000" step="any"/>
														</div>
													<?php endif ?>
												<?php endforeach ?>
											</div>
										</div>
									<?php endforeach ?>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach ?>

			</div>

			<div class="text-right" style="margin-top: 15px;">
				<a href="/agency/players_list" class="btn btn-default"><?=lang('button.back')?></a>
				<button type="submit" class="btn btn-primary"><?=lang('Save')?></button>
			</div>

		</div>

	</div>
	</form>
</div>
<script type="text/javascript">
	$(function() {
		$("input").on("invalid", function(e) { 
			$(e.target).parents('.tab-pane').each(function() {
				$('a[href="#' + $(this).prop('id') + '"').tab('show');
			});
		});
	});
</script>