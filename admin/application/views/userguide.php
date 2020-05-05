<div class ="container" >
<style>
.userguide-image{padding-bottom:1px;}
</style>
	<?php for($i=1; $i<=87; $i++): ?>
		<?php if ($i < 10 ): ?>
			<img class="userguide-image" style="width:100%;"
			data-src="<?php echo site_url().'resources/images/userguide/SmartbackendUserGuide_2.8.22.1001/SmartbackendUserGuide_2.8.22.1001-page-00'.$i.'.jpg' ?>" 
			data-src-retina="<?php echo site_url().'resources/images/userguide/SmartbackendUserGuide_2.8.22.1001/SmartbackendUserGuide_2.8.22.1001-page-00'.$i.'.jpg' ?>"
			src="<?php echo site_url().'resources/images/userguide/SmartbackendUserGuide_2.8.22.1001/SmartbackendUserGuide_2.8.22.1001-page-00'.$i.'.jpg' ?>" />
	    <?php else: ?>
			<img class="userguide-image" style="width:100%;"
			  data-src="<?php echo site_url().'resources/images/userguide/SmartbackendUserGuide_2.8.22.1001/SmartbackendUserGuide_2.8.22.1001-page-0'.$i.'.jpg' ?>"
			  data-src-retina="<?php echo site_url().'resources/images/userguide/SmartbackendUserGuide_2.8.22.1001/SmartbackendUserGuide_2.8.22.1001-page-0'.$i.'.jpg' ?>"
			   src="<?php echo site_url().'resources/images/userguide/SmartbackendUserGuide_2.8.22.1001/SmartbackendUserGuide_2.8.22.1001-page-0'.$i.'.jpg' ?>" 
			 />
		<?php endif; ?>
	<?php endfor; ?>
</div>
