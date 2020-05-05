<table class="table table-striped table-hover table-condensed">
    <thead>
        <tr>
            <th><?= lang('report.p02'); ?></th>                                                                    
            <th><?= lang('report.p04'); ?></th>
            <th><?= lang('report.p44'); ?></th>
            <th><?= lang('report.p41'); ?></th>
            <th><?= lang('report.p42'); ?></th>
            <th><?= lang('report.p40'); ?></th>
            <!-- <th>Bet Amount Rule</th> -->
            <!-- <th>Total Current Bet</th> -->
        </tr>
    </thead>
    <tbody>
        <?php  //var_dump($playerReportData);
                if(!empty($promoReportData)) {
                foreach($promoReportData as $value) {
        ?>
                    <tr>
                        <td class="table-td"><?= $value['username'] ?></td>
                        <!-- <td class="table-td"><?= $value['rankingLevelGroup'].' '.$value['rankingLevel'] ?></td>                                             -->
                        <td class="table-td"><?= $value['groupName'] == '' ? '<i>'. lang('lang.norecord') .'<i/>' : $value['groupName'].' '.$value['vipLevel'] ?></td>
                        <td class="table-td"><?= $value['promoName'] == '' ? '<i>'. lang('lang.norecord') .'</i>' : $value['promoName'] ?></td>   
                        <td class="table-td"><?= $value['dateJoined'] == '' ? '<i>'. lang('lang.norecord') .'</i>' : $value['dateJoined'] ?></td>   
                        <td class="table-td"><?= $value['bonusAmount'] == '' ? '<i>'. lang('lang.norecord') .'</i>' : $value['bonusAmount'] ?></td>
                        <?php
                            if($value['bonusStatus'] == '0') {
                                $value['bonusStatus'] = lang('report.p45');                                                    
                            } 
                            elseif($value['bonusStatus'] == '1') {
                                $value['bonusStatus'] = lang('report.p46');                                                    
                            }
                            elseif ($value['bonusStatus'] == '2') {
                                $value['bonusStatus'] = lang('report.p47');
                            }
                            elseif ($value['bonusStatus'] == '3') {
                                $value['bonusStatus'] = lang('report.p48');
                            }  
                            elseif ($value['bonusStatus'] == '4') {
                                $value['bonusStatus'] = lang('report.p49');
                            }
                            elseif ($value['bonusStatus'] == '5') {
                                $value['bonusStatus'] = lang('report.p50');
                            }
                            elseif ($value['bonusStatus'] == '6') {
                                $value['bonusStatus'] = lang('report.p51');
                            }
                        ?>  
                        <td class="table-td"><?= $value['bonusStatus'] == '' ? '<i>'. lang('lang.norecord') .'</i>' : $value['bonusStatus'] ?></td>  
                        <!-- <td class="table-td"><?= $value['betAmountRule'] == '' ? '<i>'. lang('lang.norecord') .'</i>' : $value['betAmountRule'] ?></td>    -->
                        <!-- <td class="table-td"><?= $value['currentBet'] ?></td> -->                                             
                    </tr>
        <?php   }
              }
              else{
                    echo '<tr>';
                    echo "<td colspan=14 style='text-align:center;'>". lang('lang.norecord') ."</td>";
                    echo '</td>';
              }
         ?>
    </tbody>
</table>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>