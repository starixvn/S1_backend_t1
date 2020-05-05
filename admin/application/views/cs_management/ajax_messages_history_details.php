<style>
	td > p{
		width:400px;
		word-wrap:break-word;
	}
	table{
		word-break:break-all;
		word-wrap:break-word;
	}
	pre{
		width:400px;
	}
</style>
<table class="table table-hover">
	<tbody>
		<?php $sender = null; ?>
		<?php foreach ($chat_details as $row) { ?>
			<tr class="<?= ($row['flag'] == 'admin') ? 'info' : 'warning' ?>">
				<td>
					<b><?=$row['sender'] . ": "?></b>
					<?=$row['message'] . "<br/>";?>
				</td>
				<td align="right" style="width:130px;">
					<i><?=$row['date']?></i>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>