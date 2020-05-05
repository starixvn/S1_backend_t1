<div class="well" style="overflow: auto">
	<form action="<?= BASEURL ?>menus/create_menus" method="POST">
		<div class="col-md-1">
		<label><?= lang('cms.label'); ?>"</label>
			
		</div>
		<div class="col-md-2">
			<input type="text" name="label" />
			<?= form_error('label', '<span class="errors">', '</span>'); ?>
		</div>
		<div class="col-md-1">				
			<input type="submit" name="submit" value="<?= lang('lang.add'); ?>"" class="btn btn-sm btn-info" />
		</div>
		
	</form>
</div>
<div class="row">
	<div class="col-md-12">
		<table id="records" class="table table-striped table-hover">
			<tr>
				<td><?= lang('cms.label'); ?>"</td>
				<td><?= lang('cms.parentid'); ?>"</td>
				<td><?= lang('cms.link'); ?>"</td>
				<td><?= lang('lang.status'); ?>"</td>
				<td><?= lang('cms.options'); ?>"</td>
			</tr>
			<?php foreach ($data as $menus) { ?>
			<tr>		
				<td><?= $menus['label']; ?></td>
				<td><?= $menus['parent_id']; ?></td>
				<td><?= $menus['link']; ?></td>
				<td><?= $menus['status']; ?></td>
				<td><a href="<?= BASEURL ?>cms_management/update_menu/<?= $menus['menus_id'] ?>">Update</a>&nbsp;&nbsp;<a href="<?= BASEURL ?>menus/delete_menu/<?= $menus['menus_id'] ?>"><?= lang('lang.delete'); ?>"</a></td>				
			</tr>
			<?php	} ?>
		</table>
	</div>
</div>

<p><?= lang('cms.generatedmenu'); ?>"</p>
<ul id="main_menu">
	<?php foreach ($data as $menus) { ?>
		<li><a href="<?= $menus['link']; ?>"><?= $menus['label']; ?></a></li>
	<?php } ?>
</ul>