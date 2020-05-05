<!-- style {{{1 -->
<style>
.dashboard-stat.red {
    background-color: #e7505a;
}
.dashboard-stat.blue {
    background-color: #3598dc;
}
.dashboard-stat.green {
    background-color: #32c5d2;
}
.dashboard-stat.purple {
    background-color: #8E44AD;
}
.dashboard-stat {
    display: block;
    margin-bottom: 25px;
    overflow: hidden;
    border-radius: 4px;
}
.dashboard-stat .visual {
    width: 80px;
    height: 80px;
    display: block;
    float: left;
    padding-top: 10px;
    padding-left: 15px;
    margin-bottom: 15px;
    font-size: 35px;
    line-height: 35px;
}
.dashboard-stat.blue .visual>i {
    color: #FFF;
    opacity: .1;
    filter: alpha(opacity=10);
}
.dashboard-stat.red .visual>i {
    color: #fff;
    opacity: .1;
    filter: alpha(opacity=10);
}
.dashboard-stat.green .visual>i {
    color: #FFF;
    opacity: .1;
    filter: alpha(opacity=10);
}
.dashboard-stat.purple .visual>i {
    color: #fff;
    opacity: .1;
    filter: alpha(opacity=10);
}
.dashboard-stat .visual>i {
    margin-left: -35px;
    font-size: 110px;
    line-height: 110px;
}

.dashboard-stat .details {
    position: absolute;
    right: 15px;
    padding-right: 15px;
}
.dashboard-stat .details .number {
    color: #fff;
}
.dashboard-stat .details .number {
    padding-top: 25px;
    text-align: right;
    font-size: 34px;
    line-height: 36px;
    letter-spacing: -1px;
    margin-bottom: 0;
    font-weight: 300;
}
.dashboard-stat .details .desc {
    color: #fff;
    opacity: 1;
    filter: alpha(opacity=100);
}
.dashboard-stat .details .desc {
    text-align: right;
    font-size: 16px;
    letter-spacing: 0;
    font-weight: 300;
}
.dashboard-stat.blue .more {
    background-color: #258fd7;
}
.dashboard-stat.red .more {
    background-color: #e53e49;
}
.dashboard-stat.green .more {
    background-color: #2bb8c4;
}
.dashboard-stat.purple .more {
    background-color: #823e9e;
}
.dashboard-stat .more {
    color: #fff;
    clear: both;
    display: block;
    padding: 6px 10px;
    position: relative;
    text-transform: uppercase;
    font-weight: 300;
    font-size: 11px;
    opacity: .7;
    filter: alpha(opacity=70);
}

#chartdiv {
	width	: 100%;
	height	: 500px;
}	
</style>
<!-- style }}}1 -->

<!-- <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>	 -->

<?php
$earning_statistics = "";
$earning_statistics = '{"date": "'.date('Ym').'", "value": 0},';
?>

<!-- container {{{1 -->
<div class="container">
	<br/>

    <!-- sub agents {{{2 -->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat purple">
            <div class="visual">
                <i class="fa fa-user-secret"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span><?=$total_sub_agents;?></span>
                </div>
                <div class="desc"> <?=lang('Sub Agent Statistics');?> </div>
            </div>
            <span class="more"> <?=lang('traffic.today');?>: <strong><?=$today_sub_agents;?></strong> </span>
        </div>
    </div>
    <!-- sub agents }}}2 -->
    <!-- total_players {{{2 -->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat blue">
            <div class="visual">
                <i class="fa fa-users"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span><?=$total_players;?></span>
                </div>
                <div class="desc"> <?=lang('lang.countplayers');?> </div>
            </div>
            <span class="more"> <?=lang('traffic.today');?>: <strong><?=$today_players;?></strong></span>
        </div>
    </div>
    <!-- total_players }}}2 -->
    <!-- total_deposit {{{2 -->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat red">
            <div class="visual">
                <i class="fa fa-credit-card"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span><?php if($total_deposit == '') echo '0.00'; else echo $total_deposit; ?></span>
                </div>
                <div class="desc"> <?=lang('lang.totaldeposit');?> </div>
            </div>
            <span class="more"> <?=lang('traffic.today');?>: <strong><?php if($today_deposit == '') echo '0.00'; else echo $today_deposit; ?></strong> </span>
        </div>
    </div>
    <!-- total_deposit }}}2 -->
    <!-- total_widthraw {{{2 -->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat green">
            <div class="visual">
                <i class="fa fa-money"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span><?php if($total_widthraw == '') echo '0.00'; else echo $total_widthraw; ?></span>
                </div>
                <div class="desc"> <?=lang('lang.totalwithdraw');?> </div>
            </div>
            <span class="more"> <?=lang('traffic.today');?>: <strong><?php if($today_widthraw == '') echo '0.00'; else echo $today_widthraw; ?></strong> </span>
        </div>
    </div>
    <!-- total_widthraw }}}2 -->
    <div class="clearfix"></div>
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title"> <?= lang('lang.earningstatistics'); ?> </h4>
			</div>

			<div class="panel panel-body" id="affiliate_panel_body">
				<div class="row">
					<div id="chartdiv"></div>	
				</div>
			</div>
		</div>
	</div>
</div>
<!-- container }}}1 -->

<script>
var chart = AmCharts.makeChart("chartdiv", {
    "type": "serial",
    "theme": "light",
    "marginRight": 40,
    "marginLeft": 40,
    "autoMarginOffset": 20,
    "dataDateFormat": "YYYYMM",
    "valueAxes": [{
        "id": "v1",
        "axisAlpha": 0,
        "position": "left",
        "ignoreAxisWidth":true
    }],
    "balloon": {
        "borderThickness": 1,
        "shadowAlpha": 0
    },
    "graphs": [{
        "id": "g1",
        "balloon":{
          "drop":true,
          "adjustBorderColor":false,
          "color":"#ffffff"
        },
        "bullet": "round",
        "bulletBorderAlpha": 1,
        "bulletColor": "#FFFFFF",
        "bulletSize": 5,
        "hideBulletsCount": 50,
        "lineThickness": 2,
        "title": "red line",
        "useLineColorForBulletBorder": true,
        "valueField": "value",
        "balloonText": "<span style='font-size:18px;'>[[value]]</span>"
    }],
    "chartScrollbar": {
        "graph": "g1",
        "oppositeAxis":false,
        "offset":30,
        "scrollbarHeight": 80,
        "backgroundAlpha": 0,
        "selectedBackgroundAlpha": 0.1,
        "selectedBackgroundColor": "#888888",
        "graphFillAlpha": 0,
        "graphLineAlpha": 0.5,
        "selectedGraphFillAlpha": 0,
        "selectedGraphLineAlpha": 1,
        "autoGridCount":true,
        "color":"#AAAAAA"
    },
    "chartCursor": {
        "pan": true,
        "valueLineEnabled": true,
        "valueLineBalloonEnabled": true,
        "cursorAlpha":1,
        "cursorColor":"#258cbb",
        "limitToGraph":"g1",
        "valueLineAlpha":0.2
    },
    "valueScrollbar":{
      "oppositeAxis":false,
      "offset":50,
      "scrollbarHeight":10
    },
    "categoryField": "date",
    "categoryAxis": {
        "parseDates": true,
        "dashLength": 1,
        "minorGridEnabled": true
    },
    "export": {
        "enabled": true
    },
    "dataProvider": [<?=$earning_statistics;?>]
});

chart.addListener("rendered", zoomChart);

zoomChart();

function zoomChart() {
    chart.zoomToIndexes(chart.dataProvider.length - 40, chart.dataProvider.length - 1);
}
</script>

<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of create_agent.php
