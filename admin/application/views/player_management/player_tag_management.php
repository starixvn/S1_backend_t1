<?=$this->load->view("resources/third_party/bootstrap-colorpicker")?>
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt pull-left">
            <i class="icon-user2"></i> <?=lang('player.tm01');?>
        </h4>
        <a href="#" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs btn-info' : 'btn-sm btn-default' ?>" id="addPlayerTagMngmtBtn">
            <i class="fa fa-plus-circle"></i> <?=lang('tool.pm06');?>
        </a>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12">
                <div class="well" style="overflow: auto;" id="addPlayerTagMngmt">
                    <form class="form-horizontal" action="/player_management/actionPlayerTagOtherOptions" method="post" role="form">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="playerTagName" class="control-label"><?=lang('player.tm02');?>: </label>
                                <input type="hidden" name="tagId" class="form-control" id="tagId">
                                <input type="text" name="playerTagName" class="form-control input-sm" id="playerTagName" placeholder="<?=lang('player.tm03');?>" value="<?php set_value('playerTagName')?>" required>
                                <?php echo form_error('playerTagName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>

                            <div class="col-md-3">
                                <label for="tagColor" class="control-label"><?=lang('player.tm09');?>: </label>
                                <div class="input-group colorpicker-component" sbe-ui-toogle="colorpicker" data-format="hex">
                                    <input type="text" name="tagColor" class="form-control input-sm" id="tagColor" placeholder="<?=lang('player.tm09');?>" value="<?php set_value('tagColor', '#000000')?>" required>
                                    <span class="input-group-addon"><i></i></span>
                                </div>
                                <?php echo form_error('tagColor', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>

                            <div class="col-md-4">
                                <label for="tagDescription" class="control-label"><?=lang('player.tm04');?>: </label>&nbsp;
                                <textarea name="tagDescription" id="tagDescription" class="form-control input-sm" cols="15" rows="1" style="max-height: 80px; resize: none;" placeholder="<?=lang('player.tm05');?>" required></textarea>
                                <?php echo form_error('tagDescription', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-2"></div>

                            <div class="col-md-10">
                                <div>
                                    <input type="checkbox" name="chkPlayerBlockTag" id="chkPlayerBlockTag" value="1"> &nbsp;
                                    <label for="chkPlayerBlockTag" class="control-label"><?=lang('system.word97');?></label>
                                </div>
                            </div>
                            <div class="col-md-2" style="text-align: right;">
                                <input type="submit" value="<?=lang('lang.save');?>" class="btn review-btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-info' ?>"/>
                                <input type="reset" value="<?=lang('lang.reset');?>" class="btn btn-default btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default' ?>"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form action="/player_management/deleteSelectedTag" method="post" role="form">
                    <div id="tag_table" class="table-responsive">
                        <table class="table table-hover" id="taglist_table">
                            <button type="submit" class="btn btn-danger btn-sm btn-action" data-toggle="tooltip" data-placement="top" title="<?=lang('cms.deletesel');?>" style="margin-top: 15px">
                                <i class="glyphicon glyphicon-trash" style="color:white;"></i>
                            </button>
                            <thead>
                                <tr>
                                    <th style="padding: 8px"><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                                    <th style="max-width: 120px"><?=lang('player.tm02');?></th>
                                    <th style="max-width: 300px"><?=lang('player.tm04');?></th>
                                    <th><?=lang('player.tm09');?></th>
                                    <th><?=lang('cms.createdby');?></th>
                                    <th><?=lang('lang.action');?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (is_array($tags)) {
                                	foreach ($tags as $row) { ?>
                                    <tr>
                                        <td style="padding: 8px"><input type="checkbox" class="checkWhite" id="<?=$row['tagId']?>" name="tag[]" value="<?=$row['tagId']?>" onclick="uncheckAll(this.id)"/></td>
                                        <td style="max-width: 120px"><?=$row['tagName']?></td>
                                        <td style="max-width: 300px"><?=$row['tagDescription'] ? $row['tagDescription'] : lang('player.tm06')?></td>
                                        <td><?=$row['tagColor']?></td>
                                        <td><?=$row['username']?></td>
                                        <td>
                                            <center>
                                                <a href="#editTag" id="editTagMngmt">
                                                    <span class="glyphicon glyphicon-edit" data-toggle="tooltip" title="<?=lang('player.tm07');?>"  data-placement="top" onclick="PlayerManagementProcess.getTagDetails(<?=$row['tagId']?>)">
                                                    </span>
                                                </a>
                                                <a href="/player_management/deleteTag/<?=$row['tagId']?>">
                                                    <span class="glyphicon glyphicon-trash" data-toggle="tooltip" title="<?=lang('player.tm08');?>"  data-placement="top">
                                                    </span>
                                                </a>
                                            </center>
                                        </td>
                                    </tr>
                                    <?php }
                                }?>
                            </tbody>
                        </table>
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
        $("#addEvidence").select2({width: '100%'});

        $('#taglist_table').DataTable({
            lengthMenu: JSON.parse('<?=json_encode($this->utils->getConfig('default_datatable_lengthMenu'))?>'),
            autoWidth: false,

            <?php if ($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>

            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'text-center'r><'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
                    className:'<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : '' ?>',
                    postfixButtons: [ 'colvisRestore' ]
                }
                <?php if( $this->permissions->checkPermissions('export_tag_list') ){ ?>
                ,{
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn btn-sm btn-portage' : 'btn btn-sm btn-primary' ?>',
                    action: function ( e, dt, node, config ) {
                        var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};

                        $.post(site_url('/export_data/playerTagManagement'), d, function(data){

                            //create iframe and set link
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
            columnDefs: [
                { sortable: false, targets: [ 0 ] },
                { render: function(data, type, row) {
                    return '<span style="background-color: ' + data + '">' + data + '</span>';
                },targets: [ 3 ] }
            ],
            order: [ 1, 'asc' ],
            "fnDrawCallback": function(oSettings) {
                $('.btn-action').prependTo($('.top'));
                if ($('#taglist_table').DataTable().rows( { selected: true } ).indexes().length === 0 ) {
                    $('#taglist_table').DataTable().buttons().disable();
                }
                else {
                    $('#taglist_table').DataTable().buttons().enable();
                }
            }
        });
    });
</script>