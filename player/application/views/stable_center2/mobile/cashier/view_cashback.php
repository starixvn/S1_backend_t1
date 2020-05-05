<?php /* begin body */ ?>
<div class="cb-mob">
    <div class="leading">
        <div class="avail-cb figure">
            &yen; &hellip;&hellip;
        </div>
        <div class="avail-cb title">
            <?= lang('Available Cashback Amount') ?>
        </div>
        <div class="bets-frame">
            <div class="bets total">
                <div class="figure">
                    &yen; &hellip;&hellip;
                </div>
                <div class="title">
                    <?= lang('Total Bet') ?>
                </div>
            </div>
            <div class="bets avail">
                <div class="figure">
                    &yen; &hellip;&hellip;
                </div>
                <div class="title">
                    <?= lang('Bet Available for Cashback') ?>
                </div>
            </div>
        </div>
    </div>
    <div class="following">
        <div class="cb-period">
            <?php if ($can_user_cashback && empty($cashback_request)) : ?>
                <?= lang('Cashback Calculation Period') ?>:
                <span class="start">&hellip;&hellip;&hellip;&hellip;</span>
                &ndash;
                <span class="end">&hellip;&hellip;&hellip;&hellip;</span>
            <?php endif; ?>
            <?php if ($can_user_cashback && !empty($cashback_request)) : ?>
                <div class="req-row">
                    <?= lang('xpj.cashback.pending_cashback_not_finished') ?>
                </div>
                <div class="req-row">
                    <div class="req title"><?=lang('xpj.cashback.request_datetime')?></div>
                    <div class="req text">2018-02-06 23:59</div>
                </div>
                <div class="req-row">
                    <div class="req title"><?=lang('xpj.cashback.request_amount')?></div>
                    <div class="req text">&yen; 2,054.16</div>
                </div>
            <?php endif; ?>
            <?php if (!$can_user_cashback) : ?>
                <?=lang('xpj.cashback.can_not_cashback')?>
            <?php endif; ?>
        </div>
        <div class="cb-button">
            <button type="button" class="login_btn disabled" id="settle_cashback">
                <?= lang('Settle Now') ?>
            </button>
        </div>
        <div class="cb-hist-frame">
            <div class="title">
                <?= lang('player.ui45') ?>
            </div>
            <div class="entries">

            </div>
            <!-- tmpl row -->
            <div class="row" id="entry_tmpl" style="display: none;">
                <div class="vline"> &nbsp; </div>
                <div class="dot"> &#x25cf; </div>
                <div class="stat"> 已结算 </div>
                <div class="symbols"> +&yen; </div>
                <div class="amount"> 3,000.00 </div>
                <div class="recv"> yyyy-mm-dd </div>
            </div>
            <!-- end tmpl row -->
            <div class="row empty" id="empty_tmpl" style="display: none;">
                <?= lang('Cashback records not found') ?>
            </div>
        </div>
    </div>
</div>
<?php /* end body */ ?>

<?php $this->load->view("{$this->utils->getPlayerCenterTemplate()}/includes/menu_bar"); ?>
<script type="text/javascript" src="<?= site_url('/resources/js/numeral.min.js?v=' . PRODUCTION_VERSION) ?>"></script>
<script type="text/javascript">

var dev = 0;
<?php /*
var cbhist_mock = [{"amount":"23060.29","received_at":"2018-01-30 12:01:03","status":"1"},{"amount":"1382.72","received_at":"2018-01-29 12:01:03","status":"1"},{"amount":"1581.74","received_at":"2018-01-28 12:01:03","status":"1"},{"amount":"3052.05","received_at":"2018-01-27 12:01:05","status":"1"},{"amount":"683.07","received_at":"2018-01-26 12:01:04","status":"1"},{"amount":"4259.91","received_at":"2018-01-25 12:01:04","status":"1"},{"amount":"2231.63","received_at":"2018-01-24 12:01:04","status":"1"},{"amount":"2293.72","received_at":"2018-01-23 12:01:03","status":"1"},{"amount":"3980.61","received_at":"2018-01-22 12:01:05","status":"1"},{"amount":"4313.03","received_at":"2018-01-21 12:01:03","status":"1"}];
*/ ?>
var cb = {};
var enable_cashback = Boolean(<?= $can_user_cashback && empty($cashback_request) ?>);

(function page_load(){
    // Set up page title
    $('#ht').html('<?= lang('Realtime Cashback') ?>');

    window.scrollTo(0, 0);

    load_cashback_history();

    if (enable_cashback) {
        update_cashback_stat();

        init_button_settle_cashback();
    }

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

            $('.avail-cb.figure').html(cash_format(cb.avail_cashback));
            $('.bets.total .figure').html(cash_format(cb.total_bet));
            $('.bets.avail .figure').html(cash_format(cb.avail_bet));
            $('.cb-period .start').text(dtformat(cb.period.start));
            $('.cb-period .end').text(dtformat(cb.period.end));
        }
    )
    .fail(function (resp) {
        alert('<?= lang('error.default.message') ?>');
    })
    .always(function (resp) {
        $('#settle_cashback').removeClass('disabled');
    });
}

 function show_cashback_history(ds) {
    var panel = $('.cb-hist-frame .entries');
    $(panel).html('');
    if (!Array.isArray(ds) || ds.length == 0) {
        var empty_row = $('#empty_tmpl').clone().removeAttr('id').css('display', '');
        $(panel).append(empty_row);
    }
    for (var i in ds) {
        var row = ds[i];
        var stat = row.status;
        // var stat = Math.random() > 0.5 ? true : false;
        var text_stat = stat == 1 ? '<?= lang('Settled') ?>' : '<?= lang('Unsettled') ?>';
        var color_dot = stat == 1 ? '#f6b410' : '#aaa';
        var text_symb = (stat == 1 ? '+ ' : '') + '&yen;';

        var hrow = $('#entry_tmpl').clone().css('display', '').removeAttr('id');
        $(hrow).find('.stat').text(text_stat);
        $(hrow).find('.symbols').html(text_symb)
        $(hrow).find('.dot').css('color', color_dot);
        $(hrow).find('.amount').text(cash_format(row.amount, ''));
        $(hrow).find('.recv').text(dtformat(row.received_at));

        $(panel).append(hrow);
    }
}


function load_cashback_history() {
    var page_len = 7;
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
