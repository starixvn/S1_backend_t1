<!-- search form {{{1 -->
<form class="form-horizontal" id="search-form" method="get" role="form">

    <div class="panel panel-primary hidden">

        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="fa fa-search"></i> <?php echo lang("Player Analysis Report"); ?>
                <span class="pull-right">
                    <a data-toggle="collapse" href="#collapseDuplicateAccountReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
                </span>
                <a href="javascript:void(0);" class="bookmark-this btn btn-xs pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?>" style="margin-right: 4px;"><i class="fa fa-bookmark"></i> <?php echo lang('Add to bookmark'); ?></a>
            </h4>
        </div>


        <div id="collapseDuplicateAccountReport" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4 col-lg-4">
                        <label class="control-label"><?php echo lang('Player Username'); ?></label>
                        <select class="js-data-example-ajax" id="addPlayers" name="username[]" multiple="multiple" style="width: 100%;"></select>
                    </div>
                    <div class="col-md-8 col-lg-8">
                        <label class="control-label"><?php echo lang('Overall Win / Loss'); ?></label>
                        <?php if(!empty($get_game_system_map)) : ?><br>
                            <?php foreach($get_game_system_map as $key => $value) : ?>
                                <input type="checkbox" name="game_list_search[]" id="game_<?=$key?>" value="<?=$key?>" checked/> <label for="game_<?=$key?>"><?=$value?></label> <br />
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-lg-12" style="padding: 10px;">
                        <div class=" pull-right">
                            <input type="reset" value="<?php echo lang('Reset'); ?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" onclick="resetFields();">
                            <input class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" type="submit" name="submit" value="<?php echo lang('Search'); ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- search form }}}1 -->

<!-- table info {{{1 -->
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt"><i class="icon-bullhorn"></i> <?php echo lang('Report Result'); ?></h4>
    </div>
    <div class="panel-body">
        <!-- result table -->
        <div id="logList" >
            <table class="table table-striped table-hover table-condensed" id="report_table" style="width:100%; overflow-x: scroll; display: block;">
                <thead>
                    <tr>
                        <th><?= lang('player.01') ?></th>
                        <th><?= lang('Real Name') ?></th>
                        <th><?= lang('date.of.birth') ?></th>
                        <th><?= lang('player.06') ?></th>
                        <th><?= lang('Phone / mobile number') ?></th>
                        <th><?= lang('player.07') ?></th>
                        <th><?= lang('player.41') ?></th>
                        <?php if($this->utils->isEnabledFeature('ignore_player_analysis_permissions') || $this->permissions->checkPermissions('reset_player_login_password')) {
                            echo '<th>' . lang('Password') . '</th>';
                        } if($this->utils->isEnabledFeature('ignore_player_analysis_permissions') || $this->permissions->checkPermissions('player_verification_question')) {
                            echo '<th>'.lang('Verification Question').'</th>';
                        } if($this->utils->isEnabledFeature('ignore_player_analysis_permissions') || $this->permissions->checkPermissions('player_verification_questions_answer')) {
                            echo '<th>'.lang('Verification Answer').'</th>';
                        } ?>
                        <th><?= lang('Linked Accounts') ?></th>
                        <th><?= lang('lang.status') ?></th>
                        <?php if($this->utils->isEnabledFeature('show_risk_score')){ ?>
                            <th><?=lang('risk score') ?></th>
                        <?php }?>
                        <?php if($this->utils->isEnabledFeature('show_kyc_status')){ ?>
                            <th><?=lang('KYC Level') ?></th>
                        <?php }?>
                        <th><?= lang('player.10') ?></th>
                        <th><?= lang('report.in03') ?></th>
                        <th><?= lang('report.in04') ?></th>
                        <th><?= lang('player.ui31') ?></th>
                        <th><?= lang('cashier.05') ?></th>
                        <?php if(!empty($get_game_system_map)) : ?>
                            <?php foreach($get_game_system_map as $key => $value) : ?>
                                <th><?=lang("Overall Winloss")?> (<?= $value ?>)</th>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!--end of result table -->
    <div class="panel-footer"></div>
</div>

<div class="modal fade in" id="mainModal" tabindex="-1" role="dialog" aria-labelledby="mainModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="float: left">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mainModalLabel"></h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>
<!-- table info }}}1 -->

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
        <input name='json_search' type="hidden">
    </form>
<?php }?>

<script type="text/javascript">
$(document).ready(function(){

    $(".js-data-example-ajax").select2({
        placeholder: '<?=lang('Please Select Players')?>',
        ajax: {
            url: '/payment_account_management/players',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data, params) {
                    return {
                        results: data.items
                    };
            },
            cache: true
        },
        templateResult: function (option) {
            return option.text;
        },
        templateSelection: function (option) {
            return option.text;
        },
        minimumInputLength: 3
    });

    $('.bookmark-this').click(_pubutils.addBookmark);

    $('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
        if (e.which == 13) {
            $('#search-form').trigger('submit');
        }
    });

    var dataTable = $('#report_table').DataTable({
        <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
        <?php } else { ?>
            stateSave: false,
        <?php } ?>
        autoWidth: false,
        searching: true,
        processing: true,
        aserverSide: true,
        pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
        order: [ 1, 'desc' ],
        responsive: {
            details: {
                type: 'column'
            }
        },
        dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        ajax: function (data, callback, settings) {
            data.extra_search = $('#search-form').serializeArray();

            $.post(base_url + "api/player_analysis_report", data, function(data) {
                callback(data);
                if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                        dataTable.buttons().disable();
                    }
                    else {
                        dataTable.buttons().enable();
                    }
                $('#search-form').find(':submit').prop('disabled', false);
            },'json');
        },
        buttons: [
            {
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ],
                className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
            }
            <?php if ($export_report_permission) {?>
            ,{
                text: "<?php echo lang('CSV Export'); ?>",
                className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                action: function ( e, dt, node, config ) {
                    var form_params=$('#search-form').serializeArray();

                    var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
                        'draw':1, 'length':-1, 'start':0};

                    $("#_export_excel_queue_form").attr('action', site_url('/export_data/player_analysis_report'));
                    $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                    $("#_export_excel_queue_form").submit();
                }
            }
            <?php }?>
        ]
    });

    $('#search-form').submit( function(e) {
        $(this).find(':submit').prop('disabled', true);
        e.preventDefault();
        dataTable.ajax.reload();
    });
});

    function modal(load, title) {
        var target = $('#mainModal .modal-body');
        $('#mainModalLabel').html(title);
        target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(load);
        $('#mainModal').modal('show');

    }


    function resetFields(){
        $("#addPlayers").empty();
    }
</script>

