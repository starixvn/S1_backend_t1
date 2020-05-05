
<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title pull-left"><i class="icon-price-tag"></i> <?=lang('player.tp01') . ' : <b>' . $player['username'] . '</b>';?> </h4>
		<div class="pull-right">
			<a href="#close" class="btn btn-default btn-sm" id="chat_history" onclick="closeDetails()"><span class="glyphicon glyphicon-remove"></span></a>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="panel-body" id="details_panel_body">
		<form method="post" action="<?=BASEURL . 'player_management/postEditTag/' . $playerId . '/' . $page?>" role="form">
			<input type="submit" name="tag_action" value="" style="display: none;">
			<?php if (empty($check)) {
	?>
				<div class="panel panel-info">
                            <div class="panel-heading">
				<div class="row">
					<div class="col-md-3">
						<label for="tag">
							<?=lang('player.tp02');?>:
						</label><br/>
						<?=$player_tag['tagName'] == '' ? lang('player.tp03') : $player_tag['tagName']?>
						<input type="hidden" name="tagId" value="<?=$player_tag['tagId'] == '' ? null : $player_tag['tagId']?>">
					</div>

					<div class="col-md-4">
						<label for="tag"><?=lang('player.tp04');?>: </label>
						<select id="tags" name="tags" class="form-control input-sm" onchange="showDescription(this)">
							<option value="">-Select-</option>
							<?php foreach ($tags as $tag) {?>
								<option value="<?=$tag['tagId']?>"><?=$tag['tagName']?></option>
							<?php }
	?>
						</select>
					</div>
					<div class="col-md-5" style="display: none;" id="description">
						<label for="specify"><?=lang('pay.description');?>: </label>
						<div class="" style="overflow: auto;">
							<center><div id="tagDescription"></div></center>
						</div>
					</div>
				</div>
			</div>
				</div>
			<div class="row">
				<div style="text-align:center;">
					<input class="btn btn-info btn-sm" type="submit" name="tag_action" id="tag_action" value="<?=lang('player.tp05');?>">
					<input type="button" value="<?=lang('lang.cancel');?>" class="btn btn-default btn-sm" onclick="closeModal()"/>
				</div>
			</div>
		</form>

		<?php } else {?>
			<div class="col-md-12">
			<form method="post" action="<?=BASEURL . 'player_management/postEditTag/' . $playerId . '/' . $page?>" role="form">
				<input type="hidden" name="remove_tag" value="remove_tag">

		        <div class="row">
		            <div class="col-md-12">
		                <center><h4><label for="unblocked"><?=lang('player.tp09');?> <i style="color:#66cc66;"><?=lang('player.tp10');?></i> <?=lang('player.tp11');?>? </label>
		                &nbsp;&nbsp;&nbsp;
		                <input type="submit" class="btn btn-sm btn-info" value="<?=lang('lang.yes');?>">
		                <a href="#list" class="btn btn-sm btn-default" id="chat_history" onclick="closeDetails()"><?=lang('lang.no');?></a></h4></center>
		            </div>
		        </div>
			</form>
			</div>
		<?php }
?>
	</div>
</div>
