<table class="table table-striped table-hover table-responsive" id="my_table">
    <thead>
        <tr>
            <!-- <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th> -->
            <th><?= lang('aff.apay10'); ?></th>
            <th><?= lang('aff.apay06'); ?></th>
            <th><?= lang('aff.apay07'); ?></th>
            <th><?= lang('aff.apay08'); ?></th>
            <th><?= lang('lang.status'); ?></th>
            <th><?= lang('aff.apay11'); ?></th>
            <th><?= lang('lang.action'); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php
                if(!empty($payments)) {
                    foreach($payments as $row) {
                        if($row['status'] == '2' || $row['status'] == '3') {
        ?>
                            <tr style="text-decoration: line-through;">
                                <!-- <td><input type="checkbox" class="checkWhite" id="<?= $row['paymentId']?>" name="payment[]" value="<?= $row['paymentId']?>" onclick="uncheckAll(this.id)"/></td> -->
                                <td><?= date("Y-m-d", strtotime($row['createdOn'])) ?></td>
                                <td><?= $row['username'] ?></td>
                                <td><a href="<?= BASEURL . 'affiliate_management/userInformation/' . $row['affiliateId'] ?>"><?= $row['paymentMethod'] . ": " . $row['accountNumber'] ?></a></td>
                                <td><?= $row['amount'] ?></td>
                                <?php
                                    if($row['status'] == 0) {
                                        $status = 'requests';
                                    } else if($row['status'] == 1) {
                                        $status = 'processing';
                                    } else if($row['status'] == 2) {
                                        $status = 'processed';
                                    } else if($row['status'] == 3) {
                                        $status = 'denied';
                                    }
                                ?>
                                <td><?= $status ?></td>
                                <td><?= ($row['reason'] == null) ? '<i>n/a</i>':$row['reason'] ?></td>
                                <td></td>
                            </tr>
        <?php       
                        } else {
        ?>
                            <tr>
                                <td><?= date("Y-m-d", strtotime($row['createdOn'])) ?></td>
                                <td><?= $row['username'] ?></td>
                                <td><a href="<?= BASEURL . 'affiliate_management/userInformation/' . $row['affiliateId'] ?>"><?= $row['paymentMethod'] . ": " . $row['accountNumber'] ?></a></td>
                                <td><?= $row['amount'] ?></td>
                                <?php
                                    if($row['status'] == 0) {
                                        $status = 'requests';
                                    } else if($row['status'] == 1) {
                                        $status = 'processing';
                                    } else if($row['status'] == 2) {
                                        $status = 'processed';
                                    } else if($row['status'] == 3) {
                                        $status = 'denied';
                                    }
                                ?>
                                <td><?= $status ?></td>
                                <td><?= ($row['reason'] == null) ? '<i>n/a</i>':$row['reason'] ?></td>
                                <td>
                                    <?php if($row['status'] == 0) { ?>
                                        <a href="#processing" onclick="processPayment('<?= $row['affiliatePaymentHistoryId'] ?>', '<?= $row['username'] ?>')">
                                            <span class="glyphicon glyphicon-plus-sign" data-toggle="tooltip" title="<?= lang('aff.apay12'); ?>"  data-placement="top">
                                            </span>
                                        </a>
                                        <a href="#deny" data-toggle="modal" data-target="#deny<?= $row['affiliatePaymentHistoryId'] ?>" >
                                            <span class="glyphicon glyphicon-remove-circle" data-toggle="tooltip" title="<?= lang('aff.apay13'); ?>"  data-placement="top">
                                            </span>
                                        </a>
                                    <?php } else { ?>
                                        <a href="#approve" onclick="approvePayment('<?= $row['affiliatePaymentHistoryId'] ?>', '<?= $row['username'] ?>')">
                                            <span class="glyphicon glyphicon-ok-sign" data-toggle="tooltip" title="<?= lang('aff.apay14'); ?>"  data-placement="top">
                                            </span>
                                        </a>
                                    <?php } ?>

                                    <a href="#notes" data-toggle="modal" data-target="#notes<?= $row['affiliatePaymentHistoryId'] ?>" >
                                        <span class="glyphicon glyphicon-comment" data-toggle="tooltip" title="<?= ($row['reason'] == null) ? lang('aff.apay15') : lang('aff.apay16') ?>"  data-placement="top">
                                        </span>
                                    </a>

                                    <form action="<?= BASEURL . 'affiliate_management/addNotes/' . $row['affiliatePaymentHistoryId'] . '/' . $row['username'] ?>" method="POST">
                                        <div class="modal fade" id="notes<?= $row['affiliatePaymentHistoryId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" id="myModalLabel"><?= lang('aff.apay17'); ?>: <?= $row['username'] ?></h4>
                                                    </div>

                                                    <div class="modal-body">
                                                        <label for="reason"><?= lang('aff.apay11'); ?>:</label>

                                                        <textarea name="reason" class="form-control" style="resize: none; height: 100px;"><?= $row['reason']?></textarea>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('lang.close'); ?></button>
                                                        <input type="submit" class="btn btn-primary" value="<?= lang('aff.apay18'); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <form action="<?= BASEURL . 'affiliate_management/denyPayment/' . $row['affiliatePaymentHistoryId'] . '/' . $row['username'] ?>" method="POST">
                                        <div class="modal fade" id="deny<?= $row['affiliatePaymentHistoryId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" id="myModalLabel"><?= lang('aff.apay17'); ?>: <?= $row['username'] ?></h4>
                                                    </div>

                                                    <div class="modal-body">
                                                        <label for="reason"><?= lang('aff.apay19'); ?>:</label>

                                                        <textarea name="reason" class="form-control" style="resize: none; height: 100px;"></textarea>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('lang.close'); ?></button>
                                                        <input type="submit" class="btn btn-primary" value="<?= lang('aff.apay18'); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>
        <?php
                        }
                    }
                } else {
        ?>

<!--             <tr>
                <td colspan="7" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
            </tr> -->
        <?php } ?>
    </tbody>
</table>

<script type="text/javascript">
    $(document).ready(function(){
        $('#myTable').DataTable();
    });
</script>