<html>

<head>
	<script src="/resources/js/jquery.min.js"></script>
</head>
<body>

<div>
index: <span id="index">0</span>
</div>
<div>
last is: <span id="last_index"></span>
</div>
<div>
<input type="button" id="next" value="Next">
</div>

<div>
reset index to <input type="text" id="reset_index" value=""> <input type="button" id="reset_btn" value="Do it">
</div>

<script type="text/javascript">

$("#next").click(function(){
	next();
});

$("#reset_btn").click(function(){
    reset_index();
});

function next(){
	//open window
	window.open("/test_page/launch_ag/"+all['username'][idx]);
	idx++;
	$('#index').html(idx);
	localStorage.setItem('currentIndex', idx);
}

function reset_index(){
    var reset=parseInt($("#reset_index").val(), 10);

    localStorage.setItem('currentIndex', reset);

    window.location.reload();
}

function restoreIndex(){
	var currentIndex=localStorage.getItem('currentIndex');
	if(currentIndex!=null){
		idx=currentIndex;
		$('#index').html(idx);
	}

	$('#last_index').html(all['username'].length);
}

var idx=0;

var all=<?=file_get_contents(dirname(__FILE__).'/../../doc/query_last_6months_played_game.json')?>;

restoreIndex();

</script>
<body>
</html>
