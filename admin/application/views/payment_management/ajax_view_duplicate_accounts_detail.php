<html>
    <head>
        <title><?php echo $title; ?></title>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/t/bs-3.3.6/jq-2.2.0,jszip-2.5.0,pdfmake-0.1.18,dt-1.10.11,af-2.1.1,b-1.1.2,b-colvis-1.1.2,b-flash-1.1.2,b-html5-1.1.2,b-print-1.1.2,cr-1.3.1,fc-3.2.1,fh-3.1.1,kt-2.1.1,r-2.0.2,rr-1.1.1,sc-1.4.1,se-1.1.2/datatables.min.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/t/bs-3.3.6/jq-2.2.0,jszip-2.5.0,pdfmake-0.1.18,dt-1.10.11,af-2.1.1,b-1.1.2,b-colvis-1.1.2,b-flash-1.1.2,b-html5-1.1.2,b-print-1.1.2,cr-1.3.1,fc-3.2.1,fh-3.1.1,kt-2.1.1,r-2.0.2,rr-1.1.1,sc-1.4.1,se-1.1.2/datatables.min.js"></script>

    </head>
    <body>
        <h1 align = 'center'><?php echo $title; ?></h1>

<div class="col-md-12">
    <table id="duplicateTable" class="display" cellspacing="0" width=100%>
        <thead>
            <th class="col-md-1"> username(Similar: <?= $this->duplicate_account->getRating('username', 'rate_similar'); ?>) </th>
            <th class="col-md-1"> realname(Exact: <?= $this->duplicate_account->getRating('realname', 'rate_exact') ?> / Similar: <?= $this->duplicate_account->getRating('realname', 'rate_similar'); ?>) </th>
            <th class="col-md-1"> password(Exact: <?= $this->duplicate_account->getRating('password', 'rate_exact'); ?>) </th>
            <th class="col-md-1"> email(Similar: <?= $this->duplicate_account->getRating('email', 'rate_similar'); ?>) </th>
            <th class="col-md-1"> mobile(Exact: <?= $this->duplicate_account->getRating('phone', 'rate_exact'); ?>) </th>
            <th class="col-md-1"> address(Exact: <?= $this->duplicate_account->getRating('address', 'rate_exact'); ?>) </th>
            <th class="col-md-1"> city(Exact: <?= $this->duplicate_account->getRating('city', 'rate_exact'); ?>) </th>
            <th class="col-md-1"> cookie(Exact: <?= $this->duplicate_account->getRating('cookie', 'rate_exact'); ?>) </th>
            <th class="col-md-1"> referrer(Exact: <?= $this->duplicate_account->getRating('referrer', 'rate_exact'); ?>) </th>
            <th class="col-md-1"> device(Exact: <?= $this->duplicate_account->getRating('user_agent', 'rate_exact'); ?>) </th>
            <th class="col-md-1"> regIP(Exact: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
            <th class="col-md-1"> lastLoginIP(Exact: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
            <th class="col-md-1"> dpstIP(Exact: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
            <th class="col-md-1"> withdrwIP(Exact: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
            <th class="col-md-1"> transMainToSubIP(Exact: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
            <th class="col-md-1"> transSubToMainIP(Exact: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
            <th class="col-md-1"> totalRate</th>
        </thead>
    </table>
</div>

<script type="text/javascript">
$(document).ready(function() {
    var playerId = <?=$player_id ?>;
    $('#duplicateTable').DataTable({
        order: [[16, 'desc']],
        ajax: function (data, callback, settings) {
            $.post('http://admin.og.local/index.php/api/dup_accounts_detail/' + playerId, data, function(data) {
            //$.post(base_url + 'api/dup_accounts_detail/' + playerId, data, function(data) {
                callback(data);
            },'json');
        },
            /*
        "columns": [
                { "data": "username" },
                { "data": "realname" },
                { "data": "password" },
                { "data": "email" },
                { "data": "phone" },
                { "data": "address" },
                { "data": "city" },
                { "data": "cookie" },
                { "data": "referrer" },
                { "data": "device" },
                { "data": "ip1" },
                { "data": "ip2" },
                { "data": "ip3" },
                { "data": "ip4" },
                { "data": "ip5" },
                { "data": "ip6" },
                { "data": "totalRate" }
            ]
             */
    });

    $("[data-toggle=popover]").popover({html:true});
} );
</script>

            <br><hr>
            <em>&copy; smartbackend 2016</em>
        </body>
</html>
