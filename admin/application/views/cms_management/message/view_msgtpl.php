<style type="text/css">

    table td {word-wrap:break-word;}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt"><i class="icon-mail3"></i> <?=lang('cms.msg.template');?> </h4>
			</div>

			<div class="panel-body" id="logo_panel_body">

		        <!-- edit cms logo -->
		        <div class="row edit_cmsfootercontent_sec">
		            <div class="col-md-12">
		                <div class="well" style="overflow: auto;">
		                    <form action="<?=BASEURL . 'cms_management/editMsgTemplate'?>" method="post" role="form" id="form-editMsgTplfootercontent"  accept-charset="utf-8" enctype="multipart/form-data">
		                        <div class="row">
		                            <div class="col-md-12">
		                                <h6><label for="footercontentName"><?=lang('cms.footertitle');?>: </label></h6>
		                                <input type="hidden" id="editMsgTemplateId" name="editMsgTemplateId" class="form-control input-sm" required>
		                                <input type="text" id="msgTemplateName" maxlength="100" name="msgTemplateName" class="form-control input-sm" required>
		                            </div>
		                        </div>
		                        <br/>
		                        <div class="row">
		                            <div class="col-md-12">
		                                <h6><label for="footercontentDetails"><?=lang('cms.footercontent');?>: </label></h6>
		                                <div style="background-color:#fff;">
		                                    <input name="msgTemplateContent" class="footercontentContent" type="hidden" required/>
		                                    <div class="summernote" id="msgTemplateContent"></div>
		                                </div>
		                            </div>
		                        </div>
		                        <br/>
		                        <div class="col-md-12">
		                        	<label><?=lang('cms.notes');?>:</label> <?=lang('cms.reminder');?><br/>
		                        	<div class="col-md-12">

		                        		<span id="msgptl_"><?=lang('msgtpl.deposit_time') ;?></span>
                                        <span id="msgptl_"><?=lang('msgtpl.deposit_amount') ;?></span>
                                        <span id="msgptl_"><?=lang('msgtpl.bonusAmount') ;?></span>
                                        <span id="msgptl_"><?=lang('msgtpl.withdrawals_time') ;?></span>
                                        <span id="msgptl_"><?=lang('msgtpl.withdrawals_amount') ;?></span>
		                        	</div>
		                        </div><br/>
		                        <div>
			                        <center>
			                            <br/>
			                            <input type="submit" value="<?=lang('lang.save');?>" class="btn btn-sm btn-info review-btn custom-btn-size" data-toggle="modal" />
			                            <span class="btn btn-sm btn-default editfootercontentcms-cancel-btn custom-btn-size" data-toggle="modal"/><?=lang('lang.cancel');?></span>
			                        </center>
		                        </div>
		                    </form>
		                </div>
		            </div>

		        </div>

				<div id="logoList" class="table-responsive">
                    <input type="button" id="setDefault" class="btn btn-sm btn-info review-btn custom-btn-size" value=<?=lang('cms.msg.setDefault');?> ">
	                <form action="#" method="post" role="form">
	                    <div id="cmslogo_table" class="table-responsive">
							<table class="table table-striped table-hover" id="myTable" style="width:100%;">
								<thead>
									<tr>
										<!-- <th>Logo Name</th> -->
										<th ></th>
										<th ><?=lang('ID');?></th>
										<th ><?=lang('Name');?></th>
										<th><?=lang('cms.footercontent');?></th>
										<th><?=lang('lang.action');?></th>
									</tr>
								</thead>

								<tbody>
									<?php if (!empty($email)) {
	?>
										<?php $i = 0;?>

										<?php foreach ($email as $row) {?>
											<tr>
												<td ></td>
												<td ><?=$row['id']?></td></td>
												<td style="min-width:150px;" ><?=$row['note'] == '' ? '<i class="help-block"><?= lang("lang.norecord"); ?><i/>' : $row['name']?></td></td>
												<td class=" template "><?=$row['template'] == '' ? '<i class="help-block"><?= lang("lang.norecord"); ?><i/>' : ''?>
													<iframe width="100%" style="min-height: 500px;" id="iframe-<?= $i++?>" frameborder="0"></iframe>

												   <textarea style="display:none;"><?= $row['template']?></textarea>
												</td>
												<td >
													<div class="actionCmsLogoGroup">
	                                                    <span style="cursor:pointer;" class="glyphicon glyphicon-edit editLogoCmsBtn" data-toggle="tooltip" title="<?=lang('lang.edit');?>" onclick="CMSManagementProcess.getMsgCmsDetails(<?=$row['id']?>)" data-placement="top">
	                                                    </span>
		                                            </div>
												</td>
											</tr>
										<?php }
	?>
									<?php } else {?>
											<tr>
					                            <td colspan="5" style="text-align:center"><span class="help-block"><?=lang('lang.norec');?></span></td>
					                        </tr>
									<?php }
?>
								</tbody>
							</table>
						</div>
					</form>

                    <!-- Bootstrap modal popup -->
                    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header alert alert-danger">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="confirmModalLabel"><?=lang('cms.msg.setDefault');?></h4>
                                </div>
                                <div class="modal-body">
                                    <p class="success-message"><?=lang('cms.msg.resetDefault');?> </p>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-success" id="setbtn" >Ok</button>
                                    <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of the boostrap modal popup -->
				</div>
			</div>
			<div class="panel-footer"></div>
		</div>
	</div>
</div>



<script>

$(function() {
    $('#setDefault').click(function () {
        $('#confirmModal').modal('show');
    });


    $('#setbtn').click(function () {


        $.ajax({
                url: '/api/setDefaultMsgTpl',
                data: {reset: 1},
                type: 'POST',
                success: function (data) {

                    if (data.flg) {
                        console.log(data.flg);
                        //now re-using the boostrap modal popup to show success message.
                        //dynamically we will change background colour
                        window.location.reload();

                    }
                }, error: function (err) {
                    if (!$('.modal-header').hasClass('alert-danger')) {
                        $('.modal-header').removeClass('alert-success').addClass('alert-danger');
                        $('.delete-confirm').css('display', 'none');
                    }

                }
        });
    });

var tableRows = $("#myTable tr");
tableRows.each(function(idx, row) {

var temp = $(row).find('textarea').val();
var $iframe =  $(row).find('iframe');


setTimeout(function(){
   $iframe.contents().find("body").append(temp);

},1000);


});


});











</script>





<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<br/><br/><br/>
	<center>
		<img src="" id="logo_img"/>
	</center>
</div>


<script type="text/javascript">
    $(document).ready(function(){
        $('#myTable').DataTable({
            "responsive": {
                details: {
                    type: 'column'
                }
            },
              "dom":"<'panel-body' <'pull-right'f><'pull-right progress-container'>l>t<'panel-body'<'pull-right'p>i>",
            buttons: [
            {
              extend: 'colvis',
              postfixButtons: [ 'colvisRestore' ]
          }],
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ]
        });
    });
</script>
