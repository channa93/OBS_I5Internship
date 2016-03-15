<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * @Copy Right Borama Consulting
 * 
 * @channa
 * 
 * @3/3/2016
 */
class General_model extends CI_Model {

    public function __construct(){
        parent::__construct();
    } 

    /**
     * Used to get profile of user by social_id and social type
     * @param  $socialId  : string
     * @param  $socialType: int
     * @return profile information of user : object
     */
    public function get_profile_by_social_id($socialId, $socialType) {
        
        try {
            $get_profile = $this->mongo_db
                                ->where_match_element('socialAccount',array(
                                        'socialId'   =>    (string) $socialId,
                                        'socialType' => new MongoInt32($socialType)))
                                ->get(TABLE_PROFILE);
            if ($get_profile) {
                $get_profile[0]['userId'] = $get_profile[0]['_id']->{'$id'};
                //if(empty($get_profile[0]['avatar'])) $get_profile[0]['avatar'] = DEFAULT_PROFILE_AVATAR;
                unset($get_profile[0]['_id']);
                return $get_profile[0];
            }
            return FALSE;
        } catch (Exception $e) {
            return msg_error('Unable to profile by social.');
        }
    }
}
