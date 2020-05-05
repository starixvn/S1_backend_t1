<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="fa fa-search"></i> <?=lang("lang.search")?>
                <span class="pull-right">
                    <a data-toggle="collapse" href="#collapsePlayerReport" class="btn btn-default btn-xs"></a>
                </span>
            </h4>
        </div>
        <div id="collapsePlayerReport" class="panel-collapse">
            <div class="panel-body">
                <form id="form-filter" class="form-inline" method="post">
                    <?php if($this->utils->isEnabledFeature('display_sub_affiliate_earnings_report')){ ?>
                        <label class="control-label"><?=lang('Year Month')?> : </label>
                        <div class="input-group">
                            <?=form_dropdown('year_month', $year_month_list, $conditions['year_month'], 'class="form-control"'); ?>
                            <span class="input-group-addon input-sm">
                                <input type="checkbox" name="search_on_date" id="search_on_date" value="1" checked="checked"/>
                            </span>
                        </div>
                        <div class="col-md-4"></div>
                    <?php }else { ?>
                    <label class="control-label"><?=lang('report.sum02')?></label><br>

                    <div class="input-group">
                        <input class="form-control dateInput" id="date-range" data-start="#date_from" data-end="#date_to" data-time="true" style="width: 500px;" />
                        <input type="hidden" id="date_from" name="date_from" value="<?=@$date_from?>"/>
                        <input type="hidden" id="date_to" name="date_to" value="<?=@$date_to?>"/>
                        <span class="input-group-addon input-sm">
                            <input type="checkbox" name="search_on_date" id="search_on_date" value="1" checked="checked"/>
                        </span>
                    </div>
                    <?php } ?>
                    <input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="btn btn-primary">

                </form>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="icon-users"></i> <?=lang('Sub Affiliates')?></h4>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th><?=lang('Affiliate Username')?></th>
                            <th><?=lang('Total Players')?></th>
                            <?php if($this->utils->isEnabledFeature('display_sub_affiliate_earnings_report')){ ?>
                                <th><?=lang('Active Members')?></th>
                                <th><?=lang('Net Revenue')?></th>
                                <th><?=lang('Commission Amount')?></th>
                            <?php } else { ?>
                            <th><?=lang('Deposited Players')?></th>
                            <th><?=lang('Deposited Amount')?></th>
                            <th><?=lang('Withdrawal Amount')?></th>
                            <th><?=lang('Total Bonus')?></th>
                            <th><?=lang('Total Cashback')?></th>
                            <th><?=lang('Total Loss')?></th>
                            <th><?=lang('Total Win')?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="panel-footer"></div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {

    var dataTable = $('#myTable').DataTable({
        lengthMenu: [50, 100, 250, 500, 1000],
        autoWidth: false,
        searching: false,
        dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        buttons: [{
            extend: 'colvis',
            postfixButtons: [ 'colvisRestore' ]
        }],

        // SERVER-SIDE PROCESSING
        processing: true,
        serverSide: true,
        ajax: function (data, callback, settings) {
            data.extra_search = $('#form-filter').serializeArray();
            $.post(base_url + "api/subaffiliate_reports", data, function(data) {
                callback(data);
            }, 'json');
        }
    });

    $('#form-filter').submit( function(e) {
        e.preventDefault();
        dataTable.ajax.reload();
    });

});
</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of view_player_report.php