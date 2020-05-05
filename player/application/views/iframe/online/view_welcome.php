<div class="row bn-wrapper">
    <div class="slider-wrapper" style="padding-right: 0;">
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <?php $cnt=0;
                if (!empty($homemainbanner)){
                    foreach ($homemainbanner as $key) {
                        if($cnt == 0){ ?>
                            <li data-target="#carousel-example-generic" class="active" data-slide-to="<?= $cnt ?>"></li>
                        <?php }else{ ?>
                            <li data-target="#carousel-example-generic" data-slide-to="<?= $cnt ?>"></li>
                        <?php }
                        $cnt++; 
                    }
                } 
            ?>
        </ol>

        <div class="carousel-inner" role="listbox">

            <?php $cnt=0;
                 if(!empty($homemainbanner)){
                  foreach ($homemainbanner as $key) { ?>
                
            <?php   if($cnt == 0){
                    ?>
                      <div class="item active">
                        <img src="<?= PROMOCMSBANNERPATH.$key['bannerName']; ?>"  style="width: 100%; height: 318px;">
                      </div>
              <?php     }else{ ?>
                      <div class="item">
                        <img src="<?= PROMOCMSBANNERPATH.$key['bannerName']; ?>"  style="width: 100%; height: 318px;">
                      </div>

              <?php     } 
                      $cnt++;
                    }
                }
              ?>

            <?php if ( ! $homemainbanner): ?>
                <img src="<?= IMAGEPATH.'/home/welcome-banner.jpg' ?>" style="width: 100%; height: 318px;"/>
            <?php endif ?>
        </div>

        <!-- Controls -->
        <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
        </a>
        </div>
    </div>
    <div class="news-wrapper" style="padding-left: 0;">
        
        <div class="panel panel-default" style="border-radius: 0;">
            <div class="panel-heading text-uppercase" style="font-family: Coolvetica; font-size: 16px; padding: 10px 30px 13px; background-image: url('<?= IMAGEPATH.'/home/news-list-group-header.png' ?>');"><?= lang('wc.01'); ?></div>
            <ul class="list-group">
                <?php foreach ($news as $key => $value): ?>
                    <li class="welcome list-group-item">
                        <label style="font-size: 14px; font-weight: bold; color: #3a7003;"><?= $value['title'] ?></label>
                        <p class="news_item" style="font-size: 12px;">
                            <?= $value['content'] ?>
                        </p>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <?php 
         if(!empty($homesmallbanner)){
            foreach ($homesmallbanner as $key) { ?>
                <div class="games-wrapper">
                    <a href="<?= BASEURL . 'online/casino' ?>"><img class="" src="<?= PROMOCMSBANNERPATH.$key['bannerName']; ?>" alt="Generic placeholder image" style=""></a>
                </div>
    <?php }
    } ?>
</div>
<br/>
<div class="row">
    <div class="hotgames-wrapper">
        <div class="panel panel-og">
            <div class="panel-heading"><?= lang('wc.08'); ?></div>
            <div class="panel-body" style="border-top: 3px solid #366903; height: 263px; padding-top: 25px;">
                <div align="center">
                    <?php 
                         if(!empty($homegamebanner)){
                            foreach ($homegamebanner as $key) { ?>
                                 <div style="display: inline-block; margin:3px;">
                                    <a href="<?= BASEURL.'online/casino' ?>">
                                        <a href="<?= BASEURL . 'online/casino' ?>"><img class="" src="<?= PROMOCMSBANNERPATH.$key['bannerName']; ?>" style="width: 120px; height: 158px;"></a>
                                    </a>
                                    <p></p>
                                    <!-- <p style="font-size: 13.5px; font-weight: bold;">Spiderman</p> -->
                                    <img src="<?= IMAGEPATH.'/home/star.png' ?>">
                                    <img src="<?= IMAGEPATH.'/home/star.png' ?>">
                                    <img src="<?= IMAGEPATH.'/home/star.png' ?>">
                                    <img src="<?= IMAGEPATH.'/home/star.png' ?>">
                                    <img src="<?= IMAGEPATH.'/home/star.png' ?>">
                                </div>
                    <?php }
                         } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="jackpots-wrapper">
        <div class="panel panel-og">
            <div class="panel-heading text-uppercase"><?= lang('wc.09'); ?></div>
            <div class="panel-body">
                <div class="media">
                    <div class="media-body">
                        <h4 class="media-heading page-header text-uppercase"><?= lang('wc.10'); ?></h4>
                        <div style="font-size: 14px; color: #305e04; margin-bottom: 5px;"><strong><object width="200" height="25" data="http://tickers.playtech.com/jackpots/new_jackpot.swf?info=1&casino=playtech&font_face=arial&bold=true&currency=CNY&game=mrj-4"></object></strong></div>
                        <a href="<?= BASEURL.'iframe_module/iframe_casino' ?>" class="btn btn-og text-uppercase"><?= lang('wc.11'); ?></a>
                    </div>
                    <div class="media-right media-top">
                        <img class="media-object" src="<?= IMAGEPATH.'/home/jackpot.png' ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-og">
            <div class="panel-heading text-uppercase"><?= lang('wc.12'); ?></div>
            <div class="panel-body">
                <div clas="media-list">
                <div class="media">
                    <div class="media-body">
                        <h4 class="media-heading"><?= lang('wc.13'); ?> <span class="text-uppercase" style="font-size: 17px; font-weight: bold; color: #1f5501;"><?= lang('wc.14'); ?></h4>
                        <!-- <a href="#" class="btn btn-og text-uppercase"><?= lang('wc.15'); ?></a> -->
                    </div>
                    <div class="media-right media-top">
                        <img class="media-object" src="<?= IMAGEPATH.'/home/vip.png' ?>">
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        setTimeout('news(1)', 30000);
    });
</script>
