<div class="row">
    <div class="col-md-12">              
        <!-- Sort Option -->
        <form action="<?= BASEURL . 'report_management/viewPlayerReport' ?>" method="post" role="form">
            <div class="row">
                <div class="col-md-12" style="margin-top:-10px;">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <i class="icon-search" id="hide_main_up"></i> <?= lang('lang.search'); ?>
                                <a href="#main" 
              class="btn btn-default btn-sm pull-right hide_sortby"> 
                                    <i class="glyphicon glyphicon-chevron-up hide_sortby_up"></i>
                                </a>
                                <div class="clearfix"></div>
                            </h4>
                        </div>
                        <div class="panel-body sortby_panel_body">
                            
                            <div class="row">
                                <div class="col-md-12" style="margin-top:-10px;">
                                    <div class="col-md-3">
                                        <h5>Username</h5>                            
                                          <input type="text" name="sortByUsername" class="form-control input-sm" placeholder='Enter Username'/>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Player Level</h5>                            
                                          <!-- <select class="form-control input-sm" name="sortByPlayerLvl">
                                            <option value="" <?= $this->session->userdata('sortByPlayerLvl') == 'players' ? 'selected' : ''?>>-- Select Level --</option>                                           
                                          </select> -->
                                          <select name="sortByPlayerLevel" id="sortByPlayerLevel" class="form-control input-sm">
                                            <option value="">-- Select Level --</option>
                                            <?php foreach ($playerLevels as $key => $value) { ?>
                                                <option value="<?= $value['rankingLevelSettingId'] ?>" <?= $this->session->userdata('sortByPlayerLevel') == $value['rankingLevelSettingId'] ? 'selected' : ''?>><?= $value['rankingLevelGroup'] . " " . $value['rankingLevel'] ?></option>
                                            <?php } ?>
                                           </select>
                                    </div>  
                                    <!-- <div class="col-md-3">
                                        <h5>Deposit</h5>                            
                                          <select class="form-control" name="sortByDeposit">
                                            <option value="" <?= $this->session->userdata('sortByPlayerLvl') == 'players' ? 'selected' : ''?>>-- Select Deposit --</option>
                                            <option value="players" <?= $this->session->userdata('sortByDeposit') == 'players' ? 'selected' : ''?>>1st Time</option>
                                            <option value="ip" <?= $this->session->userdata('sortByDeposit') == 'ip' ? 'selected' : ''?>>2nd Time</option>
                                            <option value="brand" <?= $this->session->userdata('sortByDeposit') == 'brand' ? 'selected' : ''?>>3rd Time</option>                                           
                                          </select>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Deposit Amount</h5>                            
                                          <input type="text" name="sortByDepositAmount" class="form-control input-sm" placeholder='Enter Amount'/>
                                    </div> -->
                                    <div class="col-md-3">
                                        <h5>Balance Amount Less Than</h5>                            
                                          <input type="text" name="sortByBetAmountLessThan" value="<?= $this->session->userdata('sortByBetAmountLessThan') ?>" class="form-control input-sm" placeholder='Enter Balance Amount'/>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Balance Amount Greater Than</h5>                            
                                          <input type="text" name="sortByBetAmountGreaterThan" value="<?= $this->session->userdata('sortByBetAmountGreaterThan') ?>" class="form-control input-sm" placeholder='Enter Balance Amount'/>
                                    </div>
                                    <!-- <div class="col-md-3">
                                        <h5>Total Bet</h5>                            
                                          <input type="text" name="sortByTotalBet" class="form-control input-sm" placeholder='Enter Total Bet'/>
                                    </div> -->
                                    <div class="col-md-3">
                                        <h5>Signup Period</h5>                           
                                          <select class="form-control input-sm" name="sortByGender">
                                            <option value="" <?= $this->session->userdata('sortByGender') == '' ? 'selected' : ''?>>-- Select Gender --</option>
                                            <option value="Male" <?= $this->session->userdata('sortByGender') == 'Male' ? 'selected' : ''?>>Male</option>
                                            <option value="Female" <?= $this->session->userdata('sortByGender') == 'Female' ? 'selected' : ''?>>Female</option>                                                    
                                          </select>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Gender</h5>                           
                                          <select class="form-control input-sm" name="sortByGender">
                                            <option value="" <?= $this->session->userdata('sortByGender') == '' ? 'selected' : ''?>>-- Select Gender --</option>
                                            <option value="Male" <?= $this->session->userdata('sortByGender') == 'Male' ? 'selected' : ''?>>Male</option>
                                            <option value="Female" <?= $this->session->userdata('sortByGender') == 'Female' ? 'selected' : ''?>>Female</option>                                                    
                                          </select>
                                    </div>   
                                   <!--  <div class="col-md-3">
                                        <h5>Affiliate</h5>                           
                                          <select name="sortByAffiliate" id="sortByAffiliate" class="form-control input-sm">
                                            <option value="">-- Select Affiliate --</option>
                                            <?php foreach ($affiliates as $key => $value) { ?>
                                                <option value="<?= $value['affiliateId'] ?>"><?= $value['firstname'] . " " . $value['lastname'] ?></option>
                                            <?php } ?>
                                           </select>
                                    </div> -->
                                    <div class="col-md-3">
                                        <h5>Tag As</h5>                           
                                          <select name="sortByTag" id="sortByTag" class="form-control input-sm">
                                            <option value="">-- Select Tags --</option>
                                            <?php foreach ($playerTags as $key => $value) { ?>
                                                <option value="<?= $value['tagId'] ?>" <?= $this->session->userdata('sortByTag') == $value['tagId'] ? 'selected' : ''?>><?= $value['tagName'] ?></option>
                                            <?php } ?>
                                           </select>
                                    </div>   
                                    <div class="col-md-3">
                                        <h5>Order By</h5>                            
                                          <select class="form-control input-sm" name="orderByReport">
                                            <option value="" <?= $this->session->userdata('orderByReport') == '' ? 'selected' : ''?>>-- Select Order --</option>
                                            <option value="userName" <?= $this->session->userdata('orderByReport') == 'userName' ? 'selected' : ''?>>Username</option>
                                            <option value="playerLevel" <?= $this->session->userdata('orderByReport') == 'playerLevel' ? 'selected' : ''?>>Player Level</option>  
                                            <option value="gender" <?= $this->session->userdata('orderByReport') == 'gender' ? 'selected' : ''?>>Gender</option>                                                  
                                          </select>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Sort By</h5>                            
                                          <select class="form-control input-sm" name="sortBySortby">
                                            <option value="ASC" <?= $this->session->userdata('sortBySortby') == 'ASC' ? 'selected' : ''?>>Ascending</option>
                                            <option value="DESC" <?= $this->session->userdata('sortBySortby') == 'DESC' ? 'selected' : ''?>>Descending</option>                                                    
                                          </select>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Item Count</h5>                            
                                          <select class="form-control input-sm" name="sortByItemCnt">
                                            <option value="5" <?= $this->session->userdata('sortByItemCnt') == '5' ? 'selected' : ''?>>5</option>
                                            <option value="10" <?= $this->session->userdata('sortByItemCnt') == '10' ? 'selected' : ''?>>10</option>                                                    
                                            <option value="20" <?= $this->session->userdata('sortByItemCnt') == '20' ? 'selected' : ''?>>20</option>
                                            <option value="50" <?= $this->session->userdata('sortByItemCnt') == '50' ? 'selected' : ''?>>50</option>
                                            <option value="100" <?= $this->session->userdata('sortByItemCnt') == '100' ? 'selected' : ''?>>100</option>
                                          </select>
                                    </div>
                                    <!-- <div class="col-md-3">
                                        <h5>Balance Amount</h5>                            
                                          <input type="text" name="sortByBalanceAmount" class="form-control input-sm" placeholder='Enter Balance Amount'/>
                                    </div>   -->          
                                     <div class="col-md-3" style="margin:-5px;">
                                        <br/><br/>
                                        <input class="btn btn-sm btn-primary" type="submit" value="Submit" />
                                    </div>                         
                                </div>
                            </div>                                    
                        </div>                                
                    </div>
                </div>
            </div>
        </form>
        <!--end of Sort Information-->     
        <br/>

        <!-- result table -->
        <div class="panel panel-primary
              " style="margin-top:-30px;">
            <div class="panel-heading">
                <h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> Payment Report Result</h4>
                <div class="clearfix"></div>
            </div>

            <div id="logList" class="table-responsive">
                    <table class="table table-striped table-hover table-condensed">
                        <thead>
                            <tr>
                                <th>Username</th>                                                                    
                                <th>Player Level</th>
                                <th>Email</th>
                                <th>Register IP</th>
                                <th>Last Login IP</th>
                                <th>Last Login Time</th>
                                <th>Last Logout Time</th>
                                <th>Joined On</th>                            
                                <!-- <th>Total Bet</th>  -->    
                                <!-- <th>Affliate</th> -->  
                                <th>Gender</th>                                                                 
                                <th>Tag As</th>
                                <th>Tag By</th>
                                <th>Blocked</th>
                                <th>Balance Amount</th>  
                            </tr>
                        </thead>

                        <tbody>
                            <?php  //var_dump($playerReportData);
                                    if(!empty($playerReportData)) {
                                    foreach($playerReportData as $value) {
                            ?>
                                        <tr>
                                            <td class="table-td"><?= $value['username'] ?></td>
                                            <td class="table-td"><?= $value['rankingLevelGroup'].' '.$value['rankingLevel'] ?></td>
                                            <td class="table-td"><?= $value['email'] ?></td>
                                            <td class="table-td"><?= $value['registerIp'] == '' ? '<i>No Record</i>' : $value['registerIp'] ?></td>   
                                            <td class="table-td"><?= $value['lastLoginIp'] == '' ? '<i>No Record</i>' : $value['lastLoginIp'] ?></td>    
                                            <td class="table-td"><?= $value['lastLoginTime'] == '' ? '<i>No Record</i>' : $value['lastLoginTime'] ?></td>  
                                            <td class="table-td"><?= $value['lastLogoutTime'] == '' ? '<i>No Record</i>' : $value['lastLogoutTime'] ?></td>   
                                            <td class="table-td"><?= $value['createdOn'] ?></td>  
                                            <!-- <td class="table-td"><?= isset($value['affiliate']) == TRUE ? $value['affiliate'] : '<i>No affiliate</i>' ?></td> -->
                                            <td class="table-td"><?= $value['gender'] ?></td>        
                                            <td class="table-td"><?= $value['tagName'] == '' ? '<i>No Tag Record</i>' : $value['tagName'] ?></td> 
                                            <td class="table-td"><?= $value['taggedBy'] == '' ? '<i>No Record</i>' : $value['taggedBy'] ?></td> 
                                            <td class="table-td"><?= $value['blocked'] == 0 ? 'No' : 'Yes' ?></td>       
                                            <td ><?= $value['totalBalanceAmount'] ?></td>                                                                            

                                        </tr>
                            <?php   }
                                  }
                                  else{
                                        echo '<tr>';
                                        echo "<td colspan=14 style='text-align:center;'>No Record</td>";
                                        echo '</td>';
                                  }
                             ?>
                        </tbody>
                    </table>

                    <div class="col-md-12 col-offset-0">
                        <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
                    </div>
                 
            </div>
            <!--end of result table -->     

            <div class="panel-footer">

            </div>
        </div>            
    </div>
</div>