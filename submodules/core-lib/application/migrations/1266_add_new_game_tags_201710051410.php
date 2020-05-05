<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_new_game_tags_201710051410 extends CI_Migration {

    private $tableName = 'game_tags';

    public function up() {

        $game_tags = array(
            "slots" => array(
                "tag_name" => '_json:{"1":"Slots","2":"老虎机","3":"Slots","4":"Slots","5":"슬롯"}',
                "tag_code" => "slots",
            ),
            "lottery" => array(
                "tag_name" => '_json:{"1":"Lottery","2":"彩票游戏","3":"Lottery","4":"Lottery","5":"복권"}',
                "tag_code" => "lottery",
            ),
            "fishing_game" => array(
                "tag_name" => '_json:{"1":"Fishing Game","2":"捕鱼","3":"Fishing Game","4":"Fishing Game","5":"피싱게임"}',
                "tag_code" => "fishing_game",
            ),
            "live_dealer" => array(
                "tag_name" => '_json:{"1":"Live Dealer","2":"真人游戏","3":"Live Dealer","4":"Live Dealer","5":"라이브 딜러"}',
                "tag_code" => "live_dealer",
            ),
            "casino" => array(
                "tag_name" => '_json:{"1":"Casino","2":"赌场游戏","3":"Casino","4":"Casino","5":"카지노"}',
                "tag_code" => "casino",
            ),
            "gamble" => array(
                "tag_name" => '_json:{"1":"Gamble","2":"赌博","3":"Gamble","4":"Gamble","5":"갬블"}',
                "tag_code" => "gamble",
            ),
            "table_games" => array(
                "tag_name" => '_json:{"1":"Table Games","2":"桌面游戏","3":"Table Games","4":"Table Games","5":"테이블 게임"}',
                "tag_code" => "table_games",
            ),
            "table_and_cards" => array(
                "tag_name" => '_json:{"1":"Table and Cards","2":"牌桌&牌游戏","3":"Table and Cards","4":"Table and Cards","5":"테이블 & 카드"}',
                "tag_code" => "table_and_cards",
            ),
            "card_games" => array(
                "tag_name" => '_json:{"1":"Card Games","2":"纸牌游戏","3":"Card Games","4":"Card Games","5":"카드 게임"}',
                "tag_code" => "card_games",
            ),
            "e_sports" => array(
                "tag_name" => '_json:{"1":"E-Sports","2":"电子竞技","3":"E-Sports","4":"E-Sports","5":"E-스포츠"}',
                "tag_code" => "e_sports",
            ),
            "fixed_odds" => array(
                "tag_name" => '_json:{"1":"Fixed Odds","2":"固定赔率","3":"Fixed Odds","4":"Fixed Odds","5":"고정 배당률 스포츠 베팅"}',
                "tag_code" => "fixed_odds",
            ),
            "arcade" => array(
                "tag_name" => '_json:{"1":"Arcade","2":"街机游戏","3":"Arcade","4":"Arcade","5":"아케이드"}',
                "tag_code" => "arcade",
            ),
            "horce_racing" => array(
                "tag_name" => '_json:{"1":"Horce Racing","2":"赛马","3":"Horce Racing","4":"Horce Racing","5":"경마"}',
                "tag_code" => "horce_racing",
            ),
            "progressives" => array(
                "tag_name" => '_json:{"1":"Progressives","2":"累积奖池","3":"Progressives","4":"Progressives","5":"프로그레시브"}',
                "tag_code" => "progressives",
            ),
            "sports" => array(
                "tag_name" => '_json:{"1":"Sports","2":"体育游戏","3":"Sports","4":"Sports","5":"스포츠"}',
                "tag_code" => "sports",
            ),
            "unknown" => array(
                "tag_name" => '_json:{"1":"Unknown","2":"不明","3":"Unknown","4":"Unknown","5":"알수없음"}',
                "tag_code" => "unknown",
            ),
            "video_poker" => array(
                "tag_name" => '_json:{"1":"Video Poker","2":"视频扑克","3":"Video Poker","4":"Video Poker","5":"Video Poker"}',
                "tag_code" => "video_poker",
            ),
            "poker" => array(
                "tag_name" => '_json:{"1":"Poker","2":"赌场扑克","3":"Poker","4":"Poker","5":"Poker"}',
                "tag_code" => "poker",
            ),
            "mini_games" => array(
                "tag_name" => '_json:{"1":"Mini Games","2":"迷你游戏","3":"Mini Games","4":"Mini Games","5":"Mini Games"}',
                "tag_code" => "mini_games",
            ),
            "others" => array(
                "tag_name" => '_json:{"1":"Others","2":"其他","3":"Others","4":"Others","5":"Others"}',
                "tag_code" => "others",
            ),
            "soft_games" => array(
                "tag_name" => '_json:{"1":"Soft Games","2":"Soft Games","3":"Soft Games","4":"Soft Games","5":"소프트게임"}',
                "tag_code" => "soft_games",
            ),
        );

        foreach ($game_tags as $key => $value) {
            $this->db->select('id')->where('tag_code',$key);
            $result = $this->db->get($this->tableName);
            if(isset($result->row()->id)){
                $value['updated_at'] = $this->utils->getNowForMysql();
                $this->db->where('tag_code', $key);
                $this->db->update($this->tableName, $value);
            }else{
                $value['created_at'] = $this->utils->getNowForMysql();
                $this->db->insert($this->tableName,$value);
            }
        }
    }

    public function down() {
        $game_tags = array("video_poker","poker","mini_games","others","soft_games");
        foreach ($game_tags as $key) {
            $this->db->where('tag_code', $key);
            $this->db->delete($this->tableName);
        }
    }
}