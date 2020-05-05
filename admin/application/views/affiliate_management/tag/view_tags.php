<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt pull-left">
            <i class="glyphicon glyphicon-tag"></i> <?=lang('aff.t01');?>
         <!--    <button id="addAffTagMngmtGlyph"  class="btn btn-default btn-xs pull-right"><i class="glyphicon glyphicon-plus-sign"></i></button> -->
        </h4>
        <a href="#" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs btn-info' : 'btn-sm btn-default'?>" id="addPlayerTagMngmtBtn">
            <span id="addAffTagMngmtGlyph"><i class="fa fa-plus-circle"></i> <?=lang('aff.t08');?> </span>
        </a>
        <div class="clearfix"></div>
    </div>

    <div class="panel-body" id="details_panel_body">
        <div class="well" style="overflow:auto;" id="addAffTagMngmt">
            <form class="form-horizontal" action="<?=BASEURL . 'affiliate_management/actionTag'?>" method="POST" role="form">
                <span id="affTagLabel"></span>
                <div class="form-groupglyphicon ">
                    <div class="col-md-4 col-lg-4">
                        <label for="tagName" class="control-label"><?=lang('aff.t02');?>: </label>
                        <input type="hidden" name="tagId" class="form-control" id="tagId">
                        <input type="text" name="tagName" class="form-control input-sm" id="tagName" placeholder="<?=lang('aff.t03');?>" value="<?php set_value('tagName')?>">
                        <?php echo form_error('tagName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    </div>
                    <div class="col-md-5 col-lg-6">
                        <label for="tagDescription" class="control-label"><?=lang('aff.t04');?>: </label>
                        <textarea name="tagDescription" id="tagDescription" class="form-control input-sm" cols="10" rows="1" style="resize: none; height: 36px; max-height: 80px;" onkeyup="autogrow(this);"  placeholder="<?=lang('aff.t05');?>"></textarea>
                        <?php echo form_error('tagDescription', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    </div>
                    <div class="col-md-3 col-lg-2" style="padding-top:23px;text-align:center">
                        <input type="submit" value="<?=lang('lang.save');?>" class="btn review-btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-info'?>"/>
                        <input type="reset" value="<?=lang('lang.reset');?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>"/>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-md-12">

                <form action="<?=BASEURL . 'affiliate_management/deleteSelectedTag'?>" id="delete_form" method="post" role="form">
                    <div id="tag_table" class="table-responsive">
                        <table class="table table-bordered table-hover dataTable" id="tagTable" style="width: 100%;">
                            <button type="submit" class="btn btn-danger btn-sm btn-action" data-toggle="tooltip" data-placement="top" title="<?=lang('cms.deletesel');?>">
                                <i class="glyphicon glyphicon-trash" style="color:white;"></i>
                            </button>
                            <dir class="clearfix"></dir>
                            <thead>
                                <tr>
                                    <th style="padding:8px; width:12px;"><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                                    <th style="width:120px;"><?=lang('aff.t02');?></th>
                                    <th style="width:800px;"><?=lang('aff.t04');?></th>
                                    <th style="width:90px;"><?=lang('aff.t06');?></th>
                                    <th style="width:1px;"><?=lang('lang.action');?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
if (!empty($tags)) {
	foreach ($tags as $row) {
		?>
                                                <tr>
                                                    <td style="padding:8px"><input type="checkbox" class="checkWhite" id="<?=$row['tagId']?>" name="tag[]" value="<?=$row['tagId']?>" onclick="uncheckAll(this.id)"/></td>
                                                    <td><?=$row['tagName']?></td>
                                                    <td><?=$row['tagDescription'] ? $row['tagDescription'] : lang('aff.t07')?></td>
                                                    <td><?=$row['username']?></td>
                                                    <td style="text-align:center;">
                                                        <a href="#editTag" class="editTagMngmt">
                                                            <span class="glyphicon glyphicon-edit" data-toggle="tooltip" title="<?=lang('tool.am12');?>"  data-placement="top" onclick="AffiliateManagementProcess.getTagDetails(<?=$row['tagId']?>)">
                                                            </span>
                                                        </a>
                                                        <a href="<?=BASEURL . 'affiliate_management/deleteTag/' . $row['tagId']?>">
                                                            <span class="glyphicon glyphicon-trash" data-toggle="tooltip" title="<?=lang('tool.am13');?>"  data-placement="top">
                                                            </span>
                                                        </a>
                                                    </td>
                                                </tr>
                                <?php }
} else {
	?>

                                    <!-- <tr>
                                        <td colspan="5" style="text-align:center"><span class="help-block"><?=lang('lang.norec');?></span></td>
                                    </tr> -->
                                <?php }
?>
                            </tbody>
                        </table>

                        <!-- <br/>

                        <div class="col-md-12 col-offset-0">
                            <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
                        </div> -->
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
    <input name='json_search' type="hidden">
    </form>
<?php }?>

<script type="text/javascript">
    $(document).ready(function(){
        $('#tagTable').DataTable( {
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: "<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>"
                },

                <?php

                    if( $this->permissions->checkPermissions('export_affiliate_tag') ){

                ?>
                        {

                            text: "<?php echo lang('CSV Export'); ?>",
                            className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                            action: function ( e, dt, node, config ) {
                                var d = {};
                                // utils.safelog(d);


                                $.post(site_url('/export_data/affiliateTag'), d, function(data){
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
            "columnDefs": [ {
                orderable: false,
                targets:   0
            }],
            "order": [ 1, 'asc' ],
           // "dom": '<"top"fl>rt<"bottom"ip>',
            "fnDrawCallback": function(oSettings) {
                $('.btn-action').prependTo($('.top'));
                if ( $('#tagTable').DataTable().rows( { selected: true } ).indexes().length === 0 ) {
                    $('#tagTable').DataTable().buttons().disable();
                }
                else {
                    $('#tagTable').DataTable().buttons().enable();
                }
            }
        } );

        //for add new tag
        var is_affTagMngmtVisible = false;

        //for edit tag
        var is_editAffTagMngmtVisible = false;

        if(!is_affTagMngmtVisible){
            $('#addAffTagMngmt').hide();
        }else{
            $('#addAffTagMngmt').show();
        }

        $("#addAffTagMngmtGlyph").click(function () {

            if(!is_affTagMngmtVisible){
                is_affTagMngmtVisible = true;
                $('#addAffTagMngmt').show();
                $('#is_playerTagMngmtVisible').hide();
                $('#affTagLabel').text("<?php echo lang('aff.t08') ?>");

            }else{
                is_affTagMngmtVisible = false;
                $('#addAffTagMngmt').hide();
            }
        });

         $(".editTagMngmt").click(function () {
            is_editAffTagMngmtVisible = true;
            $('#addAffTagMngmt').show();
            $('#affTagLabel').text("<?php echo lang('tool.am12') ?>");
        });


    });//document ready end

     $("#delete_form").submit(function(){
        var checked = $(".checkWhite:checked").length > 0;
        var deleteCheckboxWarningMsg = "<?php echo lang('Please check at least one item'); ?>";
        if (!checked){
            alert(deleteCheckboxWarningMsg);
            return false;
        }else{
            return confirmDelete();
        }
    });

    function autogrow(textarea){
        var adjustedHeight = textarea.clientHeight;

        adjustedHeight = Math.max(textarea.scrollHeight,adjustedHeight);
        if (adjustedHeight>textarea.clientHeight){
            textarea.style.height = adjustedHeight + 'px';
        }
    }
</script>
