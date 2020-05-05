<div class="panel-heading">
    <h4 class="panel-title"><strong>Bonus Information</strong> (<?= $playeraccount['currency']?>)</h4>
</div>

<div class="panel panel-body" id="bonus_panel_body">

    <div class="row">
        <div class="col-md-12">
            <div class="help-block">
                <h3><strong><?= $playeraccount['currency']?> 0.00</strong></h3>= <?= $playeraccount['currency'] ?> 0.00 (playable bonus funds) + <?= $playeraccount['currency'] ?> (pending win/loss)
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <tr>
                        <th class="active">Real win/loss while wagering: </th>
                        <td><?= $playeraccount['currency']?> 0.00</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>
