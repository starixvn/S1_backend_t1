<div class="well" style="overflow: auto" id="notifications">
    <!-- start dashboard notification -->                    
    <!-- <a class='notificationRefreshList' href="#new">
        <div class="col-md-2 notificationDashboard hover-shadow <?= ($chatNew != 0) ? 'notDboard-active' : ''?>" id="notificationDashboard-new" style="background-color: #fcf8e3;">
            TOTAL<br/><span class="notificationDashboardTxt" id="notificationDashboard-new"><?= $chatNew ?></span><br/>
            <span class="notificationDashboardLabel" id="notificationDashboard-new"> Unassigned Messages </span>
        </div>
    </a>            

    <a class='notificationRefreshList' href="#unread">
        <div class="col-md-2 notificationDashboard hover-shadow <?= ($chatUnread != 0) ? 'notDboard-active' : ''?>" id="notificationDashboard-unread" style="background-color: #d9edf7;">
            TOTAL<br/><span class="notificationDashboardTxt" id="notificationDashboard-unread"><?= $chatUnread ?></span><br/>
            <span class="notificationDashboardLabel" id="notificationDashboard-unread"> New Messages </span>
        </div>
    </a> -->

    <!-- end dashboard notification -->
</div>  

<div class="row">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> <?= lang("pay.moneytranfr"); ?> </h4>
                <div class="clearfix"></div>
            </div>

            <div class="panel panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover" style="margin: 30px 0 0 0;" id="myTable">
                            <thead>
                                <tr>
                                    <th><?= lang('lang.player') . ' ' . lang('pay.name'); ?></th>
                                    <th><?= lang('pay.playerlev'); ?></th>
                                    <th><?= lang('pay.curr'); ?></th>
                                    <th><?= lang('pay.curr'); ?></th>
                                    <th><?= lang('pay.transfrom'); ?></th>
                                    <th><?= lang('pay.transto'); ?></th>
                                    <th><?= lang('lang.transdate'); ?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php 
                                    foreach($subwallets as $value) { 
                                ?>
                                        <tr>
                                            <form action="<?= BASEURL . 'payment_management/approveMoneyTransfer/' . $value['subWalletDetailsId'] ?>" method="POST">
                                                <td><?= $value['username']?></td>
                                                <td><?= $value['amount']?></td>
                                                <td><?= $value['currency']?></td>
                                                <td><?= ($value['transferFrom'] == null) ? 'Main Wallet':$value['transferFrom'] . ' Wallet' ?></td>
                                                <td><?= ($value['transferTo'] == null) ? 'Main Wallet':$value['transferTo'] . ' Wallet' ?></td>
                                                <td><?= $value['requestDatetime']?></td>
                                                <td>
                                                    <input type="hidden" name="player_id" value="<?= $value['playerId']?>">
                                                    <input type="hidden" name="amount" value="<?= $value['amount']?>">
                                                    <input type="hidden" name="player_account_from" value="<?= $value['playerAccountFrom']?>">
                                                    <input type="hidden" name="player_account_to" value="<?= $value['playerAccountTo']?>">
                                                    <input type="hidden" name="transfer_from" value="<?= $value['transferFrom']?>">
                                                    <input type="submit" class="btn-sm btn-info review-btn" value="Approve Request">
                                                </td>
                                            </form>
                                        </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="panel-footer">

            </div>
        </div>
    </div>
</div>