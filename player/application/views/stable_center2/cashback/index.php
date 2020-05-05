<div class="tab-pane main-content cashback">
    <h1><?php echo lang("Realtime Cashback") ?></h1>
    <div class="tab-content">
        <div class="row ">
            <div class="col-sm-12 col-md-12">
                <div class="cells tri">
                    <p><?= lang('Available Cashback Amount') ?></p>
                    <h2 id="cb-total-cb" class="hilite">¥ &hellip;&hellip;</h2>
                    <div class="text-right"> </div>
                </div>
                <div class="cells tri">
                    <p><?= lang('Total Bet') ?></p>
                    <h2 id="cb-total-bet">¥ &hellip;&hellip;</h2>
                    <div class="text-right"> </div>
                </div>
                <div class="cells tri">
                    <p><?= lang('Bet Available for Cashback') ?></p>
                    <h2 id="cb-avail-bet">¥ &hellip;&hellip;</h2>
                    <div class="clearfix text-right"> </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="settle-period borders text-center">
                    <?= lang('Cashback Calculation Period') ?>:
                    <span class="start">&hellip;&hellip;</span>
                    &ndash;
                    <span class="end">&hellip;&hellip;</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="req borders text-left">
                    <?php if ($can_user_cashback) : ?>
                        <?php if (empty($cashback_request)) : ?>
                            <div class="borders grid info">
                                &nbsp;
                            </div>
                            <div class="grid btn-panel">
                                <button class="btn" id="settle_cashback"><?= lang('Settle Now') ?></button>
                            </div>
                        <?php else : ?>
                            <div class="borders grid info">
                                <div>
                                    <b><?= lang('xpj.cashback.pending_cashback_not_finished') ?></b>
                                </div>
                                <div>
                                    <div class="item title"><?=lang('xpj.cashback.request_datetime')?></div>
                                    <div class="item text">2018-02-06 23:59</div>
                                </div>
                                <div>
                                    <div class="item title"><?=lang('xpj.cashback.request_amount')?></div>
                                    <div class="item text">&yen; 2,054.16</div>
                                </div>
                            <!--
                                <div>
                                    <div class="item title"><?=lang('xpj.cashback.status')?></div>
                                    <div class="item text"><?=lang('xpj.cashback.pending')?></div>
                                </div>
                            -->
                            </div>
                            <div class="grid btn-panel">
                                <button class="btn disabled">----</button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (!$can_user_cashback) : ?>
                        <div class="borders grid info">
                            <b><?=lang('xpj.cashback.can_not_cashback')?></b>
                        </div>
                        <div class="grid btn-panel">
                            <button class="btn disabled">----</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="borders hist">
                    <p><?= lang('player.ui45') ?></p>
                    <div class="entries">
                    </div>

                    <!-- tmpl row -->
                    <div class="ent-row" id="entry_tmpl" style="display: none;">
                        <div class="h-dot"> &#x25b8; </div>
                        <div class="symbols"> +&yen; </div>
                        <div class="amount"> 3,000.00 </div>
                        <div class="recv"> 2018-01-30 12:01 </div>
                        <div class="stat"> 已结算 </div>
                    </div>
                    <!-- end tmpl row -->
                    <!-- empty tmpl -->
                    <div class="ent-row" id="empty_tmpl" style="display: none;" >
                        <div class="mesg-empty"><?= lang('Cashback records not found') ?></div>
                    </div>
                    <!-- end empty tmpl -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <?php /*
    <div class="modal fade promo-modal" id="promodetails_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <img id="promoItemPreviewImg" src="<?=base_url() . $this->utils->getPlayerCenterTemplate()?>/img/promotions/promotion-1L.jpg"/>
                    <h4 class="modal-title" id="myModalLabel"><span id="promoCmsTitleModal" style="text-transform:uppercase"></span> <span class="badge-new" id="badgeNew"><?php echo lang("lang.new") ?></span></h4>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-xs-12">
                            <div id="dateApplied">
                                <p><span><?php echo lang("Date Applied") ?>:</span> <p id="dateAppliedTxt"></p></p>
                            </div>
                            <p><span><?php echo lang("Promo Type") ?>:</span> <p id="promoCmsPromoTypeModal"></p></p>
                            <h4><?php echo lang("sys.description") ?>:</h4>
                            <p id="promoCmsPromoDetailsModal"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" id="promoMsgSec">
                        <center>
                            <p id="promoMsg" style="color: #222;"></p>
                        </center>
                    </div>
                    <?php

                    ?>
                    <div class="applyBtn">
                        <input type="hidden" id="itemDetailsId">

                        <a href="javascript:void(0)" onclick="Promotions.requestPromoNow();" class="requestPromoBtn btn btn-default submit-btn"><?=lang('Claim Now');?></a>
                    </div>
                    <div class="reject-mesg" style="text-align: center; font-weight: bold; color: #222; display: none;">
                    </div>
                    <button type="button" class="btn btn-default submit-btn" id="closeModal" data-dismiss="modal"><?=lang('Close');?></button>
                </div>
            </div>
        </div>
    </div>
    */ ?>

<script type="text/javascript" src="<?= site_url('/resources/js/numeral.min.js?v=' . PRODUCTION_VERSION) ?>"></script>
<script type="text/javascript">
var cb = {};
var button_settle = $('#settle_cashback');
<?php /*
var cbhist_mock = [{"amount":"23060.29","received_at":"2018-01-30 12:01:03","status":"1"},{"amount":"1382.72","received_at":"2018-01-29 12:01:03","status":"1"},{"amount":"1581.74","received_at":"2018-01-28 12:01:03","status":"1"},{"amount":"3052.05","received_at":"2018-01-27 12:01:05","status":"1"},{"amount":"683.07","received_at":"2018-01-26 12:01:04","status":"1"},{"amount":"4259.91","received_at":"2018-01-25 12:01:04","status":"1"},{"amount":"2231.63","received_at":"2018-01-24 12:01:04","status":"1"},{"amount":"2293.72","received_at":"2018-01-23 12:01:03","status":"1"},{"amount":"3980.61","received_at":"2018-01-22 12:01:05","status":"1"},{"amount":"4313.03","received_at":"2018-01-21 12:01:03","status":"1"}];
*/ ?>
var dev = 0;

(function page_load(){
    // window.scrollTo(0, 0);
    var can_user_cashback = Boolean('<?= $can_user_cashback ?>');

    load_cashback_history();

    if (can_user_cashback) {
        update_cashback_stat();

        init_button_settle_cashback();
    }

    // init_button_settle_cashback();
})();

function cash_format(s, prefix) {
    if (typeof(prefix) == 'undefined') {
        prefix = '&yen; ';
    }
    var s_clean = String(s).replace(',', '');
    var s_val = parseFloat(s_clean);
    return prefix + numeral(s_val).format('1,234.56');
}

function dtformat(dts) {
    return dts.replace(/:\d+$/, '');
}

function update_cashback_stat() {
    $('#settle_cashback').addClass('disabled');
    var jqxhr = $.post(
        '/player_center/get_cashback_stat' ,
        { date_type: 'yesterday', cashback_game_platform: null } ,
        function (resp) {
            if (typeof(resp) != 'object') {
                console.log('Error', resp);
                return;
            }

            cb = {
                total_bet: resp.total_bet,
                avail_bet: resp.available_for_cashback_bet,
                avail_cashback: resp.available_cashback,
                period: resp.calculate_time[0]
            };

            $('#cb-total-cb').html(cash_format(cb.avail_cashback));
            $('#cb-total-bet').html(cash_format(cb.total_bet));
            $('#cb-avail-bet').html(cash_format(cb.avail_bet));
            $('.settle-period .start').text(dtformat(cb.period.start));
            $('.settle-period .end').text(dtformat(cb.period.end));
        }
    )
    .fail(function (resp) {
        alert('<?= lang('error.default.message') ?>');
    })
    .always(function (resp) {
        $('#settle_cashback').removeClass('disabled');
    });
}

function load_cashback_history() {
    var page_len = 6;
    var jqxhr = $.get(
        '/api/cashbackHistory_json' ,
        {
            page_len: page_len ,
            page: 0 ,
        } ,
        function (resp) {
            console.log('cb-history', resp);
            var ds = dev ? cbhist_mock.slice(0, page_len) : resp.res;
            show_cashback_history(ds);
        }
    )
    .fail(function (resp) {
        alert('<?= lang('error.default.message') ?>');
    });
}

function show_cashback_history(ds) {
    var panel = $('.borders.hist .entries');
    // $(panel).html('');

    if (!$.isArray(ds)) {
        var empty_row = $('#empty_tmpl').clone().removeAttr('id').css('display', '');
        $(panel).append(empty_row);
        return;
    }

    for (var i in ds) {
        var row = ds[i];
        var stat = row.status;
        // var stat = Math.random() > 0.5 ? true : false;
        var text_stat = stat == 1 ? '<?= lang('Settled') ?>' : '<?= lang('Unsettled') ?>';
        var color_dot = stat == 1 ? '#f6b410' : '#aaa';
        var text_symb = (stat == 1 ? '+' : '') + '&yen;';

        var hrow = $('#entry_tmpl').clone().css('display', '').removeAttr('id');
        $(hrow).find('.stat').text(text_stat);
        $(hrow).find('.symbols').html(text_symb);
        $(hrow).find('.dot').css('color', color_dot);
        $(hrow).find('.amount').text(cash_format(row.amount, ''));
        $(hrow).find('.recv').text(dtformat(row.received_at));

        $(panel).append(hrow);
    }
}

function init_button_settle_cashback() {
    $('#settle_cashback').click( function (e) {
        e.preventDefault();
        var button_settle = $('#settle_cashback');

        // Check amount/bet
        // update_cashback_stat();
        if (cb.avail_bet <= 0 || cb.avail_cashback <= 0) {
            alert('<?= lang("Requires availiable bet or cashback over 0") ?>');
            return;
        }

        // Prevent multiple submitting
        if ($(button_settle).hasClass('disabled')) {
            return;
        }
        $(button_settle).addClass('disabled');

        <?php if ($disable_request == true) : ?>
            alert("<?= lang('Processing scheduled cashback, realtime cashback temporarily unavailable during') ?>\n<?= $disable_start_datetime ?> - <?= $disable_end_datetime ?>");
        <?php endif; ?>

        <?php if ($disable_request == false) : ?>
            var jqxhr = $.post(
                '/player_center/cashbackRequest' ,
                { date_type: 'yesterday', cashback_game_platform: null } ,
                function(resp) {
                    alert(resp.msg);
                }
            )
            .fail(function(resp) {
                alert('<?= lang("Pay cashback failed") ?>')
                console.log('error', resp);
            })
            .always(function(resp) {
                // $(button).removeClass('disabled');
                window.location.href = '/player_center/menu';
            });
        <?php endif; ?>
    });
}

</script>