<h4><b><?=lang('cashier.31');?></b></h4>
<div class="panel panel-default">
    <table class="table table-hover table-striped table-bordered">
        <thead>
            <tr>
                <th><?=lang('cashier.25');?></th>
                <th><?=lang('cashier.26');?></th>
                <th><?=lang('cashier.28');?></th>
                <th><?=lang('cashier.09');?></th>
                <th><?=lang('cashier.29');?></th>
            </tr>
        </thead>

        <tbody>
            <?php
if (!empty($withdrawals)) {
	?>
                <?php foreach ($withdrawals as $row) {
		?>
                    <tr>
                        <td><?=$row['processDatetime']?></td>
                        <td><?=$row['transactionCode']?></td>
                        <td>
                            <?php switch ($row['dwStatus']) {
		case 'request':
			/*$status = lang('report.playerWithdrawal.status.request');
			if ($row['is_checking']) {
				$status = lang('payment.checking');
			}
			echo $status;*/
            echo lang('Request');
			break;

		case 'approved':
			echo lang('Approved');
			break;

		case 'declined':
			echo lang('Declined');
			break;

        case 'paid':
            echo lang('Paid');
            break;

        default:
            echo lang('Processing');
            #echo lang($this->operatorglobalsettings->getCustomStageName($row['dwStatus']));
            break;
		}?>
                        </td>
                        <td><?=number_format($row['amount'], 2)?></td>
                        <td>
                            <?=$row['showNotesFlag'] == 'true' ? $row['notes'] : '<i class="help-block">' . lang('cashier.92') . '</i>'?></td>
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
    <div class="col-md-12 col-offset-0">
        <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
    </div>
</div>
