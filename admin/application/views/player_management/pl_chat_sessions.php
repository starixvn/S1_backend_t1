<input type="hidden" id="player_id" value="<?= $player['playerId'] ?>">
<div class="panel-heading">
    <h4 class="panel-title"><strong>Chat Sessions</strong></h4>
</div>

<div class="panel panel-body" id="chat_sessions_panel_body">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Session Id</th>
                            <th>Admin username</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(!empty($chat_history)) {
                                    foreach($chat_history as $chat) { ?>
                                        <tr>
                                            <td><a href="#session" class="details" onclick="viewChatHistoryDetails('<?= $chat['session']?>');"><?= $chat['session'] ?></a></td>
                                            <td><?= $chat['recepient'] ?></td>
                                            <td><?= $chat['date']?></td>
                                        </tr>
                                    <?php } ?>
                        <?php } else { ?>
                                <tr>
                                    <td colspan="3" style="text-align:center"><span class="help-block">No Records Found</span></td>
                                </tr>
                        <?php } ?>
                    </tbody>

                </table>

                <br/>

                <div class="col-md-12 col-offset-0">
                    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
                </div>
            </div>
        </div>
    </div>
</div>