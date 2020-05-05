<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><font style="color:red;">*</font> <?=lang('Commission Setting');?></h3>
    </div>
    <div class="panel-body">
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th><?=lang('Enabled');?></th>
                    <th><?=lang('Game Platform');?></th>
                    <th><?=lang('Game Type');?></th>
                    <th><?=lang('Tier Comm Patterns');?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($game_platform_list as $game_platform): ?>
                    <?php foreach ($game_platform['game_types'] as $index => $game_type): ?>
                        <tr>
                            <td>
                                <?php if ($index == 0): ?>
                                    <input type="checkbox" name="game_platforms[<?=$game_platform['id']?>][enabled]" id="game-platform-<?=$game_platform['id']?>" value="1"
                                        onchange="$('.platform-field-<?=$game_platform['id']?>').prop('disabled', ! this.checked); $('.platform-field-<?=$game_platform['id']?>').trigger('change');" <?=isset($conditions['game_platforms'][$game_platform['id']]) ? 'checked="checked"' : ''?> <?=isset($view_only) && $view_only ? 'disabled="disabled"' : ''?>/></td>
                                <?php endif ?>
                                <input type="hidden" name="game_types[<?=$game_type['id']?>][game_platform_id]" value="<?=$game_platform['id']?>"/>
                            </td>
                            <td><?php if ($index == 0): ?><label for="game-platform-<?=$game_platform['id']?>"><?=$game_platform['name']?></label><?php endif ?></td>
                            <td><?=lang($game_type['name'])?></td>
                            <td>
                                <select name="game_types[<?=$game_type['id']?>][pattern_id]" class="form-control input-sm platform-field-<?=$game_platform['id']?>" <?= ! isset($conditions['game_platforms'][$game_platform['id']]) && ! isset($conditions['game_types'][$game_type['id']]) ? 'disabled="disabled"':''?> <?=isset($view_only) && $view_only ? 'disabled="disabled"' : ''?>>
                                    <?php foreach($patterns as $pattern): ?>
                                    <option value="<?=$pattern['pattern_id']?>"
                                    <?=(isset($conditions['game_types'][$game_type['id']]['pattern_id']) && $conditions['game_types'][$game_type['id']]['pattern_id'] == $pattern['pattern_id']) ? 'selected':''?>>
                                    <?=$pattern['pattern_name'];?>
                                    </option>
                                    <?php endforeach ?>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
