<html>
<head></head>
<body>
Loading...
<?php echo $form_html;?>
<script type="text/javascript">
(function(){
	//submit
	var frm=document.getElementById('<?php echo $form_id;?>');
	var submit_btn_id = 'submit_form_btn_' + '<?php echo $form_id;?>'
	var submit_btn=document.getElementById(submit_btn_id);

	submit_btn.addEventListener('click', function(e) {
		var submittedClass = 'submitted';
		if (this.classList.contains(submittedClass)) {
			e.preventDefault();
		} else {
			this.classList.add(submittedClass);
			frm.submit();
		}
	}, false);

	submit_btn.click();
})();
</script>
</body>
</html>
