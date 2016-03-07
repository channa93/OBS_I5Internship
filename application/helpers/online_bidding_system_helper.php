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
