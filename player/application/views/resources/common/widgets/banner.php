<div id="<?=$widget_id?>" class="t1t-widget widget-banner carousel slide <?=($options['indicators']) ? '' : 'hidden-indicators'?> <?=(count($banner_list)) ? '' : 'hidden'?>" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
        <li data-target="#carousel-example-generic" data-slide-to="2"></li>
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox">
        <?php foreach($banner_list as $banner): ?>
        <div class="item">
            <img src="<?=$banner['banner_img_url']?>" alt="<?=$banner['title']?>">
            <div class="carousel-caption">
                <?php if(!empty($banner['title'])): ?>
                    <h3><a href="<?=((empty($banner['link'])) ? 'javascript: void(0);' : $banner['link'])?>" target="<?=$banner['link_target']?>"><?=$banner['title']?></a></h3>
                <?php endif ?>
                <?php if(!empty($banner['summary'])): ?>
                <p><?=$banner['summary']?></p>
                <?php endif ?>
            </div>
        </div>
        <?php endforeach ?>
    </div>

    <!-- Controls -->
    <a class="left carousel-control" href="#<?=$widget_id?>" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only"><?=lang('Previous')?></span>
    </a>
    <a class="right carousel-control" href="#<?=$widget_id?>" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only"><?=lang('Next')?></span>
    </a>
</div>
<script type="text/javascript">
    if($('#<?=$widget_id?> .carousel-inner .item').length){
        $($('#<?=$widget_id?> .carousel-inner .item')[0]).addClass('active');
    }
    $('#<?=$widget_id?>').carousel({
        interval: <?=$options['interval']?>
    });
</script>