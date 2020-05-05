<div role="tabpanel" class="tab-pane active" id="gameInfo">
    <div class="clearfix m-b-15">
        <div class="pull-right">
            <?php if ($this->utils->isEnabledFeature('create_ag_demo') && !empty($isAGGameAccountDemoAccount)): ?>
                <a href="/player_management/createGameProviderAccount/<?=$player['playerId'] . '/' . AG_API . '/true'?>" class="btn btn-scooter btn-xs" data-toggle="tooltip" data-placement="top" title="<?=lang('It will create a new ag demo account to replace old account')?>" onclick="return confirm('<?=sprintf(lang('Are you sure you want to switch player to demo account?'), 'AG API')?>')">
                    <i class="fa fa-refresh"></i> <?=lang('Change AG to Demo Account')?>
                </a>
            <?php endif; ?>

            <?php if ($this->utils->isEnabledFeature('create_agin_demo') && !empty($isAGINGameAccountDemoAccount)): ?>
                <a href="/player_management/createGameProviderAccount/<?=$player['playerId'] . '/' . AGIN_API . '/true'?>" class="btn btn-scooter btn-xs" data-toggle="tooltip" data-placement="top" title="<?=lang('It will create a new agin demo account to replace old account')?>" onclick="return confirm('<?=sprintf(lang('Are you sure you want to switch player to demo account?'), 'AGIN API')?>')">
                    <i class="fa fa-refresh"></i> <?=lang('Change AGIN to Demo Account')?>
                </a>
            <?php endif; ?>

            <a href="/player_management/refreshAllGames/<?=$player['playerId']?>" class="btn btn-scooter btn-xs">
                <i class="glyphicon glyphicon-refresh"></i> <?=lang('lang.refresh')?>
            </a>
        </div>

    </div>
    <div class="table-responsive" id="game_panel_body">
        <table class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <?php if($this->utils->getConfig('use_total_hour')): ?>
                        <th rowspan="2" class="active text-center" style="vertical-align: middle;"><?=lang('player.ui29')?></th>
                        <th rowspan="2" class="active text-center" style="vertical-align: middle;"><?=lang('cashier.78')?></th>
                        <th rowspan="2" class="active text-center" style="vertical-align: middle;"><?=lang('Status')?></th>
                        <th colspan="1" class="active text-center"><?=lang('player.ui26')?></th>
                        <th colspan="1" class="active text-center"><?=lang('player.ui27')?></th>
                        <th colspan="1" class="active text-center"><?=lang('player.ui28')?></th>
                        <th rowspan="2" class="active text-center" style="vertical-align: middle;"><?=lang('Result Percentage')?></th>
                        <th rowspan="2" class="active text-center" style="vertical-align: middle;"><?=lang('mark.resultAmount')?></th>
                    <?php else: ?>
                        <th rowspan="2" class="active text-center" style="vertical-align: middle;"><?=lang('player.ui29')?></th>
                        <th rowspan="2" class="active text-center" style="vertical-align: middle;"><?=lang('cashier.78')?></th>
                        <th rowspan="2" class="active text-center" style="vertical-align: middle;"><?=lang('Status')?></th>
                        <th colspan="3" class="active text-center"><?=lang('player.ui26')?></th>
                        <th colspan="4" class="active text-center"><?=lang('player.ui27')?></th>
                        <th colspan="4" class="active text-center"><?=lang('player.ui28')?></th>
                        <th rowspan="2" class="active text-center" style="vertical-align: middle;"><?=lang('player.ui25')?></th>
                    <?php endif; ?>
                </tr>
                <tr>
                    <?php if($this->utils->getConfig('use_total_hour')): ?>
                        <th class="active text-center"><?=lang('system.word32')?></th>
                        <th class="active text-center"><?=lang('system.word32')?></th>
                        <th class="active text-center"><?=lang('system.word32')?></th>
                    <?php else: ?>
                        <th class="active text-center"><?=lang('player.mp03')?></th>
                        <th class="active text-center"><?=lang('lang.average')?></th>
                        <th class="active text-center"><?=lang('system.word32')?></th>
                        <th class="active text-center"><?=lang('player.mp03')?></th>
                        <th class="active text-center"><?=lang('cms.percentage')?></th>
                        <th class="active text-center"><?=lang('lang.average')?></th>
                        <th class="active text-center"><?=lang('system.word32')?></th>
                        <th class="active text-center"><?=lang('player.mp03')?></th>
                        <th class="active text-center"><?=lang('cms.percentage')?></th>
                        <th class="active text-center"><?=lang('lang.average')?></th>
                        <th class="active text-center"><?=lang('system.word32')?></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($game_data['game_platforms'] as $game_platform): ?>
                    <tr>
                        <td><?=$game_platform['system_code']?></td>
                        <?php if (!$game_platform['register']): ?>
                            <td align="left">
                                <a href="/player_management/createGameProviderAccount/<?=$player['playerId']?>/<?=$game_platform['id']?>" data-toggle="tooltip" data-placement="right" title="<?=sprintf(lang('gameplatformaccount.title.create'), $game_platform['system_code'])?>" class="pull-right" onclick="return confirm('<?=sprintf(lang('gameplatformaccount.confirm.create'), $game_platform['system_code'])?>')">
                                    <i class="fa fa-user-plus"></i>
                                </a>
                            </td>
                            <td align="center">
                                <i class="text-muted"><?=lang('lang.norec')?></i>
                            </td>
                        <?php else: ?>
                            <td align="left">
                                <b class="text-info"><?=$game_platform['login_name']; ?></b>
                                <?php if ($this->permissions->checkPermissions('force_create_game_account')): ?>
                                    <a href="/player_management/createGameProviderAccount/<?=$player['playerId']?>/<?=$game_platform['id']?>" data-toggle="tooltip" data-placement="right" title="<?=sprintf(lang('gameplatformaccount.title.create'), $game_platform['system_code'])?>" class="pull-right" onclick="return confirm('<?=sprintf(lang('gameplatformaccount.confirm.create'), $game_platform['system_code'])?>')">
                                        <i class="fa fa-user-plus"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <ul class="list-inline pull-right">
                                    <li>
                                        <a href="/player_management/reset_player/<?=$player['playerId']?>/<?=$game_platform['id']?>" data-toggle="tooltip" data-placement="right" title="<?=sprintf(lang('gameplatformaccount.title.reset'), $game_platform['system_code'])?>" onclick="return confirm('<?=sprintf(lang('gameplatformaccount.confirm.reset'), $game_platform['system_code'])?>')">
                                            <i class="fa fa-undo"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/player_management/syncPassword/<?=$player['playerId']?>/<?=$game_platform['id']?>" data-toggle="tooltip" data-placement="right" title="<?=sprintf(lang('gameplatformaccount.title.sync'), $game_platform['system_code'])?>" onclick="return confirm('<?=sprintf(lang('gameplatformaccount.confirm.sync'), $game_platform['system_code'])?>')">
                                            <i class="fa fa-refresh"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/player_management/update_game_info_view/<?=$player['playerId']?>/<?=$game_platform['id']?>/<?=$game_platform['system_code']?>" data-toggle="tooltip" data-placement="right" title="<?=sprintf(lang('gameplatformaccount.title.update'), $game_platform['system_code'])?>" onclick="return confirm('<?=sprintf(lang('gameplatformaccount.confirm.update'), $game_platform['system_code'])?>')">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                    </li>
                                    <?php if ($this->permissions->checkPermissions('block_player')): ?>
                                        <?php if ($game_platform['blocked'] == 0): ?>
                                            <li>
                                                <a href="/player_management/blockGameProviderAccount/<?=$player['playerId']?>/<?=$game_platform['id']?>" data-toggle="tooltip" data-placement="right" title="<?=sprintf(lang('gameplatformaccount.title.block'), $game_platform['system_code'])?>" onclick="return confirm('<?=sprintf(lang('gameplatformaccount.confirm.block'), $game_platform['system_code'])?>')">
                                                    <i class="fa fa-lock"></i>
                                                </a>
                                            </li>
                                        <?php elseif ($game_platform['blocked'] == 1): ?>
                                            <li>
                                                <a href="/player_management/unblockGameProviderAccount/<?=$player['playerId']?>/<?=$game_platform['id']?>" title="<?=sprintf(lang('gameplatformaccount.title.unblock'), $game_platform['system_code'])?>" onclick="return confirm('<?=sprintf(lang('gameplatformaccount.confirm.unblock'), $game_platform['system_code'])?>')">
                                                    <i class="fa fa-unlock-alt"></i>
                                                </a>
                                            </li>
                                        <?php endif?>
                                    <?php endif?>
                                    <?php if ($this->permissions->checkPermissions('set_player_bet_limit_for_api')): ?>
                                        <?php if(!empty($this->utils->getConfig('api_with_set_bet_limit')) && in_array($game_platform['id'], $this->utils->getConfig('api_with_set_bet_limit'))): ?>
                                            <li>
                                                <a href="/player_management/setMemberBetSetting/<?=$player['playerId']?>/<?=$game_platform['id']?>" data-toggle="tooltip" data-placement="right" title="<?=sprintf(lang('gameplatformaccount.title.set_bet_setting'), $game_platform['system_code'])?>">
                                                    <i class="fa fa-bars"></i>
                                                </a>
                                            </li>
                                        <?php endif?>
                                    <?php endif?>
                                </ul>
                                <?php if ($game_platform['blocked'] == 1): ?>
                                    <b class="text-danger"><?=lang('sys.ip16')?></b>
                                <?php elseif ($game_platform['is_demo_flag'] == 1): ?>
                                    <b class="text-warning"><?=lang('Demo')?></b>
                                <?php elseif ($game_platform['blocked'] == 0): ?>
                                    <b class="text-success"><?=lang('status.normal')?></b>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <?php if($this->utils->getConfig('use_total_hour')): ?>
                            <td align="right" class="info"><?=number_format((isset($game_platform['bet']['sum']) ? $game_platform['bet']['sum'] : 0), 2)?></td>
                            <td align="right" class="success"><?=number_format((isset($game_platform['gain']['sum']) ? $game_platform['gain']['sum'] : 0), 2)?></td>
                            <td align="right" class="danger"><?=number_format((isset($game_platform['loss']['sum']) ? $game_platform['loss']['sum'] : 0), 2)?></td>
                        <?php else: ?>
                            <td align="right" class="info"><?=isset($game_platform['bet']['count']) ? $game_platform['bet']['count'] : 0?></td>
                            <td align="right" class="info"><?=number_format((isset($game_platform['bet']['ave']) ? $game_platform['bet']['ave'] : 0), 2)?></td>
                            <td align="right" class="info"><?=number_format((isset($game_platform['bet']['sum']) ? $game_platform['bet']['sum'] : 0), 2)?></td>
                            <td align="right" class="success"><?=isset($game_platform['gain']['count']) ? $game_platform['gain']['count'] : 0?></td>
                            <td align="right" class="success"><?=number_format(isset($game_platform['gain']['percent']) ? $game_platform['gain']['percent'] : 0, 2)?>%</td>
                            <td align="right" class="success"><?=number_format((isset($game_platform['gain']['ave']) ? $game_platform['gain']['ave'] : 0), 2)?></td>
                            <td align="right" class="success"><?=number_format((isset($game_platform['gain']['sum']) ? $game_platform['gain']['sum'] : 0), 2)?></td>
                            <td align="right" class="danger"><?=isset($game_platform['loss']['count']) ? $game_platform['loss']['count'] : 0?></td>
                            <td align="right" class="danger"><?=number_format(isset($game_platform['loss']['percent']) ? $game_platform['loss']['percent'] : 0, 2)?>%</td>
                            <td align="right" class="danger"><?=number_format((isset($game_platform['loss']['ave']) ? $game_platform['loss']['ave'] : 0), 2)?></td>
                            <td align="right" class="danger"><?=number_format((isset($game_platform['loss']['sum']) ? $game_platform['loss']['sum'] : 0), 2)?></td>
                        <?php endif; ?>


                        <?php if($this->utils->getConfig('use_total_hour')): ?>
                            <td align="right" class="bg-warning">
                                <strong><?=number_format((isset($game_platform['result_percentage']['percent']) ? $game_platform['result_percentage']['percent'] : 0), 2)?>%</strong>
                            </td>
                        <?php endif; ?>
                        <td align="right" class = "bg-warning"
                            <?php if (!isset($game_platform['gain_loss']['sum']) || $game_platform['gain_loss']['sum'] == 0): ?>
                            <?php elseif ($game_platform['gain_loss']['sum'] < 0): ?>
                                class="text-danger"
                            <?php elseif ($game_platform['gain_loss']['sum'] > 0): ?>
                                class="text-success warning"
                            <?php endif; ?>
                        >
                            <strong><?=number_format((isset($game_platform['gain_loss']['sum']) ? $game_platform['gain_loss']['sum'] : 0), 2)?></strong>
                        </td>
                    </tr>
                <?php endforeach?>
            </tbody>
            <tfoot>
                <tr>
                    <?php if($this->utils->getConfig('use_total_hour')): ?>
                        <th colspan="3"></th>
                        <th class="text-right"><?=number_format($game_data['total_bet_sum'], 2)?></th>
                        <th class="text-right"><?=number_format($game_data['total_gain_sum'], 2)?></th>
                        <th class="text-right"><?=number_format($game_data['total_loss_sum'], 2)?></th>
                        <th class="text-right"><?=number_format($game_data['total_result_percent'], 2)?>%</th>
                        <th class="text-right"
                            <?php if ($game_data['total_gain_loss_sum'] < 0): ?>
                                class="text-danger"
                            <?php elseif ($game_data['total_gain_loss_sum'] > 0): ?>
                                class="text-success"
                            <?php endif; ?>
                        >
                            <?=number_format($game_data['total_gain_loss_sum'], 2)?>
                        </th>
                    <?php else: ?>
                        <th colspan="3"></th>
                        <th class="text-right"><?=$game_data['total_bet_count']?></th>
                        <th class="text-right"><?=number_format($game_data['total_bet_ave'], 2)?></th>
                        <th class="text-right"><?=number_format($game_data['total_bet_sum'], 2)?></th>
                        <th class="text-right"><?=$game_data['total_gain_count']?></th>
                        <th class="text-right"><?=number_format($game_data['total_gain_percent'], 2)?>%</th>
                        <th class="text-right"><?=number_format($game_data['total_gain_ave'], 2)?></th>
                        <th class="text-right"><?=number_format($game_data['total_gain_sum'], 2)?></th>
                        <th class="text-right"><?=$game_data['total_loss_count']?></th>
                        <th class="text-right"><?=number_format($game_data['total_loss_percent'], 2)?>%</th>
                        <th class="text-right"><?=number_format($game_data['total_loss_ave'], 2)?></th>
                        <th class="text-right"><?=number_format($game_data['total_loss_sum'], 2)?></th>
                        <th class="text-right"
                            <?php if ($game_data['total_gain_loss_sum'] < 0): ?>
                                class="text-danger"
                            <?php elseif ($game_data['total_gain_loss_sum'] > 0): ?>
                                class="text-success"
                            <?php endif; ?>
                        >
                            <?=number_format($game_data['total_gain_loss_sum'], 2)?>
                        </th>
                    <?php endif; ?>
                </tr>
            </tfoot>
        </table>
    </div>
</div>