<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_new_game_tags_201711091313 extends CI_Migration {

    private $tableName = 'game_tags';

    public function up() {

        $game_tags = array(
            "video_poker" => array(
                "tag_name" => '_json:{"1":"Video Poker","2":"视频扑克","3":"Video Poker","4":"Video Poker","5":"비디오 포커"}',
                "tag_code" => "video_poker",
            ),
            "poker" => array(
                "tag_name" => '_json:{"1":"Poker","2":"赌场扑克","3":"Poker","4":"Poker","5":"포커"}',
                "tag_code" => "poker",
            ),
            "mini_games" => array(
                "tag_name" => '_json:{"1":"Mini Games","2":"迷你游戏","3":"Mini Games","4":"Mini Games","5":"미니게임"}',
                "tag_code" => "mini_games",
            ),
            "others" => array(
                "tag_name" => '_json:{"1":"Others","2":"其他","3":"Others","4":"Others","5":"기타"}',
                "tag_code" => "others",
            ),
            "soft_games" => array(
                "tag_name" => '_json:{"1":"Soft Games","2":"Soft Games","3":"Soft Games","4":"Soft Games","5":"소프트게임"}',
                "tag_code" => "soft_games",
            ),
            "scratch_card" => array(
                "tag_name" => '_json:{"1":"Scratch Card","2":"刮刮乐游戏","3":"Scratch Card","4":"Scratch Card","5":"스크래치 카드"}',
                "tag_code" => "scratch_card",
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
        // $this->db->where('tag_code',"scratch_card");
        // $this->db->delete($this->tableName);
    }
}
