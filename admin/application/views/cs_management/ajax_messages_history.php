<table class="table table-striped table-hover" style="margin: 30px 0 0 0;" id="myTable">
    <thead>
        <tr>
            <th><?= lang('cs.subject'); ?></th>
            <th><?= lang('cs.session'); ?></th>
            <th><?= lang('cs.sender'); ?></th>
            <th><?= lang('cs.recipient'); ?></th>
            <th><?= lang('lang.action'); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php 
            if(!empty($chat_history)) {
                foreach($chat_history as $chat_history) { 
        ?>
                    <tr>
                        <td><?= $chat_history['subject']?></td>
                        <td><?= $chat_history['session']?></td>
                        <td><?= $chat_history['sender']?></td>
                        <td><?= $chat_history['recepient']?></td>
                        <td>
                            <a href="#" data-toggle="tooltip" title="<?= lang('tool.cs01'); ?>" onclick="viewChatHistoryDetails('<?= $chat_history['messageId']?>');"><span class="glyphicon glyphicon-zoom-in"></span></a>

                            <?php if($this->permissions->checkPermissions('delete_chat_history')) { ?>
                                <a href="<?= BASEURL . 'cs_management/deleteChatHistory/' . $chat_history['messageId']?>" data-toggle="tooltip" title="<?= lang('tool.cs02'); ?>"><span class="glyphicon glyphicon-trash"></span></a>
                            <?php } ?>
                        </td>
                    </tr>
        <?php 
                } 
            } else {
        ?>
                <tr>
                    <td colspan="5" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
                </tr>
        <?php
            }
        ?>
    </tbody>
</table>

<br/><br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>