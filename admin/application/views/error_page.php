<script type="text/javascript">
	$(document).ready(function() {
		var url =  window.location.origin + '<?=$redirect;?>';
		setTimeout(() => {
	 		window.location.replace(url);
		}, 2000);
	});
</script>