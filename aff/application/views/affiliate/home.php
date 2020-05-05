<div class="jumbotron">
    <div class="container">
        <div class="col-md-12" style="text-align:center; margin-top:-20px;">
            <?php if($login == null) { ?>
          <!-- <p><a class="btn btn-primary btn-lg" href="<?= BASEURL . 'affiliate/register'?>" role="button">Register Now &raquo;</a></p> -->
                <a href="<?= BASEURL . 'affiliate/register'?>" ><img src="<?= IMAGEPATH.'affiliate_banner.png' ?>" width="1070"></a>
            <?php }else{ ?>
                <img src="<?= IMAGEPATH.'affiliate_banner.png' ?>">
            <?php } ?>
             <br/>
        </div>
        
    </div>
</div>

<div class="container">
    <!-- Example row of columns -->
    <div class="row">
      <div class="col-md-12" style="margin:0px 20px 0px 20px;">
        <div class="col-md-4">
          <!-- <h2>How It Works</h2>
          <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p> -->
          <a href="#" ><img src="<?= IMAGEPATH.'mini_panel1.png' ?>"></a>
        </div>
        <div class="col-md-4">
          <!-- <h2>FAQs</h2>
          <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p> -->
          <a href="#" ><img src="<?= IMAGEPATH.'mini_panel2.png' ?>"></a>
        </div>
        <div class="col-md-4">
          <!-- <h2>Heading</h2>
          <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
          <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p> -->
          <a href="#" ><img src="<?= IMAGEPATH.'mini_panel3.png' ?>">
        </div>
      </div>
    </div>
</div> <!-- /container -->
<br/>
