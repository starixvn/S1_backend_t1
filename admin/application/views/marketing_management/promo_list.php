<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list"></i> Promo List </h4>
		<button class="btn btn-info btn-sm pull-right" data-toggle="collapse" data-target="#panel-collapse-2"><i class="glyphicon glyphicon-chevron-up"></i></button>
		<div class="clearfix"></div>
	</div>
	<div id="panel-collapse-2" class="collapse in">
		<form action="<?= BASEURL . 'marketing_management/delete_selected_promos' ?>" method="post">
			<div class="panel-body" align="right">
				<a href="add_promo" class="btn btn-primary btn-sm" data-container="body" data-toggle="tooltip" title="Add Promo"><i class="glyphicon glyphicon-plus"></i> Add Promo</a>
			</div>
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Name</th>
						<th>Type</th>
						<th>Start</th>
						<th>End</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($promo_list as $promo): ?>
						<tr>
							<td><?php echo $promo['name'] ?></td>
							<td nowrap="nowrap"><?php echo $promo['type']['name'] ?></td>
							<td nowrap="nowrap"><?php echo $promo['period']['start'] ?></td>
							<td nowrap="nowrap"><?php echo $promo['period']['end'] ?></td>
							<td nowrap="nowrap"><?php echo $promo['status']['name'] ?></td>
							<td nowrap="nowrap">
								<a href="<?= BASEURL . 'marketing_management/promo_item/' . $promo['id'] ?>" class="btn btn-default btn-sm" data-container="body" data-toggle="tooltip" title="View Promo"><i class="glyphicon glyphicon-zoom-in"></i></a>
								<!-- <a href="<?= BASEURL . 'marketing_management/edit_promo/' . $promo['id'] ?>" class="btn btn-default btn-sm"  data-container="body" data-toggle="tooltip" title="Edit Promo"><i class="glyphicon glyphicon-pencil"></i></a> -->
							</td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		</form>
		<div class="panel-body" align="right"><?= $pagination ?></div>
		<div class="panel-footer"></div>
	</div>
</div>