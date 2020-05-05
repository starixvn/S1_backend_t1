<div class="panel-heading">
    <h4 class="panel-title"><strong>Other Informations</strong></h4>
</div>

<div class="panel panel-body" id="other_informations_panel_body">
    <div class="row">
        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <tr>
                        <th class="active">Player Tags</th>
                        <td><?= $tag['tagName'] ? $tag['tagName'] : 'No tag yet'?></td>
                    </tr>

                    <tr>
                        <th class="active">Comments</th>
                        <td><textarea name="comments" disabled="disabled"></textarea></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>
