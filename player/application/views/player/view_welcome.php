<div class="row">
    <div class="home_banner">
            <a href=""><img src="<?= IMAGEPATH.'/home/banner_1.jpg'; ?>"></a>
    </div> 
    <div class="container">
  <br>
  
</div>
<div class="row">    
    <div class='home_mid_sec'>
        <i>Top rated online casino with over <b>98,000</b> active members</i>
        <br/><br/>
        <div class='quick_links'>
            <ul>                        
                <li><a href=''>Home</a></li>
                <li><a href=''>Getting Started</a></li>
                <li><a href=''>Payment Options</a></li>
                <li><a href=''>Instant Play</a></li>
                <li><a href=''>Promotions</a></li>
                <li><a href=''>About Us</a></li>
                <li><a href=''>Contact Us</a></li>
            </ul>
        </div>

        <div class='live_casino_sec'>
            <a href=''><img src="<?= IMAGEPATH.'/home/live_casino.png'; ?>"></a>
        </div>

        <div class='spin_slot_sec'>
            <a href=''><img src="<?= IMAGEPATH.'/home/spin_slots.png'; ?>"></a>
        </div>

        <div class='roulette_sec'>
            <a href=''><img src="<?= IMAGEPATH.'/home/roulette.png'; ?>"></a>
        </div>
    </div>            
</div>

<?php
    $disable = '';
    $show = '';

    if($this->player_functions->getPlayerAccount($this->authentication->getPlayerId())) {
        
        // if(isset($playerGamePasswordPT)){
        //     echo "<input type='text' id='pt_pw' value='".$playerGamePasswordPT->password."' />";
        //     echo "<input type='text' id='userName' value='".$this->authentication->getUsername()."' />";
        // }
        
        $disable = '';
        $show = "display: none;'";
    } else {
        $disable = "disabled='disabled'";
        $show = "display: block;";
    }
?>
<?php //echo 'player id: '. $this->authentication->getPlayerId(); ?>
<div class="row">    
    <div class='home_mid_sec2'>
        <div class='featured_games_sec'>
            <div class='featured_games_sec_title'>
                <img src="<?= IMAGEPATH.'/home/featuregames.png'; ?>">
            </div>
            <ul class='featured_games_list1'>                        
                <li><a href=''><img src="<?= IMAGEPATH.'/home/game_1.jpg'; ?>"></a></li>
                <li><a href=''><img src="<?= IMAGEPATH.'/home/game_2.jpg'; ?>"></a></li>
                <li><a href=''><img src="<?= IMAGEPATH.'/home/game_3.jpg'; ?>"></a></li>
            </ul>
            <ul class='featured_games_list1'>                        
                <li><a href=''><img src="<?= IMAGEPATH.'/home/game_4.jpg'; ?>"></a></li>
                <li><a href=''><img src="<?= IMAGEPATH.'/home/game_5.jpg'; ?>"></a></li>
                <li><a href=''><img src="<?= IMAGEPATH.'/home/game_6.jpg'; ?>"></a></li>
            </ul>            
        </div>

        <div class='news_sec'>
            <div class='featured_news_sec_title'>
                <img src="<?= IMAGEPATH.'/home/news.png'; ?>" width='70' height='20' />                
            </div>
            <br/>
            <div class="news_item_sec">
                <div style="position: relative; width: 400px; height: 250px;">
                    <?php 
                        $count = 0;
                        foreach ($news as $key => $value) { 
                        $count++;
                    ?>
                        <div class="news" id="news_<?= $count?>" style="position: absolute; <?= ($count != 1) ? 'display: none;':''?>  height: auto;">
                            <label><?= $value['title']?></label>
                            <br/>
                            <p class="news_item">
                                <?= $value['content']?>
                            </p>
                        </div>
                    <?php } ?>
                    <input type="hidden" name="count" id="count" value="<?= $count?>"/>
                </div>
                <hr/>

                <h3>Top Winner</h3>
                <ul class='topWinner'>
                    <li class='playerName'>Asrii</li>
                    <li class='winAmount'>$120,000</li>
                    <li class='winGame'>Roullete Game</li>
                </ul>
                <ul class='topWinner'>
                    <li class='playerName'>Rendol</li>
                    <li class='winAmount'>$104,000</li>
                    <li class='winGame'>Spin Game</li>
                </ul>
                <ul class='topWinner'>
                    <li class='playerName'>Johann</li>
                    <li class='winAmount'>$80,000</li>
                    <li class='winGame'>Spider Game</li>
                </ul>
                <ul class='topWinner'>
                    <li class='playerName'>Jocelyn</li>
                    <li class='winAmount'>$50,000</li>
                    <li class='winGame'>Baccarat Game</li>
                </ul>
                <ul class='topWinner'>
                    <li class='playerName'>Almie</li>
                    <li class='winAmount'>$80,000</li>
                    <li class='winGame'>Blackjack Game</li>
                </ul>                
            </div>
        </div>
    </div>
</div>

<div class="row">    
    <div class='home_mid_sec3'>
    </div>
</div>

