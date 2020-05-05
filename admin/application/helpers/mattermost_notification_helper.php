<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('sentNotificationToMattermost')){

    /**
     * @param $user string  - the user will be shown on message notif
     * @param $channel string - mattermost channel , you will get this from mattermosr config - ask system admin
     * @param $messages array of array

     //ex. attachment message

     $this->load->helper('mattermost_notification_helper');

        $user = "manual-sync"
        $channel = "manual_sync" // add mattermost channel url at config_default_common.php

       // ex. config
        $config['mattermost_channels'] = [
            'manual_sync' => "https://talk.chatchat365.com/hooks/9xou8tqf13rubxmahoeo3unaec"
        ];

        $notif_message = array(
            array(
                'text' => 'This is message 1',
                'text' => $message,
                'type' => 'info',   // choose from these 'info', 'danger','warning','success'
                'title' => 'This is title', //optional
                'author' => 'Aris', // optional
                'author_link' => 'www.totbet.com' // optional
                'pretext' => 'Prextext  before attachment message '// optional

            )// you can create multiple message attachments
             array(
                'text' => 'This is message 2',
                'type' => 'info',   // choose from these 'info', 'danger','warning','success'
                'title' => 'This is title2', //optional
                'author' => 'Aris', // optional
                'author_link' => 'www.totbet.com' // optional
                'pretext' => 'Prextext  before attachment message '// optional

            )
        );

        sendNotificationToMattermost($user,$channel,$notif_message);
    */

    function sendNotificationToMattermost($user,$channel,$messages,$texts_and_tags=null) {
        $success=false;
        $ci = &get_instance();

        $mm_channels = $ci->config->item('mattermost_channels');
        $channel_url = $mm_channels[$channel];

        $color = "";

        $color_map = [
            'info' => "#3498DB",
            'success' => "#58D68D",
            'warning' => "#F4D03F",
            'danger' => "#EC7063",
            'default' =>"#3498DB"
        ];

        $attachments = [];

        foreach ($messages as $message) {
            $default = array(
                'text' => "Please say something!",
                'color' => $color_map['info']
            );
            $message['color'] = $color_map[$message['type']];
            $attachment = array_merge($default,$message);
            array_push($attachments, $attachment);

        }

        if ( ! empty($channel_url)) {

            $payload = array( 'username'=> $user, 'attachments' => $attachments);

            if(!empty($texts_and_tags)){
                if(is_array($texts_and_tags)){
                    $payload['text'] = implode(" ", $texts_and_tags);
                }else{
                    $payload['text'] = $texts_and_tags;
                }
            }
            $data = array('payload' => json_encode($payload));

            $ch = curl_init($channel_url);

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);

            //get error
            $errCode = curl_errno($ch);
            $error = curl_error($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if($errCode!=0 || $statusCode>=400){
                $ci->utils->error_log('error code', $errCode, $error, $statusCode, $result);
                $success=false;
            }else{
                $ci->utils->debug_log('return result', $errCode, $error, $statusCode, $result);
                $success=true;
            }

            curl_close($ch);
        }

        return $success;
    }

}