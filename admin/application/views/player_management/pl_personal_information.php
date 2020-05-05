<div class="panel-heading">
    <h4 class="panel-title"><strong>Personal Information</strong></h4>
</div>

<div class="panel panel-body" id="personal_panel_body">

    <div class="row">
        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <tr>
                        <th class="active">First Name</th>
                        <td><?= $player['firstName'] ?></td>
                    </tr>

                    <tr>
                        <th class="active">Last Name</th>
                        <td><?= $player['lastName'] ?></td>
                    </tr>

                    <tr>
                        <th class="active">Email Address</th>
                        <td><?= $player['email'] ?></td>
                    </tr>

                    <tr>
                        <th class="active">Gender</th>
                        <td><?= $player['gender'] ?></td>
                    </tr>

                    <tr>
                        <th class="active">City</th>
                        <td><?= $player['city'] ? $player['city'] : 'No record found' ?></td>
                    </tr>

                    <tr>
                        <th class="active">Contact Number</th>
                        <td><?= $player['contactNumber'] ?></td>
                    </tr>

                    <!-- <tr>
                        <th class="active">Region</th>
                        <td><?= $player['region'] ?></td>
                    </tr> -->
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <tr>
                        <th class="active">Birthdate</th>
                        <td><?= $player['birthdate']?></td>
                    </tr>

                    <tr>
                        <th class="active">Age</th>
                        <td><?= $age ?></td>
                    </tr>

                    <tr>
                        <th class="active">Country</th>
                        <td><?= $player['country'] ? $player['country'] : 'No record found' ?></td>
                    </tr>

                    <tr>
                        <th class="active">Zip code</th>
                        <td><?= $player['zipcode'] ? $player['zipcode'] : 'No record found' ?></td>
                    </tr>

                    <tr>
                        <th class="active">Address</th>
                        <td><?= $player['address'] ? $player['address'] : 'No record found' ?></td>
                    </tr>

                    <tr>
                        <th class="active">Verification question</th>
                        <td><?= $player['secretQuestion']?></td>
                    </tr>

                    <!-- <tr>
                        <th class="active">Verification question's answer</th>
                        <td><?= $player['secretAnswer']?></td>
                    </tr> -->
                </table>
            </div>
        </div>
    </div>

</div>