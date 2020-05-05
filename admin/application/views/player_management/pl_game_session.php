<div class="panel-heading">
    <h4 class="panel-title"><strong>Casino game sessions</strong></h4>
</div>

<div class="panel panel-body" id="casino_game_sessions_panel_body">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Game Start</th>
                            <th>Game End</th>
                            <th>Game</th>
                            <th>Total Wins</th>
                            <th>Total Loss</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if($game_session) { ?>
                            <?php foreach($game_session as $row) { ?>
                                <tr>
                                    <td><?= $row['gameBegin']?></td>
                                    <td><?= $row['gameEnd']?></td>
                                    <td><?= $row['gameType']?></td>
                                    <td><?= $row['totalWin']?></td>
                                    <td><?= $row['totalLoss']?></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                                <tr>
                                    <td colspan="5" style="text-align:center"><span class="help-block">No Records Found</span></td>
                                </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>