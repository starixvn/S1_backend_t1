<?php
function player_tagged_list($playerId, $text = FALSE){
    $CI= &get_instance();

    $tag_list = $CI->player->getPlayerTags($playerId);

    if(FALSE === $tag_list || !is_array($tag_list)){
        return lang('player.tp12');
    }

    $html_tag_list = [];
    $text_list = [];

    foreach($tag_list as $tag_entry){
        $html_tag_list[] = player_tagged_formate($tag_entry['tagId'], $tag_entry['tagName'], $tag_entry['tagColor']);
        $text_list[] = $tag_entry['tagName'];
    }

    return ($text) ? implode(',', $text_list) : implode('', $html_tag_list);
}

function tag_formatted($tagId){
    $CI= &get_instance();
    static $tag_list;

    if (empty($tag_list)) {
        $tag_list = $CI->player->getAllTagsOnly();
    }

    foreach($tag_list as $tag){
        if($tag['tagId'] == $tagId){
            return player_tagged_formate($tag['tagId'], $tag['tagName'], $tag['tagColor']);
        }
    }

    return $tagId;
}

function player_tagged_formate($tagId, $tagName, $tagColor){
    $CI= &get_instance();

    return '<a href="' . $CI->utils->getSystemUrl('admin') . '/player_management/taggedlist?tag=' . $tagId . '&search_reg_date=false" class="tag tag-component"><span class="tag label label-info" style="background-color: ' . $tagColor . '">' . $tagName . '</span></a>';
}