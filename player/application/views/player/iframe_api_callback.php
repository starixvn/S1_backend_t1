<html>
<body>
<script>
var result=<?php echo $result; ?>;
parent.postMessage(JSON.stringify(result),'<?php echo $origin; ?>');
</script>
</body>
</html>
