<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,minimum-scale=1,maximum-scale=1" content="user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo @$platformName; ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!-- <link rel="shortcut icon" href="/<?=$this->utils->getPlayerCenterTemplate()?>/fav.ico" type="image/x-icon" /> -->
<link rel="shortcut icon" href="<?= $this->utils->getPlayerCenterFaviconURL() ?>" type="image/x-icon" />


</head>
<body>
<script type="text/javascript">
    //线路
    var hostData = <?php echo json_encode(@$host_data);?>

    //登入
    var loginUrl = "<?php echo @$url; ?>";
    

    function imgLoad(data) {
        this.errCount = 0
        this.retryCount = 0
        this.maxRetry = 5
        this.data = data
        this.count = data.length
        this.imgNotLoad = true
        this.imgonLoad = this.imgonLoad.bind(this)
        this.imgonLoadError = this.imgonLoadError.bind(this)
        this.reset = this.reset.bind(this)
        this.test = this.test.bind(this)
        this.img = []
        this.test()

    }
    imgLoad.prototype = {
        reset: function() {
            this.retryCount++
            this.errCount = 0
            this.imgNotLoad = true
            this.img = []
            this.test()
        },
        test: function() {
            for (var i = 0; i < this.data.length; i++) {
                this.img[i] = new Image()
                this.img[i].onload = this.imgonLoad.bind(this, this.data[i].host + loginUrl)
                this.img[i].onerror = this.imgonLoadError
                this.img[i].src = this.data[i].host + '/favicon.ico?time=' + +new Date()
            }
        },
        imgonLoad: function(url) {
            if (this.imgNotLoad) {
                this.imgNotLoad = false
                window.location.href = url
            }
        },
        imgonLoadError: function(b) {
            if (++this.errCount === this.count) {
                if (this.retryCount < this.maxRetry) {
                    this.reset()
                } else {
                    alert('所有线路异常，请稍后再试。')
                }
            }
        }
    }
    new imgLoad(hostData)
</script>

</body>
</html>
