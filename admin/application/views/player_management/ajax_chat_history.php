<table class="table table-striped table-hover" style="margin: 30px 0 0 0;" id="myTable">
	<thead>
		<tr>
			<th>Sender</th>
			<th>Receiver</th>
			<th>Action</th>
		</tr>
	</thead>

	<tbody>
		<?php if(!empty($chat_history)) { ?>
			<?php foreach($chat_history as $chat_history) { ?>
				<tr>
					<td><?= $chat_history['sender']?></td>
					<td><?= $chat_history['recepient']?></td>
					<td>
						<a href="#" data-toggle="tooltip" title="<?= lang('tool.cms05'); ?>" id="view" onclick="viewChatHistoryDetails('<?= $chat_history['session']?>');"><span class="glyphicon glyphicon-zoom-in"></span></a>

						<?php if($this->permissions->checkPermissions('delete_chat_history')) {?>
							<a href="<?= BASEURL . 'player_management/deleteChatHistory/' . $chat_history['session']?>" data-toggle="tooltip" title="<?= lang('lang.delete'); ?>" id="delete"><span class="glyphicon glyphicon-trash"></span></a>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		<?php } else { ?>
				<tr>
                    <td colspan="3" style="text-align:center"><span class="help-block">No Records Found</span></td>
                </tr>
		<?php } ?>
	</tbody>
</table>

<br/><br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>