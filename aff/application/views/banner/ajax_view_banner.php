<table class="table table-striped table-hover" id="bannerTable">
	<thead>
		<tr>
			<th><?= lang('lang.date'); ?></th>
			<th><?= lang('ban.name'); ?></th>
			<th><?= lang('ban.size'); ?></th>
			<th><?= lang('ban.lang'); ?></th>
			<th><?= lang('ban.thumb'); ?></th>
			<th><?= lang('lang.status'); ?></th>
			<th><?= lang('ban.download'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php if(!empty($banner)) { ?>
			<?php 
				foreach ($banner as $value) { 
					$date = date('Y-m-d', strtotime($value['createdOn']));
			?>
				<tr>
					<td><?= $date ?></td>
					<td><?= $value['bannerName'] ?></td>
					<td><?= $value['width'] . " x " . $value['height'] ?></td>
					<td><?= $value['language'] ?></td>
					<td><a href="#" onclick="window.open('<?= $value['bannerURL'] ?>','_blank', 'width=<?= $value['width'] ?>,height=<?= $value['height'] ?>,scrollbars=yes,status=yes,resizable=no,screenx=0,screeny=0')"><img src="<?= $value['bannerURL'] ?>" style="width: 50px; height: 40px;"/></a></td>
					<td><?= ($value['status'] == 0) ? 'Active':'Inactive' ?></td>
					<?php 
						$path = explode('/', $value['bannerURL']);
						$cnt = count($path) - 1;
					?>
					<td><a href="<?= BASEURL . 'affiliate/downloadBanner/' . rawurlencode($path[$cnt]) . '/' . rawurlencode($path[2]) ?>" data-toggle="tooltip" title="<?= lang('ban.download'); ?>"><i class="glyphicon glyphicon-download-alt"></i></a></td>
				</tr>
			<?php 
					}
				} 
			?>
	</tbody>
</table>

<script type="text/javascript">
$(document).ready(function(){
	$('#bannerTable').DataTable();
});
</script>