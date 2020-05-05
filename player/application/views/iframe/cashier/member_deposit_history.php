<link href="<?=$this->utils->thirdpartyUrl('bootstrap-toggle-master/css/bootstrap-toggle.min.css');?>" rel="stylesheet" >
<script src="<?=$this->utils->thirdpartyUrl('bootstrap-toggle-master/js/bootstrap-toggle.min.js');?>"></script>
<style type="text/css"></style>
<div class="panel panel-primary">
    <!-- <div class="panel-heading">
        <?=lang('cashier.24');?>
        <div class="pull-right">
            <input id="toggle-event" checked="false" type="checkbox" data-toggle="toggle" data-size="mini" data-onstyle="warning" data-on="<?=lang('lang.show')?>" data-off="<?=lang('lang.hide')?>">
        </div>
    </div> -->
    <div id="deposit_history_table">
        <table class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th><?=lang('cashier.deposit_datetime');?></th>
                    <th><?=lang('cashier.deposit_secure_id');?></th>
                    <th><?=lang('cashier.27');?></th>
                    <th><?=lang('cashier.28');?></th>
                    <th><?=lang('system.word32');?></th>
                    <th><?=lang('cashier.29');?></th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($deposits)) {
	?>
                    <?php foreach ($deposits as $row) {?>
                        <tr>
                            <td><?=$row['created_at']?></td>
                            <td><?=$row['id']?></td>
                            <td><?=lang('sale_orders.payment_flag.' . $row['payment_flag'])?></td>
                            <td><?=lang('sale_orders.status.' . $row['status'])?></td>
                            <td><?=number_format($row['amount'], 2)?></td>
                            <td><?=$row['show_reason_to_player'] == 'true' ? $row['reason'] : '<i class="text-muted">' . lang('cashier.92') . '</i>'?></td>
                        </tr>
                    <?php }
	?>
                <?php } else {?>
                        <tr>
                            <td colspan="6" style="text-align:center"><span class="help-block"><?=lang('cashier.32');?></span></td>
                        </tr>
                <?php }
?>
            </tbody>
        </table>
        <?php if (!empty($deposits)) {?>
            <div class="panel-footer">
                <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links();?> </ul>
            </div>
        <?php }
?>
    </div>
</div>
<script type="text/javascript">
    $('#toggle-event').change(function() {
      if($(this).prop('checked')){
        $('#deposit_history_table').hide();
      }else{
        $('#deposit_history_table').show();
      }
    });
</script>
