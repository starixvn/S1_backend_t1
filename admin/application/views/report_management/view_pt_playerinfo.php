<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#ptgame-sec-table" data-toggle="tab">PT</a></li>
          <li><a href="#aggame-sec-lc" data-toggle="tab">AG</a></li>
          <li><a href="#opusgame-sec-cc" data-toggle="tab">OPUS</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="ptgame-sec-table">
                <!-- sub menu -->                
                <div class="btn-group">
                  
                  <a href="<?= BASEURL . 'pt_report_management/viewGameReport/' ?>">
                  <span type="button" class="btn  btn-danger">
                    API Issue <!-- <span class="caret"></span> -->
                  </span>
                  </a> 
                                    
                </div>                
              
                <div class="btn-group">
                  <button type="button" class="btn  btn-success btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    Player Function <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Player Login</a></li>
                    <li><a href="<?= BASEURL . 'pt_report_management/sortPTPlayerGames/' ?>">Player Games</a></li>
                    <li><a href="#">Player Transactions</a></li>
                    <li><a href="#">Player Stats</a></li>
                    <li><a href="<?= BASEURL . 'pt_report_management/sortOnlinePTPlayers' ?>">Players online</a></li>
                    <!-- <li class="divider"></li>
                    <li><a href="#">Separated link</a></li> -->
                  </ul>&nbsp;
                    <?php if($export_report_permission){ ?>
                        <a href="<?= BASEURL . 'report_management/exportPTGameApiReportToExcel' ?>" >
                            <span data-toggle="tooltip" title="Export report in excel" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top">Export Report
                            </span>
                        </a>
                    <?php } ?>
                </div>
                <!-- end sub menu --> 

                <br/><br/>
                <!-- Sort Option -->
                <form action="<?= BASEURL . 'pt_report_management/getPTPlayerInfo' ?>" method="post" role="form" name="myForm">
                    <div class="row">
                        <div class="col-md-12" style="margin-top:-10px;">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a href="#personal" style="color: white;" class="btn btn-info btn-sm hide_sortby"> 
                                            <i class="glyphicon glyphicon-chevron-down hide_sortby_up" id=""></i>
                                        </a> 
                                        Sort Option
                                    </h4>
                                </div>

                                <div class="panel panel-body sortby_panel_body" id="" style="display: none;">
                                    
                                    <div class="row">
                                        <div class="col-md-12" style="margin-top:-10px;">
                                            <br/>
                                            <div class="col-md-1">
                                                Player Name
                                            </div>
                                            <div class="col-md-3">                                                
                                                <input type="text" name="playerName" class="form-control"/>
                                            </div>
                                             
                                             <div class="col-md-3" style="margin-left:-15px;">
                                                
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
                        <h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> Player Report Result</h4>
                        <div class="clearfix"></div>
                    </div>

                    <div id="logList" class="table-responsive">
                            <table class="table table-striped table-hover table-condensed">
                                <thead>
                                    <tr>
                                        <th>Player Name</th> 
                                        <th>VIP Level</th>                                       
                                        <th>Balance</th>
                                        <th>Currency</th>
                                        <th>Sign up Date</th>
                                        <th>Last Login Date</th>
                                        <th>Frozen</th> 
                                        <th>Comments</th>                                        
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php   if($this->session->userdata('onlinePlayers')){
                                                $onlinePlayers = isset($this->session->userdata('onlinePlayers')['result']) ? $this->session->userdata('onlinePlayers')['result'] : '';
                                                //var_dump($onlinePlayers[0]);
                                            }
                                            if(!empty($onlinePlayers)) {
                                            foreach($onlinePlayers as $value) {
                                    ?>
                                                <tr>
                                                    <td><?= $value['PLAYERNAME'] ?></td>
                                                    <td><?= $value['VIPLEVEL'] ?></td>
                                                    <td><?= $value['BALANCE'] ?></td>
                                                    <td><?= $value['CURRENCY'] ?></td>
                                                    <td><?= $value['SIGNUPDATE'] ?></td>
                                                    <td><?= $value['LASTLOGINDATE'] ?></td>     
                                                    <td><?= $value['FROZEN'] ?></td>  
                                                    <td><?= $value['COMMENTS'] ?></td>                                                                                                  
                                                </tr>
                                    <?php   }
                                          }
                                          else{
                                                echo '<tr>';
                                                echo "<td colspan=12 style='text-align:center;'>No Record</td>";
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
            <div class="tab-pane" id="aggame-sec-lc">test2</div>
            <div class="tab-pane" id="opusgame-sec-cc">test3</div>
        </div>
        
    </div>
</div>