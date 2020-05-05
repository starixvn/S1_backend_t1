<?php
  $favorites = $this->utils->get_favorites();
  $recently_played = $this->utils->get_recently_played();

  // list of api which game code is in attribute field - for game launch purpose
  $apiGameCodeInAttribute = array(PNG_API);
?>
<div id="favorite-games" class="tab-pane main-content">
  <h1><?= lang('Favorite Games') ?></h1>
  <!-- Nav tabs -->
  <ul class="fm-ul row" role="tablist">
    <li class="col-xs-6 col-sm-3 active"><a href="#favourites" aria-controls="mypromo" role="tab" data-toggle="tab"><?= lang('Favourites') ?></a></li>
    <li class="col-xs-6 col-sm-3"><a href="#recently-played" aria-controls="allpromo" role="tab" data-toggle="tab"><?= lang('Recently Played') ?></a></li>
  </ul>
  <div class="tab-content">
    <!-- ============= Favourites CONTENT ============= -->
    <div role="tabpanel" class="tab-pane active" id="favourites">
      <div class="row">
        <?php $index = 0; ?>
        <?php foreach ($favorites as $game): ?>
          <div class="col-sm-4">
            <div class="shop-content">
              <div class="shop-header">
                <h1 class="title-name"><?=lang($game['name'])?></h1>
                <img src="<?=isset($game['image']) ? str_replace("..","",$game['image']) : 'http://placehold.it/300x225?text=' . lang($game['name']) ?>" alt="<?=$game['name']?>" /> 
              </div>
              <div class="shop-body clearfix">
                <div class="col-xs-6 text-right">
                  <a href="<?=$game['url']?>" target="_blank" class="btn"><?= lang('Play') ?></a>
                </div>
                <div class="col-xs-6 text-right"><span class="glyphicon glyphicon-star fav-game favorite-game" data-toggle="modal" data-target="#favorite_games_myfavorite" data-url="<?=$game['url']?>"></span></div>
              </div>
            </div>
          </div>
          <?php if (($index + 1) % 3 == 0) : ?>
            </div>
            <div class="row">
          <?php endif; ?>
          <?php $index++; ?>
        <?php endforeach ?>
      </div>
    </div>
    <!-- ============= recently played CONTENT ============= -->
    <div role="tabpanel" class="tab-pane" id="recently-played">
      <div class="row">
        <?php $index = 0; ?>
        <?php foreach ($recently_played as $game): ?>
           <?php          
            $gameId = $game['id'];
            $gameName = $game['game_name'];
            $gameCode = isset($game['game_code']) ? $game['game_code'] : null;
            $imgloc = isset($game['image_location']) ? $game['image_location'] : null;

            if (in_array($game['game_platform_id'], $apiGameCodeInAttribute)) {
               $gameCode = $game['attributes'];
            }
          ?>
          <div class="col-sm-4">
            <div class="shop-content">
              <div class="shop-header">
                <h1 class="title-name"><?=lang($game['game_name'])?></h1>
                <img id="img<?=$game['id']?>" class="img-recently-played" data-gameid="<?=$gameId?>" data-gamecode="<?=$gameCode?>" data-gamename="<?=lang($gameName)?>" data-imageloc="<?=$imgloc?>" src="" alt="<?=lang($game['game_name'])?>" />
              </div>
              <div class="shop-body clearfix">
                <div class="col-xs-6 text-right">
                  <a href="<?=$game['url']?>" target="_blank" class="btn"><?= lang('Play') ?></a>
                </div>
                <div class="col-xs-6 text-right"><span id="favIcon<?=$gameId?>" class="glyphicon <?=$game['favorite'] ? 'glyphicon-star' : 'glyphicon-star-empty'?> fav-game recently-played" 
                  data-name="<?=lang($game['game_name'])?>"
                  data-image="<?=isset($game['image']) ? $game['image'] : 'http://placehold.it/300x225?text=' . lang($game['game_name']) ?>"
                  data-url="<?=$game['url']?>"
                ></span></div>
              </div>
            </div>
          </div>
          <?php if (($index + 1) % 3 == 0) : ?>
            </div>
            <div class="row">
          <?php endif; ?>
          <?php $index++; ?>
        <?php endforeach ?>
      </div>
    </div>
  </div>
</div>

<div class="modal fade favorite-games-modal" id="favorite_games_myfavorite" tabindex="-1" role="dialog" aria-labelledby="msgModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
          <!-- <div class="modal-header"></div> -->         
          <div class="panel-body fav-game-myfavorite">
            <span><?= lang('confirm.delete') ?></span>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-primary" id ="remove_fvg_btn"><?= lang('Confirm') ?></button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('lang.cancel') ?></button>
           </div>
            
        </div>
    </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $('.img-recently-played').each(function() {
      var objImage = $(this);
      var gameId = $(this).data('gameid');
      var gameCode = $(this).data('gamecode');
      var gameName = $(this).data('gamename');
      var imgloc = $(this).data('imageloc');
      var objFavorite = $('#favIcon'+gameId);

      setImagePath(objImage, objFavorite, imgloc, gameCode,  gameName, 'png');
      setImagePath(objImage, objFavorite, imgloc, gameCode,  gameName, 'jpg');
    });
  });

  function setImagePath(objImage, objFavorite, imgDir, gameCode,  gameName, format) {
    var host = '<?= $this->utils->getSystemUrl('www'); ?>';

    // If image is not found yet. cotinue search
    
    if(objImage.attr('src') == 0){
      objImage.attr('src', host + '/includes/images/defaults/favoritescasino.png');
    }

    var currentImgSrcValue = objImage.attr('src');

    if (currentImgSrcValue.indexOf('defaults') !== -1) {
        
        var imgUrl = host + '/includes/images/' + imgDir + '/' + gameCode + '.' + format;  
      
        imageExists(imgUrl, function(exists) {
          if (exists) {
            objImage.attr('src', imgUrl); 
            objFavorite.attr('data-image', imgUrl);
          }
        });
    }
  }

  function imageExists(url, callback) {    
    var img = new Image();
    img.onload = function() { callback(true); };
    img.onerror = function() { callback(false); };
    img.src = url;
  }

  function checkIfImageFound(imgSrc) {
    if (imgSrc.indexOf('www') !== -1) {
      return true;
    }
    return false;
  }

  var myfavorite_modal = $("#favorite_games_myfavorite");
    myfavorite_modal.on("show.bs.modal", function(e) {    
    var btn = $(e.relatedTarget);
    var url = btn.data("url"); 

      $("#remove_fvg_btn").click(function () {

        $.getJSON('/player_center/remove_from_favorites', {url:url}, function() {
           btn.parents('.col-sm-4').remove();      

        }); 

        myfavorite_modal.modal('hide');

      });        
       
  });
    
</script> 