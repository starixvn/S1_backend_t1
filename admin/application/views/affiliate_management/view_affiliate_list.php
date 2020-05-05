<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseAffiliateList" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
            <?php include __DIR__ . "/../includes/report_tools.php" ?>
        </h4>
    </div>

    <div id="collapseAffiliateList" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
			<form class="form-horizontal" id='search-form' action="<?php echo site_url('affiliate_management/aff_list'); ?>" method="get" role="form" name="myForm">
				<div class="form-group">
					<div class="col-md-4">
						<label class="control-label"><?=lang('aff.ap04');?></label>
						<div class="input-group">
						<input type="text" class="form-control input-sm dateInput" data-start="#start_date" data-end="#end_date" data-time="true"/>
						<input type="hidden" name="start_date" id="start_date" value="<?=(isset($conditions['start_date']) ? $conditions['start_date'] : '')?>">
						<input type="hidden" name="end_date" id="end_date" value="<?=(isset($conditions['end_date']) ? $conditions['end_date'] : '')?>">
                                <span class="input-group-addon input-sm">
                                    <input type="checkbox" name="search_reg_date" id="search_reg_date" value="1" <?php if (isset($conditions['search_reg_date']) && $conditions['search_reg_date']) {echo 'checked';}
?>/>
                                </span>
						</div>
					</div>
					<div class="col-md-2">
						<label for="by_status" class="control-label"><?=lang('aff.al16');?></label>
                        <select name="by_status" id="by_status" class="form-control input-sm">
                            <option value="">--  <?php echo lang('None'); ?> --</option>
                            <option value="0"  <?php echo ($conditions['by_status'] === '0') ? 'selected' : ''; ?> ><?php echo lang('Active Only'); ?></option>
                            <option value="1" <?php echo ($conditions['by_status'] === '1') ? 'selected' : ''; ?> ><?php echo lang('Inactive only'); ?></option>
                            <!-- <option value="2" <?php echo ($conditions['by_status'] === '2') ? 'selected' : ''; ?> > --><!-- <?php echo lang('Deleted only'); ?></option> -->
                            <option value="-1" <?php echo ($conditions['by_status'] === '-1') ? 'selected' : ''; ?> ><?php echo lang('No empty affiliate'); ?></option>
                        </select>
					</div>
					<div class="col-md-2">
						<label for="by_username" class="control-label"><?=lang('aff.al10');?></label>
						<input type="text" name="by_username" id="by_username" class="form-control input-sm" value="<?php echo $conditions['by_username']; ?>">
						<?php echo form_error('by_username', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-2">
						<label for="by_code" class="control-label"><?php echo lang('Tracking Code');?></label>
						<input type="text" name="by_code" id="by_code" class="form-control input-sm" value="<?php echo $conditions['by_code']; ?>">
						<?php echo form_error('by_code', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-2">
						<label for="by_firstname" class="control-label"><?=lang('aff.al14');?></label>
						<input type="text" name="by_firstname" id="by_firstname" class="form-control input-sm" value="<?php echo $conditions['by_firstname']; ?>">
						<?php echo form_error('by_firstname', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-2">
						<label for="by_lastname" class="control-label"><?=lang('aff.al15');?></label>
						<input type="text" name="by_lastname" id="by_lastname" class="form-control input-sm" value="<?php echo $conditions['by_lastname'] ?>">
						<?php echo form_error('by_lastname', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-2">
						<label for="by_email" class="control-label"><?=lang('aff.al11');?></label>
						<input type="by_email" class="form-control input-sm" name="by_email" id="by_email" value="<?php echo $conditions['by_email']; ?>">
						<?php echo form_error('by_email', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-2">
						<label for="by_parent_id" class="control-label"><?=lang('lang.parentAffiliate');?></label>
						<?php echo form_dropdown('by_parent_id', $aff_parent_list, $conditions['by_parent_id'], 'class="form-control input-sm"'); ?>
					</div>

					<div class="col-md-2">
                        <label class="control-label"><?php echo lang('sys.dm6'); ?></label><br>
						<input type="text" name="domain" id="domain" class="form-control input-sm" value="<?php echo $conditions['domain']; ?>">
						<?php echo form_error('domain', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    </div>
					<div class="col-md-2">
                        <label class="control-label"><?php echo lang('With parent'); ?></label><br>
                        <input type="checkbox" data-off-text="<?php echo lang('off'); ?>" data-on-text="<?php echo lang('on'); ?>" name="show_with_parent" data-size='mini' value='true' <?php echo $conditions['show_with_parent'] ? 'checked="checked"' : ''; ?>>
                    </div>

                    <div class="col-md-12">
                        <label class="control-label"><?=lang('Affiliate Tag');?> </label>
                        <div class="row">
                            <?php if(isset($tags) && !empty($tags)):?>
                                <?php foreach ($tags as $tag_id => $tag) {?>
                                    <div class="col-md-2">
                                        <label>
                                            <input type="checkbox" name="tag_id[]" value="<?=$tag_id?>" <?=in_array($tag_id, $conditions['tag_id']) ? 'checked="checked"' : ''?>>
                                            <?=$tag?>
                                        </label>
                                    </div>
                                <?php }?>
                            <?php endif;?>
                        </div>
                    </div>

					<div class="col-md-2" style="padding-top:23px;text-align:left">
						<input type="button" value="<?=lang('aff.al22');?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>" onclick="window.location='/affiliate_management/aff_list';">
						<input type="submit" value="<?=lang('aff.al21');?>" id="search_main"class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-primary'?>">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


	<div class="row">
		<div class="col-md-12" id="toggleView">
			<div class="panel panel-primary">
				<div class="panel-heading custom-ph">
					<h4 class="panel-title custom-pt pull-left">
						<i class="icon-list"></i>
						<?=lang('aff.al33');?>
					</h4>
					<a href="<?=$this->utils->getSystemUrl('aff') . '/affiliate/register?lang='?>" id="affreg" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs btn-info' : 'btn-sm btn-default'?>" target="_blank">
						<i class="fa fa-plus-circle"></i> <?=lang('player.ui71');?>
					</a>
					<div class="clearfix"></div>
				</div>

				<div class="panel-body" id="affiliate_panel_body">
					<!-- <hr class="hr_between_table"/> -->
					<div class="table-responsive">
						<form action="<?=site_url('affiliate_management/actionType')?>" method="post" role="form">
                            <?php if($this->permissions->checkPermissions('activate_deactivate_affiliate')) { ?>
							<input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-havelockblue' : 'btn-success'?>" name="action_type" value="<?=lang('Activate Selected')?>" onclick="return confirm('<?=lang('sys.ga.conf.able.msg')?>');"/>
                            <?php } ?>
							<table class="table table-bordered table-hover dataTable" style="width: 100%;" id="affiliatesTable">
								<thead>
									<tr>
										<th style="padding:8px"><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
										<th><?=lang('aff.aj08');?></th>
										<th><?=lang('aff.aj01');?></th>
										<th><?=lang('Tracking Code');?></th>
										<th><?=lang('aff.aj02');?></th>
										<th><?=lang('aff.aj03');?></th>
										<th><?=lang('aff.aj04');?></th>
										<th><?=lang('aff.al49');?></th>
										<th><?=lang('aff.aj05');?></th>
										<th><?=lang('aff.aj06');?></th>
										<th><?=lang('Prefix of player');?></th>
										<th><?=lang('Balance Wallet');?></th>
										<th><?=lang('aff.aj07');?></th>
										<th><?=lang('Last Login');?></th>
										<th><?=lang('Last Login IP');?></th>
									</tr>
								</thead>

								<tbody>

								</tbody>
							</table>
						</form>
					</div>
				</div>
				<div class="panel-footer"></div>
			</div>
		</div>
		<div class="col-md-5" id="affiliate_details" style="display: none;"></div>

	</div>

	<div class="modal fade in" id="affiliate_notes" tabindex="-1" role="dialog" aria-labelledby="label_affiliate_notes">
	      <div class="modal-dialog" role="document">
	          <div class="modal-content">
	              <div class="modal-header">
	                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                      <span aria-hidden="true">&times;</span>
	                  </button>
	                  <h4 class="modal-title" id="label_affiliate_notes"></h4>
	              </div>
	              <div class="modal-body"></div>
	          </div>
	      </div>
	  </div> <!--  modal for level name setting }}}4 -->
<!--end of MODAL for edit column-->

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
<input name='json_search' type="hidden">
</form>
<form id="_export_csv_form" class="hidden" method="POST" target="_blank">
<input name='json_search' id = "json_csv_search" type="hidden">
</form>
<?php }?>

<script type="text/javascript">
	var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";
    $(document).ready(function() {
    	var new_href = $('#affreg').attr('href')+$('#lang_select').val();
    	$('#affreg').attr('href',new_href);
    	$('#by_status_2').change(function(){
    		// console.log($('#by_status_2').find('option:selected').val());
    		$('#by_status').val($('#by_status_2').find('option:selected').val());
    	});
	    var dataTable = $('#affiliatesTable').DataTable({
            searching: false,
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            buttons: [
                {
                    extend: 'colvis',
					postfixButtons: [ 'colvisRestore' ],
					className: "<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>"
                }
                <?php if ($export_report_permission) {?>
                ,{
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                    action: function ( e, dt, node, config ) {
                        // var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                        // // utils.safelog(d);
                        // $.post(site_url('/export_data/aff_list'), d, function(data){
                        //     // utils.safelog(data);

                        //     //create iframe and set link
                        //     if(data && data.success){
                        //         $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                        //     }else{
                        //         alert('export failed');
                        //     }
                        // }).fail(function(){
                        //     alert('export failed');
                        // });

                        var d = {'extra_search': $('#form-filter').serializeArray(), 'export_format': 'csv', 'export_type': export_type,
                            'draw':1, 'length':-1, 'start':0};
                        <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                                    $("#_export_excel_queue_form").attr('action', site_url('/export_data/aff_list'));
                                    $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                    $("#_export_excel_queue_form").submit();
                        <?php }else{?>

                        $.post(site_url('/export_data/aff_list'), d, function(data){
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

                        <?php }?>


                    }
                }
                <?php } ?>
            ],
            columnDefs: [
                { sortable: false, targets: [ 0 ],orderable: false},
                { className: 'text-right', targets: [ 9 ] }
            ],
            "order": [ 9, 'desc' ],
            processing: true,
            serverSide: true,
            createdRow: function(row,data,index){
            	if(data[10]=="<?php echo lang('Inactive'); ?>"){
            		$(row).addClass('warning');
            	}else if(data[10]=="<?php echo lang('Deleted'); ?>"){
            		$(row).addClass('danger');
				}
            },
            //"dom": '<"top"fl>rt<"bottom"ip>',
            <?php if(!$this->utils->isEnabledFeature('enable_reset_affiliate_list')) {?>
            "fnDrawCallback": function(oSettings) {
                var newData = JSON.parse(localStorage.getItem("data"));
                	if(newData != null){
						for (i = 0; i < newData.length; i++) {
							$("#" + newData[i]).attr("checked", true);
						}
					}
            },
            <?php } ?>
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/aff_list", data, function(data) {
                    callback(data);
                    if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
					    dataTable.buttons().disable();
					}
					else {
						dataTable.buttons().enable();
					}
                    // $('#total_amount').text(data.summary[0].total_amount);
                },
            'json');

            }
	    });

	    $("#affiliatesTable").change(function(){
	    	var old_data = JSON.parse(localStorage.getItem("data"));
			var list = document.querySelectorAll('td input[type="checkbox"]:checked');
			var unchecked = document.querySelectorAll('td input[type="checkbox"]:not(:checked)');

			var data_list = [];
			for (i = 0; i < list.length; i++) {
				data_list.push(list[i].id);
			}

			var unchecked_list = [];
			for (i = 0; i < unchecked.length; i++) {
				unchecked_list.push(unchecked[i].id);
			}
			if(old_data!=null){
				var newData = data_list.concat(old_data);
				var filter_dup_data = jQuery.unique(newData);
				var remove_unchecked = newData = newData.filter( function( val ) {
				  return !unchecked_list.includes( val );
				} );
				localStorage.setItem("data", JSON.stringify(remove_unchecked));
			}else{
				localStorage.setItem("data", JSON.stringify(data_list));
			}
		});

	    $('#btn-freeze').on('click', function(){
	    	$('#action_type').val('locked');

	    	var affiliates = getAffiliatesId();
	    	$('#affiliates').val(affiliates);

	    	if(affiliates == '') {
	    		return false;
	    	}
	    });

	    $('#btn-tag').on('click', function(){
	    	$('#action_type').val('tag');

	    	var affiliates = getAffiliatesId();
	    	$('#affiliates').val(affiliates);

	    	if(affiliates == '' || $('#tags').val() == '') {
	    		return false;
	    	}
	    });

	    function getAffiliatesId() {
	    	affiliateIDs = Array();
	    	$('input[name^="affiliate"]:checked').each(function() {
			    affiliateIDs.push($(this).val());
			});

			return affiliateIDs.join(", ");
	    }


	   
    } );


		function affiliate_notes($affId)
		{
			var dst_url = "/affiliate_management/affiliate_notes/" + $affId;
			open_modal('affiliate_notes', dst_url, "<?php echo lang('Affiliate Remarks'); ?>");
		}

		function open_modal(name, dst_url, title) {
			var main_selector = '#' + name;

			var label_selector = '#label_' + name;
			$(label_selector).html(title);

			var body_selector = main_selector + ' .modal-body';
			var target = $(body_selector);
			target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(dst_url);

			$(main_selector).modal('show');
		}

		function add_affiliate_notes(self, affiliate_id) {
		    var url = $(self).attr('action');
		    var params = $(self).serializeArray();
		    $.post(url, params, function(data) {
		        if (data.success) {
		            refresh_modal('affiliate_notes', "/affiliate_management/affiliate_notes/" + affiliate_id, 'Affiliate Notes');
		        }
		    });
		    return false;
		}

		function remove_player_note(note_id, affiliate_id) {
		    var confirm_val = confirm('Are you sure you want to delete this player note?');
		    if (confirm_val) {
		        var url = '/player_management/remove_player_note/' + note_id;
		        // console.log(url);
		        $.getJSON(url, function(data) {
		            if (data.success) {
		                refresh_modal('affiliate_notes', "/affiliate_management/affiliate_notes/" + affiliate_id, 'Affiliate Notes');
		            }
		        });
		    }
		    return false;
		}

		function refresh_modal(name, dst_url, title) {
		    var main_selector = '#' + name;
		    var body_selector = main_selector + ' .modal-body';
		    var target = $(body_selector);
		    target.load(dst_url);
		}
		
</script>
