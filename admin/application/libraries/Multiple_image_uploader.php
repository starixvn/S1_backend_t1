<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

    /**
    * This library is created for uploading multiple images
    *
    * Functions (For futher information about the function check it inside the class):
    *     set_upload_configs($array_config) - Use this function to set the configuration you want for the uploading
    *                                       - NOTE: that this configuration is the same for CI Library - Upload
    *                                               (here is the link for reference: https://www.codeigniter.com/userguide3/libraries/file_uploading.html#setting-preferences)
    *     do_multiple_uploads($variable) - Use this function to execute the multiple-upload of images
    *
    *  Demo:
    *      1st. Create a <form> that has enctype="multipart/form-data" and method="post".
    *          eg. <form enctype="multipart/form-data" method="post"></form>
    *      2nd. Create a <input> inside the created form that has type="file", a variable with array for name, and multiple property.
    *          eg. <input type="file" name="imageFiles[]" multiple />
    *      3rd. Create a <input> inside the created form that has type="submit".
    *          eg. <input type="submit" name="uploadButton" value="UPLOAD" />
    *      4th. Use these functions (See the comments on the functions inside the class for examples.):
    *          set_upload_configs($array_config) for setting up the configuration of uploading the images,
    *          do_multiple_uploads($variable) by executing this function you will be able to upload the said images.
    */
    class Multiple_image_uploader {
        public function __construct() {
            $this->CI =& get_instance(); // Assigning this to use the native libraries and maximize the OOP of CI
            $this->CI->load->helper(array('form', 'url','html'));

        }

        public $temp_array = array();

        /**
         * This function aims to upload multiple images
         *
         * @param  Array $variable the image data collected from the inputfile.
         * @param  Array $config list of configuration in uploading of image.
         * @param  String $path file directory of image uploaded.
         * @return Array of filename of images and status.
        */
        public function do_multiple_uploads($variable,$path,$config,$custom_name = null) {

            $this->CI->load->library('upload','utils');

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // Count image
            $number_of_files_uploaded = count($variable['name']);
            $filename = [];
            // Faking upload calls to $_FILE
            for ($i = 0; $i < $number_of_files_uploaded; $i++) :
                $_FILES['userfile']['name']     = $variable['name'][$i];
                $_FILES['userfile']['type']     = $variable['type'][$i];
                $_FILES['userfile']['tmp_name'] = $variable['tmp_name'][$i];
                $_FILES['userfile']['error']    = $variable['error'][$i];
                $_FILES['userfile']['size']     = $variable['size'][$i];
                $ext = explode('.', $variable['name'][$i]);
                $ext = $ext[count($ext) - 1];
                $ext_allowed = explode("|",$config['allowed_types']);//"jpg|jpeg|gif|png|PNG"
                $in_array = in_array(strtolower($ext), $ext_allowed);

                if (empty($custom_name)) {
                    $newFilename = str_random(5).strtotime('today').str_random(5);
                } else {
                    $newFilename = $custom_name;
                }


                if(!$in_array){
                    return array(
                        "status" => "fail",
                        "message" => sprintf(lang('upload image limit and format'),$config['max_size']/1000000,$config['allowed_types']),
                    );
                }

                $config['file_name'] = $newFilename;
                $this->CI->upload->initialize($config);

                if ( ! $this->CI->upload->do_upload()) :
                    return array(
                        "status" => "fail",
                        "message" => $this->CI->upload->display_errors(),//lang('An error occurred and the upload failed.'),
                    );
                else :
                    $final_files_data[] = $this->CI->upload->data();
                    $filename[] = $newFilename.".".$ext;
                    // Continue processing the uploaded data
                endif;
            endfor;
            return array(
                "status" => "success",
                "filename" => $filename,
            );
        }
    }
