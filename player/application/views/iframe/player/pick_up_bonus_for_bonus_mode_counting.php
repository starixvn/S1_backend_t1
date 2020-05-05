<style type="text/css">
	body {
		background-color:#FF0000;
	}
	.bonus_liondance{
	    /*min-height:600px;*/
	    /*padding-top:20px;*/
	}
</style>
<div class="row bonus_liondance" style="text-align: center;">
	<div class="col-md-12">
<?php

if ($bonus_amount == $this->utils->getConfig('big_random_bonus_amount')) {
	$swf = $this->utils->getConfig('random_bonus_result_50_swf_url');
	$img = $this->utils->getConfig('random_bonus_result_50_img_url');
} else {
	$swf = $this->utils->getConfig('random_bonus_result_1_swf_url');
	$img = $this->utils->getConfig('random_bonus_result_1_img_url');
}

?>

<object type="application/x-shockwave-flash" data="<?php echo $swf; ?>" width="1000" height="700" id="liondance" style="float: none; vertical-align:middle;">
   <param name="movie" value="<?php echo $swf; ?>" />
   <param name="quality" value="high" />
   <param name="bgcolor" value="#ffffff" />
   <param name="play" value="true" />
   <param name="loop" value="true" />
   <param name="wmode" value="transparent" />
   <param name="scale" value="showall" />
   <param name="menu" value="true" />
   <param name="devicefont" value="false" />
   <param name="salign" value="" />
   <param name="allowScriptAccess" value="sameDomain" />
   <a href="javascript:void(0)">
     <center><img src="<?php echo $img; ?>" alt="Lion Dance" width="1000" height="700"/></center>
   </a>
</object>

	</div>
</div>
