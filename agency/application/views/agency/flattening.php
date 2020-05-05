<style>
    tfoot{
        background-color: #2c3e50;
        color: white;
    }
    .table th{
        text-align: center;
    }
    .dateInput.gray_out {
        color: #bbb;
    }
</style>

<div class="container">



    <form class="form-horizontal" id="search-form" style="">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fa fa-search"></i> <?=lang("lang.search")?>
                    <span class="pull-right">
                        <a data-toggle="collapse" href="#collapseViewGameLogs"
                           class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>">
                        </a>
                    </span>
                    <!--
                    <span class="pull-right m-r-10">
                        <label class="checkbox-inline">
                            <input type="checkbox" id="gametype" value="gametype" onclick="checkSearchGameLogs(this.value);"/>
                            <?php echo lang('Game Type');?>
                        </label>
                    </span> -->
                    <?php //include __DIR__ . "/../includes/report_tools.php" ?>
                </h4>
            </div>

            <div id="collapseViewGameLogs" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? 'collapse in' : ''?>">
                <div class="panel-body">
                    <div class="col-md-6">
                        <label class="control-label" for="search_game_date"><?=lang('Date Range');?></label>
                        <div class="input-group">
                            <input id="search_game_date" class="form-control input-sm dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
                            <span class="input-group-addon">
                                <input type="checkbox" id="use_date" name="use_date"
                                    <?php if ($search['use_date']) : ?>
                                        checked='checked'
                                    <?php endif; ?>
                                />
                            </span>
                        </div>
                        <input type="hidden" id="date_from" name="date_from" value="<?php echo $search['date_from']; ?>" />
                        <input type="hidden" id="date_to" name="date_to"  value="<?php echo $search['date_to']; ?>"/>
                    </div>
                    <?php /*
                    <div class="col-md-2 has-error">
                        <label class="control-label"><?=lang('Current Agent')?></label>
                        <input type="text" class="form-control input-sm text-danger" value="<?=$agent['agent_name']?>" readonly="readonly">
                    </div>
                    */ ?>
                    <div class="col-md-2">
                        <label class="control-label" for="by_username"><?=lang('Agent Username');?> </label>
                        <input type="text" name="agent_username" id="agent_username" class="form-control input-sm"
                               value="<?php echo $search['agent_username']; ?>" />
                    </div>

                </div>
                <div class="panel-footer text-right">
                    <input type="reset" class="btn btn-sm" id="search-reset" value="<?php echo lang('Reset'); ?>" >
                    <input type="submit" class="btn btn-primary btn-sm" id="btn-submit" value="<?php echo lang('Search'); ?>" >
                </div>
            </div>
        </div>
        <input type="hidden" name="master_agent_id" value="<?= $search['master_agent_id'] ?>" />
    </form>

    <form class="form-horizontal" id="options-form" method="post">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <?=lang("Flattening Options")?>
                    <span class="pull-right">
                        <a data-toggle="collapse" href="#collapseOptions"
                           class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>">
                        </a>
                    </span>
                </h4>
            </div>

            <div id="collapseOptions" class="panel-collapse collapse in">
                <div class="panel-body">
<!--
                     <div class="row">
                        <div class="col-md-10">
                            <label class="control-label"> <?=lang('Flattening Period');?></label>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>
                                        <input type="radio" class="" name="period" value="daily"
                                        <?= $options['period'] == 'daily' ? 'checked' : '' ?> />
                                        <?= lang('Daily') ?>
                                    </label>
                                </div>
                                <div class="col-md-2">
                                    <label>
                                        <input type="radio" class="" name="period" value="weekly"
                                        <?= $options['period'] == 'weekly' ? 'checked' : '' ?> />
                                        <?= lang('Weekly') ?>
                                    </label>
                                </div>
                                <div class="col-md-2">
                                    <label>
                                        <input type="radio" class="" name="period" value="monthly"
                                        <?= $options['period'] == 'monthly' ? 'checked' : '' ?> />
                                        <?= lang('Monthly') ?>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label>
                                        <input type="radio" class="" name="period" value="custom_daterange"
                                        <?= $options['period'] == 'custom_daterange' ? 'checked' : '' ?> />
                                        <?= lang('Custom Daterange') ?>
                                    </label>
                                    <input id="custom_daterange" class="form-control input-sm dateInput"
                                   data-start="#custom_date_from" data-end="#custom_date_to" data-time="true" style="display: inline; margin-top: -5px; width: 300px;" />
                                    <input type="hidden" id="custom_date_from" name="custom_daterange_from" value="<?= $options['custom_daterange_from'] ?>" />
                                    <input type="hidden" id="custom_date_to" name="custom_daterange_to"  value="<?= $options['custom_daterange_to'] ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
-->
                    <div class="row">
                        <div class="col-md-4">
                            <label class="control-label"><?=lang('Base Credit');?></label><br />
                            <input type="number" class="input-sm form-control" name="base_credit" value="<?= $options['base_credit'] ?>" />
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <input type="submit" class="btn btn-primary btn-sm" id="btn-submit" value="<?php echo lang('Update'); ?>" >
                </div>
            </div>
        </div>
        <input type="hidden" name="options_update" value="1" />
        <input type="hidden" name="period" value="weekly" />
    </form>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">
                <?=lang('Flattening List');?>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table table-condensed table-bordered" id="table_flattening">
                <thead>
                    <tr>
                        <!-- <th>#</th> -->
                        <th><?= lang('Period') ?></th>
                        <th><?= lang('Date Range') ?></th>
                        <th><?= lang('Agent Name') ?></th>
                        <th><?= lang('W/L') ?></th>
                        <th><?= lang('Rolling') ?></th>
                        <th><?= lang('W/L Comm') ?></th>
                        <th><?= lang('Base Credit') ?></th>
                        <th><?= lang('Flattening') ?></th>
                        <th><?= lang('Created At') ?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>
<script>
    $(document).ready( function () {
        var dataTable = $('#table_flattening').DataTable({
            autoWidth: true,
            searching: false,
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            paging: true ,
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ]
                }
            ],
            columnDefs: [
                { className: 'dt-right dt-head-center', targets: [ 3, 4, 5, 6, 7 ] } ,
                { className: 'dt-center', targets: [ 0, 1, 8 ] } ,
                { visible: false, targets: [ 9 ] }
            ],
            order: [ 1, 'desc' ],
            processing: true,
            // serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "agency/flattening_list", data, function(data) {
                    callback(data);
                },'json');
            }
        });

        (function init_search_panel() {
            function use_date_click() {
                var use_date = $('#use_date');
                var search_game_date = $('#search_game_date');
                if (!$(use_date).is(':checked')) {
                    $(search_game_date).attr('disabled', 1).addClass('gray_out');
                }
                else {
                    $(search_game_date).removeAttr('disabled').removeClass('gray_out');
                }
            }
            use_date_click();
            $('#use_date').click(use_date_click);

            $('#search-reset').click( function (e) {
                e.preventDefault();
                $('#agent_username').val('');
                var use_date = $('#use_date');
                if ($(use_date).is(':checked')) {
                    $('#search_game_date').val('');
                }
            })
        })();
    });
</script>