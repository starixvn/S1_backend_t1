    <div class="panel panel-primary">
        <div class="panel panel-body pull-right">
            <a href="javascript:void(0)" onclick="showAllowedKYCRiskScoreChart();"><i class="glyphicon glyphicon-info-sign"></i><?= lang('Table Chart') ?></a>
        </div>
        <div class="panel panel-body" id="player_panel_body">
            <table class="table table-bordered" style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th class="active"><?=lang('KYC Level')?></th>
                        <th class="active"><?=lang('Risk Level')?></th>
                        <!--<th class="active"><?=lang('Verified Identification')?>?</th>-->
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;"><?=lang($kyc_lvl)?></td>
                        <td style="text-align: center;"><?=lang($risk_lvl)?></td>
                    </tr>
                    <td colspan="2" style="text-align: center;" class="<?=($risk_kyc_result) ? 'text-success' : 'text-danger' ?>"><?=($risk_kyc_result) ? lang('Allowed Withdrawal') : lang('Not Allowed Withdrawal')?></td>
                </tbody>
            </table>
        </div>
        <div class="panel panel-body">
            <table class="table table-bordered" style="margin-bottom:0;" id="chart_body">
                <thead>
                    <tr></tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
<script>
    $(document).ready(function() {
        $('#allowed_withdrawal_status').text('<?=($risk_kyc_result) ? lang('Yes') : lang('No') ?>');
    });

    function showAllowedKYCRiskScoreChart() {
        $.post("/player_management/allowed_withdrawal_status_chart", function(result){
            $('#chart_body').empty();
            $('#chart_body').append($('<thead/>').append($('<tr/>')),$('<tbody/>'));
            $('#chart_body > thead > tr').append($('<th/>',{'class' : 'active', 'style' : 'text-align: center','html': '<a class="pull-left" href="javascript:void(0)" onclick="closeRulesInfo()"><i class="pull-left text-danger"> X </i></a>'+'<?=lang('Risk Level\KYC Level')?>'}));
            var arr_column_label = [];

            //fill the hearder
            $.each(result.kyc_list, function(i, val) {
                $('#chart_body > thead > tr').append(
                     $('<th/>',{'class' : 'active', 'style' : 'text-align: center','html': val.rate_code})
                );
            });

            //fill the chart body
            $.each(result.renderChart, function(i, val) {
                $('#chart_body > tbody').append( 
                    $('<tr/>').append(
                        $('<td/>',{'class' : '','html': i}),
                        $.map(val, function(vals, key) {
                            return $('<td/>',{'class' : '','html': vals.tag, 'style': (vals.tag == 'Y') ? 'color: green;' : 'color: red;'})
                        })
                    )
                );
            });    
        },'json');
    }

    function closeRulesInfo() {
        $('#chart_body > thead > tr').empty();
        $('#chart_body > tbody').empty();
    }

</script>
