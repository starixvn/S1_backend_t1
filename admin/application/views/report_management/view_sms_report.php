<style type="text/css">
input, select{
	font-weight: bold;
}
.select2-container--default .select2-selection--single{
	padding-top: 2px;
	height: 35px;
	font-size: 1.2em;
	position: relative;
	border-radius: 0;
	font-size:12px;;
}
</style>
<div class="panel panel-primary hidden">

	<div class="panel-heading">
		<h4 class="panel-title">
			<i class="fa fa-search"></i> <?=lang("lang.search")?>
			<span class="pull-right">
				<a data-toggle="collapse" href="#collapseSMSReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
			</span>
		</h4>
	</div>

	<div id="collapseSMSReport" class="panel-collapse <?= $this->config->item('default_open_search_panel') ? '' : 'collapse'?>">
		<div class="panel-body ">
			<form id="form-filter" class="form-horizontal" method="post">
			<div class="row">
				<div class="col-md-6">
					<label class="control-label"><?=lang('report.sum02')?></label>
					<input class="form-control dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
					<input type="hidden" id="date_from" name="date_from"/>
					<input type="hidden" id="date_to" name="date_to"/>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<label class="control-label" for="username"><?=lang('Username');?></label>
					<input type="text" name="username" id="username" class="form-control"/>
				</div>
				<div class="col-md-3">
					<label class="control-label" for="mobileNumber"><?=lang('Mobile Number')?></label>
					<input type="text" name="mobileNumber" id="mobileNumber" class="form-control"/>
				</div>
			</div>
			<div class="row">
				<div class="col-md-1" style="padding-top: 20px">
					<input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>">
				</div>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title"><i class="icon-mobile"></i> <?=lang('SMS Verification Code')?> </h4>
	</div>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="table table-bordered table-hover" id="myTable">
				<thead>
					<tr>
						<th><?=lang('Username')?></th>
						<th><?=lang('Session')?></th>
						<th><?=lang('Mobile Number')?></th>
						<th><?=lang('Verification Code')?></th>
						<th><?=lang('Verified')?></th>
						<th><?=lang('Created')?></th>
						<th><?=lang('Timeout')?></th>
					</tr>
				</thead>
			</table>
		</div><!-- EOF .table-responsive -->
	</div>
	<div class="panel-footer"></div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		var dataTable = $('#myTable').DataTable({
			lengthMenu: JSON.parse('<?=json_encode($this->utils->getConfig('default_datatable_lengthMenu'))?>'),
			pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
			order: [[ 5, "desc" ]],
			dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
			autoWidth: false,
			searching: false,
			buttons: [
				{
					extend: 'colvis',
					postfixButtons: [ 'colvisRestore' ],
            		className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
				},

				<?php

                    if( $this->permissions->checkPermissions('sms_report') ){

                ?>
				{
                    text: '<?php echo lang("lang.export_excel"); ?>',
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                    action: function ( e, dt, node, config ) {
                        var d = {'extra_search':$('#form-filter').serializeArray(), 'draw':1, 'length':-1, 'start':0};

                            $.post(site_url('/export_data/smsVerificationCodeReport'), d, function(data){
                              // utils.safelog(data);

                              //create iframe and set link
                              if(data && data.success){
                                  $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                              }else{
                                  alert('export failed');
                              }
                          });
                    }
                }
                <?php

                 }

                ?>
			],
			processing: true,
			serverSide: true,
			ajax: function (data, callback, settings) {
				data.extra_search = $('#form-filter').serializeArray();
				$.post(base_url + "api/smsVerificationCodeReport", data, function(data) {
					callback(data);
					if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
					    dataTable.buttons().disable();
					}
					else {
						dataTable.buttons().enable();
					}
				}, 'json');
			},
		});

		$('#form-filter').submit( function(e) {
            e.preventDefault();
            dataTable.ajax.reload();
        });
	});
</script>
