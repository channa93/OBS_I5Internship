<?php

/**
 * used to return the message when missing parameter
 * @param  [array] $param [description]
 * @return [array] array of obejct{code,data,message} for missing parameer
 */

if (!function_exists('msg_missingParameter')) {
    function msg_missingParameter($param) {
        $error = array(
            'code' => PROCESS_FAILS_CODE,
            'data' => '',
            'message' => array('code' => MISSING_PARAM_CODE,'description' => 'Missing parameter '.  implode(',', $param))
        );
        return $error;
    }
}

if (!function_exists('invalid_charactor_length')) {
    function invalid_charactor_length($data, $feild) {
        $error = array(
            'code' => PROCESS_FAILS_CODE,
            'data' => $data,
            'message' => array('code' => INVALID_CHARACTOR_LENGTH,'description' => 'Oop! You have enter many content for '.$feild)
        );
        return $error;
    }
}

if (!function_exists('msg_invalidAccessKey')) {
    function msg_invalidAccessKey() {
        $error = array(
            'code' => PROCESS_FAILS_CODE,
            'data' => '',
            'message' => array('code' => INVALID_ACCESS_KEY,'description' => 'Invalid access key')
        );
        return $error;
    }
}

/**
* used to return the message when success
* @param  [array] $return_result 
* @return [array] array of obejct{code,data,message} for success message
*/
if (!function_exists('msg_success')) {
    function msg_success($return_result) {
        $response = array(
            'code' => SUCCESS_CODE,
            'data' => $return_result,
            'message' =>array('code' => SUCCESS_CODE,'description' => 'success')
        );
        return $response;
    }
}

if (!function_exists('msg_error')) {
    function msg_error($message, $data=[]) {
        $error = array(
            'code' => PROCESS_FAILS_CODE,
            'data' => $data,
            'message' => array('code' => PROCESS_FAILS_CODE,'description' => $message)
        );
        return $error;
    }
}

if (!function_exists('msg_invalidAccessKey')) {
    function msg_invalidAccessKey() {
        $error = array(
            'code' => PROCESS_FAILS_CODE,
            'data' => '',
            'message' => array('code' => INVALID_ACCESS_KEY,'description' => 'Invalid access key')
        );
        return $error;
    }
}

if (!function_exists('msg_exception')) {
    function msg_exception($msg_eexception) {
        $error = array('code' => PROCESS_FAILS_CODE, 'data' => '', 'message' => array('code' => SERVER_IS_NOT_AVAILABLE_CODE, 'description' => "Server is not reachable."));
        return $error;
    }
}

