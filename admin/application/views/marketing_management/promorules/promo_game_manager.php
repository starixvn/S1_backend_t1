</style>
<div class="panel panel-primary bonus-games">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="fa fa-gamepad"></i> <?=lang('Bonus Game Settings');?>
            <a href="#" class="btn btn-info pull-right" id="button_bonus_game_add" data-toggle="tooltip" data-placement="left" title="<?=lang('Add New Game');?>" style="line-height: 24px; margin-top: -6px; padding: 6px 12px;">
                <span class="glyphicon glyphicon-plus-sign"></span>
            </a>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <div class="row" id="panel_bonus_game_add_edit">
            <?php if ($game_id > 0) : ?>
                <div class="col-md-12">
                    <h4> <?= lang('Edit') ?> <?= lang('Game') ?>: <?= $game['gamename'] ?> (ID: <?= $game_id ?>)</h4>
                </div>
            <?php endif; ?>

            <div class="col-md-12">
                <div class="well" style="overflow: auto;" id="addPlayerTagMngmt">
                    <form id="form_game_add_edit" role="form" action="<?= BASEURL . 'marketing_management/bonusGameOps/add_edit' ?>" method="post" role="form" enctype="multipart/form-data">
                        <input type="hidden" name="game_id" id="game_id" value="<?= $game_id ?>" />

                        <!-- gametype -->
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label for="input_gametype">
                                    <?= lang('Game Type') ?>
                                </label>
                            </div>
                            <div class="form-group col-md-10">
                                <div class="row">
                                    <?php foreach ($elems['gametypes'] as $gametype) : ?>
                                        <div class="form-group col-md-3">
                                            <label class="control-label">
                                                <?= form_radio([ 'name' => 'gametype_id' ], $gametype['id'], $game['gametype_id'] == $gametype['id'] ) ?>
                                                <?= $gametype['gametype_text'] ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <!-- gamename -->
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label for="input_gamename"> <?= lang('Game Display Name') ?> </label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" name="gamename" id="input_gamename" class="form-control input-sm" value="<?= $game['gamename'] ?>">
                            </div>
                        </div>
                        <!-- deploy channel -->
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label for="input_deploy_channel">
                                    <?= lang('Deploy Channel') ?>
                                </label>
                            </div>
                            <div class="form-group col-md-10">
                                <div class="row">
                                    <?php foreach ($elems['deploy_channels'] as $channel) : ?>
                                        <div class="form-group col-md-3">
                                            <label class="control-label">
                                                <input type="checkbox" name="deploy_channel[]" value="<?= $channel['id'] ?>"
                                                    <?= $channel['enabled'] == 0 ? ' disabled="1" ' : '' ?>
                                                    <?= in_array($channel['id'], $game['deploy_channels']) ? ' checked="1" ' : '' ?>
                                                />
                                                <?= $channel['channel_text'] ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <!-- theme -->
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label for="input_theme">
                                    <?= lang('Theme') ?>
                                </label>
                            </div>
                            <div class="form-group col-md-10">
                               <div class="row">
                                    <?php foreach ($elems['themes'] as $theme) : ?>
                                        <div class="form-group col-md-2">
                                            <label class="control-label">
                                                <?= form_radio([ 'name' => 'theme_id' ], $theme['id'], $theme['id'] == $game['theme_id'] ) ?>
                                                <?= $theme['theme_text'] ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <!-- description -->
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label for="input_desc">
                                    <?= lang('Game Description') ?>
                                </label>
                            </div>
                            <div class="form-group col-md-10">
                                <textarea name="description" id="input_desc" class="form-control input-sm" rows="3" cols="60" style="resize:none"><?= $game['desc'] ?></textarea>
                            </div>
                        </div>
                        <!-- prizes -->
                        <div class="row">
                            <div class="col-md-2">
                                <label class="control-label">
                                    <?= lang('Prizes') ?>
                                </label>


                            </div>
                            <div class="col-md-10">
                                <div class="row prize">
                                    <div class="field num"> # </div>
                                    <div class="field sort"> <?= lang('Sort') ?> </div>
                                    <div class="field type"> <?= lang('Prize Type') ?> </div>
                                    <div class="field title"> <?= lang('Prize Title') ?> </div>
                                    <div class="field numbers"> <?= lang('Prize Amount') ?> </div>
                                    <div class="field numbers"> <?= lang('Chances (%)') ?> </div>
                                    <div class="field message"> <?= lang('Message When Hit') ?> </div>
                                    <div class="field ops"> <?= lang('lang.action') ?> </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div>
                                    <button type="button" id="btn_add_prize" class="btn btn-default btn-sm">
                                        <i class="fa fa-plus"></i> <?= lang('Add Prize') ?>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-10 prize-rows">

                                <?php // prize-editing; populated by php ?>
                                <?php if (is_array($game['prizes']) && count($game['prizes']) > 0) : ?>
                                    <?php $row_num = 0; ?>
                                    <?php foreach ($game['prizes'] as $prize) : ?>
                                        <div class="row prize">
                                            <div class="field num"> <?= ++$row_num ?> </div>
                                            <input type="hidden" name="prize[prize_id][]" value="<?= $prize['id'] ?>" />
                                            <input type="hidden" name="prize[remove_flag][]" value="" />
                                            <div class="field sort">
                                                <a class="sort arrow move-up" href="javascript: void(0);"><i class="fa fa-arrow-up"></i></a>
                                                <a class="sort arrow move-down" href="javascript: void(0);"><i class="fa fa-arrow-down"></i></a>
                                            </div>
                                            <div class="field type">
                                                <select name="prize[prize_type][<?= $prize['id'] ?>]">
                                                    <option <?= $prize['prize_type'] == 'cash' ? 'selected="1"' : '' ?> value="cash"><?= lang('Cash Bonus') ?></option>
                                                    <option <?= $prize['prize_type'] == 'vip_exp' ? 'selected="1"' : '' ?>  value="vip_exp"><?= lang('VIP Experience') ?></option>
                                                    <option <?= $prize['prize_type'] == 'nothing' ? 'selected="1"' : '' ?>  value="nothing"><?= lang('Nothing') ?></option>
                                                </select>
                                            </div>
                                            <div class="field title">
                                                <input type="text" name="prize[title][<?= $prize['id'] ?>]" class="" value="<?= $prize['title'] ?>">
                                            </div>
                                            <div class="field numbers amount">
                                                <input type="number" name="prize[amount][<?= $prize['id'] ?>]" class="" step="any" min="0" value="<?= $prize['amount'] ?>">
                                            </div>
                                            <div class="field numbers prob">
                                                <input type="number" name="prize[prob][<?= $prize['id'] ?>]" class="" max="100" min="0" step="any" value="<?= $prize['prob'] ?>"> %
                                            </div>
                                            <div class="field message">
                                                <textarea name="prize[message][<?= $prize['id'] ?>]" rows="2" cols="60"><?= $prize['message'] ?></textarea>
                                            </div>
                                            <div class="field ops">
                                                <a href="javascript: void(0);"  onclick="return confirm_remove_prize(this);">
                                                    <span class="glyphicon glyphicon-trash op-remove" data-toggle="tooltip" title="<?=lang('Delete');?>"  data-placement="top"></span>
                                                    <span class="fa fa-undo op-cancel" style="display: none;" data-toggle="tooltip" title="<?=lang('Cancel');?>" data-placement="top"></span>
                                                </a>

                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-2"></div>
                                    <div class="form-group col-md-10">
                                        <div class="row prize footer">
                                            <div class="field num"></div>
                                            <div class="field sort"> </div>
                                            <div class="field type"> </div>
                                            <div class="field title"> </div>
                                            <div class="field numbers"></div>
                                            <div class="field numbers prob">
                                                <div class="sum">
                                                    &Sigma;=<input type="text" readonly="1"> %
                                                </div>
                                                <div class="short error" style="display: none;">
                                                    &#x2716; <input type="text" readonly="1"> %
                                                </div>
                                            </div>
                                            <div class="field message">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <br/>
                                    <button type="button" id="game_save" class="btn btn-info review-btn btn-sm"><?=lang('lang.save');?></button>
                                    <input type="reset" id="game_reset" value="<?=lang('lang.reset');?>" class="btn btn-warning btn-sm"/>
                                    <button type="button" id="game_close" class="btn btn-default btn-sm" onclick="window.location='/marketing_management/bonusGameSettings/';"><?=lang('Close');?></button>
                                </div>
                            </div>

                        </div>
                        </div>
                    </form>
                </div>
            <!-- </div> -->

        <!-- </div> -->
        <div class="row">
            <div class="col-md-12">
                <!-- <form action="<?=BASEURL . 'marketing_management/deleteSelectedPromoType'?>" method="post" role="form"> -->

                    <!-- <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="<?=lang('cms.deletesel');?>">
                        <i class="glyphicon glyphicon-trash" style="color:white;"></i>
                    </button> -->
                    <!-- <hr class="hr_between_table"/> -->

                    <div id="" class="table-responsive">
                        <table class="table table-striped table-hover" id="table_games" style="width:100%">
                            <thead>
                                <tr>
                                    <!-- <th style="padding: 8px;"><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th> -->
                                    <th>#</th>
                                    <th><?=lang('Game Type');?></th>
                                    <th><?=lang('Game Name');?></th>
                                    <th><?=lang('sys.description');?></th>
                                    <th><?=lang('Promo Rule');?></th>
                                    <th><?=lang('Theme');?></th>
                                    <th><?=lang('Status');?></th>
                                    <th><?=lang('Updated At');?></th>
                                    <th><?=lang('cms.updatedby');?></th>
                                    <th><?=lang('lang.action');?></th>
                                </tr>
                            </thead>

                            <tbody>
<?php if (!empty($bonus_games)) : ?>

	<?php foreach ($bonus_games as $row) : ?>
        <?php
            $enabling_allowed = (count($row['promo_ar']) == 0);
            $enabling_allowed_class = $enabling_allowed ? '' : 'disallowed' ;
            $removing_allowed = $enabling_allowed && $row['status'] == 'disabled';
            $removing_allowed_class = $removing_allowed ? '' : 'disallowed' ;
        ?>
        <tr class="game-list-main">
            <?php /*
            <td style="padding: 8px;"><input type="checkbox" class="checkWhite" id="<?= $row['promo_game_id'] ?>" name="promoType[]" value="<?=$row['promo_game_id']?>" onclick="uncheckAll(this.id)"/></td>
            */ ?>
            <td><?= $row['promo_game_id'] ?></td>
            <td><?= lang($row['gametype']) ?></td>
            <td>
                <?php if (empty($row['gamename'])) : ?>
                    <i class="text-muted"><?= lang('lang.norecyet') ?></i>
                <?php else : ?>
                    <a href="/marketing_management/bonusGameSettings/<?=$row['promo_game_id']?>"><?= $row['gamename'] ?></a>
                <?php endif; ?>
            </td>
            <td><?= $row['desc'] ?: lang('player.tm06') ?></td>
            <td>
                <?php if (count($row['promo_ar']) > 0) : ?>
                    <?php foreach ($row['promo_ar'] as $promo) : ?>
                        <a href="http://admin.og.local/marketing_management/editPromoRule/<?= $promo['id'] ?>"><?= $promo['name'] ?></a>
                        <br />
                    <?php endforeach; ?>
                <?php else : ?>
                    <?= '<i class="text-muted">' . lang('NONE') . '</i>' ?>
                <?php endif; ?>
            </td>
            <td><?= lang($row['theme']) ?></td>
            <td class="status <?= $row['status'] ?>"><?= lang($row['status']) ?></td>
            <td><?= $row['updated_at'] ?></td>
            <td><?= $row['updated_by'] ?></td>
            <td>
                <?php if ($row['status'] == 'enabled') : ?>
                    <a class="game_ops disable <?= $enabling_allowed_class ?>" href="/marketing_management/bonusGameOps/disable/<?= $row['promo_game_id'] ?>">
                        <span class="glyphicon glyphicon-remove-sign" data-toggle="tooltip"  data-placement="top" title="<?= lang('Disable') ?>"></span>
                    </a>
                <?php else : ?>
                    <a class="game_ops enable <?= $enabling_allowed_class ?>" href="/marketing_management/bonusGameOps/enable/<?= $row['promo_game_id'] ?>">
                        <span class="glyphicon glyphicon-ok-sign" data-toggle="tooltip" data-placement="top" title="<?= lang('Enable') ?>"></span>
                    </a>
                <?php endif; ?>
                <a href="/marketing_management/bonusGameSettings/<?=$row['promo_game_id']?>">
                    <span class="glyphicon glyphicon-edit" data-toggle="tooltip" data-placement="top" title="<?=lang('Edit');?>" >
                    </span>
                </a>
                <a class="game_ops <?= $removing_allowed_class ?>" href="/marketing_management/bonusGameOps/remove/<?= $row['promo_game_id'] ?>" onclick="return confirm_remove_game('<?= $row['promo_game_id'] ?>', '<?= $row['gamename'] ?>');" >
                    <span class="glyphicon glyphicon-trash delete-promo" data-toggle="tooltip"  data-placement="top" title="<?=lang('Delete');?>"  >
                    </span>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
                            </tbody>
                        </table>
                        <!-- <div class="col-md-12 col-offset-0">
                            <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
                        </div> -->
                    </div>
                <!-- </form> -->

            </div>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>

<!-- begin template row -->
<!-- Move template row out of form, preventing submitting extra contents -->
<div class="row prize" style="display: none;" id="prize_row_tmpl">
    <div class="field num"> 1 </div>
    <input type="hidden" name="prize[prize_id][]" value="" />
    <input type="hidden" name="prize[remove_flag][]" value="" />
    <div class="field sort">
        <a class="sort arrow move-up" href="javascript: void(0);"><i class="fa fa-arrow-up"></i></a>
        <a class="sort arrow move-down" href="javascript: void(0);"><i class="fa fa-arrow-down"></i></a>
    </div>
    <div class="field type">
        <select name="prize[prize_type][]">
            <option value="cash"><?= lang('Cash Bonus') ?></option>
            <option value="vip_exp"><?= lang('VIP Experience') ?></option>
            <option value="nothing" selected="1"><?= lang('Nothing') ?></option>
        </select>
    </div>
    <div class="field title">
        <input type="text" name="prize[title][]" class="">
    </div>
    <div class="field numbers amount">
        <input type="number" name="prize[amount][]" class="" step="any">
    </div>
    <div class="field numbers prob">
        <input type="number" name="prize[prob][]" class="" step="any"> %
    </div>
    <div class="field message">
        <textarea name="prize[message][]" rows="2" cols="60" style="resize:none"></textarea>
    </div>
    <div class="field ops">
        <a href="javascript: void(0);"  onclick="return confirm_remove_prize(this);">
            <span class="glyphicon glyphicon-trash op-remove" data-toggle="tooltip" title="<?=lang('Delete');?>"  data-placement="top">
            </span>
            <span class="fa fa-undo op-cancel" data-toggle="tooltip" title="<?=lang('Cancel');?>"  data-placement="top" style="display: none;">
            </span>
        </a>
    </div>
</div>
<!-- end template row -->

<script type="text/javascript">
    // Datatable setup for #table_games
    $(document).ready(function(){
        $('#table_games').DataTable({
            dom: "<'panel-body'<'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            "columnDefs": [
                {
                    orderable: false,
                    targets:   [ 9 ]
                }
            ],
            "order": [ 0, 'asc' ]
        });
    });

    /**
     * Confirmation when removing game
     * @param   numeric     game_id     = bonus_game_games.id
     * @param   string      gamename    = bonus_game_games.gamename
     * @return  bool        true if user clicked OK; otherwise false
     */
    function confirm_remove_game(game_id, gamename) {
        var confirm_mesg = "<?= lang('Deleting promo game #$ID `$GAMENAME`, please confirm') ?>";
        confirm_mesg = confirm_mesg.replace('$GAMENAME', gamename).replace('$ID', game_id);
        return confirm(confirm_mesg);
    }

    /**
     * Remove/unremove prize
     * @param   reference   target      event target, use 'this'
     * @return  none
     */
    function confirm_remove_prize(target) {
        // First, from the event target, find the row container
        var row = $(target).parents('.row.prize');

        // Use class to_remove to
        if ($(row).hasClass('to_remove')) {
            $(row).removeClass('to_remove');
            $(row).find("input[name*='remove_flag']").val('');
        }
        else {
            $(row).addClass('to_remove');
            $(row).find("input[name*='remove_flag']").val('1');
        }
        $(target).find('span.op-remove').toggle();
        $(target).find('span.op-cancel').toggle();
    }

    /**
     * Re-number prize rows
     * Invoked on adding or deleting a prize row
     *
     * @return  none
     */
    function prize_row_renum() {
        var container = $('.prize-rows');
        var rows = $(container).find('.row.prize');
        var row_count= $(rows).length;
        var row_i = 0;
        $(rows).each(function() {
            var fnum = $(this).find('.field.num');
            if (fnum.length == 0) return;
            $(fnum).text(++row_i);
        });
    }

    /**
     * Calculate and show the sum of all chances field
     * Invoked when each chance field changes
     *
     * @return  bool    true if sum of all chances == 100.0; otherwise false
     */
    function sum_all_prob_fields() {
        var frame = $('.prize-rows');
        var row_footer = $('.row.prize.footer');
        var input_prob = $('.field.numbers.prob input');
        var panel_prob_sum = $(row_footer).find('.sum');
        var out_prob_sum = $(row_footer).find('.sum input');
        var panel_prob_short = $(row_footer).find('.short');
        var out_prob_short = $(row_footer).find('.short input');

        // Find each field and add the value up
        var sum_prob = 0;
        var count = 0;
        var count_minus = 0;
        $(frame).find('.field.numbers.prob input').each(function() {
            var row = $(this).parents('row.prize');
            var val = parseFloat($(this).val());
            if ($(row).hasClass('to_delete')) {
                return;
            }
            ++count;
            // Restore field appearance if no errors
            if (!isNaN(val) && val >= 0.0) {
                sum_prob += val;
                $(this).removeClass('error');
                $(panel_prob_sum).removeClass('error');
            }
            // Or mark the field on illegal value
            else {
                ++count_minus;
                $(this).addClass('error');
                $(panel_prob_sum).addClass('error');
            }
        });

        // Show sum of chances
        $(panel_prob_short).hide();
        var sum_prob_fixed = parseFloat(sum_prob.toFixed(3));
        $(out_prob_sum).val(sum_prob_fixed);
        // Show shortage (100 - sum)
        var short_prob = 100.0 - sum_prob_fixed;
        if (short_prob > 0 || short_prob < 0) {
            $(panel_prob_short).show();
            $(out_prob_short).val(short_prob.toFixed(3));
        }

        return { sum: sum_prob, count: count, count_minus: count_minus };
    }

    /**
     * Initializer for chances field change event
     * Binding sum_all_prob_fields()
     *
     * @return  none
     */
    (function init_prob_field_sum() {
        var frame = $('.prize-rows');
        // Binding
        $(frame).on('change', '.field.numbers.prob', sum_all_prob_fields);
        // Initial run
        $(document).ready(function () {
            sum_all_prob_fields();
        });

    })();

    /**
     * Initializer for #button_bonus_game_add click event
     * Binding #panel_bonus_game_add_edit show/hide routine
     *
     * @return  none
     */
    (function init_panel_bonus_game_add_edit() {
        var panel = $('#panel_bonus_game_add_edit');
        var button = $('#button_bonus_game_add');
        var glyph = $(button).find('.glyph');
        var dur_delay = 250;

        var game_id = parseInt($('#game_id').val());
        console.log('game_id', game_id);

        if (!isNaN(game_id) && game_id > 0) {
            $(button).hide();
        }
        else {
            $(panel).hide();
            $(button).click(function() {
                if (!$(panel).is(':visible')) {
                    $(panel).show(dur_delay);
                    $(glyph).removeClass('glyphicon glyphicon-plus-sign');
                    $(glyph).addClass('glyphicon glyphicon-minus-sign');
                }else{
                    $(panel).hide(dur_delay);
                    $(glyph).removeClass('glyphicon glyphicon-minus-sign');
                    $(glyph).addClass('glyphicon glyphicon-plus-sign');
                }

            });
        }
    })();

    /**
     * Initializer for #btn_add_prize click event
     * Binding new prize adding routine
     *
     * @return  none
     */
    (function init_btn_add_prize() {
        var button = $('#btn_add_prize');
        var tmpl = $('#prize_row_tmpl');
        var frame = $('.prize-rows');
        $(button).click(function () {
            // Clone new row
            var row_count = $(frame).find('.row.prize').length;
            var new_row = $(tmpl).clone().css('display', '').attr('id', '');

            $(frame).append(new_row);
            prize_row_renum();
        });
    })();

    /**
     * Initializer for #game_save click event
     * Binding form error checking and submitting
     *
     * @return none
     */
    (function init_btn_game_save() {
        var form = $('#form_game_add_edit');
        var button = $('#game_save');
        $(button).click( function () {
            var all_chances = sum_all_prob_fields();
            console.log('all_chances', all_chances);
            if (all_chances.count == 0) {
                alert('<?= lang('Requires at least one prize') ?>');
                return false;
            }

            var shortage = 100.0 - all_chances.sum;
            if (Math.abs(shortage) > 0.001) {
                // Sum of all chances not 100% - alert and exit
                var chance_alert = '<?= lang('Sum of all chances does not make 100.0 %, there is still $SHORT % in short') ?>';
                chance_alert = chance_alert.replace('$SHORT', shortage.toFixed(3));
                alert(chance_alert);
                return false;
            }
            else if (all_chances.count_minus > 0) {
                // There are minus chances
                var minus_alert = '<?= lang('Chances must be greater than 0') ?>';
                alert(minus_alert);
                return false;
            }
            else {
                // Sum of chances reached 100% - submit form
                $(form).submit();
            }
        });

    })();

    /**
     * Initializer for .sort.arrow click events
     * Binding prize row moving up/down routine
     *
     * @return none
     */
    (function init_sort_actions() {
        $('.prize-rows').on('click', '.sort.arrow', function (e) {
            var dir = 0;
            var row = $(this).parents('.row.prize');
            if ($(this).hasClass('move-up')) {
                console.log('move-up');
                var neighbor_above = $(row).prev();
                console.log('neighbor_above', neighbor_above);
                if (neighbor_above.length > 0) {
                    // has neighbor on top; not the first of list
                    var row_prime = $(row).clone();
                    // $(neighbor_above).css('border', '1px dashed red');
                    $(neighbor_above).before(row_prime);
                    $(row).remove();
                    prize_row_renum();
                }
            }
            else if ($(this).hasClass('move-down')) {
                console.log('move-down');
                var neighbor_under = $(row).next();
                console.log('neighbor_under', neighbor_under);
                if (neighbor_under.length > 0) {
                    // has neighbor under; not the last
                    var row_prime = $(row).clone();
                    // $(neighbor_above).css('border', '1px dashed red');
                    $(neighbor_under).after(row_prime);
                    $(row).remove();
                    prize_row_renum();
                }

            }
        })
    })();

    <?php /*
    // function readURL(input, imgSelector) {
    //     if (input.files && input.files[0]) {
    //         var reader = new FileReader();

    //         reader.onload = function (e) {
    //             $('#'+imgSelector).attr('src', e.target.result);
    //         }

    //         reader.readAsDataURL(input.files[0]);
    //     }
    // }

    // $("#filPromoCatIcon").change(function(){
    //     readURL(this, 'imgPromoCatIcon');
    // });

    // $("#filEditPromoCatIcon").change(function(){
    //     readURL(this, 'editImgPromoCatIcon');
    // });
    */ ?>
</script>