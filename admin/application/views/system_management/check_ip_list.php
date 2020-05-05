<button type="submit" value="<?= lang('sys.ip04'); ?>" name="type_of_action" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="<?= lang('sys.vu38'); ?>">
    <i class="glyphicon glyphicon-trash" style="color:white;"></i>
</button>&nbsp;
<button type="submit" value="<?= lang('sys.ip05'); ?>" name="type_of_action" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="<?= lang('sys.vu36'); ?>">
    <i class="glyphicon glyphicon-lock" style="color:white;"></i>
</button>&nbsp;
<button type="submit" value="<?= lang('sys.ip06'); ?>" name="type_of_action" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="<?= lang('sys.vu37'); ?>">
    <i class="icon-unlocked" style="color:white;"></i>
</button>&nbsp;
<?php if($ipList == 'true') { ?>
	<a href="#" class="btn btn-info btn-sm" data-toggle="tooltip" title="<?= lang('sys.ip02'); ?>" onclick="IpList('false');">
		<i class="glyphicon glyphicon-ok-sign"></i>
	</a>
<?php } else { ?>
	<a href="#" class="btn btn-danger btn-sm" data-toggle="tooltip" title="<?= lang('sys.ip03'); ?>" onclick="IpList('true');">
		<i class="glyphicon glyphicon-minus-sign"></i>
	</a>
<?php } ?>