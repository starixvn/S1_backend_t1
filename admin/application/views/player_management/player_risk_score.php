<style type="text/css">
    .dataTables_wrapper { overflow-y: hidden; overflow-x: hidden; }
</style>
<div class="panel panel-primary">
    <div class="panel panel-body pull-right">
        <a href="javascript:void(0)" onclick="showRiskScoreChart();"><i class="glyphicon glyphicon-info-sign"></i><?= lang('Risk Score Chart') ?></a>
    </div>
    <div class="panel panel-body" id="player_panel_body">
        <table class="table table-bordered" style="margin-bottom:0;">
            <thead>
                <tr>
                    <th class="active"><?=lang('cms.category')?></th>
                    <th class="active"><?=lang('sys.description')?></th>
                    <th class="active"><?=lang('Generated Result')?></th>
                    <th class="active"><?=lang('Score')?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $key => $value) { ?>

                <tr>
                    <td><?=lang($value['category'])?><a class="pull-right" href="javascript:void(0)" onclick="showRules('<?=$value['category']?>');"><i class="glyphicon glyphicon-question-sign"></i></a></td>
                    <td style="text-align: center;"><?= lang($value['description'])?></td>

                    <?php if($value['category'] == Risk_score_model::R1 || $value['category'] == Risk_score_model::R2) : ?>
                        <td align="right"><?=$value['generated_result']?></td>
                    <?php else: ?>
                        <td><?=$value['generated_result']?></td>
                    <?php endif; ?>

                    <td><?=$value['score']?></td>
                </tr>
                <?php } ?>
                <tr>
                    <td><?=lang('Risk Level')?></td>
                    <td class='<?=(strpos($risk_level, 'High') !== false)? 'text-danger': 'text-success' ?>'><?=lang($risk_level)?></td>
                    <td><?=lang('Total Score')?></td>
                    <td><?=$total_score?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="panel panel-body">
        <table class="table table-bordered" style="margin-bottom:0;" id="risk_rules_body">
            <thead>
                <tr></tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#risk_score').text('<?=$risk_level?> / <?=$total_score?>');
        $('#allowed_withdrawal_status').text('<?=$allowed_withdrawal_status?>');
    });

    function initializeDataTable(){
        $('#risk_rules_body_wrapper').show();
        $('#risk_rules_body').DataTable({
            "bLengthChange": false,
            "bInfo": false,
            "bFilter": false,
            "iDisplayLength": 10
        });
    }

    function showRules(riskId) {
        $.post("/player_management/get_risk_score_info/"+riskId, function(result){
            $('#risk_rules_body').empty();
            $('#risk_rules_body').append($('<thead/>').append($('<tr/>')),$('<tbody/>'));
            //$('#risk_rules_body > thead > tr').empty();
            //$('#risk_rules_body > tbody').empty();
            $('#risk_rules_body > thead > tr').append(
                    $('<th/>',{'class' : 'active', 'style' : 'text-align: center','html': '<a class="pull-left" href="javascript:void(0)" onclick="closeRulesInfo()"><i class="pull-left text-danger"> X </i></a>'+result.category_description}),
                    $('<th/>',{'class' : 'active', 'style' : 'text-align: center','html': '<?=lang('Score')?>'})
                );
            var rules = JSON.parse(result.rules);

            $.each(rules, function(i, val) {
                $('#risk_rules_body > tbody').append(
                     $('<tr/>').append(
                         $('<td/>',{'class' : '','html': val.rule_name}),
                         $('<td/>',{'class' : '','html': val.risk_score})
                     )
                );
            });
            initializeDataTable();
        },'json');

    }

    function showRiskScoreChart() {
        $.post("/player_management/get_risk_score_info/RC", function(result){
            $('#risk_rules_body').empty();
            $('#risk_rules_body').append($('<thead/>').append($('<tr/>')),$('<tbody/>'));
            $('#risk_rules_body > thead > tr').append(
                    $('<th/>',{'class' : 'active', 'style' : 'text-align: center','html': '<a class="pull-left" href="javascript:void(0)" onclick="closeRulesInfo()"><i class="pull-left text-danger"> X </i></a>'+'<?=lang('Risk Score')?>'}),
                    $('<th/>',{'class' : 'active', 'style' : 'text-align: center','html': '<?=lang('Risk Level')?>'}),
                     $('<th/>',{'class' : 'active', 'style' : 'text-align: center','html': '<?=lang('player.up06')?>'})
                );
            var rules = JSON.parse(result.rules);
            $.each(rules, function(i, val) {
                $('#risk_rules_body > tbody').append(
                     $('<tr/>').append(
                         $('<td/>',{'class' : '','html': val.rule_name}),
                         $('<td/>',{'class' : '','html': val.risk_score}),
                         $('<td/>',{'class' : '','html': val.withdrawal_status})
                     )
                );
            });
            initializeDataTable();
        },'json');
    }

    function showWithdrawList(playerId) {
        $.post("/player_management/getWithdrawalHistoryList/"+playerId, function(result){
            $('#risk_rules_body').empty();
            $('#risk_rules_body').append($('<thead/>').append($('<tr/>')),$('<tbody/>'));
            $('#risk_rules_body > thead > tr').append(
                    $('<th/>',{'class' : 'active', 'style' : 'text-align: center','html': '<a class="pull-left" href="javascript:void(0)" onclick="closeRulesInfo()"><i class="pull-left text-danger"> X </i></a>'+'<?=lang('lang.date')?>'}),
                    $('<th/>',{'class' : 'active', 'style' : 'text-align: center','html': '<?=lang('Withdraw Code')?>'}),
                     $('<th/>',{'class' : 'active', 'style' : 'text-align: center','html': '<?=lang('lang.amount')?>'})
                );
            if(result != "") {
                $.each(result, function(i, val) {
                    $('#risk_rules_body > tbody').append(
                         $('<tr/>').append(
                             $('<td/>',{'class' : '','html': val.dwDateTime}),
                             $('<td/>',{'class' : '','html': val.transactionCode}),
                             $('<td/>',{'class' : '','html': val.amount})
                         )
                    );
                });
            } else {
                $('#risk_rules_body > tbody').append(
                         $('<tr/>').append(
                             $('<td/>',{'colspan': 3 , 'style': 'text-align: center','html' : "<?= lang('No data available in table') ?>"})
                         )
                    );

            }
            initializeDataTable();
        },'json');
    }

    function showDepositList(playerId) {
        $.post("/player_management/getDepositHistoryList/"+playerId, function(result){
            $('#risk_rules_body').empty();
            $('#risk_rules_body').append($('<thead/>').append($('<tr/>')),$('<tbody/>'));
            $('#risk_rules_body > thead > tr').append(
                    $('<th/>',{'class' : 'active', 'style' : 'text-align: center','html': '<a class="pull-left" href="javascript:void(0)" onclick="closeRulesInfo()"><i class="pull-left text-danger"> X </i></a>'+'<?=lang('pay.transperd')?>'}),
                    $('<th/>',{'class' : 'active', 'style' : 'text-align: center','html': '<?=lang('deposit_list.order_id')?>'}),
                     $('<th/>',{'class' : 'active', 'style' : 'text-align: center','html': '<?=lang('Amount')?>'})
                );
            if(result != "") {
                $.each(result, function(i, val) {
                    $('#risk_rules_body > tbody').append(
                         $('<tr/>').append(
                             $('<td/>',{'class' : '','html': val.player_submit_datetime}),
                             $('<td/>',{'class' : '','html': val.secure_id}),
                             $('<td/>',{'class' : '','html': val.amount})
                         )
                    );
                });
            } else {
                $('#risk_rules_body > tbody').append(
                         $('<tr/>').append(
                             $('<td/>',{'colspan': 3 , 'style': 'text-align: center','html' : "<?= lang('No data available in table') ?>"})
                         )
                    );

            }
            initializeDataTable();
        },'json');
    }

    function closeRulesInfo() {
        $('#risk_rules_body > thead > tr').empty();
        $('#risk_rules_body > tbody').empty();
        $('#risk_rules_body_wrapper').hide();
    }
</script>
