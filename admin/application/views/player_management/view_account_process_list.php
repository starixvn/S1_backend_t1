<style type="text/css">
	.control-label {
		text-align: right !important;
	}
	textarea.form-control {
		height: auto !important;
		resize: none !important;
	}
</style>
<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt pull-left">
					<i class="icon-user-plus"></i> <?=lang('player.sd05');?>
				</h4>
				<a href="#" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs btn-info' : 'btn-sm btn-default'?>" onclick="addAccountProcess();">
					<i class="fa fa-plus-circle"></i> <?=lang('Add Batch Account');?>
				</a>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body" id="player_panel_body">

				<div id="accountProcessList" class="">
					<table class="table table-striped table-hover" id="myTable" >
						<thead>
							<tr>
								<th><?=lang('Player Username')?></th>
								<th><?=lang('player.mp03');?></th>
								<th><?=lang('player.mp04');?></th>
								<th><?=lang('lang.action');?></th>
							</tr>
						</thead>

						<tbody>
							<?php if (!empty($batch)) {
	?>
								<?php foreach ($batch as $batch) {
		?>
									<tr>
										<td><?=$batch['name']?></td>
										<td><?=$batch['count']?></td>
										<td><?=($batch['description'] == null) ? '<i>' . lang('player.mp05') . '</i>' : $batch['description']?></td>
										<td>
											<a href="#" data-toggle="tooltip" title="<?=lang('tool.cms05');?>" class="details" onclick="viewAccountProcess(<?=$batch['batchId']?>);"><span class="glyphicon glyphicon-zoom-in"></span></a>
											<a href="#" data-toggle="tooltip" class="edit" onclick="editAccountProcess(<?=$batch['batchId']?>);"><span class="glyphicon glyphicon-pencil"></span></a>
											<a href="<?=BASEURL . 'player_management/deleteAccountProcess/' . $batch['batchId']?>" data-toggle="tooltip" class="delete" onclick="return confirm('<?=lang('sys.gd4')?>?')"><span class="glyphicon glyphicon-trash"></span></a>
										</td>
									</tr>
								<?php }
	?>
							<?php }
?>
						</tbody>
					</table>
				</div> <!-- EOF #accountProcessList -->
			</div>

			<div class="panel-footer"></div>
		</div>
	</div>

	<div class="col-md-6 col-lg-6" id="account_process_details" style="display:none;">

	</div>
</div>
<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
    <input name='json_search' type="hidden">
    </form>
<?php }?>
<script type="text/javascript">
    $(document).ready(function(){
        $('#myTable').DataTable({
        	dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'text-center'r><'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        	buttons: [
				{
					extend: 'colvis',
					className:'<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : '' ?>',
					postfixButtons: [ 'colvisRestore' ]
				},
                <?php if( $this->permissions->checkPermissions('export_affiliate_payment') ){ ?>
				{
					text: "<?php echo lang('CSV Export'); ?>",
                    className:'<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn btn-sm btn-portage' : 'btn btn-sm btn-primary' ?>',
					action: function ( e, dt, node, config ) {
						var d = {};
						$.post(site_url('/export_data/batchCreate'), d, function(data){
							if(data && data.success){
								$('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
							}else{
								alert('export failed');
							}
						});
					}
				}
                <?php } ?>
			],
			responsive:false,
            "order": [ 1, 'asc' ],
            drawCallback: function () {
                if ( $('#myTable').DataTable().rows( { selected: true } ).indexes().length === 0 ) {
                    $('#myTable').DataTable().buttons().disable();
                }
                else {
                    $('#myTable').DataTable().buttons().enable();
                }
            }
        });
    });

    function verifyAddAccountProcess() {
    	$('#verifyAddAccountProcess')[0].checkValidity();
    	return false;
	}

	function editAccountProcess(id) {
	    var xmlhttp = GetXmlHttpObject();

	    if (xmlhttp == null) {
	        alert("Browser does not support HTTP Request");
	        return;
	    }

	    url = base_url + "player_management/editAccountProcess/" + id;

	    $('#toggleView').removeClass('col-md-12');
	    $('#toggleView').addClass('col-md-6 col-lg-6');

	    var div = document.getElementById("account_process_details");

	    $('#account_process_details').show();

	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4) {
	            div.innerHTML = xmlhttp.responseText;
	        }
	        if (xmlhttp.readyState != 4) {
	            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
	        }
	    }

	    xmlhttp.open("GET", url, true);
	    xmlhttp.send(null);
	}
</script>
<script type="text/javascript">
// Gitlab issue #1022, 5/04/2017
// Submit counter for Bath Create form
var vap_count = 0;
</script>