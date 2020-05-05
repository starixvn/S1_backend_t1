<style>
.break-word{
    word-wrap:break-word;
    word-break:break-all;
    white-space: pre-wrap;
}
.max-tag-name {
    max-width:170px;
}

.max-description {
     max-width:556px;
}

</style>
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt pull-left">
            <i class="icon-user2"></i> <?=lang('player.sd11');?>
        </h4>
        <a href="#" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs btn-info' : 'btn-sm btn-default' ?>" id="addManualSubtractBalanceTagMngmtBtn">
            <i class="fa fa-plus-circle"></i> <?=lang('tool.pm06');?>
        </a>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12">
                <div class="well" style="overflow: auto;" id="addManualSubtractBalanceTagMngmt">
                    <form class="form-horizontal" action="<?=BASEURL . 'player_management/postManualSubtractBalanceTag'?>" method="post" role="form">
                        <div class="form-group">
                            <div class="col-md-4">
                                <label for="adjust_tag_name" class="control-label"><?=lang('player.tm02');?>: </label>
                                <input type="hidden" name="id" class="form-control" id="id">
                                <input type="text" name="adjust_tag_name" class="form-control input-sm" id="adjust_tag_name" placeholder="<?=lang('player.tm03');?>" value="<?php set_value('adjust_tag_name')?>">
                                <?php echo form_error('adjust_tag_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-6">
                                <label for="adjust_tag_description" class="control-label"><?=lang('player.tm04');?>: </label>&nbsp;
                                <textarea name="adjust_tag_description" id="adjust_tag_description" class="form-control input-sm" cols="15" rows="1" style="resize: none; height: 36px; max-height: 80px;" onkeyup="autogrow(this);" placeholder="<?=lang('player.tm05');?>"></textarea>
                                <?php echo form_error('adjust_tag_description', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-2 col-lg-2" style="padding-top:24px;text-align:center;">
                                <input type="submit" value="<?=lang('lang.save');?>" class="btn review-btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-info' ?>"/>
                                <input type="reset" value="<?=lang('lang.reset');?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default' ?>"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form action="<?=BASEURL . 'player_management/deleteSelectedManualSubtractBalanceTag'?>" method="post" role="form">
                    <div id="tag_table" class="table-responsive">
                        <table class="table table-striped table-hover" id="my_table" style="margin: 0px 0 0 0; width: 100%;">
                            <button type="submit" class="btn btn-danger btn-sm btn-action" data-toggle="tooltip" data-placement="top" title="<?=lang('cms.deletesel');?>">
                                <i class="glyphicon glyphicon-trash" style="color:white;"></i>
                            </button>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th style="padding: 8px"><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                                    <th><?=lang('player.tm02');?></th>
                                    <th><?=lang('player.tm04');?></th>
                                    <th><?=lang('cms.createdby');?></th>
                                    <th><?=lang('lang.action');?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($tags)) {
    foreach ($tags as $row) {
        ?>
                                    <tr>
                                        <td></td>
                                        <td style="padding: 8px"><input type="checkbox" class="checkWhite" id="<?=$row['id']?>" name="tag[]" value="<?=$row['id']?>" onclick="uncheckAll(this.id)"/></td>
                                        <td class="break-word max-tag-name"><?=$row['adjust_tag_name']?></td>
                                        <td class="break-word max-description"><?=$row['adjust_tag_description'] ? $row['adjust_tag_description'] : lang('player.tm06')?></td>
                                        <td><?=$row['username']?></td>
                                        <td>
                                            <center>
                                                <a href="#editTag" id="editManualSubtractBalanceTagMngmt">
                                                    <span class="glyphicon glyphicon-edit" data-toggle="tooltip" title="<?=lang('player.tm07');?>"  data-placement="top" onclick="PlayerManagementProcess.getManualSubtractBalanceTagDetails(<?=$row['id']?>)">
                                                    </span>
                                                </a>
                                                <a href="<?=BASEURL . 'player_management/deleteManualSubtractBalanceTag/' . $row['id']?>">
                                                    <span class="glyphicon glyphicon-trash" data-toggle="tooltip" title="<?=lang('player.tm08');?>"  data-placement="top">
                                                    </span>
                                                </a>
                                            </center>
                                        </td>
                                    </tr>
                                <?php }
} else {
    ?>
                                <?php }
?>
                            </tbody>
                        </table>
                        <!-- <div class="col-md-12 col-offset-0">
                            <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
                        </div> -->
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#my_table').DataTable({
            dom: "<'panel-body' <'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'text-center'r><'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
           "responsive": {
                details: {
                    type: 'column'
                }
            },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            }, {
                orderable: false,
                targets:   1
            } ],
            "order": [ 2, 'asc' ],
            //"dom": '<"top"fl>rt<"bottom"ip>',
            "fnDrawCallback": function(oSettings) {
                $('.btn-action').prependTo($('.top'));
            }
        });
    });


    function autogrow(textarea){
        var adjustedHeight = textarea.clientHeight;

        adjustedHeight = Math.max(textarea.scrollHeight,adjustedHeight);
        if (adjustedHeight>textarea.clientHeight){
            textarea.style.height = adjustedHeight + 'px';
        }
    }
</script>