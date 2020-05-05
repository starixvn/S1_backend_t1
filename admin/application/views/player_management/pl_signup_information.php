<div class="panel-heading">
    <h4 class="panel-title"><strong>Signup Information</strong></h4>
</div>

<div class="panel panel-body" id="signupinfo_panel_body">
    <div class="row">
        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <tr>
                        <th class="active">Username</th>
                        <td><?= $player['username'] ?></td>
                    </tr>

                    <tr>
                        <th class="active">Game Name</th>
                        <td><?= $player['gameName'] ?></td>
                    </tr>

                    <tr>
                        <th class="active">Sign up Ip</th>
                        <td><?= $player['registerIp'] ?></td>
                    </tr>

                    <tr>
                        <th class="active">Sign up date</th>
                        <td><?= $player['playerCreatedOn'] ?></td>
                    </tr>

                    <tr>
                        <th class="active">Referral Code</th>
                        <td><?= $player['invitationCode']?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <tr>
                        <th class="active">Last login date</th>
                        <td><?= $player['lastLoginTime']?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>