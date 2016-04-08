<?php

/**
 * check the require parameter in the given input array
 * @param  [associative array] $array_input
 * @return true if required parameter is passing, else return the require param key that not passing
 */
if (!function_exists('require_parameter')) {  
    function require_parameter($array_input) {
        $result = array();
        try {
            foreach ($array_input as $paramKey => $paramVal) {
                if (($paramVal === "") || ($paramVal === false) || ($paramVal === null) || empty($paramVal)) {
                    $result[] = $paramKey;
                }
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        if (empty($result)) {
            return true;
        } else {
            return $result;
        }
    }   
}


if (!function_exists('filter_param_update')) {   // we dont update if their field cotain empty
     function filter_param_update($update_data) {
        $output = array();
        foreach ($update_data as $key => $val) {
            if ($val != '' || $val!=null || $val!=false || !empty($val))
                $output[$key] = $val;
        }
        $output['modifiedDate'] = date(DATE_FORMAT);
        $output['isEdit'] = true;

        return $output;
    } 
}



if (!function_exists('check_charactor_length')) {
    function check_charactor_length($content='', $length){
        if(empty($content)) return TRUE;
        if (count($content) > $length) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

}

if (!function_exists('authenticate')) {
    function authenticate($accessKey) {
        $CI = &get_instance();
        $CI->load->library('mongo_db');
        $compareAcc = $CI->mongo_db->where(array('accessKey' => $accessKey))->get(TABLE_PROFILE);
        if ($compareAcc) {
            $objUserID = (array) $compareAcc['0']['_id'];
            $compareAcc[0]['userId'] = $objUserID['$id'];
            return $compareAcc[0];
        } else {
            return FALSE;
        }
    }

}

// check if product exist
if (!function_exists('check_product_exist')) {
    function check_product_exist($productId){
        $CI = &get_instance();
        $CI->load->library('mongo_db');

        try {
            $product = $CI->mongo_db
                        ->where(array('_id' => new MongoId($productId)))
                        ->get(TABLE_PRODUCT);

            if(empty($product)) return false;
            else return true;

        } catch (Exception $e) {
            return msg_exception(($e->getMessage()));
        }     
    }
}

// check if bidroom exist
if (!function_exists('check_bidroom_exist')) {
    function check_bidroom_exist($bidroomId){
        $CI = &get_instance();
        $CI->load->library('mongo_db');

        try {
            $product = $CI->mongo_db
                        ->where(array('_id' => new MongoId($bidroomId)))
                        ->get(TABLE_BIDROOM);

            if(empty($product)) return false;
            else return true;

        } catch (Exception $e) {
            return msg_exception(($e->getMessage()));
        }     
    }
}





/**
 * used to generate access key for new user
 * @param  [string] $id   
 * @param  [date] $date 
 * @return [string] access key     
 */
if (!function_exists('generate_access_key')) {
    
    function generate_access_key($id, $date) {
        $key = $id . $date . ENCRYPT_KEY;
        return base64_encode($key);
    } 
}

/**
 * Used to get the base url for later use. i.e: Path to image
 */

if (!function_exists('base_url')) {
    function base_url($atRoot=FALSE, $atCore=FALSE, $parse=FALSE){
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

            $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), NULL, PREG_SPLIT_NO_EMPTY);
            $core = $core[0];

            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf( $tmplt, $http, $hostname, $end );
        }
        else $base_url = 'http://localhost/';

        if ($parse) {
            $base_url = parse_url($base_url);
            if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
        }

        return $base_url;
    }
}


if (!function_exists('validate_file_type')) {

    function validate_file_type($file) {
        $allowedExts = array("gif", "jpeg", "jpg", "png","PNG","JPEG");
        $allowedType = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png");
        $temp = explode(".", $file["name"]);
        $extension = end($temp);

        if (in_array($file["type"], $allowedType) && ($file["size"] < 4400000) && in_array($extension, $allowedExts)) {
            return TRUE;
        }
        return FALSE;
    }

}
if (!function_exists('upload_file')) {
     function upload_file($path,$file, $sizes = array(100 => 100, 150 => 150, 250 => 250))
    {
        try {

            $image_name = $file['name']; 
            $check_extension = explode('.', $image_name); 
            $exp = end($check_extension);
            if (!validate_file_type($file)) {
                return msg_error('Invalid file type or size', $file);
            }
            $full_name = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).round(microtime(true)) . basename($file['name']);
            $full_name=str_replace(" ","_",$full_name);
            $full_name=  preg_replace("/( |\+|\|\,|\(|\)|')/", "", $full_name);
            $full_name=strip_tags($full_name);
            $full_path = $path .'/'. $full_name;
           
            $upload = move_uploaded_file($file["tmp_name"], $full_path); 
            chmod($full_path, 0777);
            if ($upload == true) { 
//                foreach ($sizes as $w => $h) { 
//                   _resize_image($full_name,$full_path,$w,$h); 
//                }
                return $full_name;
            }
            return false;
        } catch (Exception $exc) {
            return msg_error('Unable to upload.');
        }
    }
}

if (!function_exists('validate_email')) {
    function validate_email($value){
         if (!filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            return true;
          } else {
            return false;
          }
    }  
}
if (!function_exists('create_folder')) {
    function create_folder($folderName, $fullPath = false) {
        $cwd = getcwd();
        $dir = getcwd() . '/' . UPLOAD_PATH_IMAGE_PRODUCT. '/'. $folderName;
        if (file_exists($dir))
            return $fullPath ? $dir : $folderName;

        mkdir(UPLOAD_PATH_IMAGE_PRODUCT. '/' . $folderName, 0744, true);
        chmod(UPLOAD_PATH_IMAGE_PRODUCT. '/' . $folderName, 0744);
        clearstatcache();
        return $fullPath ? $dir : $folderName;
    }

}




