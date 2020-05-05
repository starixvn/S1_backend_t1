<?php
    $bet_amount = 0;
    $win_amount = 0;
    $loss_amount = 0;
    $result_amount = 0;
 ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="renderer" content="webkit" />
    <title> Mix Parlay </title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <script type="text/javascript" src="<?=$this->utils->jsUrl('jquery-2.1.4.min.js')?>"></script>
    <script type="text/javascript" src="<?=$this->utils->jsUrl('bootstrap.min.js')?>"></script>

    <?php
    $user_theme = !empty($this->session->userdata('admin_theme')) ? $this->session->userdata('admin_theme') : $this->config->item('sbe_default_theme');
    if($_SERVER['SERVER_NAME']=='admin.vip-win007.com'){
        $user_theme = $this->session->userdata['admin_theme'] = "win007";
    }
    ?>
    <link href="<?=$this->utils->cssUrl('themes/bootstrap.' . $user_theme . '.css')?>" rel="stylesheet">
    <link href="<?=$this->utils->cssUrl('font-awesome.min.css')?>" rel="stylesheet">
</head>
<body data-theme="<?=$user_theme?>">
    <div class="panel panel-primary">
        <div class="panel-body">
            <div class="table-responsive">
                <table id="myTable" class="table table-bordered">
                    <thead>
                        <span><h2><b><?=lang('Game')?>: </b><?=lang($game_name)?></h2></span>
                        <tr>
                            <?php if ($is_sports): ?>
                                <th><?=lang('yourBet')?></th>
                                <th><?=lang('isLive')?></th>
                                <th><?=lang('odds')?></th>
                                <th><?=lang('hdp')?></th>
                                <th><?=lang('htScore')?></th>
                                <th><?=lang('eventName')?></th>
                                <th><?=lang('League')?></th>
                            <?php else: ?>
                                <th><?=lang('Serial No:')?></th>
                                <th><?=lang('Bet Placed')?></th>
                                <th><?=lang('Won Side')?></th>
                                <th><?=lang('Odds')?></th>
                                <th><?=lang('Bet Amount')?></th>
                                <th><?=lang('Win Amount')?></th>
                                <th><?=lang('Loss Amount')?></th>
                                <th><?=lang('Result Amount')?></th>
                            <?php endif ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($is_sports):
                            $noData = '<tr><td colspan="7" align="center">No Data Found!</td></tr>';
                            if(!empty($data)){
                                foreach($data as $val) {
                                    $parlay = json_decode($val, true);
                                    if (isset($parlay['sports_bet']) && !empty($parlay['sports_bet'])) {
                                        foreach ($parlay['sports_bet'] as $row) { ?>
                                            <tr>
                                                <?php foreach ($row as $key => $field) {
                                                    if ($key == 'htScore') {
                                                        $htScore = $row[$key];
                                                        if (is_array($htScore)) {
                                                            $scoreDet = '';
                                                            foreach ($htScore as $n => $score) {
                                                                $scoreDet .= $htScore[$n]['score'] . ' ';
                                                            }
                                                            $htScore = "(" . $scoreDet . ")";
                                                        }
                                                        echo '<td>' . $htScore . '</td>';
                                                    } else {
                                                        if ($key == 'isLive') {
                                                            echo (empty($field) || $field == 0) ? '<td>'.lang('Not Live').'</td>' : '<td>'.lang('Live').'</td>';
                                                        } else {
                                                            echo '<td>' . $field . '</td>';
                                                        }
                                                    }
                                                } ?>
                                            </tr>
                                        <?php }
                                    }
                                }
                            } else { echo $noData; }

                        else:
                            if (!empty($data)):
                                $multibet = json_decode($data['bet_details'],true);
                                foreach ($multibet['bet_details'] as $key => $row):?>
                                        <?php
                                            $bet_amount += $row['bet_amount'];
                                            $win_amount += $row['win_amount'];
                                            $loss_amount += ($row['winloss_amount'] < 0) ? $row['winloss_amount']:0;
                                            $result_amount += $row['winloss_amount'];
                                        ?>
                                    <tr>
                                        <td><?=$key?></td >
                                        <td><?=isset($row['bet_placed']) ? lang($row['bet_placed']) : 'N/A'?></td >
                                        <td><?=isset($row['won_side']) ? $row['won_side']: 'N/A'?></td >
                                        <td><?=isset($row['odds']) ? $row['odds'] : 'N/A'?></td >
                                        <td><?=isset($row['bet_amount']) ? number_format($row['bet_amount'] , 2, '.', '') : '0.00'?></td >
                                        <td><?=isset($row['win_amount']) ? number_format($row['win_amount'] , 2, '.', '') : '0.00'?></td >
                                        <td><?=isset($row['winloss_amount']) && $row['winloss_amount'] < 0 ? number_format(abs($row['winloss_amount']) , 2, '.', '') : '0.00'?></td >
                                        <td><?=isset($row['winloss_amount']) ? number_format($row['winloss_amount'] , 2, '.', '') : '0.00'?></td >
                                    </tr >
                                <?php endforeach; ?>
                                <tfoot>
                                    <tr>
                                        <td colspan="4"></td >
                                        <td><b><?=number_format($bet_amount , 2, '.', '')?></b></td >
                                        <td><b><?=number_format($win_amount , 2, '.', '')?></b></td >
                                        <td><b><?=number_format(abs($loss_amount) , 2, '.', '')?></b></td >
                                        <td><b><?=number_format($result_amount , 2, '.', '')?></b></td >
                                    </tr>
                                </tfoot>
                            <?php else: ?>
                                <tr><td colspan="5" align="center"><?=lang('No Data Found')?>!</td></tr>
                            <?php endif;
                        endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>