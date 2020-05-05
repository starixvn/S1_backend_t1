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
    .well {
        border-radius: 5px;
        height : 55px;
        padding-left: 2px !important;
    }
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
<div id="cashbackGameListModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-fs">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?= lang('Cashback Setting Form'); ?></h4>
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
                        <button type="button" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-burntsienna' : 'btn-success' ?>" id="filterTree" style="display: none">
                            <i class="fa fa-caret-square-o-right" aria-hidden="true"></i><?= lang('Search Tree'); ?>
                        </button>
                        <button type="button" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary' ?>" id="previewResult">
                            <i class="fa fa-caret-square-o-right" aria-hidden="true"></i><?= lang('Preview Result'); ?>
                        </button>
                        <button type="button" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary' ?>" id="save_setting_btn">
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
                            <form class="form-horizontal" id="setting_form" action="<?=site_url('vipsetting_management/setVipGameCashbackPercentage/')?>" method="post" role="form">
                                <input type="hidden" name="selected_game_tree" value="">
                                <div class="row" style="margin-bottom:15px;">

                                </div>
                                <div id="gameTree" class="col-md-12"></div>


                            </form>
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
                <button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default' ?>" data-dismiss="modal"><?= lang('lang.close'); ?></button>
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
    function isChrome(){
        var result=false;
        var ua=window.navigator.userAgent;
        if(ua!=''){
            var arr=ua.split(' ');
            for (var i = 0; i < arr.length; i++) {
                if(arr[i].indexOf('Chrome') !== -1){
                    //found Chrome
                    var tmpArr=arr[i].split('/');
                    if(tmpArr.length>=2){
                        var verArr=tmpArr[1].split('.');
                        if(verArr.length>1){
                            result=parseInt(verArr[0], 10)>=69;
                        }
                    }
                }
            }
        }

        return result;
    }

    $(document).ready(function(){
        var baseUrl = '<?php echo base_url(); ?>';

        $('#save_setting_btn').on('click', function(){
            if(!isChrome()){
                alert("<?=lang('Sorry, cannot use other browser to save settings.')?> <?=lang('Please ONLY use Chrome Browser, version should be more than 69, otherwise settings will be lost.')?>");
                return false;
            }

            var selected_game=$('#gameTree').jstree('get_checked');
            // console.log(selected_game);

            if(selected_game.length>0){
                var selected_game_data = selected_game.join();
                var input_select_number = $('#gameTree').jstree('generate_number_fields');

                var post_data = {
                    vipsettingcashbackruleId: <?=$data['vipsettingcashbackruleId'] ?>,
                    selected_game_tree_count: selected_game.length,
                    selected_game_tree : selected_game_data
                };
                $.each(input_select_number, function( selected_game_id, cashback_value ) {
                    post_data[selected_game_id] = cashback_value;
                });
                // console.log(post_data);

                $.post( baseUrl + 'vipsetting_management/setVipGameCashbackPercentage', post_data, function(result_data){
                    // var result_data = JSON.parse(result);
                    // console.log(result_data);
                    if(result_data.success == true) {
                        previewResult('#gameTree', '#summarize-table', expand_and_collapse=true,  expand_and_collapse_count=5,input_number=true, default_num_value="<?=$data['cashback_percentage']?>");
                        previewResult('#gameTree', '#allowed-cashback-game-list-table', expand_and_collapse=true, expand_and_collapse_count=2, input_number=true, default_num_value="<?=$data['cashback_percentage']?>");
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
                        alert(result_data.message);
                    }
                    $('#gameTree').jstree('generate_number_fields');
                });
            }
            else {
                BootstrapDialog.alert("<?php echo lang('Please choose one game at least'); ?>");
                e.preventDefault();
            }

            <?php //endif; ?>
        });
    });
</script>