<div class="panel panel-primary">
	<div class="panel-heading">
		<div class="pull-right">
			<a href="#" class="close" onclick="closeDetails()">&times;</a>
		</div>
		<h4 class="panel-title"><?= lang('aff.aa03'); ?>: <b><?= $affiliate['username'] ?></b></h4>
	</div>

	<div class="panel-body" id="details_panel_body">
		<table class="table">
			<thead>
				<tr>
					<th><?=lang('aff.al25')?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($affiliate_tags as $affiliate_tag): ?>
					<tr>
						<td><?=$affiliate_tag['tagName']?></td>
						<td><a href="/affiliate_management/removeTag/<?=$affiliate_tag['affiliateTagId']?>" class="close" style="color: #f04124;" onclick="return confirm('<?=lang('sys.sure')?>')">&times;</a></td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
		<form method="post" action="/affiliate_management/postEditTag/<?=$affiliateId?>">
			<div class="input-group">
				<select id="tags" name="tags" class="form-control input-sm">
					<option value="">-<?= lang('lang.select'); ?>-</option>
					<?php foreach ($tags as $tag) { ?>
						<option value="<?= $tag['tagId']?>"><?= $tag['tagName']?></option>
					<?php } ?>
				</select>
				<span class="input-group-btn">
	                <button type="submit" class="btn btn-info btn-sm" id="tag_action"><?= lang('Tag'); ?></button>
                </span>
            </div>
		</form>
	</div>
</div>
