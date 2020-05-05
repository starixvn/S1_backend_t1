    <h4><b><?=lang('cashier.11');?></b></h4>
    <div class="panel panel-default">
        <table class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th><?=lang('cashier.25');?></th>
                    <th><?=lang('cashier.09');?></th>
                </tr>
            </thead>

            <tbody>
                <!-- <tr>
                    <td colspan="2" style="text-align:center"><span class="help-block">No Records Found</span></td>
                </tr> -->
                <?php if (!empty($cashbackHistory)) {
	?>
                    <?php foreach ($cashbackHistory as $row) {?>
                        <tr>
                            <td><?=$row['receivedOn']?></td>
                            <td><?=number_format($row['amount'], 2)?></td>
                        </tr>
                    <?php }
	?>
                <?php } else {?>
                        <tr>
                            <td colspan="4" style="text-align:center"><span class="help-block"><?=lang('cashier.32');?></span></td>
                        </tr>
                <?php }
?>
            </tbody>
        </table>

        <br/>

        <div class="col-md-12 col-offset-0">
            <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links();?> </ul>
        </div>
    </div>
