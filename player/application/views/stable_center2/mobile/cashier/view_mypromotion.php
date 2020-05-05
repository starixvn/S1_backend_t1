<!-- <div class="mmenu">
    <div class="mm_header">
        <ul>
            <li>选择分类</li>
        </ul>
        <div class="mm_exit"></div>
    </div>
    <div class="mm_main">
        <ul id="prmain">
            <li id="pt0">全部活动<i></i></li>
            <li id="pt6">老虎机优惠<i></i></li>
            <li id="pt1">签到抽奖<i></i></li>
            <li id="pt2">返水优惠<i></i></li>
            <li id="pt3">存款优惠<i></i></li>
            <li id="pt4">往前回顾<i></i></li>
            <li id="pt5">特别活动<i></i></li>
        </ul>
    </div>
</div> -->
<!--头部-->
<!---文字菜单-->
<!-- <div class="pr_nav_promo">
    <div class="click xlmenu" id="prmenu">
        <div class="arrow"></div>
        <ul>
            <li id="title">全部活动</li>
        </ul>
    </div>
</div>
 --><!---文字菜单结束->
<!---顶部结束-->
<!-- <div class="pr_blank"></div> -->
<!---活动---->
<!-- <div id="now" class="show">
    <div class="pr_show threepage cpt5" onclick="document.location='Promotion1.html' " style="display: block;cursor:pointer;">
        <div class="icon"></div>
        <div class="title"><a>EA 十万现金天天赏，全民疯抢iPhone7 Plus</a></div>
        <div class="primage">
            <img src="/<?=$this->utils->getPlayerCenterTemplate(FALSE)?>/images/promotions/ebetprom58.jpg">
        </div>
        <div class="pr_time">2016/9/19 17:07:12</div>
        <div class="lookover">
            <div class="lookover_text"><?=lang('tool.cms05')?></div>
            <div class="lookover_icon"></div>
        </div>
    </div>

    <div class="pr_show threepage cpt4" onclick="document.location='Promotion1.html' ">
        <div class="icon"></div>
        <div class="title"><a>中秋月圆之夜 百万现金 – 惊现免费大魔王！（已结束）</a></div>
        <div class="primage">
            <img src="/<?=$this->utils->getPlayerCenterTemplate(FALSE)?>/images/promotions/promo_ag_dw.jpg">
        </div>
        <div class="pr_time">2016/8/22 14:47:33</div>
        <div class="lookover">
            <div class="lookover_text"><?=lang('tool.cms05')?></div>
            <div class="lookover_icon"></div>
        </div>
    </div>
</div>
 -->
 <div id="now" class="show">
    <?php foreach($playerpromo as $key) { ?>
    <?php if ($key['promoCmsTitle'] != "_SYSTEM_MANUAL") { ?>
    <div class="pr_show threepage cpt5" data-toggle="collapse" data-target="#promo_<?=$key["playerpromoId"]?>" style="display: block;cursor:pointer; padding: 10px; margin-bottom: 0">
        <div class="icon"></div>
        <div class="primage" style="width: 25%">
            <?php
            if(file_exists($this->utils->getPromoThumbnails().$key['promoThumbnail']) && !empty($key['promoThumbnail'])){
                $promoThumbnail = $this->utils->getPromoThumbnailRelativePath() . $key['promoThumbnail'];
            } else {
                if(!empty($key['promoThumbnail'])){
                    $promoThumbnail = $this->utils->imageUrl('promothumbnails/'.$key['promoThumbnail']);
                } else {
                    $promoThumbnail = $this->utils->imageUrl('promothumbnails/default_promo_cms_1.jpg');
                }
            }
            ?>
            <img src="<?=$promoThumbnail?>?v=<?=PRODUCTION_VERSION?>" style="width: 71px; min-height: 53px;">
        </div>
         <div class="title" style="width: 48%; display: block; -webkit-margin-start: 0px; -webkit-margin-end: 0px; font-weight: bold; font-size: 16px;">【<?php echo $key['promoCmsTitle'] ?>】</div>
         <div class="title" style="width: 48%; color: #e50000; font-size: 14px;"><?php echo $key['promoDescription'] ?></div>
         <div class="pr_time"><?=$key['dateApply']?></div>
         <div class="lookover">
            <div class="lookover_text"><?=lang('lang.details')?></div>
            <div class="lookover_icon"></div>
        </div>
    </div>
    <div id="promo_<?=$key["playerpromoId"]?>" class="collapse" style="background-color: #fff;">
        <div class="panel-body" style="border-top: 1px solid #ddd;">
            <div class="lestCo">
                <div class="row">
                    <div class="col-xs-7">
                        <div id="dateApplied">
                                <p><span><?php echo lang("Date Applied") ?>:</span> <p id="dateAppliedTxt"><?=$key['dateApply']?></p></p>
                        </div>
                        <p><span><?php echo lang("Promo Type") ?>:</span> <p id="promoCmsPromoTypeModal"><?=$key['promoTypeName']?></p></p>
                        <h4><?php echo lang("sys.description") ?>:</h4>
                        <p id="promoCmsPromoDetailsModal"><?=$key['promoDescription']?></p>
                    </div>
                    <div class="col-xs-5 amount">
                        <h4><?php echo lang("Bonus Amount") ?>:</h4>
                        <span id="promoCmsBonusAmountModal"><?=$key['bonusAmount']?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php   }?>
    <?php } ?>
</div>
<?=$this->load->view($this->utils->getPlayerCenterTemplate(FALSE) . '/mobile/includes/template_footer');?>

    <script>
        $(document).ready(function () {
            $('#ht').html('<?=lang("header.promotions")?>');

    // var btn = $(".user_btn,.menu_btn,.menu_btn_two");
    // var menu = $(".menu_right,.menu_left");
    // var leftmove = "left_move";
    // var mmove = "menu_move";
    // var leftm = $(".menu_left");
    // var rightm = $(".menu_right");
    // var tap = "click"; //"touchstart";//



    // //下拉菜单
    // $(".xlmenu").click(function () {

    //     var xlmenu = ".mmenu";
    //     var xlmovie = "mmenu_movie"

    //     $(xlmenu).addClass(xlmovie);

    //     var mmenu = $(this).attr("id");

    //     choicemenu()

    //     $(".mm_exit").click(function () {
    //         $(xlmenu).removeClass(xlmovie);

    //     });

    //     function choicemenu() {
    //         $(".mm_main li").unbind("click");
    //         $(".mm_main li").click(function () {
    //             $(".mm_main li i").removeClass("border");
    //             $(this).find("i").addClass("border");

    //             $(xlmenu).removeClass(xlmovie);
    //             var title = $(this).text();

    //             if (mmenu == "prmenu") {

    //                 $("#title").text(title); /*! 优惠选项 */
    //                 var itemindex = $(this).attr("id");
    //                 $(".pr_show").hide();
    //                 if (itemindex == "pt0") {
    //                     $(".pr_show").show();
    //                     $(".cpt4").hide();
    //                 }
    //                 else {
    //                     $(".c" + itemindex).show();
    //                 }
    //             }
    //         });
    //     };
    // });
});
    </script>