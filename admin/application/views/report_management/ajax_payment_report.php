<table class="table table-striped table-hover table-condensed">
    <thead>
        <tr>
            <th><?= lang("player.01"); ?></th>                                                                    
            <th><?= lang("report.p04"); ?></th>
            <th><?= lang("report.p09"); ?></th>
            <th><?= lang("lang.status"); ?></th>
            <th><?= lang("report.p25"); ?></th>
            <th><?= lang("report.p26"); ?></th>
            <th><?= lang("report.p23"); ?></th>
            <th><?= lang("report.p21"); ?></th>
            <th><?= lang("report.p24"); ?></th>                                                            
        </tr>
    </thead>

    <tbody>
        <?php  //var_dump($paymentReportData);exit();
                if(!empty($paymentReportData)) {
                foreach($paymentReportData as $value) {
        ?>
                    <tr>
                        <td class="table-td"><?= $value['username'] ?></td>
                        <td class="table-td"><?= $value['groupName'] == '' ? '<i>'. lang('lang.norecord') .'<i/>' : $value['groupName'].' '.$value['vipLevel'] ?></td>
                        <td class="table-td"><?= $value['transactionType'] ?></td>
                        <td class="table-td"><?= $value['dwStatus'] == '' ? '<i>'. lang('lang.norecord') .'</i>' : $value['dwStatus'] ?></td>   
                        <td class="table-td"><?= $value['amount'] == '' ? '<i>'. lang('lang.norecord') .'</i>' : $value['amount'] ?></td>    
                        <td class="table-td"><?= $value['dwMethod'] == '' ? '<i>'. lang('lang.norecord') .'</i>' : $value['dwMethod'] == '1' ? lang('report.p27') : lang('report.p28') ?></td>  
                        <td class="table-td"><?= $value['adminName'] == '' ? '<i>'. lang('lang.norecord') .'</i>' : $value['adminName'] ?></td>   
                        <td class="table-td"><?= $value['processDatetime'] == '' ? '<i>'. lang('lang.norecord') .'</i>' : $value['processDatetime'] ?></td>   
                        <td class="table-td"><?= $value['notes'] == '' ? '<i>'. lang('lang.norecord') .'</i>' : $value['notes'] ?></td>                                                                                  
                    </tr>
        <?php   }
              }
              else{
                    echo '<tr>';
                    echo "<td colspan=9 style='text-align:center;'>". lang('lang.norecord') ."</td>";
                    echo '</td>';
              }
         ?>
    </tbody>
</table>
<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>