<?php if(isset($error)): ?>
    <h4><b><?=$error?></b></h4>
<?php elseif(isset($result)): ?>
    <?php switch ($result['type']) {
        case 'smtp':
            foreach($result as $key => $value){
                if($key == 'type'){
                    continue;
                }
                else if($key == 'url'){
                    echo "<h4><b>".lang('Sent to').": ".$value."</b></h4>";
                }
                else if($key == 'params'){
                    echo "<h4><b>".lang('Sent Params')."(".$key.")</b></h4>";
                    echo "<textarea cols='150' rows='15'>".htmlentities(json_encode($value, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR))."</textarea>";
                }
                else if($key == 'content'){
                    echo "<h4><b>".lang('Received Params')."(".$key.")</b></h4>";
                    if(is_null(json_decode($value))){
                        echo "<textarea cols='150' rows='15'>".htmlentities($value)."</textarea>";
                    }
                    else{
                        echo "<textarea cols='150' rows='15'>".htmlentities(json_encode(json_decode($value), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR))."</textarea>";
                    }
                }
            }
            break;

        default:
            echo lang('Invalid Access');
            break;
    }
    ?>
<?php endif; ?>
