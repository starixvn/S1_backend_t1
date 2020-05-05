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
                    <th><?=lang('lang.date');?></th>
                    <th><?=lang('traffic.playerlocation');?></th>
                    <th><?=lang('aff.at06');?></th>
                    <th><?=lang('aff.vb10');?></th>
                    <th><?=lang('player.udw02');?></th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($withdraws)) {
                    foreach ($withdraws as $row) {
                        switch ($row['dwStatus']) {
                            case 'request':
                                $dwStatusDisplay = lang('Request');
                                break;

                            case 'approved':
                                $dwStatusDisplay = lang('Approved');
                                break;

                            case 'declined':
                                $dwStatusDisplay = lang('Declined');
                                break;

                            case 'paid':
                                $dwStatusDisplay = lang('Paid');
                                break;

                            default:
                                $dwStatusDisplay = lang('Processing');
                                break;
                        }
                    ?>
                        <tr>
                            <td><?=$row['dwDateTime']?></td>
                            <td><?=$row['dwLocation']?></td>
                            <td><?=number_format($row['amount'], 2)?></td>
                            <td><?=$dwStatusDisplay?></td>
                            <td><?=$row['transactionCode']?></td>
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
                <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
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
