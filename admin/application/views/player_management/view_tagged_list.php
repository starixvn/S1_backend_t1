<!--main-->
<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseTaggedList" class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapseTaggedList" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
			<form class="" id="search-form">
				<div class="row">
					<!-- date of tag -->
					<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
						<label class="control-label" for="search_date"><?=lang('tagged_players.date_of_tag');?></label>
						<div class="input-group">
		                    <input id="search_date" class="form-control input-sm dateInput user-success" data-time="true" data-start="#date_from" data-end="#date_to">
		                    <input type="hidden" id="date_from" name="date_from" value="<?= $date_from ?>">
		                    <input type="hidden" id="date_to" name="date_to" value="<?= $date_to ?>">
		                    <span class="input-group-addon input-sm">
                                <input type="checkbox" name="search_reg_date" id="search_reg_date" class="user-success">
		                    </span>
		                </div>
		            </div>
		            <!-- date of last update -->
					<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
						<label class="control-label" for="search_last_update"><?=lang('tagged_players.date_of_last_update');?></label>
						<div class="input-group">
		                    <input id="search_last_update" class="form-control input-sm dateInput user-success" data-time="true" data-start="#last_update_from" data-end="#last_update_to">
		                    <input type="hidden" id="last_update_from" name="last_update_from" value="<?= $last_update_from ?>">
		                    <input type="hidden" id="last_update_to" name="last_update_to" value="<?= $last_update_to ?>">
		                    <span class="input-group-addon input-sm">
                                <input type="checkbox" name="search_last_update_date" id="search_last_update_date" class="user-success">
		                    </span>
		                </div>
		            </div>
	            	<!-- username -->
					<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
							<label class="control-label" for="username"><?=lang('Username');?></label>
							<input id="username" name="username" class="form-control input-sm user-success">
		            </div>
		            <!-- select tags -->
					<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
							<label class="control-label" for="tag"><?=lang('tagged_players.select_tags');?></label>
							<?php echo form_multiselect('selected_tags[]', is_array($tags) ? $tags : [], [], ' class="form-control input-sm chosen-select" id="selected_tags" data-placeholder="" data-untoggle="checkbox" data-target=""') ?>
					</div>
				</div>
	            <!-- buttons -->
				<div class="row">
	            	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style=" margin-top: 1rem;">
						<div class="pull-right">
							<input type="submit" value="<?=lang('lang.search');?>" id="search_main" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-primary' ?>">
							<a href="/player_management/taggedlist" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default' ?>"><?=lang('lang.reset');?></a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<!--end of main-->

<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt pull-left">
					<i class="icon-price-tags"></i> <?=lang('player.tl10');?>
				</h4>
				<span class="clearfix"></span>
			</div>

			<div class="panel-body" id="player_panel_body">
				<div class="table-responsive">
					<table class="table table-striped table-hover" style="margin: 0px 0 0 0; width: 100%;" id="myTable">
						<thead>
							<tr>
								<th data-th="1" ><?=lang('player.01'); # 1 ?> </th>
								<th data-th="2"><?=lang('sys.vu19'); # 2 ?></th>
								<th data-th="3"><?=lang('VIP Level'); # 3 ?></th>
								<th data-th="7"><?=lang('player.41'); # 7 ?></th>
								<th data-th="8"><?=lang('tagged_players.tagged_at'); # 8 ?></th>
								<th data-th="11" class="hidden-col"><?=lang('player.42');  # 11 ?></th>
								<th data-th="12" class="hidden-col"><?=lang('player.43');  # 12 ?></th>
								<th data-th="13"><?=lang('lang.status');  # 13 ?></th>
								<th data-th="14"><?=lang('tagged_players.account_status_last_update');  # 14 ?></th>
							</tr>
						</thead>
					</table>
				</div>
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>

	<div class="col-md-7" id="player_details" style="display: none;">

	</div>
</div>

<?php if ($this->utils->isEnabledFeature('export_excel_on_queue')) {?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
    <input name='json_search' type="hidden">
    </form>
<?php }?>
<script type="text/javascript">
    $(document).ready(function(){
    	var hiddenColumns = [];
    	var elem = $('#myTable thead tr th');
    	var search_tag = '<?=$search_tag?>';
    	var search_reg_date = '<?=$search_reg_date?>';

    	var flagColIndex = elem.filter(function(index){
	        if ($(this).hasClass('hidden-col')) {
	            hiddenColumns.push(index);
	        }
	    }).index();

        var dataTable = $('#myTable').DataTable({
        	autoWidth: false,
			searching: false,
        	<?php if ($this->utils->isEnabledFeature('column_visibility_report')) { ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
        	 dom: "<'panel-body nopadding' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'text-center'r><'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
             columnDefs: [
	             	{
	         	       /* set the default columns for tagged list table*/
	             		// "targets": [8,9],
	             		"targets": hiddenColumns,
	             		"visible": false
	             	}
             	],
        	 buttons: [
                {
					extend: 'colvis',
					className:'<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : '' ?>',
                    postfixButtons: [ 'colvisRestore' ]
                },
                <?php if ($this->permissions->checkPermissions('export_tagged_players')) : ?>
                        {
                            text: "<?php echo lang('CSV Export'); ?>",
                            className:'<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn btn-sm btn-portage' : 'btn btn-sm btn-primary' ?>',
                            action: function ( e, dt, node, config ) {
                                var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                                // utils.safelog(d);

                                <?php if ($this->utils->isEnabledFeature('export_excel_on_queue')) : ?>
                                    $("#_export_excel_queue_form").attr('action', site_url('/export_data/playertaggedlist'));
                                    $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                    $("#_export_excel_queue_form").submit();
                                <?php else : ?>
	                                $.post(site_url('/export_data/playertaggedlist'), d, function(data){
	                                    // utils.safelog(data);

	                                    //create iframe and set link
	                                    if(data && data.success){
	                                        $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
	                                    }else{
	                                        alert('export failed');
	                                    }
	                                });
	                            <?php endif; ?>
                            }
                        }
                <?php endif; ?>
            ],
        	"order": [ 0, 'asc' ],
        	// SERVER-SIDE PROCESSING
			processing: true,
			serverSide: true,

			ajax: function (data, callback, settings) {
				data.extra_search = $('#search-form').serializeArray();
				$.post(base_url + "api/playertaggedlist", data, function(data) {
					callback(data);
					if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
					    dataTable.buttons().disable();
					}
					else {
						dataTable.buttons().enable();
					}
				},'json');
			},

        	// drawCallback: function () {
         //        if ($('#myTable').DataTable().rows( { selected: true } ).indexes().length === 0 ) {
         //            $('#myTable').DataTable().buttons().disable();
         //        }
         //        else {
         //            $('#myTable').DataTable().buttons().enable();
         //        }
         //    }
        });

        $(".chosen-select").select2({
            disable_search: true,
			width: '100%',
        });


        if(search_reg_date == 'true'){
            $('#search_reg_date').prop('checked', true);
        }else{
            $('#search_reg_date').prop('checked', false);
        }

        if(!!search_tag){
            $(".chosen-select").val([search_tag]).trigger('change');
        }

        $('#search-form').submit( function(e) {
			e.preventDefault();
	        dataTable.ajax.reload();
		});

    });
</script>