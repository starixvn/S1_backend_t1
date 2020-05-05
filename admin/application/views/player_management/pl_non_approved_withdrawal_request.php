<input type="hidden" id="player_id" value="<?= $player['playerId'] ?>">
<div class="panel-heading">
    <h4 class="panel-title"><strong>Non approved withdrawal requests</strong></h4>
</div>

<div class="panel panel-body" id="non_approved_withdrawal_requests_panel_body">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Request date & time</th>
                            <th>Decline date & time</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($player_account)) { ?>
                            <?php foreach ($player_account as $row) { ?>
                                <tr>
                                    <td><?= $row['dwDateTime']?></td>
                                    <td><?= $row['processDatetime']?></td>
                                    <td><?= $row['dwMethod']?></td>
                                    <td><?= $row['amount']?></td>
                                    <td><?= $row['dwStatus']?></td>
                                </tr>
                            <?php }?>
                       <?php } else { ?>
                                <tr>
                                    <td colspan="5" style="text-align:center"><span class="help-block">No Records Found</span></td>
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
