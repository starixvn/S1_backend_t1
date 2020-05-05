<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $platformName;?></title>
    <link rel="shortcut icon" href="<?= $this->utils->getPlayerCenterFaviconURL() ?>" type="image/x-icon" />
    <link rel="stylesheet" href="<?= $this->utils->getSystemUrl('player','/resources/third_party/bootstrap/3.3.7/bootstrap.min.css') ?>">
    <script type="text/javascript" src="<?= $this->utils->getSystemUrl('player','/resources/third_party/jquery/jquery-3.1.1.min.js') ?>"></script> 
	<script type="text/javascript" src="<?= $this->utils->getSystemUrl('player','/resources/third_party/bootstrap/3.3.7/bootstrap.js') ?>"></script> 
</head>
<body>
    <!-- Button trigger modal -->
    <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#sucess_modal">
        Launch demo modal
    </button> -->
    
    <!-- Modal -->
    <div class="modal fade" id="sucess_modal" tabindex="-1" role="dialog" aria-labelledby="sucess_modal"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="vertical-alignment-helper">
            <div class="modal-dialog vertical-align-center" role="document">
                <div class="modal-content">
                    <img class="bg-star" src="<?= $this->utils->getSystemUrl('www','/includes/images/stars.png') ?>" alt="">
                    <img class="bg-ray" src="<?= $this->utils->getSystemUrl('www','/includes/images/wheel_ray.png') ?>" alt="">
                    <div class="modal-body">
                        <button type="button" class="close isb-fr-close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="content_data">
                            <h2><?= lang('You have available free rounds')?>!</h2>
                            <p><?= @$player_free_rounds['game_name'];?></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button  type="button" class="btn btn-primary btn-sm fr-accept" data-id="<?= $player_free_rounds['freeround_id']?>"><?= lang('Accept')?>!</button>
                        <button  type="button" class="btn btn-primary btn-sm fr-decline" data-id="<?= $player_free_rounds['freeround_id']?>"><?= lang('Decline')?>!</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<style>
	html,body {
		width: 100%;
		height: 100%;
	}
    body {
        background: #36D1DC;  /* fallback for old browsers */
        background: -webkit-linear-gradient(to right, #5B86E5, #36D1DC);  /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to right, #5B86E5, #36D1DC); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

    }
    @keyframes rotating {
	  from {
	    -ms-transform: rotate(0deg);
	    -moz-transform: rotate(0deg);
	    -webkit-transform: rotate(0deg);
	    -o-transform: rotate(0deg);
	    transform: rotate(0deg); }
	  to {
	    -ms-transform: rotate(360deg);
	    -moz-transform: rotate(360deg);
	    -webkit-transform: rotate(360deg);
	    -o-transform: rotate(360deg);
	    transform: rotate(360deg); } 
	}
    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 560px !important;
        }
    }
    .modal-content {
        border: 0;
        border-radius: 15px;
        background: transparent;
    }
    .modal-body {
        padding: 40px;
        text-align: center;
        background: #272727;
        color: #fff;
        border: 0;
        border-radius: 15px;
    }
    .modal-body h2 {
        text-transform: uppercase;
        font-weight: 700;
        font-size: 37px;
    }
    .modal-body p {
        text-transform: uppercase;
        font-size: 20px;
        padding-top: 20px;
    }
    .modal-body .close {
        position: absolute;
        right: -32px;
        top: -32px;
        padding: 5px 11px 8px;
        border: 2px solid #ffd471;
        border-radius: 50%;
        background: rgba(255,255,255,.8);
        opacity: 1;
    }
    .modal-footer {
        display: flex;
        justify-content: center;
        border-top: 0;
        margin-top: 6px;
    }
    .modal-footer button {
        background: #485563;
        background: -webkit-linear-gradient(to bottom, #29323c, #485563);
        background: linear-gradient(to bottom, #29323c, #485563);
        border: 2px solid #ffd471;
        padding: 13px 35px;
        text-transform: uppercase;
        border-radius: 8px;
    }
    .modal-footer button:hover, .modal-footer button:focus, .modal-footer button:active {
        border: 2px solid #fff2af !important;
        background: #485563;
        background: -webkit-linear-gradient(to top, #29323c, #485563) !important;
        background: linear-gradient(to top, #29323c, #485563) !important;
        box-shadow: none;
    }
    .bg-star {
        position: absolute;
        left: -27px;
        top: -143px;
        z-index: -1;
    }
    .bg-ray {
        position: absolute;
        z-index: -2;
        width: 570px;
        top: -225px;
        left: 0;
        right: 0;
        margin: 0 auto;
        opacity: .3;
        animation: rotating 10s linear infinite;
    }
    .vertical-alignment-helper {
    display:table;
    height: 100%;
    width: 100%;
    pointer-events:none; 
    }
    .vertical-align-center {
        /* To center vertically */
        display: table-cell;
        vertical-align: middle;
        pointer-events:none;
    }
    .modal-content {
        width:inherit;
        max-width:inherit; 
        height:inherit;
        /* To center horizontally */
        margin: 0 auto;
        pointer-events: all;
        box-shadow: none;
    }
    .modal-backdrop.in {
        opacity: .6 !important;
    }
</style>
<script type="text/javascript">
	$(document).ready(function(){
	   $('#sucess_modal').modal('show');
	});

	$(document).on("click",".isb-fr-close",function(){
		var uri = "<?= $url;?>";
		window.location.href=uri;
	});

	$(document).on("click",".fr-accept",function(){
		run('accept',$(this).attr("data-id"));    
	});

	$(document).on("click",".fr-decline",function(){
		run('decline',$(this).attr("data-id"));
	});

	function run(method,id){
		var urlName=document.location.hostname;
	    var prefix= window.location.protocol+'//player.';
	    var urlArr=urlName.split('.');
	    if(urlArr.length>2){
	        //remove first
	        urlArr.shift();
	        urlName=urlArr.join('.');
	    }
	    var game_url = "<?= $url;?>";
		var url = prefix+urlName+"/async/accept_declined_isb_fr/"+method+"/"+id+"/?callback=?";
        $.ajax({
           type: 'GET',
            url: url,
            async: false,
            jsonpCallback: 'jsonCallback',
            contentType: "application/json",
            dataType: 'jsonp',
            success: function(json) {
                // console.log(json);
                if(json.success == true){
                    window.location.href=game_url;
                } else {
                    alert("Something happen. Please try again!");
                }
            }
        });
	}
</script>
</html>