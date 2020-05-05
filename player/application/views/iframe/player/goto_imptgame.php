<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <!-- <script type="text/javascript" src="<?php echo $api_play_pt; ?>/integrationjs.php"></script> -->
    <!-- <script type="text/javascript" src="<?php echo $api_play_pt; ?>/services/js/1/integration.conf.js"></script> -->
    <script type="text/javascript" src="<?php echo $api_play_js; ?>"></script>

    <script type="text/javascript" src="<?php echo $this->utils->playerResUrl('jquery-1.11.3.min.js?v='.PRODUCTION_VERSION); ?>"></script>
</head>
<body>
<div>正在载入... <a href="javascript:window.location.reload();" class='retry_link' style='display:none'>重试</a> </div>
<script type="text/javascript">

function getParam(val) {
    var result = "",
        tmp = [];
    var items = location.search.substr(1).split("&");
    for (var index = 0; index < items.length; index++) {
        tmp = items[index].split("=");
        if (tmp[0] === val) result = decodeURIComponent(tmp[1]);
    }
    return result;
}

var gameCode="<?php echo $game_code; ?>";
var lang="<?php echo $lang; ?>";
var api_play_pt="<?php echo $api_play_pt; ?>";
var player_url="<?php echo $player_url; ?>";
var mobile="<?php echo ( isset( $mobile ) ) ? $mobile : ''; ?>";
var mobile_systemId="<?php echo ( isset( $mobile_systemId ) ) ? $mobile_systemId : ''; ?>";

var ptjs = document.createElement('script');
ptjs.setAttribute("type","text/javascript");
ptjs.setAttribute("src", "<?=$this->utils->playerResUrl('impt.js');?>");
document.body.appendChild(ptjs);

</script>

</body>
</html>
