<?php
require_once 'PlayerCenterBaseController.php';

/**
 * Provides Security function
 *
 * @property Player_security_library $player_security_library
 * @property External_system $external_system
 * @property Player_model $player_model
 * @property player_attached_proof_file_model $player_attached_proof_file_model
 * @property Player_Functions $player_functions
 */
class Security extends PlayerCenterBaseController{
    public function __construct(){
        parent::__construct();

        $this->load->library(['player_security_library', 'gisfromstring']);

        $this->load->vars('content_template', 'default_with_menu.php');
        $this->load->vars('activeNav', 'security');
    }

    public function index(){

        $player_id = $this->load->get_var('playerId');
        $player = $this->load->get_var('player');

        $this->player_security_library->setPlayer($player);
        $this->player_security_library->assign_common_vars();

        $upload_image_max_size_limit = $this->utils->getMaxUploadSizeByte();
        $data['upload_image_max_size_limit'] = $upload_image_max_size_limit;
        $data['game_platforms'] = $this->external_system->getSystemCodeMapping();

        $rule = $this->utils->getConfig('player_validator');
        $data['contactRule'] = isset($rule['contact_number']) ? $rule['contact_number']  : "" ;

        $data['lang_upload_image_types'] = preg_replace( '/\|/', lang('comma'), 'jpg|jpeg|png|gif'); // allowed_upload_file : "jpg|jpeg|png|gif|PNG", Overide for text string.
        $ini_upload_max_filesize = ini_get('upload_max_filesize');
        $data['lang_upload_max_filesize'] = $ini_upload_max_filesize;
        $data['upload_max_filesize'] = $this->return_bytes($ini_upload_max_filesize);
        $data['lang_upload_image_max_size_limit'] = ($upload_image_max_size_limit / 1000000). 'M';
        $data['currentLanguage'] = $this->language_function->getCurrentLanguageName();
        $device = ($this->agent->is_mobile() == TRUE) ? $this->agent->mobile():'';
        $allow_type = ['Apple iPhone', 'iPad', 'Apple iPod Touch'];
        $data['ios_device'] = in_array($device, $allow_type) ? true : false;

        $this->loadTemplate();
        $this->template->append_function_title(lang('Security'));
        $this->template->add_function_css('/common/css/player_center/security.css');
        $this->template->add_function_js('/common/js/player_center/player-security.js');
        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate(FALSE) . '/security/security', $data);
        $this->template->render();
    }

    /**
     * Is there an easy way in PHP to convert from strings like '256M', '180K', '4G' to their integer equivalents?
     *
     * @see https://stackoverflow.com/a/1336624
     *
     * @param string $val The strings like '256M', '180K', '4G'.
     * @return integer The integer of byte.
     */
    function return_bytes($val) {
        $val = trim($val);
        $last = strtolower(substr($val, -1));
        $val = substr($val, 0, -1);
        switch($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }// EOF return_bytes

    /**
     * Correct Image Orientation for jpg
     *
     * @param binary $imageString
     * @param integer &$orientation The image orientation for get.
     * @return binary $final_image The corrected binary.
     */
    public function correctImageOrientation($imageString, &$orientation){

        $size = getimagesizefromstring($imageString);
        $width = $size[0];
        $height = $size[1];
        // $tmp = imagecreatetruecolor($width,$height);

        $img = imagecreatefromstring($imageString);
        $mime = $this->gisfromstring->getMimeType($imageString);
        // $mime = GisFromString::getMimeType($imageString);

        if($mime == 'image/jpeg' || $mime == 'image/jpg'){
            $orientation = $this->getOrientation($imageString);
        }else{
            $orientation = 0;
        }

        if($orientation != 1){
            $deg = 0;
            $newWidth = $width;
            $newHeight = $height;
            switch ($orientation) {
              case 3:
                $deg = 180;
                break;
              case 6:
                $deg = 270;
                $newWidth = $height;
                $newHeight = $width;
                break;
              case 8:
                $deg = 90;
                $newWidth = $height;
                $newHeight = $width;
                break;
            }
            if ($deg) {
              $img = imagerotate($img, $deg, 0);
            }

            $processImageFuncInfo = $this->getProcessImageFuncInfoByMime($mime);
            if($processImageFuncInfo){
                $image_create_func = $processImageFuncInfo['create_func'];
                $image_save_func = $processImageFuncInfo['save_func'];
                $new_image_ext = $processImageFuncInfo['ext'];
            }

            /*
            * imageXXX() only has two options, save as a file, or send to the browser.
            * It does not provide you the oppurtunity to manipulate the final GIF/JPG/PNG file stream
            * So I start the output buffering, use imageXXX() to output the data stream to the browser,
            * get the contents of the stream, and use clean to silently discard the buffered contents.
            */
            ob_start();
            $image_save_func($img);
            $final_image = ob_get_contents();
            ob_end_clean();

        }else{

            $final_image = $imageString;
        } // if there is some rotation necessary

        // imagedestroy($tmp);
        imagedestroy($img);

        return $final_image;
    }// EOF correctImageOrientation

    /**
     * Get Process Image Function Info By Mime
     *
     * @param string $mime ex: image/png, image/jpeg,...
     * @return array $return The array format,
     * - $return[create_func] function name for create image.
     * - $return[save_func] function name for save image to file.
     * - $return[ext] function name for image type.
     */
    public function getProcessImageFuncInfoByMime($mime){
        $return= false;
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $return['create_func'] = 'imagecreatefromjpeg';
                $return['save_func'] = 'imagejpeg';
                $return['ext'] = 'jpg';
                break;

            case 'image/png':
                $return['create_func'] = 'imagecreatefrompng';
                $return['save_func'] = 'imagepng';
                $return['ext'] = 'png';
                break;

            case 'image/gif':
                $return['create_func'] = 'imagecreatefromgif';
                $return['save_func'] = 'imagegif';
                $return['ext'] = 'gif';
                break;

            default:
                throw new Exception('Unknown image type.');
        }

        return $return;
    } // EOF getProcessImageFuncInfoByMime

    /**
     * get Orientation for jpg image
     *
     * https://www.php.net/manual/zh/function.exif-read-data.php#117355
     *
     * @param blob $imageString The image blob.
     * @return integer $orientation
     */
    public function getOrientation($imageString){
        $orientation = 1;
        if (function_exists('exif_read_data')
            && false // Disable for SERVER,"kgvipenstaging" NOT support!
        ) {
            $mime = $this->gisfromstring->getMimeType($imageString);
            // $mime = GisFromString::getMimeType($imageString);

            // https://stackoverflow.com/a/5465741
            $exif = exif_read_data("data://". $mime. ";base64,". base64_encode($imageString));
            // $exif = exif_read_data($filename);

            if (isset($exif['Orientation'])){
                $orientation = $exif['Orientation'];
            }
        } else if (preg_match('@\x12\x01\x03\x00\x01\x00\x00\x00(.)\x00\x00\x00@', $imageString, $matches)) {
            /// Ref. to https://www.php.net/manual/en/function.exif-read-data.php#117355
            // Little endian EXIF
            $orientation = ord($matches[1]);
        } else if (preg_match('@\x01\x12\x00\x03\x00\x00\x00\x01\x00(.)\x00\x00@', $imageString, $matches)) {
            /// Ref. to https://gist.github.com/EionRobb/8e0c76178522bc963c75caa6a77d3d37
			// Big endian EXIF
			$orientation = ord($matches[1]);
		}
        return $orientation;
    } // EOF getOrientation


    /**
     * To reduce the image size until to the specified file size.
     * 要將圖檔壓縮直到指定文件大小。
     *
     *
     * @internal integer $smallerPx The reduce image size(px) pre compress.
     * @return json $return The format,
     * - $return[compressed][{inputName4fileType}][{n}][base64Binary] The binary after base64 encode.
     * - $return[compressed][{inputName4fileType}][{n}][binaryLength] The binary length.
     * - $return[compressed][{inputName4fileType}][{n}][mime] The image type.
     * - $return[compressed][{inputName4fileType}][{n}][compressedTimes] The compressed time.
     * - $return[files][{inputName4fileType}] same as $_FILES.
     * - $return[input] same as $_POST.
     *
     *
     */
    public function doCompressImagesBySize(){
        if (!$this->authentication->isLoggedIn()) {
            return $this->goPlayerLogin();
        }
        set_time_limit(0);

        // $smallerPx = $this->safeGetParam('smallerPx', 10);
        $smallerPx = $this->input->post('smallerPx');
        if( empty($smallerPx)){
            $smallerPx = 10;
        }

        $return = [];
        $return['compressed'] = [];

        // Organize $_FILES
        $fileList = []; // tidied $_FILES
        foreach($_FILES as $inputName => $files){
            $fileList[$inputName] = [];
            $indexOfInputName = 0;
            foreach($files['tmp_name'] as $indexNumber => $tmp_name){
                $fileList[$inputName][$indexOfInputName]['error'] = $files['error'][$indexNumber];
                $fileList[$inputName][$indexOfInputName]['name'] = $files['name'][$indexNumber];
                $fileList[$inputName][$indexOfInputName]['size'] = $files['size'][$indexNumber];
                $fileList[$inputName][$indexOfInputName]['type'] = $files['type'][$indexNumber];
                $fileList[$inputName][$indexOfInputName]['tmp_name'] = $files['tmp_name'][$indexNumber];
                $indexOfInputName++;
            }
        }
        $whileLimit = 100; // Avoid infinite loops for _compressImageBySize()


        // $return['fileList'] = $fileList; // tidied $_FILES assign to return json (Optional)
        // $return['compressedTimes'] = 0;
        foreach($fileList as $inputName => $filesInfoOfInputName){
            $indexNumber = 0;
            foreach($filesInfoOfInputName as $indexNumber => $filesInfo){

                if($filesInfo['name'] == '190918.IMG_5963.JPG'){ // for TEST file size over limit after compressed.
                    $whileLimit = 2;
                }

                $return['compressed'][$inputName][$indexNumber] = [] ;
                if( empty($return['compressed'][$inputName][$indexNumber]['compressedTimes']) ){
                    $return['compressed'][$inputName][$indexNumber]['compressedTimes'] = 0;
                }


                // $return['compressed'][$inputName][$indexNumber]['tmp_name'] = $filesInfo['tmp_name'];
                $dataString = file_get_contents($filesInfo['tmp_name']);
                $mime = $this->gisfromstring->getMimeType($dataString);
                // $mime = GisFromString::getMimeType($dataString); //$imageinfo['mime'];
                $return['compressed'][$inputName][$indexNumber]['mime'] = $mime;
                // $return['compressed'][$inputName][$indexNumber]['src'] = '';
                // $return['compressed'][$inputName][$indexNumber]['src'] .= 'data:'. $mime. ';base64, '; // data:image/png;base64,

                $maxUploadSizeK = $this->utils->getMaxUploadSizeByte();// assign max limit of file size


                $compressedImage = $this->_compressImageBySize( $filesInfo['tmp_name'] // tmp file form upload.
                                            , $maxUploadSizeK // limit max file size. (ignore "K")
                                            , $smallerPx
                                            , $whileLimit // Avoid infinite loops for _compressImageBySize()
                                            , $return['compressed'][$inputName][$indexNumber]['compressedTimes']  // assign to return json.
                                        ); // EOF $this->_compressImageBySize().
                $return['compressed'][$inputName][$indexNumber]['base64Binary'] = base64_encode( $compressedImage ) ;
                $return['compressed'][$inputName][$indexNumber]['binaryLength'] = strlen($compressedImage);
                $return['compressed'][$inputName][$indexNumber]['name'] = $filesInfo['name'];

                // data:image/png;base64,
                $indexNumber++;

            }
        }
        $return['input'] = $this->input->post() ;
        $return['files'] = $_FILES ;

        // $return['compressed'] = null;
        $this->returnJsonResult($return);
    } // EOF doCompressImagesBySize()

    /**
     * To compress image (reduce image size) until to the specified file size.
     *
     * @api doCompressImagesBySize
     * @param string $tmpPathfile The tmp path file form uplaod.
     * @param integer $maxLimitFileSize The file size max limit.
     * @param integer $smallerPx The reduce image size(px) pre compress.
     * @param &integer $lastIndexWhile The compress image time for trace performance.
     * @return blob The Image binary contant.
     */
     function _compressImageBySize(  $tmpPathfile
                                , $maxLimitFileSize = 500* 1024 // 500K OR 2* 1024* 1024 // 2M
                                , $smallerPx = 10 // 10px
                                , $whileLimit = 100 // Avoid infinite loops
                                , &$lastIndexWhile
    ){
        // $whileLimit = 100; // Avoid infinite loops

       if( ! empty($tmpPathfile) ){ // for called by self.
            $dataString = file_get_contents($tmpPathfile);
        }else{ // @todo handle empty input.
            return false;
        }

        /// orientation
        $dataString = $this->correctImageOrientation($dataString, $orientation);

        if (! in_array($orientation, array(3,6,9) ) ){
            $lastIndexWhile++;
        }

        // To compress image (reduce image size)
        $smallerImage = $dataString;
        $indexWhile = 0;
        $calcSmallerPx = array();
        $calcSmallerPx['mime'] = $this->gisfromstring->getMimeType($dataString);
        // $calcSmallerPx['mime'] = GisFromString::getMimeType($dataString); // for calcSmallerPx()

        $isOverLimitFileSize = strlen($smallerImage) > $maxLimitFileSize;
        if($isOverLimitFileSize){ // convert to PHP generated image.
            $smallerImage = $this->_changeImageSizeToSmaller($smallerImage, 0); // convert to image of PHP.
            $lastIndexWhile = $lastIndexWhile+ 1;
        }

        $isOverLimitFileSizeChangedFirst = strlen($smallerImage) > $maxLimitFileSize;

        while(  $isOverLimitFileSizeChangedFirst // over the file size of max limit from PHP generated image.
            && strlen($smallerImage) > $maxLimitFileSize //  over the file size of max limit from while loop.
            && $indexWhile < $whileLimit // Avoid infinite loops OR system timeout
        ){
            // detect image size(HxW) and file size. (before change image size)
            $size = getimagesizefromstring($smallerImage);
            $calcSmallerPx['before']['imageSize']['width'] = $size[0];
            $calcSmallerPx['before']['imageSize']['height'] = $size[1];
            $calcSmallerPx['before']['fileSize'] = strlen($smallerImage);

            $smallerImage = $this->_changeImageSizeToSmaller($smallerImage, $smallerPx);

            // detect image size(HxW) and file size. (sfter change image size)
            $size = getimagesizefromstring($smallerImage);
            $calcSmallerPx['after']['imageSize']['width'] = $size[0];
            $calcSmallerPx['after']['imageSize']['height'] = $size[1];
            $calcSmallerPx['after']['fileSize'] = strlen($smallerImage);

            //  Calculate adjustment amount of the next round
            $smallerPx = $this->calcSmallerPx($calcSmallerPx, $maxLimitFileSize);

            $indexWhile++; // index number increment in while loop.

        }

        $lastIndexWhile = $lastIndexWhile+ $indexWhile; // assign for compress time.
        return $smallerImage;
    } // EOF _compressImageBySize

    /**
     * To calculate adjustment amount of the next round
     *
     * @param array $calcSmallerPx For reference, more,
     * - $calcSmallerPx['mime'] string The image type.
     * - $calcSmallerPx['before']['imageSize']['width'] integer The image width BEFTER compress.
     * - $calcSmallerPx['before']['imageSize']['height'] integer The image height BEFTER compress.
     * - $calcSmallerPx['before']['fileSize'] integer The image file size BEFTER compress.
     * - $calcSmallerPx['after']['imageSize']['width'] integer The image width AFTER compress.
     * - $calcSmallerPx['after']['imageSize']['height'] integer The image height AFTER compress.
     * - $calcSmallerPx['after']['fileSize']The image file size AFTER compress.
     *
     * @param integer $maxLimitFileSize The file size max limit.
     * @return integer The adjustment amount.
     */
    function calcSmallerPx($calcSmallerPx, $maxLimitFileSize){
        $smallerPx = $this->calcSmallerPxByFileSize($calcSmallerPx, $maxLimitFileSize);
        return $smallerPx;
    } // EOF calcSmallerPx


    /**
     * The Solution for calculate adjustment amount of the next round.
     * Get adjustment amount By ratio.
     *
     * @param array $calcSmallerPx The format visit $calcSmallerPx of calcSmallerPx().
     *
     * @param integer $maxLimitFileSize The file size max limit.
     * @return integer The adjustment amount.
     */
    function calcSmallerPxByFileSize($calcSmallerPx, $maxLimitFileSize){
        $calcSmallerPxDefault = 10;
        $calcedSmallerPx = $calcSmallerPxDefault;

        // The image Width/Height ratio.
        // 原圖的寬高比
        $ratioBeforeWH = $calcSmallerPx['before']['imageSize']['width'] / $calcSmallerPx['before']['imageSize']['height'];

        // The differene amount of Height and Width.
        // 壓縮後圖案大小差值（寬、高）
        $diffHeight = $calcSmallerPx['before']['imageSize']['height']- $calcSmallerPx['after']['imageSize']['height'];
        $diffWidth = $calcSmallerPx['before']['imageSize']['width']- $calcSmallerPx['after']['imageSize']['width'];

        // The differene file size by before and after processing.
        //  上次壓縮後，檔案大小差值
        $diffFilesize = $calcSmallerPx['before']['fileSize'] - $calcSmallerPx['after']['fileSize'];

        /// The longer side get another side by ratio of width and height.
        if( $ratioBeforeWH > 1) { //landscape ( width > height  )
            $diffHWmax = $diffWidth;
        }else{ //portrait	( height >= width )
            $diffHWmax = $diffHeight;
        }

        // Get diff amount by now file size and limit.
        $diffFilesize4MaxLimit = $calcSmallerPx['after']['fileSize'] - $maxLimitFileSize;
        //
        if($diffFilesize4MaxLimit > $diffFilesize){
            // The file size will still be too large after the next processing.
            // 預計下次長寬等尺寸縮小後，圖檔案大小依然偏大

            // Get times as big as $diffFilesize.
            //  壓縮後檔案大小差值 與 最大限制的檔案大小差值 的倍數
            $diffFileSizeRatioWithMaxLimit = floor( $diffFilesize4MaxLimit / $diffFilesize);

            /// Following the ratio,obtain length difference. (=,="?
            // BECAUSE jpg not such as this... and No idear.
            // 壓縮後圖案大小差值（寬、高） 乘上 倍數
            $calcedSmallerPx = $diffHWmax* $diffFileSizeRatioWithMaxLimit ;
        }else{// 預計下次長寬等尺寸縮小後，圖檔案大小過小
            // $calcedSmallerPx = $diffHWmax;
            $calcedSmallerPx = $calcSmallerPxDefault;
        }

        // calcedSmallerPx longer than image size.
        $afterHWmax = max($calcSmallerPx['after']['imageSize']['width'], $calcSmallerPx['after']['imageSize']['height']);
        if($calcedSmallerPx > $afterHWmax){ // use default while calc not work.
            $calcedSmallerPx = $calcSmallerPxDefault;
        }

        return $calcedSmallerPx;
    }// EOF calcSmallerPxByFileSize


    /**
     * Resize Image Size to Smaller for compress.
     *
     * @param string $dataString The content of image.
     * @param integer $smallerPx Smaller size amount, unit: px.
     * @return string The content of image after resize.
     */
    function _changeImageSizeToSmaller( $dataString
                                        , $smallerPx = 50 // smaller 10px
    ){

        $size = getimagesizefromstring($dataString);
        $width = $size[0];
        $height = $size[1];
        $ratio = $size[0]/$size[1]; // ratio width and height, width/height.

        /// The longer side get another side by ratio of width and height.
        if( $ratio > 1) { //landscape ( width > height  )
            $maxSize = $width; // The longer side,width
            $newWidth = $maxSize- $smallerPx; // reduce
            $newHeight = round($newWidth/ $ratio); // get another side
        } else { //portrait	( height >= width )
            $maxSize = $height; // The longer side,height
            $newHeight = $maxSize- $smallerPx; // reduce
            $newWidth = round($newHeight* $ratio); // get another side
        }

        // Get functions for image processing.
        $mime = $this->gisfromstring->getMimeType($dataString);
        // $mime = GisFromString::getMimeType($dataString);
        $processImageFuncInfo = $this->getProcessImageFuncInfoByMime($mime);
        if($processImageFuncInfo){
            $image_create_func = $processImageFuncInfo['create_func'];
            $image_save_func = $processImageFuncInfo['save_func'];
            $new_image_ext = $processImageFuncInfo['ext'];
        }

        $img = imagecreatefromstring($dataString);
        $tmp = imagecreatetruecolor($newWidth,$newHeight);
        imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        /*
        * imageXXX() only has two options, save as a file, or send to the browser.
        * It does not provide you the oppurtunity to manipulate the final GIF/JPG/PNG file stream
        * So I start the output buffering, use imageXXX() to output the data stream to the browser,
        * get the contents of the stream, and use clean to silently discard the buffered contents.
        */
        ob_start();
        $image_save_func($tmp);
        $final_image = ob_get_contents();
        ob_end_clean();

        imagedestroy($tmp);
        imagedestroy($img);

        // $this->utils->debug_log('2_compressImageBySize.final_image:', strlen($final_image) );
        return $final_image;
    } // EOF _changeImageSizeToSmaller

    public function upload_proof_of_realname_verification() {
        if (!$this->authentication->isLoggedIn()) {
            return $this->goPlayerLogin();
        }

        $tag = $this->input->post('tag');
        $id_card_number = $this->input->post('id_card_number');
        $player_id = $this->authentication->getPlayerId();
        $file = isset($_FILES['txtImage']) ? $_FILES['txtImage'] : null;

        if (empty($id_card_number)) {
            $this->returnJsonResult(array('status' => 'error', 'message' => lang('notify.117')));
            return;
        }

        if(!preg_match('/^[a-z0-9\s\(\)\-]+$/', $id_card_number )) {
            $this->returnJsonResult(array('status' => 'error', 'message' => lang('notify.115')));
            return;
        }

        if (strlen($id_card_number) > 36) {
            $this->returnJsonResult(array('status' => 'error', 'message' => lang('notify.116')));
            return;
        }

        if(empty($tag)){
            $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('notify.67'), NULL, $this->utils->getPlayerSecurityUrl());
        }

        if(empty($file)){
            $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('collection.upload.msg.unsuccess'), NULL, $this->utils->getPlayerSecurityUrl());
        }

        if(isset($id_card_number) && !empty($id_card_number)){
            $this->load->library(['player_library']);
            $playerdetails['id_card_number'] = $id_card_number;
            $modifiedFields = $this->player_library->checkModifiedFields($player_id, $playerdetails);
            $this->player_library->editPlayerDetails($playerdetails, $player_id);
            $this->player_library->savePlayerUpdateLog($player_id, lang('lang.edit') . ' ' . lang('lang.playerinfo') . ' (' . $modifiedFields . ')', $this->authentication->getUsername()); // Add log in playerupdatehistory

        }

        $result = $this->player_security_library->request_upload_realname_verification($player_id, $tag, $file);

        if($result['status'] != 'error' && isset($id_card_number)){
            $result['msg_type'] = BaseController::MESSAGE_TYPE_SUCCESS;
            $result['msg'] = lang('notify.real_name_verification_updated.');
        } else {
            $result['msg_type'] = BaseController::MESSAGE_TYPE_ERROR;
            $result['msg'] = lang('collection.upload.msg.unsuccess');
        }
        $this->returnCommon($result['msg_type'], $result['msg'], NULL, $this->utils->getPlayerSecurityUrl());
    }

    public function upload_proof_of_address() {
        if (!$this->authentication->isLoggedIn()) {
            return $this->goPlayerLogin();
        }

        $tag = $this->input->post('tag');
        $player_id = $this->authentication->getPlayerId();
        $file = isset($_FILES['txtImage']) ? $_FILES['txtImage'] : null;

        if(empty($tag)){
            $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('notify.67'), NULL, $this->utils->getPlayerSecurityUrl());
        }

        if(empty($file)){
            $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('collection.upload.msg.unsuccess'), NULL, $this->utils->getPlayerSecurityUrl());
        }

        $result = $this->player_security_library->request_upload_address($player_id, $tag, $file);
        $this->returnCommon($result['msg_type'], $result['msg'], NULL, $this->utils->getPlayerSecurityUrl());
    }

    public function upload_proof_of_deposit_withdrawal() {
        if (!$this->authentication->isLoggedIn()) {
            return $this->goPlayerLogin();
        }

        $tag = $this->input->post('tag');
        $player_id = $this->authentication->getPlayerId();
        $file = isset($_FILES['txtImage']) ? $_FILES['txtImage'] : null;

        if(empty($tag)){
            $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('notify.67'), NULL, $this->utils->getPlayerSecurityUrl());
        }

        if(empty($file)){
            $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('collection.upload.msg.unsuccess'), NULL, $this->utils->getPlayerSecurityUrl());
        }

        $result = $this->player_security_library->request_upload_deposit_withdrawal($player_id, $tag, $file);
        $this->returnCommon($result['msg_type'], $result['msg'], NULL, $this->utils->getPlayerSecurityUrl());
    }

    public function upload_proof_of_income() {
        if (!$this->authentication->isLoggedIn()) {
            return $this->goPlayerLogin();
        }

        $tag = $this->input->post('tag');
        $player_id = $this->authentication->getPlayerId();
        $file = isset($_FILES['txtImage']) ? $_FILES['txtImage'] : null;

        if(empty($tag)){
            $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('notify.67'), NULL, $this->utils->getPlayerSecurityUrl());
        }

        if(empty($file)){
            $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('collection.upload.msg.unsuccess'), NULL, $this->utils->getPlayerSecurityUrl());
        }

        $result = $this->player_security_library->request_upload_income($player_id, $tag, $file);
        $this->returnCommon($result['msg_type'], $result['msg'], NULL, $this->utils->getPlayerSecurityUrl());
    }

    public function update_proof_filename($playerId,$verification,$img_file,$profile_image) {
        $data = array(
            'proof_filename' => json_encode(array(
                'verification' => $verification,
                'img_file' => $img_file,
                'profile_image' => $profile_image,
            )),
        );

        $this->player_functions->editPlayerDetails($data, $playerId);
    }

    public function ajax_update_secret_question(){
        $playerId = $this->authentication->getPlayerId();

        if($this->utils->isEnabledFeature('disabled_player_to_change_security_question')){
            $this->load->model('player_model');
            $player = (array)$this->player_model->getPlayerById($playerId);

            if(empty($player)){
                return $this->returnCommon(self::MESSAGE_TYPE_ERROR,lang('notify.67'));
            }

            if(!empty($player['secretQuestion']) && !empty(urldecode($player['secretAnswer']))){
                return $this->returnCommon(self::MESSAGE_TYPE_ERROR,lang('save.failed'));
            }
        }

        $security_question = $this->input->post('security_question');
        $security_answer = $this->input->post('security_answer');

        if(empty($security_answer) || trim($security_answer) == ''){
            return $this->returnCommon(self::MESSAGE_TYPE_ERROR,lang('con.aff04'));
        }

        $data = array(
            'secretQuestion' => $security_question,
            'secretAnswer' => $security_answer,
        );

        $success = $this->player_model->update_secret_question($playerId,$data);

        if(!$success){
            $status = self::MESSAGE_TYPE_ERROR;
            $message = lang('save.failed');
        } else {
            $status = self::MESSAGE_TYPE_SUCCESS;
            $message = lang('sys.gd25');
        }
        return $this->returnCommon($status, $message);
    }
}