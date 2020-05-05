<?php include APPPATH . "/views/includes/popup_promorules_info.php";?>

<form class="form-horizontal" id="search-form" method="get" role="form">

<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapsePromotionReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
            <?php include __DIR__ . "/../includes/report_tools.php"?>
        </h4>
    </div>


    <div id="collapsePromotionReport" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4 col-lg-4">

                    <label class="control-label" for="f-date"><?=lang('report.sum02')?></label>
                    <div class="input-group">
                        <input id="f-date" class="form-control dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
                        <span class="input-group-addon input-sm">
                            <input type="checkbox" data-off-text="<?= lang('off'); ?>" data-on-text="<?= lang('on'); ?>"  name="enable_date" id="enable_date" data-size='mini' value='true' <?= $this->input->get() ? $args['enable_date'] ? 'checked="checked"':'' : 'checked="checked"'; ?>>
                        </span>
                    </div>
                    <input type="hidden" id="date_from" name="date_from" value="<?= $args['date_from'] ?>" />
                    <input type="hidden" id="date_to" name="date_to" value="<?= $args['date_to'] ?>" />
                </div>

                <div class="col-md-4 col-lg-4">
                    <label class="control-label">
                        <input type="radio" name="player_match" value="partial"
                            <?= $args['player_match'] == 'partial' ? 'checked' : '' ?> />
                        <?= lang('Similar') ?>
                    </label>
                    <label class="control-label">
                        <input type="radio" name="player_match" value="exact"
                            <?= $args['player_match'] == 'exact' ? 'checked' : '' ?> />
                        <?= lang('Exact') ?>
                    </label>
                    <label>
                        <?=lang('Player Username');?>
                    </label>

                    <input type="text" name="player_username" id="player_username" class="form-control input-sm" placeholder=' <?=lang('report.p03');?>'
                    value="<?= $args['player_username']; ?>"/>
                </div>
                <div class="col-md-4 col-lg-4">
                    <label class="control-label"><?=lang('Player Level');?></label>
                    <select name="player_level_id" id="player_level_id" class="form-control input-sm">
                        <option value="" <?=empty($args['player_level_id']) ? 'selected' : ''?>>
                            --  <?=lang('lang.selectall');?> --
                        </option>
                        <?php foreach ($allPlayerLevels as $levelId => $level_title) : ?>
                            <option value="<?= $levelId ?>" <?= $args['player_level_id'] == $levelId ? 'selected' : '' ?> >
                                <?= $level_title ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-lg-4">
                    <label class="control-label" for="game_type"><?= lang('Game Type') ?></label>
                    <select name="game_type" id="game_type" class="form-control input-sm">
                        <option value="">-- <?=lang('N/A');?> --</option>
                        <?php foreach ($gametypes as $item) : ?>
                            <option value="<?= $item['id'] ?>"
                                <?= $args['game_type'] == $item['id'] ? 'selected' : '' ?> >
                                <?= $item['gametype_text']?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 col-lg-4">
                    <label class="control-label"><?=lang('Promo Type');?></label>
                    <select name="promo_type" id="promo_type" class="form-control input-sm">
                        <option value="">-- <?=lang('N/A');?> --</option>
                        <?php foreach ($allPromoTypes as $key => $value) : ?>
                            <option value="<?=$value['id']?>" <?=$args['promo_type'] == $value['id'] ? 'selected' : ''?>><?=lang($value['label'])?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 col-lg-4">
                    <label class="control-label"><?=lang('Promo Rule');?></label>
                    <select name="promo_rule" id="promo_rule" class="form-control input-sm">
                        <option value="">-- <?=lang('N/A');?> --</option>
                        <?php if(!empty($promorules)): foreach ($promorules as $promo) : ?>
                            <option value="<?=$promo['id']?>" <?=$args['promo_rule'] == $promo['id'] ? 'selected' : ''?> >
                                <?= $promo['label'] ?>
                            </option>
                        <?php endforeach; endif;?>
                    </select>
                </div>
            </div>
                <div class="row">
                    <div class="col-md-4 col-lg-4">
                        <label class="control-label"><?=lang('Bonus type');?></label>
                        <select name="bonus_type" id="bonus_type" class="form-control input-sm">
                        <option value="">-- <?=lang('N/A');?> --</option>
                        <?php foreach ($bonus_types as $type) : ?>
                            <option value="<?= $type['id'] ?>" <?=$args['bonus_type'] == $type['id'] ? 'selected' : ''?> >
                                <?= $type['title'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <label class="control-label"><?=lang('report.p36');?></label>
                        <input type="number" min="0" name="amount_min" id="amount_min" value="<?=$args['amount_min']?>" class="form-control input-sm" placeholder='<?=lang('report.p37');?>'/>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <label class="control-label"><?=lang('report.p35');?></label>
                        <input type="number" min="0" name="amount_max" id="amount_max" value="<?=$args['amount_max']?>" class="form-control input-sm" placeholder='<?=lang('report.p37');?>'/>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-3 col-lg-3" style="padding: 10px;">
                        <button type="button" class="btn btn-sm search-reset <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>"><?= lang('lang.reset') ?></button>
                        <input class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" type="submit" value="<?=lang('lang.search');?>" />
                    </div>
                </div>
        </div>
    </div>

</div>
</form>
        <!--end of Sort Information-->
<style type="text/css">
    #bonus_games_report_table .footer.row div { display: inline-block; }
    #bonus_games_report_table .footer.row .figure { width: 90px; padding-right: 15px; }
    #bonus_games_report_table td.never { display: none; }
</style>

        <div class="panel panel-primary">
            <div class="panel-heading custom-ph">
                <h4 class="panel-title custom-pt"><i class="fa fa-gamepad"></i> <?=lang('Bonus Games Report');?></h4>
            </div>
            <div class="panel-body">
                <!-- result table -->
                <div id="logList" class="table-responsive">
                    <table class="table table-striped table-hover table-condensed" id="bonus_games_report_table" style="width: 99%;">
                        <thead>
                            <tr>
                                <th><?=lang('Date');?></th>
                                <th><?=lang('Player Username');?></th>
                                <th><?=lang('Player Level');?></th>
                                <th><?=lang('Game Type');?></th>
                                <th><?=lang('Game Display Name');?></th>
                                <th><?=lang('Promo Type');?></th>
                                <th><?=lang('Promo Rule');?></th>
                                <th><?=lang('Bonus Type');?></th>
                                <th><?=lang('Bonus Amount');?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="9" class="text-right">

                                    <div class="footer row">
                                        <div class="title"><?= lang('Cash Bonus') ?> <?= lang('Sub Total') ?>:</div>
                                        <div class="figure"><span class=" amount cash">0.00</span></div>
                                    </div>
                                    <div class="footer row">
                                        <div class="title"><?= lang('VIP Experience') ?> <?= lang('Sub Total') ?>:</div>
                                        <div class="figure"><span class=" amount vip_exp">0.00</span></div>
                                    </div>
                                    <div class="footer row">
                                        <div class="title"><?= lang('Cash Bonus') ?> <?= lang('rounds') ?>:</div>
                                        <div class="figure"><span class=" rounds cash">0</span></div>
                                    </div>
                                    <div class="footer row">
                                        <div class="title"><?= lang('VIP Experience') ?> <?= lang('rounds') ?>:</div>
                                        <div class="figure"><span class=" rounds vip_exp">0</span></div>
                                    </div>
                                    <div class="footer row">
                                        <div class="title"><?= lang('Nothing') ?> <?= lang('rounds') ?>:</div>
                                        <div class="figure"><span class=" rounds nothing">0</span></div>
                                    </div>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!--end of result table -->
            <div class="panel-footer"></div>
        </div>

<script type="text/javascript">
    $(document).ready(function(){
        // $('#search-form').submit( function(e) {
        //     e.preventDefault();
        //     dataTable.ajax.reload();
        // });

        // $("input[type='checkbox']").bootstrapSwitch();

        $('.bookmark-this').click(_pubutils.addBookmark);

        $('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').trigger('submit');
            }
        });

        $('.search-reset').click(function () {
            $("#enable_date").prop("checked", true);
            $('#player_username').val('');
            $('#amount_min').val('');
            $('#amount_max').val('');
            $('#player_level_id').val('');
            $('#game_type').val('');
            $('#promo_type').val('');
            $('#promo_rule').val('');
            $('#bonus_type').val('');

            var dateInput = $('.dateInput');
            var default_start = moment().format('YYYY-MM-DD 00:00:00');
            var default_end = moment().format('YYYY-MM-DD 23:59:59');
            dateInput.data('daterangepicker').setStartDate(default_start);
            dateInput.data('daterangepicker').setEndDate(default_end);
            $(dateInput.data('start')).val(default_start);
            $(dateInput.data('end')).val(default_end);

        });

        $('#bonus_games_report_table').DataTable({
            <?php if(1 || $this->utils->isEnabledFeature('column_visibility_report')) : ?>
                stateSave: true,
            <?php else : ?>
                stateSave: false,
            <?php endif; ?>
            lengthMenu: JSON.parse('<?=json_encode($this->utils->getConfig('default_datatable_lengthMenu'))?>'),
            autoWidth: false,
            searching: false,
            columnDefs: [
                // { sortable: false, targets: [ 1 ] },
                { class: 'never', targets: [ 7 ] } ,
                { className: 'text-right', targets: [ 9 ] }
            ],
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            // "responsive": {
            //     details: {
            //         type: 'column'
            //     }
            // },
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
                }
                <?php if ($export_report_permission) : ?>
                ,{
                    text: "<?= lang('CSV Export'); ?>",
                    className:'btn btn-sm btn-primary',
                    action: function ( e, dt, node, config ) {
                        var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                        // utils.safelog(d);
                        $.post(site_url('/export_data/promotion_report'), d, function(data){
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
                    }
                }
                <?php endif; ?>
            ],

            "order": [ 0, 'desc' ],
            processing: true,
            serverSide: false,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/bonus_games_report", data, function(data) {
                    callback(data);
                    if ( $('#bonus_games_report_table').DataTable().rows( { selected: true } ).indexes().length === 0 ) {
                        $('#bonus_games_report_table').DataTable().buttons().disable();
                    }
                    else {
                        $('#bonus_games_report_table').DataTable().buttons().enable();
                    }

                    // if (data.summary) {
                    //     var sum = data.summary[0];
                    //     $('.footer .amount.cash').text(sum.sum_bonus_cash);
                    //     $('.footer .amount.vip_exp').text(sum.sum_bonus_vip_exp);
                    //     $('.footer .rounds.cash').text(sum.count_rounds_cash);
                    //     $('.footer .rounds.vip_exp').text(sum.count_rounds_vip_exp);
                    //     $('.footer .rounds.nothing').text(sum.count_rounds_nothing);
                    // }
                },'json');
            },
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();
                var sum = { cash: 0, vip_exp: 0, r_cash: 0, r_vip_exp: 0, r_nothing: 0 };
                for (var i in data) {
                    var row = data[i];
                    var bonus_type = row[7], bonus_amount = parseFloat(row[9]);
                    switch (bonus_type) {
                        case 'cash'     :
                            sum.cash      += bonus_amount; sum.r_cash    += 1; break;
                        case 'vip_exp'  :
                            sum.vip_exp   += bonus_amount; sum.r_vip_exp += 1; break;
                        case 'nothing'  :
                            sum.r_nothing += 1; break;
                    }
                }
                console.log('sum', sum);

                var tfooter = $( api.column(0).footer() );
                $(tfooter).find('.amount.cash')     .text(numeral(sum.cash).format('11,111.23'));
                $(tfooter).find('.amount.vip_exp')  .text(numeral(sum.vip_exp).format('11,111.23'));
                $(tfooter).find('.rounds.cash')     .text(numeral(sum.r_cash).format('11,111'));
                $(tfooter).find('.rounds.vip_exp')  .text(numeral(sum.r_vip_exp).format('11,111'));
                $(tfooter).find('.rounds.nothing')  .text(numeral(sum.r_nothing).format('11,111'));
            }
        });
    });
</script>