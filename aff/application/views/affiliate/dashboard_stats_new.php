<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="dashboard-stat blue players">
        <div class="visual"> <i class="fa fa-users"></i> </div>
        <div class="details">
            <div class="number"> <span class="total"><?=$total_players;?></span> </div>
            <div class="desc"> <div class="desc"> <?=lang('lang.countplayers');?> </div> </div>
        </div>
        <span class="more">&nbsp;</span>
    </div>
</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="dashboard-stat red active_players">
        <div class="visual"> <i class="fa fa-sign-in"></i></div>
        <div class="details">
            <div class="number"> <span class="today"><i class="fa fa-spinner fa-pulse"></i></span> </div>
            <div class="desc"> <?=lang('today.active');?> </div>
        </div>
        <span class="more">
            <?=lang('This Month');?>: <strong class="this_month"><i class="fa fa-spinner fa-pulse"></i></strong>
        </span>
    </div>
</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="dashboard-stat green deposit">
        <div class="visual"> <i class="fa fa-credit-card"></i></div>
        <div class="details">
            <div class="number"> <span class="today"><?= $today_deposit ?></span> </div>
            <div class="desc"> <?=lang('today.deposit');?> </div>
        </div>
        <span class="more">
            <?=lang('This Month');?>: <strong class="this_month"><i class="fa fa-spinner fa-pulse"></i></strong>
        </span>
    </div>
</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="dashboard-stat purple withdraw">
        <div class="visual"> <i class="fa fa-money"></i> </div>
        <div class="details">
            <div class="number"> <span class="today"><?= $today_withdraw ?></span> </div>
            <div class="desc"> <?=lang('today.withdraw');?> </div>
        </div>
        <span class="more">
            <?=lang('This Month');?>: <strong class="this_month"><i class="fa fa-spinner fa-pulse"></i></strong>
        </span>
    </div>
</div>

<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="dashboard-stat yellow-gold gross_rev">
        <div class="visual"> <i class="fa fa-area-chart"></i> </div>
        <div class="details">
            <div class="number"> <span class="today"><i class="fa fa-spinner fa-pulse"></i></span> </div>
            <div class="desc"> <div class="desc"> <?=lang('today.gross');?> </div> </div>
        </div>
        <span class="more">
            <?=lang('This Month');?>: <strong class="this_month"><i class="fa fa-spinner fa-pulse"></i></strong>
        </span>
    </div>
</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="dashboard-stat red-pink bonus">
        <div class="visual"> <i class="fa fa-birthday-cake"></i></div>
        <div class="details">
            <div class="number"> <span class="today"><i class="fa fa-spinner fa-pulse"></i></span> </div>
            <div class="desc"> <?=lang('today.bonus');?> </div>
        </div>
        <span class="more">
            <?=lang('This Month');?>: <strong class="this_month"><i class="fa fa-spinner fa-pulse"></i></strong>
        </span>
    </div>
</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="dashboard-stat blue-soft tx_fee">
        <div class="visual"> <i class="fa fa-bar-chart"></i></div>
        <div class="details">
            <div class="number"> <span class="today"><i class="fa fa-spinner fa-pulse"></i></span> </div>
            <div class="desc"> <?=lang('today.transaction');?> </div>
        </div>
        <span class="more">
            <?=lang('This Month');?>: <strong class="this_month"><i class="fa fa-spinner fa-pulse"></i></strong>
        </span>
    </div>
</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="dashboard-stat green-haze net_rev">
        <div class="visual"> <i class="fa fa-pie-chart"></i> </div>
        <div class="details">
            <div class="number"> <span class="today"><i class="fa fa-spinner fa-pulse"></i></span> </div>
            <div class="desc"> <?=lang('today.net');?> </div>
        </div>
        <span class="more">
            <?=lang('This Month');?>: <strong class="this_month"><i class="fa fa-spinner fa-pulse"></i></strong>
        </span>
    </div>
</div>
<script>
    var GET = new Get_vars();
    var req_id = rand_req_id();
    // console.log('req_id', req_id);

    $(document).ready(function() {
        dashboard_load();
    });

    /**
     * Recursively loads data by ajax for all dashboard parts
     * @param   int     part        dashboard part number (1, 2, 3)
     * @param   bool    recursive   loads only specified part if false.  Defaults to true.
     * @return  none
     */
    function dashboard_load(part, recursive) {
        if (typeof(part) == 'undefined')        { part = 1; }
        if (typeof(recursive) == 'undefined')   { recursive = true; }

        // Build ajax URL, accepts GET var force
        var datasource_url = '/affiliate/aff_dashboard_new_ajax/' + part;
        var args = { req_id: req_id };
        if (GET.has('refresh')) { args.refresh = true; }

        $.get( datasource_url, args )
        .success(function (resp) {
            if (resp.success != true) {
                console.log(resp.result);
                return;
            }

            // console.log('timing', resp.timing);
            console.log('Dashboard part', part, '_pid_load_', resp.timing.pidg, '_calc_', resp.timing.calc);
            dashboard_render(part, resp.result);

            if (part < 3 && recursive == true) {
                return dashboard_load(part + 1);
            }
        })
        .fail( function (jqxhr, status_text)  {
            if ( jqxhr.status >= 300 && jqxhr.status < 500 ) {
                if (confirm('<?= lang('session.timeout') ?>')) {
                    window.location.href = '/';
                }
            }
            else {
                alert(status_text);
            }
        });
    } // End function dashboard_load()

    /**
     * Renders specified dashboard part
     * @param   int     part       dashboard part number (1, 2, 3)
     * @param   object  dset       dataset for given dashboard part
     * @return  none
     */
    function dashboard_render(part, dset) {
        switch (part) {
            case 1 :
                $('.active_players .today').text(dset.active_players_today);
                $('.active_players .this_month').text(dset.active_players_this_month);
                $('.deposit     .this_month').text(dset.deposit_this_month);
                $('.withdraw    .this_month').text(dset.withdraw_this_month);
                break;

            case 2 :
                $('.gross_rev   .today').text(dset.gross_rev_today);
                $('.bonus       .today').text(dset.bonus_today);
                $('.tx_fee      .today').text(dset.tx_fee_today);
                $('.net_rev     .today').text(dset.net_rev_today);
                break;

            case 3 :
                $('.gross_rev   .this_month').text(dset.gross_rev_this_month);
                $('.bonus       .this_month').text(dset.bonus_this_month);
                $('.tx_fee      .this_month').text(dset.tx_fee_this_month);
                $('.net_rev     .this_month').text(dset.net_rev_this_month);
                break;

            default :
                console.log('Illegal value for part');
        }

    } // End function dashboard_render()

    /**
     * GET variable class
     * members: has(key)    bool    returns true if GET vars has given key.  Otherwise false.
     *          val(key)    string  returns value of given key in GET vars.
     *          all()       object  returns all get vars as an object.
     */
    function Get_vars() {
        var get = {};

        function construct() {
            parse();
        }

        function parse() {
            var search = location.search.substr(1);
            // Return when search string empty, or parse result will contain an empty string ("")
            if (search.length == 0) { return; }
            var groups = search.split('&');

            for (var i in groups) {
                var pair = groups[i].split('=');
                var key = pair[0];
                var val = pair.length > 1 ? pair[1] : null;
                get[key] = val;
            }
        }

        // function has(key) { return typeof(get[key]) != 'undefined'; }
        function has(key) { return Object.keys(get).indexOf(key) >= 0; }
        function val(key) { return get[key]; }
        function all() { return get; }

        construct();

        return { has: has, val: val, all: all };
    } // End function get_vars()

    /**
     * Generate random request ID, grouping multiple ajax requests in a same page reload
     * @param   none
     * @return  string  request ID
     */
    function rand_req_id() {
        var chunks = [];
        for (var i=0; i<3; ++i) { chunks.push(rand_alnum()); }
        return chunks.join('_');
    }

    /**
     * generates random 4-place alphanumeric string /[0-9a-z]{4}/
     * @return  string
     */
    function rand_alnum() {
        // return Math.round(Math.random() * 0x7c000000 + 0x4000000).toString(36);
        return Math.round(Math.random() * 0x18ea00 + 0xb700).toString(36);
    }
</script>