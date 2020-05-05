<style type="text/css">
	.container .bootstrap-switch, .container .bootstrap-switch-label, .container .bootstrap-switch-handle-off, .container .bootstrap-switch-handle-on{
		height: auto;
	}
</style>
<div class="container">
	<br/>

	<div class="row">
		<div class="col-md-12" id="toggleView">
			<div class="panel panel-primary">
				<div class="nav-head panel-heading">
					<h4 class="panel-title pull-left"> <?=lang('nav.traffic');?></h4>
					<div class="clearfix"></div>
				</div>

				<div class="panel panel-body" id="affiliate_panel_body">
					<div class="panel panel-default">
						<div class="panel-heading">
							<form name="search-form" id="search-form" method="GET" action="<?php echo site_url('/affiliate/traffic_stats'); ?>">
					            <div class="col-md-12 searchOptions">
					            	<div class="col-md-6">
						            	<label for="period" class="control-label"><?=lang('Date');?>:</label>
				                        <input type="text" class="date_ranger form-control input-sm dateInput" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
				                        <input type="hidden" class="date_ranger" id="dateRangeValueStart" name="by_date_from" value="<?php echo $conditions['by_date_from']; ?>" />
				                        <input type="hidden" class="date_ranger" id="dateRangeValueEnd" name="by_date_to" value="<?php echo $conditions['by_date_to']; ?>" />

 									</div>
									<div class="col-md-3">
				                        <label class="control-label">
				                        <?php echo lang('Enabled date'); ?>
				                        <input type="checkbox" id="enable_date" name="enable_date" data-size='mini' value='true' <?php echo $conditions['enable_date'] ? 'checked="checked"' : ''; ?>>
				                        </label>
									</div>
				                    <div class="col-md-3 col-lg-3">
				                    </div>
					            </div>
					            <div class="col-md-12 searchOptions">
									<div class="col-md-3">
				                        <label for="by_banner_name" class="control-label"><?php echo lang('Banner'); ?>:</label>
				                        <input type="text" name="by_banner_name" id="by_banner_name" class="form-control input-sm" value="<?php echo $conditions['by_banner_name']; ?>"/>
					                </div>
									<div class="col-md-3">
				                        <label for="by_tracking_code" class="control-label"><?php echo lang('Tracking Code'); ?>:</label>
				                        <input type="text" name="by_tracking_code" id="by_tracking_code" class="form-control input-sm" value="<?php echo $conditions['by_tracking_code']; ?>"/>
					                </div>
									<div class="col-md-3">
				                        <label for="by_tracking_source_code" class="control-label"><?php echo lang('Source Code'); ?>:</label>
				                        <input type="text" name="by_tracking_source_code" id="by_tracking_source_code" class="form-control input-sm" value="<?php echo $conditions['by_tracking_source_code']; ?>"/>
					                </div>
									<div class="col-md-3">
                                <label for="by_type" class="control-label"><?php echo lang('Type'); ?>:</label>
                                <select name="by_type" id="by_type" class="form-control input-sm"> 
                                    <option value=""><?php echo lang('Select All')?></option>
                                  <option  <?php echo ($conditions['by_type'] == Http_request::TYPE_AFFILIATE_BANNER) ? 'selected' : ''  ?>  value="<?php echo Http_request::TYPE_AFFILIATE_BANNER ?>"><?php echo lang('con.aff29')?></option>
                                  <option  <?php echo ($conditions['by_type'] == Http_request::TYPE_AFFILIATE_SOURCE_CODE )? 'selected' : '' ?> value="<?php echo Http_request::TYPE_AFFILIATE_SOURCE_CODE ?>"><?php echo lang('Affiliate Source Code');?></option>
                                </select>
                                </div>

                                 <div class="col-md-3">
                                        <label for="registrationWebsite" class="control-label"><?php echo lang('Registration Website'); ?>:</label>
                                        <input type="text" name="registrationWebsite" id="registrationWebsite" class="form-control input-sm" value="<?php echo $conditions['registrationWebsite']; ?>"/>
                                  </div>
                                    <?php if( $this->utils->isEnabledFeature('enable_tracking_remarks_field') ){?>
                                    <div class="col-md-3">
                                        <label for="remarks" class="control-label"><?php echo lang('Remarks'); ?>:</label>
                                        <input type="text" name="remarks" id="remarks" class="form-control input-sm" value="<?php echo $conditions['remarks']; ?>"/>
                                    </div>
                                    <?php } ?>
                            </div>
					            <div class="col-md-12 search_command" style="margin-top: 20px;">
				                	<a href="/affiliate/traffic_stats?enable_date=true" id="refresh_main" class="btn-hov btn btn-info btn-sm"><?=lang('Refresh');?></a>
				                	<input type="submit" value="<?=lang('Search');?>" id="search_main" class="btn-hov btn btn-info btn-sm">
					            </div>

								<div class="clearfix"></div>
							</form>
							<br>
						</div>

						<div class="col-md-12" id="view_stats" style="margin: 30px 0 0 0;">
							<table class="table table-striped table-hover" id="statisticsTable" style="width:100%">
								<thead>
									<tr>
                                        <th><?=lang('Registration Website'); ?></th>
										<th><?=lang('Banner'); ?></th>
										<th><?=lang('Tracking Code'); ?></th>
										<th><?=lang('Source Code'); ?></th>
										<th><?=lang('No of Clicks'); ?></th>
										<th><?=lang('Sign Up'); ?></th>
										<th><?=lang('First Time Deposit'); ?></th>
										<th><?=lang('First Time Deposit Amount'); ?></th>
										<th><?=lang('Total Deposit Amount'); ?></th>
                                        <?php if( $this->utils->isEnabledFeature('enable_tracking_remarks_field') ){?>
                                        <th><?=lang('Remarks'); ?></th>
                                        <?php } ?>
									</tr>
								</thead>
								<tfoot>
									<tr>
                                        <th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th id="first-time-deposit-amount"></th>
										<th id="total-deposit-amount"></th>
                                        <?php if( $this->utils->isEnabledFeature('enable_tracking_remarks_field') ){?>
                                        <th></th>
                                        <?php } ?>
									</tr>
				                </tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include $this->utils->getIncludeView('report_action.php');?>

<script type="text/javascript">
    $(document).ready(function(){
        $("#trafficStats").addClass('active');
    });
    $(function() {

    	$('#enable_date').change(function(){
    		if($(this).is(":checked")){
    			$(".date_ranger").prop('disabled',false);
    		}else{
    			$(".date_ranger").prop('disabled',true);
    		}
    	});

    	var orderIdx=0;

    	var dataTable=$('#statisticsTable').DataTable( {
            dom: "<'panel-body' <'pull-right'B> <'pull-right progress-container'r>l>t<'panel-body'<'pull-right'p>i>",
            "order": [ orderIdx, 'asc' ],
			buttons:[
				{
                text: '<?php echo lang("Column visibility"); ?>',
                extend: 'colvis',
                className: 'btn-sm',
                postfixButtons: [ 'colvisRestore' ]
            	}
                <?php if ($export_report_permission) {?>
                ,{
                    text: '<?php echo lang("lang.export_excel"); ?>',
                    className:'btn btn-sm btn-primary',
                    action: function ( e, dt, node, config ) {
                        var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};

                            // utils.safelog(d);
                            $.post(base_url + "export_data/traffic_statistics_aff", d, function(data){ 
                                // utils.safelog(data);

                                //create iframe and set link
                                if(data && data.success){
                                    $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                                }else{
                                    alert('export failed');
                                }
                            }).fail(function(){
                                alert('export failed');
                            });
                        
                    }
                }
                <?php } ?>

    		],
            columnDefs: [
                { sortable: false, targets: [ 4,5,6 ] },
                { className: 'text-right', targets: [ 3,4,5,6,7 ] }
            ],
            "scrollX": true,
            processing: true,
            serverSide: true,
            searching: false,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post('<?php echo site_url("api/traffic_statistics_aff");?>', data, function(data) {
                    $('#first-time-deposit-amount').text(data.total.first_time_deposit_amount);
                    $('#total-deposit-amount').text(data.total.deposit_amount);
                    callback(data);
                    if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                        dataTable.buttons().disable();
                    }
                    else {
                        dataTable.buttons().enable();
                    }
                },'json');
            },
			"initComplete": function(settings, json) {
			    // $(".buttons-colvis").addClass('btn-sm');
			}

        } );


    	// var dataTable=initDataTable('#statisticsTable', '#search-form', '<?php echo site_url("api/traffic_statistics_aff");?>',
    	// 	'<?php echo site_url("export_data/traffic_statistics_aff");?>',
    	// 	orderIdx, 'asc', [ 4,5,6 ], [ 3,4,5,6,7 ] );

    	$('#refresh_main').click(function(){
    		dataTable.ajax.reload();
    	});

        $('[data-toggle="tooltip"]').tooltip();

        $('#enable_date').trigger('change');
    } );
</script>