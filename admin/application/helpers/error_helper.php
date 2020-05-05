<?php
register_shutdown_function("fatal_handler");

function fatal_handler() {
    $ci = get_instance();
    // $error = error_get_last();

    // if ( ! empty($error)) {

    //     if (current_url() != null) {
    //         $error['url'] = current_url();
    //         // var_dump($error);
    //     }
    //     //E_DEPRECATED
    //     if ($error['type'] == 8192 && $error['line'] == 0) {
    //         return null;
    //     }
    //     /* $url = 'https://hooks.slack.com/services/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'; */
    //     $url = $ci->config->item('slack_url');
    //     $user = $ci->config->item('slack_user');
    //     $channel = $ci->config->item('slack_channel');

    //     if ( ! empty($url)) {
    //         $data = array('payload' => json_encode(array("channel"=> $channel, 'username'=> $user, 'text' => '```'.trim(print_r($error, true)).'```',)));
    //         $ch = curl_init($url);

    //         if ($data) {
    //             curl_setopt($ch, CURLOPT_POST, true);
    //             curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //         }

    //         $result = curl_exec($ch);
    //         curl_close($ch);
    //     }
    // }

    //close db
    if(isset($ci->db)){
        if(is_string($ci->db)){
            raw_debug_log('get wrong ci db');
        }else{
            $ci->db->close();
        }
        // $ci->utils->debug_log('close db');
    }

}
