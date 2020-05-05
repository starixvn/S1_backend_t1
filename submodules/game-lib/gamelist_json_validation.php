<?php 

const GAME_LIST_VALID_FIELDS_WITH_LIMIT = [
        "game_platform_id"     => [ "min" => 1, "max" => 2147483648],
        "related_game_desc_id" => [ "min" => 0, "max" => 2147483648],
        // "game_type_id"         => [ "min" => 1, "max" => 4294967295],
        "game_order"           => [ "min" => 0, "max" => 2147483648],
        "game_name"            => [ "min" => 1, "max" => 500],
        "game_code"            => [ "min" => 1, "max" => 200],
        "note"                 => [ "min" => 0, "max" => 1000],
        "attributes"           => [ "min" => 0, "max" => 2000],
        "english_name"         => [ "min" => 2, "max" => 300],
        "external_game_id"     => [ "min" => 1, "max" => 300],
        "clientid"             => [ "min" => 1, "max" => 200],
        "moduleid"             => [ "min" => 1, "max" => 200],
        "sub_game_provider"    => [ "min" => 1, "max" => 100],
        "demo_link"            => [ "min" => 0, "max" => 500],
        "dlc_enabled"          => [ "min" => 0, "max" => 1],
        "progressive"          => [ "min" => 0, "max" => 1],
        "flash_enabled"        => [ "min" => 0, "max" => 1],
        "offline_enabled"      => [ "min" => 0, "max" => 1],
        "mobile_enabled"       => [ "min" => 0, "max" => 1],
        "status"               => [ "min" => 0, "max" => 1],
        "flag_show_in_site"    => [ "min" => 0, "max" => 1],
        "no_cash_back"         => [ "min" => 0, "max" => 1],
        "void_bet"             => [ "min" => 0, "max" => 1],
        "html_five_enabled"    => [ "min" => 0, "max" => 1],
        "enabled_on_android"   => [ "min" => 0, "max" => 1],
        "enabled_on_ios"       => [ "min" => 0, "max" => 1],
        "flag_new_game"        => [ "min" => 0, "max" => 1],
        "enabled_freespin"     => [ "min" => 0, "max" => 1],
    ];

const FIELDS_DATATYPES = [
    "int" => ["game_type_id","game_platform_id","related_game_desc_id","game_order"],
    "boolean" => ["flag_show_in_site","enabled_on_ios","enabled_on_android","progressive","html_five_enabled","flash_enabled","flag_new_game","dlc_enabled","mobile_enabled","enabled_freespin","status","void_bet","no_cash_back"],
    "text" => ["game_name","english_name","attributes","demo_link","game_code","external_game_id","client_id","moduleid","sub_game_provider","note"],
];



/**
 * [validateGameJsonFormat validate if game list json is valid]
 * @param  [string] $directory [path of the file]
 * @param  [string] $file_name [file name]
 * @return [boolean]            [description]
 */
 function validateGameJsonFormat($game_list){
    $game_list = json_decode($game_list);

    $success = false;
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            $success = true;
        break;
        case JSON_ERROR_DEPTH:
            $message = ' - Maximum stack depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            $message = ' - Underflow or the modes mismatch';
        break;
        case JSON_ERROR_CTRL_CHAR:
            $message = ' - Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            $message = ' - Syntax error, malformed JSON';
        break;
        case JSON_ERROR_UTF8:
            $message = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
        default:
            $message = ' - Unknown error';
        break;
    }

    if (!empty($message)){
        echo "\nERROR validateGameJsonFormat =======>" . $message; exit(1);
    }

}

/**
 * [checkIfFieldsAreValid
 *  - check if all required fields exist
 *  - check if all fields are valid
 *  - check min and max input per field
 * ]
 * @param  [array] &$game            [game attributes]
 * @param  [array] $fields_datatypes [fields with datatypes]
 * @return [boolean]                   [description]
 */
function checkIfFieldsAreValid($game,$fields_datatypes, $game_list_valid_fields){
    $success = true;
    $game_fields = array_keys($game);
    $missing_required_fields = $min_max_result = [];

    foreach ($game_list_valid_fields as $required_field) {
        if (!in_array($required_field, $game_fields)) {
            array_push($missing_required_fields, $required_field);
        }
    }

    foreach ($game_fields as $field) {
    	#skip
    	if ($field == "game_type_code" || $field == "release_date") continue;

        if (!in_array($field, $game_list_valid_fields)){
            echo "\nERROR Invalid Field ======>[external_game_id=".$game['external_game_id']."] ==>" . $field;
            exit(1);
        }

        if (in_array($field, $fields_datatypes['text'])) {
            if (strlen($game[$field]) > GAME_LIST_VALID_FIELDS_WITH_LIMIT[$field]['max']) {
                array_push($min_max_result, [$field=>strlen($game[$field])]);
            }
        }

        if (in_array($field, $fields_datatypes['int']) || in_array($field, $fields_datatypes['boolean'])) {
            if ((int)$game[$field] > (int)GAME_LIST_VALID_FIELDS_WITH_LIMIT[$field]['max']) {
                array_push($min_max_result, [$field=>$game[$field]]);
            }
            if ((int)$game[$field] < (int) GAME_LIST_VALID_FIELDS_WITH_LIMIT[$field]['min']) {
            	if ( ! empty($game[$field])) {
	                array_push($min_max_result, [$field=>$game[$field]]);
            	}
            }
        }

    }

    if (count($min_max_result) > 0){
        echo "\nERROR Min/Max not met ======>[external_game_id=".$game['external_game_id']."]" . json_encode($min_max_result);
        exit(1);
    }

    if (count($missing_required_fields) > 0){
        echo "\nERROR Missing Required Field[s] ======>[external_game_id=".$game['external_game_id']."]" . json_encode($missing_required_fields);
        exit(1);
    }

}

function validateGameListFiles($file_name = null){
	$game_list_valid_fields = array_keys(GAME_LIST_VALID_FIELDS_WITH_LIMIT);

    $directory = dirname(__FILE__) . '/models/game_description/json_data' ;
    $json_files = array_diff(scandir($directory), array('..', '.'));

    if ( ! empty($file_name)) {
    	if ( ! in_array($file_name, $json_files)) {
	    	echo "File [".$file_name."] Not Found!";
	    	exit(1);
    	}else{
    		$json_files = ['0' => $file_name];
    	}
    }

	foreach ($json_files as $key => $json_file_name) {
		echo "\nChecking file: " . $json_file_name;
		$game_list_data = file_get_contents($directory . "/". $json_file_name);
		validateGameJsonFormat($game_list_data);
		$game_list_data = json_decode($game_list_data,true);

		foreach ($game_list_data as $key => $game_detail) {
			checkIfFieldsAreValid($game_detail,FIELDS_DATATYPES, $game_list_valid_fields);
		}
		echo " - Done checking";
	}
}

if(isset($argv[1])){
    $file_name=$argv[1];
    validateGameListFiles($file_name);
}
