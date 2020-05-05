<link href="<?=site_url().'resources/third_party/bower_components/bootstrap-toggle/css/bootstrap-toggle.min.css'?>" rel="stylesheet">
<link href="<?=site_url().'resources/third_party/bower_components/toastr/toastr.min.css'?>" rel="stylesheet">
<script type="text/javascript" src="<?=site_url().'resources/third_party/bower_components/bootstrap-toggle/js/bootstrap-toggle.min.js'?>"></script>
<script type="text/javascript" src="<?=site_url().'resources/third_party/bower_components/toastr/toastr.min.js'?>"></script>
<script type="text/javascript" src="<?=site_url().'resources/third_party/bower_components/jquery-mask-plugin/src/jquery.mask.js'?>"></script>
<style>
    @media all and (-webkit-min-device-pixel-ratio:0) and (min-resolution: .001dpcm) {
        select.condition
        {
            -webkit-appearance: none;
            appearance: none;
            padding : 2px 5px 2px 5px;
            box-shadow: none !important;
        }
    }
    .inline { display:inline; }
    .custom-input { width: 60px; }
/*    .well {
        border-radius: 5px;
        height : 55px;
        padding-left: 2px !important;
    }*/
    @media screen and (min-width: 992px) {
        .modal-lg {
            width: 1650px; /* New width for large modal */
        }
        @-moz-document url-prefix() {
            .modal-lg {
                width: 970px; /* Firefox New width for large modal */
            }
        }
    }
    .popover-title { border-radius: 5px 5px 0 0; text-align: center; }
    .popover {
        background-color: #fff;
        max-width: 100%;
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        font-size: 12px;
        line-height: 1;
        border: 1px solid #ccc;
        border-radius: 6px;
        -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
        box-shadow: 0 5px 10px rgba(0,0,0,.2);
        line-break: auto;
        z-index: 1010; /* A value higher than 1010 that solves the problem */
        position: fixed;
    }
    .popover-content {background-color: white; color:#545454;}
    .toast-top-center { margin-top : 80px; }
    #settingTbl_wrapper {
        overflow-y: hidden;
        overflow-x: hidden;
    }
    .td-short {
        width: 25%;
    }
    .td-long {
        width: 65%;
    }
</style>

<!-- Level Upgrade Setting -->
<div id="promoGameListModal" class="modal fade " role="dialog">

    <div class="modal-dialog modal-fs">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?= lang('Allowed Promo Game Setting Form'); ?></h4>
            </div>
            <div class="modal-body custom-height-modal">
                <div class="row">
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" id="searchTree" class="form-control input-sm" placeholder="<?=lang('Search Game List')?>">
                            </div>
                            <div class="col-md-10">
                                <div class="row" id="filter_col">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-sm btn-success" id="filterTree" style="display: none">
                            <i class="fa fa-caret-square-o-right" aria-hidden="true"></i><?= lang('Search Tree'); ?>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" id="previewResult">
                            <i class="fa fa-caret-square-o-right" aria-hidden="true"></i><?= lang('Preview Result'); ?>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" id="save_setting_btn">
                            <i class="fa"></i> <?= lang('Save Setting'); ?>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <fieldset style="padding:20px;margin-bottom: 5px;">
                            <legend>
                                <h5><strong> <?= lang('Tree List') ?> </strong></h5>
                            </legend>
                            <div id="gameTree" class="col-md-12"></div>
                        </fieldset>
                    </div>

                    <div class="col-xs-6">
                        <fieldset style="padding:20px">
                            <legend>
                                <h5><strong> <?= lang('Summarize Table') ?> </strong></h5>
                            </legend>

                            <div id="summarize-table"></div>
                        </fieldset>
                    </div>
                </div>

                <div class="row hide" id="listContainer">
                    <div class="col-xs-12">
                        <fieldset style="padding:20px">
                            <legend>
                                <h5><strong> Setting List </strong></h5>
                            </legend>

                            <table id="settingTbl" class="table table-hover" data-page-length='5'>
                                <thead>
                                <tr>
                                    <th></th>
                                    <th><?= lang('Setting Name'); ?></th>
                                    <th><?= lang('sys.description'); ?></th>
                                    <th><?= lang('Formula'); ?></th>
                                    <th><?= lang('lang.status'); ?></th>
                                    <th><?= lang('lang.action'); ?></th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </fieldset>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('lang.close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" style="margin-top:130px !important;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><?= lang('Delete Setting') ?></h4>
            </div>
            <input type="hidden" id="hiddenId">
            <div class="modal-body">
                <?= lang('sys.gd4'); ?>  <span style="color:#ff6666" id="name"></span>?
            </div>
            <div class="modal-footer">
                <a data-dismiss="modal" class="btn btn-default"><?= lang('lang.no'); ?></a>
                <a class="btn btn-primary" id="deleteBtn"><i class="fa"></i> <?= lang('lang.yes'); ?></a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        var baseUrl = '<?php echo base_url(); ?>';

        $('#save_setting_btn').on('click', function(){
            var selected_game=$('#gameTree').jstree('get_checked');
            // console.log("selected_game: ");
            // console.log(selected_game);
            if(selected_game.length>0){
                var selected_game_data = selected_game.join();

                var post_data = {
                    promorulesId: "<?=$promoRuleDetails['promorulesId'] ?>",
                    selected_game_tree : selected_game_data
                };
                // console.log("post_data: ");
                // console.log(post_data);

                if(!promorulesId) {
                    previewResult('#gameTree', '#summarize-table', expand_and_collapse=true, expand_and_collapse_count=5, input_number=false);
                    previewResult('#gameTree', '#allowed-promo-game-list-table', expand_and_collapse=true, expand_and_collapse_count=2, input_number=false);
                    $('#promoform input[name=selected_game_tree]').val(selected_game_data);

                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_SUCCESS,
                        title: 'Success',
                        message: 'Already successfully saved selected games.',
                        buttons: [{
                            label: '<?php echo lang('Close'); ?>',
                            action: function(dialogRef){
                                dialogRef.close();
                            }
                        }]
                    });
                    return;
                }

                $.post( baseUrl + 'marketing_management/setPromoAllowedGame', post_data, function(result){
                    var result_data = JSON.parse(result);
                    // console.log(result_data);
                    if(result_data.success == true) {
                        previewResult('#gameTree', '#summarize-table', expand_and_collapse=true, expand_and_collapse_count=5, input_number=false);
                        previewResult('#gameTree', '#allowed-promo-game-list-table', expand_and_collapse=true, expand_and_collapse_count=2, input_number=false);
                        BootstrapDialog.show({
                            type: BootstrapDialog.TYPE_SUCCESS,
                            title: 'Success',
                            message: result_data.message,
                            buttons: [{
                                label: '<?php echo lang('Close'); ?>',
                                action: function(dialogRef){
                                    dialogRef.close();
                                }
                            }]
                        });
                    }
                    else {
                        BootstrapDialog.show({
                            type: BootstrapDialog.TYPE_DANGER,
                            title: 'Failed',
                            message: result_data.message,
                            buttons: [{
                                label: '<?php echo lang('Close'); ?>',
                                action: function(dialogRef){
                                    dialogRef.close();
                                }
                            }]
                        });
                    }
                });
            }
            else {
                BootstrapDialog.alert("<?php echo lang('Please choose one game at least'); ?>");
                e.preventDefault();
            }
        });
    });
</script>