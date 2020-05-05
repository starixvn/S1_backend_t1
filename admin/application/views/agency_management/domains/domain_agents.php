<div class="row">
<div class="col-md-offset-4 col-md-4">
<div class="list-group">
<?php
if(!empty($agents)){
	foreach ($agents as $agentInfo){
		echo $agentInfo;
	}
}else{
	echo lang('NONE');
}
?>
</div>
<a href="/agency_management/agent_domain_list" class="btn btn-default"><?=lang('Back')?></a>
</div>
</div>