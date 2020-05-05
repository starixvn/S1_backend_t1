<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Duplicate
 *
 * This model represents duplicate data. It operates the following tables:
 * - duplicate_account_info
 * - player
 * - http_request
 */

class Duplicate_account_info extends CI_Model
{

    public $result_duplicate_account_info = [];
    public $duplicate_account_info_enalbed_condition;

    private $start_date;

	function __construct() {
		parent::__construct();
        $this->load->model(array('duplicate_account_setting'));
        $this->duplicate_account_info_enalbed_condition = $this->config->item('duplicate_account_info_enalbed_condition');

        $calc_days = $this->config->item('duplicate_account_calc_days');
        $this->start_date = date('Y-m-d 00:00:00', strtotime("-$calc_days days"));
	}


    public function generateDupIp($result_insert_arr) {
        $this->result_duplicate_account_info = $result_insert_arr;
        if(in_array("ip", $this->duplicate_account_info_enalbed_condition)) {
             $http_ip_set = $this->getAllDuplicateIp();
            $this->extractPlayerListData('ip', $http_ip_set);
        }

        return $this->result_duplicate_account_info;
    }

    public function generateDupRealName($result_insert_arr) {
        $this->result_duplicate_account_info = $result_insert_arr;
        if(in_array("realname", $this->duplicate_account_info_enalbed_condition)) {
            $dup_first_name_set = $this->getAllDuplicateRealName();
            $this->extractPlayerListData('dup_firstname', $dup_first_name_set);
        }

        return $this->result_duplicate_account_info;
    }

    public function generateDupPassword($result_insert_arr) {
        $this->result_duplicate_account_info = $result_insert_arr;
        if(in_array("password", $this->duplicate_account_info_enalbed_condition)) {
            $dup_password_set = $this->getAllDuplicatePassword();
            $this->extractPlayerListData('dup_password', $dup_password_set);
        }

        return $this->result_duplicate_account_info;
    }

    public function generateDupEmail($result_insert_arr) {
        $this->result_duplicate_account_info = $result_insert_arr;
        if(in_array("email", $this->duplicate_account_info_enalbed_condition)) {
            $dup_email_set = $this->getAllDuplicateEmail();
            $this->extractPlayerListData('dup_email', $dup_email_set);
        }

        return $this->result_duplicate_account_info;
    }

    public function generateDupMobile($result_insert_arr) {
        $this->result_duplicate_account_info = $result_insert_arr;
        if(in_array("mobile", $this->duplicate_account_info_enalbed_condition)) {
            $dup_mobile_set = $this->getAllDuplicateMobile();
            $this->extractPlayerListData('dup_mobile', $dup_mobile_set);
        }

        return $this->result_duplicate_account_info;
    }

    public function generateDupAddress($result_insert_arr) {
        $this->result_duplicate_account_info = $result_insert_arr;
        if(in_array("address", $this->duplicate_account_info_enalbed_condition)) {
            $dup_address_set = $this->getAllDuplicateAddress();
            $this->extractPlayerListData('dup_address', $dup_address_set);
        }

        return $this->result_duplicate_account_info;
    }

    public function generateDupCountry($result_insert_arr) {
        $this->result_duplicate_account_info = $result_insert_arr;
        if(in_array("country", $this->duplicate_account_info_enalbed_condition)) {
            $dup_country_set = $this->getAllDuplicateCountry();
            $this->extractPlayerListData('dup_country', $dup_country_set);
        }

        return $this->result_duplicate_account_info;
    }

    public function generateDupCity($result_insert_arr) {
        $this->result_duplicate_account_info = $result_insert_arr;
        if(in_array("city", $this->duplicate_account_info_enalbed_condition)) {
            $dup_city_set = $this->getAllDuplicateCity();
            $this->extractPlayerListData('dup_city', $dup_city_set);
        }

        return $this->result_duplicate_account_info;
    }

    public function generateDupCookie($result_insert_arr) {
        $this->result_duplicate_account_info = $result_insert_arr;
        if(in_array("cookie", $this->duplicate_account_info_enalbed_condition)) {
            $dup_cookie_set = $this->getAllDuplicateCookie();
            $this->extractPlayerListData('dup_cookie', $dup_cookie_set);
        }

        return $this->result_duplicate_account_info;
    }

    public function generateDupReferrer($result_insert_arr) {
        $this->result_duplicate_account_info = $result_insert_arr;
        if(in_array("referrer", $this->duplicate_account_info_enalbed_condition)) {
            $dup_referrer_set = $this->getAllDuplicateReferrer();
            $this->extractPlayerListData('dup_referrer', $dup_referrer_set);
        }

        return $this->result_duplicate_account_info;
    }

    public function generateDupDevice($result_insert_arr) {
        $this->result_duplicate_account_info = $result_insert_arr;
        if(in_array("device", $this->duplicate_account_info_enalbed_condition)) {
            $dup_device_set = $this->getAllDuplicateDevice();
            $this->extractPlayerListData('dup_device', $dup_device_set);
        }

        return $this->result_duplicate_account_info;
    }

    public function extractPlayerListData($data_type, $player_list_data_set) {
        if(!empty($player_list_data_set)) {
            foreach ($player_list_data_set as $value) {
                $each_player_list_for_data_type = explode(",", $value['player_list']);

                foreach ($each_player_list_for_data_type as $key => $player_id) {
                    $main_player_id = $player_id;
                    $other_player_id_list_for_data_type = $each_player_list_for_data_type;
                    unset($other_player_id_list_for_data_type[$key]);

                    foreach ($other_player_id_list_for_data_type as $check_dup_data_type_player_id) {
                        switch ($data_type) {
                            case 'ip':
                                $dup_data_type_for_two_players = $this->getDupIpInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_type_player_id, $value[$data_type]);
                                $this->createDupIpInfo($dup_data_type_for_two_players);
                                break;

                            case 'dup_firstname':
                                $dup_data_type_for_two_players = $this->getDupRealnameInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_type_player_id, $value[$data_type]);
                                $this->createDupRealNameInfo($dup_data_type_for_two_players);
                                break;

                            case 'dup_password':
                                $dup_data_type_for_two_players = $this->getDupPasswordInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_type_player_id, $value[$data_type]);
                                $this->createDupPasswordInfo($dup_data_type_for_two_players);
                                break;

                            case 'dup_email':
                                $dup_data_type_for_two_players = $this->getDupEmailInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_type_player_id, $value[$data_type]);
                                $this->createDupEmailInfo($dup_data_type_for_two_players);
                                break;

                            case 'dup_mobile':
                                $dup_data_type_for_two_players = $this->getDupMobileInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_type_player_id, $value[$data_type]);
                                $this->createDupMobileInfo($dup_data_type_for_two_players);
                                break;

                            case 'dup_address':
                                $dup_data_type_for_two_players = $this->getDupAddressInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_type_player_id, $value[$data_type]);
                                $this->createDupAddressInfo($dup_data_type_for_two_players);
                                break;

                            case 'dup_country':
                                $dup_data_type_for_two_players = $this->getDupCountryInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_type_player_id, $value[$data_type]);
                                $this->createDupCountryInfo($dup_data_type_for_two_players);
                                break;

                            case 'dup_city':
                                $dup_data_type_for_two_players = $this->getDupCityInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_type_player_id, $value[$data_type]);
                                $this->createDupCityInfo($dup_data_type_for_two_players);
                                break;

                            case 'dup_cookie':
                                $dup_data_type_for_two_players = $this->getDupCookieInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_type_player_id, $value[$data_type]);
                                $this->createDupCookieInfo($dup_data_type_for_two_players);
                                break;

                            case 'dup_referrer':
                                $dup_data_type_for_two_players = $this->getDupReferrerInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_type_player_id, $value[$data_type]);
                                $this->createDupReferrerInfo($dup_data_type_for_two_players);
                                break;

                            case 'dup_device':
                                $dup_data_type_for_two_players = $this->getDupDeviceInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_type_player_id, $value[$data_type]);
                                $this->createDupDeviceInfo($dup_data_type_for_two_players);
                                break;

                            default:
                                # code...
                                break;
                        }

                    }
                }
            }
        }
    }

    public function initValueForRow($combined_player_id) {
        $duplicate_account_info_key_list = ['dup_regIp', 'dup_loginIp', 'dup_depositIp', 'dup_withDrawIp', 'dup_TranMain2SubIp', 'dup_TranSub2MainIp', 'dup_realName', 'dup_passwd', 'dup_email', 'dup_mobile', 'dup_address', 'dup_city', 'dup_country', 'dup_cookie', 'dup_referrer', 'dup_device', 'total_rate'];

        if(isset($this->result_duplicate_account_info[$combined_player_id])) {
            // $this->result_duplicate_account_info[$combined_player_id]['username'] = $this->result_duplicate_account_info[$combined_player_id]['userName'];
            // $this->result_duplicate_account_info[$combined_player_id]['dup_userName'] = $this->result_duplicate_account_info[$combined_player_id]['userName'];
            foreach ($duplicate_account_info_key_list as $duplicate_account_info_key) {
                if($duplicate_account_info_key == 'total_rate') {
                    $this->result_duplicate_account_info[$combined_player_id][$duplicate_account_info_key] = isset($this->result_duplicate_account_info[$combined_player_id][$duplicate_account_info_key]) ? $this->result_duplicate_account_info[$combined_player_id][$duplicate_account_info_key] : 0;
                }
                else {
                    $this->result_duplicate_account_info[$combined_player_id][$duplicate_account_info_key] = isset($this->result_duplicate_account_info[$combined_player_id][$duplicate_account_info_key]) ? $this->result_duplicate_account_info[$combined_player_id][$duplicate_account_info_key] : '';
                }
            }
        }
        else {
            foreach ($duplicate_account_info_key_list as $duplicate_account_info_key) {
                if($duplicate_account_info_key == 'total_rate') {
                    $this->result_duplicate_account_info[$combined_player_id][$duplicate_account_info_key] = 0;
                }
                else {
                    $this->result_duplicate_account_info[$combined_player_id][$duplicate_account_info_key] = '';
                }
            }
        }
    }

    /**
     * get all duplicate ip from http_request
     *
     * @param   array
     * @return  array
     */
    public function getAllDuplicateIp() {

        $skipIP = $this->utils->getConfig('skip_save_http_request_from_ip_list');
        $skipIPStr = ($skipIP) ? '"' . implode('","', $skipIP) . '"' : false;

        $query = '
            SELECT
                  ip,
                  playerId
            FROM http_request
            WHERE
                createdat >= "' . $this->start_date . '"
            ' .
            (($skipIPStr) ? ' AND ip NOT IN (' . $skipIPStr . ') ' : ' ') .
            'GROUP BY ip, playerId'
        ;

        $run = $this->db->query("$query");
        $rlt = $run->result_array();
        $rlt = $this->groupConcat($rlt, 'ip', 'playerId', 'player_list', true);
        return $rlt;
    }

    /**
     * get all duplicate real name from player_details
     *
     *
     * @return  array
     */
    public function getAllDuplicateRealName() {
        $query = '
            SELECT
                playerdetails.firstName as dup_firstname,
                playerdetails.playerId
            FROM playerdetails
            INNER JOIN player on player.playerId = playerdetails.playerId
            WHERE
                playerdetails.firstName IS NOT NULL AND
                playerdetails.firstName != "" AND
                player.lastLoginTime >= "' . $this->start_date . '"
            GROUP BY firstName, playerId
        ';

        $run = $this->db->query("$query");
        $rlt = $run->result_array();
        $rlt = $this->groupConcat($rlt, 'dup_firstname', 'playerId', 'player_list', true);
        return $rlt;
    }

    /**
     * get all duplicate password from player
     *
     *
     * @return  array
     */
    public function getAllDuplicatePassword() {
        $query = '
            SELECT
                password as dup_password,
                playerId
            FROM player
            WHERE
                lastLoginTime >= "' . $this->start_date . '"
            GROUP BY password, playerId
        ';

        $run = $this->db->query("$query");
        $rlt = $run->result_array();
        $rlt = $this->groupConcat($rlt, 'dup_password', 'playerId', 'player_list', true);
        return $rlt;
    }

    /**
     * get all duplicate email from player
     *
     *
     * @return  array
     */
    public function getAllDuplicateEmail() {
        $query = '
            SELECT
                email as dup_email, playerId
            FROM player
            WHERE
                email IS NOT NULL AND
                email != "" AND
                lastLoginTime >= "' . $this->start_date . '"
            GROUP BY email, playerId
        ';

        $run = $this->db->query("$query");
        $rlt = $run->result_array();
        $rlt = $this->groupConcat($rlt, 'dup_email', 'playerId', 'player_list', true);
        return $rlt;
    }

    /**
     * get all duplicate mobile from player_details
     *
     *
     * @return  array
     */
    public function getAllDuplicateMobile() {
        $query = '
            SELECT
                playerdetails.contactNumber as dup_mobile,
                playerdetails.playerId
            FROM playerdetails
            INNER JOIN player on player.playerId = playerdetails.playerId
            WHERE
                playerdetails.contactNumber IS NOT NULL AND
                playerdetails.contactNumber != "" AND
                player.lastLoginTime >= "' . $this->start_date . '"
            GROUP BY contactNumber, playerId
        ';

        $run = $this->db->query("$query");
        $rlt = $run->result_array();
        $rlt = $this->groupConcat($rlt, 'dup_mobile', 'playerId', 'player_list', true);
        return $rlt;
    }

    /**
     * get all duplicate address from player_details
     *
     *
     * @return  array
     */
    public function getAllDuplicateAddress() {
        $query = '
            SELECT
                playerdetails.address as dup_address,
                playerdetails.playerId
            FROM playerdetails
            INNER JOIN player on player.playerId = playerdetails.playerId
            WHERE
                playerdetails.address IS NOT NULL AND
                playerdetails.address != "" AND
                player.lastLoginTime >= "' . $this->start_date . '"
            GROUP BY address, playerId
        ';

        $run = $this->db->query("$query");
        $rlt = $run->result_array();
        $rlt = $this->groupConcat($rlt, 'dup_address', 'playerId', 'player_list', true);
        return $rlt;
    }

    /**
     * get all duplicate country from player_details
     *
     *
     * @return  array
     */
    public function getAllDuplicateCountry() {
        $query = '
            SELECT
                playerdetails.residentCountry as dup_country,
                playerdetails.playerId
            FROM playerdetails
            INNER JOIN player on player.playerId = playerdetails.playerId
            WHERE
                playerdetails.residentCountry IS NOT NULL AND
                playerdetails.residentCountry != "" AND
                player.lastLoginTime >= "' . $this->start_date . '"
            GROUP BY residentCountry, playerId
        ';

        $run = $this->db->query("$query");
        $rlt = $run->result_array();
        $rlt = $this->groupConcat($rlt, 'dup_country', 'playerId', 'player_list', true);
        return $rlt;
    }

    /**
     * get all duplicate city from player_details
     *
     *
     * @return  array
     */
    public function getAllDuplicateCity() {
        $query = '
            SELECT
                playerdetails.city as dup_city,
                playerdetails.playerId
            FROM playerdetails
            INNER JOIN player on player.playerId = playerdetails.playerId
            WHERE
                playerdetails.city IS NOT NULL AND
                playerdetails.city != "" AND
                player.lastLoginTime >= "' . $this->start_date . '"
            GROUP BY city, playerId
        ';

        $run = $this->db->query("$query");
        $rlt = $run->result_array();
        $rlt = $this->groupConcat($rlt, 'dup_city', 'playerId', 'player_list', true);
        return $rlt;
    }

    /**
     * get all duplicate cookie from http_request
     *
     * @param   array
     * @return  array
     */
    public function getAllDuplicateCookie() {
        $query = '
            SELECT
                cookie as dup_cookie,
                playerId
            FROM http_request
            WHERE
                cookie IS NOT NULL AND
                cookie != "" AND
                createdat >= "' . $this->start_date . '"
            GROUP BY cookie, playerId
        ';

        $run = $this->db->query("$query");
        $rlt = $run->result_array();
        $rlt = $this->groupConcat($rlt, 'dup_cookie', 'playerId', 'player_list', true);
        return $rlt;
    }

    /**
     * get all duplicate referrer from http_request
     *
     * @param   array
     * @return  array
     */
    public function getAllDuplicateReferrer() {
        $query = '
            SELECT
                referrer as dup_referrer,
                playerId
            FROM http_request
            WHERE
                referrer IS NOT NULL AND
                referrer != "" AND
                createdat >= "' . $this->start_date . '"
            GROUP BY referrer, playerId
        ';

        $run = $this->db->query("$query");
        $rlt = $run->result_array();
        $rlt = $this->groupConcat($rlt, 'dup_referrer', 'playerId', 'player_list', true);
        return $rlt;
    }

    /**
     * get all duplicate referrer from http_request
     *
     * @param   array
     * @return  array
     */
    public function getAllDuplicateDevice() {
        $query = '
            SELECT
                device as dup_device,
                playerId
            FROM http_request
            WHERE
                device IS NOT NULL AND
                device != "" AND
                createdat >= "' . $this->start_date . '"
            GROUP BY device, playerId
        ';

        $run = $this->db->query("$query");
        $rlt = $run->result_array();
        $rlt = $this->groupConcat($rlt, 'dup_device', 'playerId', 'player_list', true);
        return $rlt;
    }

    /**
     * get duplicate ip info by two playerIds and certain ip
     *
     * @param   string
     * @param   string
     * @return  array
     */
    public function getDupIpInfoByPlayerIdsAndIp($main_player_id, $check_dup_ip_player_id, $ip) {
        $this->load->model(array('http_request'));
        $query = '
            SELECT
                dup_ip_table.combined_player_id,
                dup_ip_table.username,
                dup_ip_table.dup_userName,
                dup_ip_table.dup_regIp,
                dup_ip_table.dup_loginIp,
                dup_ip_table.dup_depositIp,
                dup_ip_table.dup_withDrawIp,
                dup_ip_table.dup_TranMain2SubIp,
                dup_ip_table.dup_TranSub2MainIp,
                (
                    (SELECT CASE WHEN dup_ip_table.dup_regIp IS NULL THEN 0 ELSE dup_ip_table.rate_dup_regIP END)
                    +
                    (SELECT
                        CASE WHEN (
                            dup_ip_table.dup_loginIp IS NULL AND
                            dup_ip_table.dup_depositIp IS NULL AND
                            dup_ip_table.dup_withDrawIp IS NULL AND
                            dup_ip_table.dup_TranMain2SubIp IS NULL AND
                            dup_ip_table.dup_TranSub2MainIp IS NULL)
                        THEN 0
                        ELSE dup_ip_table.rate_dup_loginIP END
                    )
                )
                as rate
            FROM
                (
                    SELECT
                        CONCAT("combined-", '.$main_player_id.', "-",'.$check_dup_ip_player_id.') as combined_player_id,
                        (
                            SELECT username
                            FROM player
                            WHERE playerId="'.$main_player_id.'"
                        ) as username,
                        (
                            SELECT username
                            FROM player
                            WHERE playerId="'.$check_dup_ip_player_id.'"
                        ) as dup_userName,
                        (
                            SELECT ip
                            FROM http_request
                            WHERE
                                ip = "'.$ip.'" AND
                                playerId="'.$main_player_id.'"  AND
                                type = '.Http_request::TYPE_REGISTRATION.' AND
                                createdat >= "' . $this->start_date . '"
                                LIMIT 1
                        ) as dup_regIp,
                        (
                            SELECT ip
                            FROM http_request
                            WHERE
                                ip = "'.$ip.'" AND
                                playerId="'.$main_player_id.'" AND
                                type = '.Http_request::TYPE_LAST_LOGIN.' AND
                                createdat >= "' . $this->start_date . '"
                            LIMIT 1
                        ) as dup_loginIp,
                        (
                            SELECT ip
                            FROM http_request
                            WHERE
                                ip = "'.$ip.'" AND
                                playerId="'.$main_player_id.'" AND
                                type = '.Http_request::TYPE_DEPOSIT.' AND
                                createdat >= "' . $this->start_date . '"
                            LIMIT 1
                        ) as dup_depositIp,
                        (
                            SELECT ip
                            FROM http_request
                            WHERE
                                ip="'.$ip.'" AND
                                playerId="'.$main_player_id.'" AND
                                type = '.Http_request::TYPE_WITHDRAWAL.' AND
                                createdat >= "' . $this->start_date . '"
                            LIMIT 1
                        ) as dup_withDrawIp,
                        (
                            SELECT "'.$ip.'"
                            FROM http_request
                            WHERE
                                ip="'.$ip.'" AND
                                playerId="'.$main_player_id.'" AND
                                type = '.Http_request::TYPE_MAIN_WALLET_TO_SUB_WALLET.' AND
                                createdat >= "' . $this->start_date . '"
                            LIMIT 1
                        ) as dup_TranMain2SubIp,
                        (
                            SELECT ip
                            FROM http_request
                            WHERE
                                ip="'.$ip.'" AND
                                playerId="'.$main_player_id.'" AND
                                type = '.Http_request::TYPE_SUB_WALLET_TO_MAIN_WALLET.' AND
                                createdat >= "' . $this->start_date . '"
                            LIMIT 1
                        ) as dup_TranSub2MainIp,
                        (
                            SELECT rate_exact
                            FROM duplicate_account_setting
                            WHERE item_id='.Duplicate_account_setting::ITEM_IP.'
                        ) as rate_dup_regIP,
                        (
                            SELECT rate_exact
                            FROM duplicate_account_setting
                            WHERE item_id='.Duplicate_account_setting::ITEM_LOGIN_IP.'
                        ) as rate_dup_loginIP
                )
            as dup_ip_table'
        ;

        $run = $this->db->query("$query");
        return $run->row_array();
    }

    /**
     * get duplicate realname info by two playerIds
     *
     * @param   string
     * @param   string
     * @return  array
     */
    public function getDupRealnameInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_player_id, $dup_firstname) {
        $query = '
            SELECT
                CONCAT("combined-", '.$main_player_id.', "-",'.$check_dup_data_player_id.') as combined_player_id,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$main_player_id.'"
                ) as username,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$check_dup_data_player_id.'"
                ) as dup_userName,
                (
                    SELECT firstName
                    FROM playerdetails
                    WHERE
                        playerId="'.$main_player_id.'" AND
                        firstName="'.$dup_firstname.'"
                    LIMIT 1
                ) as dup_realName,
                (
                    SELECT rate_exact
                    FROM duplicate_account_setting
                    WHERE item_id = '.Duplicate_account_setting::ITEM_REAL_NAME.'
                ) as rate
        ';

        $run = $this->db->query("$query");
        return $run->row_array();
    }

    /**
     * get duplicate password info by two playerIds
     *
     * @param   string
     * @param   string
     * @return  array
     */
    public function getDupPasswordInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_player_id, $dup_password) {
        $query = '
            SELECT
                CONCAT("combined-", '.$main_player_id.', "-", '.$check_dup_data_player_id.') as combined_player_id,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$main_player_id.'"
                ) as username,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$check_dup_data_player_id.'"
                ) as dup_userName,
                (
                    SELECT password
                    FROM player
                    WHERE
                        playerId="'.$main_player_id.'" AND
                        password="'.$dup_password.'"
                    LIMIT 1
                ) as dup_passwd,
                (
                    SELECT rate_exact
                    FROM duplicate_account_setting
                    WHERE item_id = '.Duplicate_account_setting::ITEM_PASSWORD.'
                ) as rate
        ';

        $run = $this->db->query("$query");
        return $run->row_array();
    }

    /**
     * get duplicate email info by two playerIds
     *
     * @param   string
     * @param   string
     * @return  array
     */
    public function getDupEmailInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_player_id, $dup_email) {
        $query = '
            SELECT
                CONCAT("combined-", '.$main_player_id.', "-",'.$check_dup_data_player_id.') as combined_player_id,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$main_player_id.'"
                ) as username,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$check_dup_data_player_id.'"
                ) as dup_userName,
                (
                    SELECT email
                    FROM player
                    WHERE
                        playerId="'.$main_player_id.'" AND
                        email="'.$dup_email.'"
                    LIMIT 1
                ) as dup_email,
                (
                    SELECT rate_similar
                    FROM duplicate_account_setting
                    WHERE item_id = '.Duplicate_account_setting::ITEM_EMAIL.'
                ) as rate
        ';

        $run = $this->db->query("$query");
        return $run->row_array();
    }

    /**
     * get duplicate mobile info by two playerIds
     *
     * @param   string
     * @param   string
     * @return  array
     */
    public function getDupMobileInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_player_id, $dup_mobile) {
        $query = '
            SELECT
                CONCAT("combined-", '.$main_player_id.', "-", '.$check_dup_data_player_id.') as combined_player_id,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$main_player_id.'"
                ) as username,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$check_dup_data_player_id.'"
                ) as dup_userName,
                (
                    SELECT contactNumber
                    FROM playerdetails
                    WHERE
                        playerId="'.$main_player_id.'" AND
                        contactNumber="'.$dup_mobile.'"
                    LIMIT 1
                ) as dup_mobile,
                (
                    SELECT rate_exact
                    FROM duplicate_account_setting
                    WHERE item_id = '.Duplicate_account_setting::ITEM_MOBILE.'
                ) as rate
        ';

        $run = $this->db->query("$query");
        return $run->row_array();
    }

    /**
     * get duplicate address info by two playerIds
     *
     * @param   string
     * @param   string
     * @return  array
     */
    public function getDupAddressInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_player_id, $dup_address) {
        $query = '
            SELECT
                CONCAT("combined-", '.$main_player_id.', "-", '.$check_dup_data_player_id.') as combined_player_id,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$main_player_id.'"
                ) as username,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$check_dup_data_player_id.'"
                ) as dup_userName,
                (
                    SELECT address
                    FROM playerdetails
                    WHERE
                        playerId="'.$main_player_id.'" AND
                        address="'.$dup_address.'"
                    LIMIT 1
                ) as dup_address,
                (
                    SELECT rate_exact
                    FROM duplicate_account_setting
                    WHERE item_id = '.Duplicate_account_setting::ITEM_ADDRESS.'
                ) as rate
        ';

        $run = $this->db->query("$query");
        return $run->row_array();
    }

    /**
     * get duplicate country info by two playerIds
     *
     * @param   string
     * @param   string
     * @return  array
     */
    public function getDupCountryInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_player_id, $dup_country) {
        $query = '
            SELECT
                CONCAT("combined-", '.$main_player_id.', "-", '.$check_dup_data_player_id.') as combined_player_id,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$main_player_id.'"
                ) as username,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$check_dup_data_player_id.'"
                ) as dup_userName,
                (
                    SELECT country
                    FROM playerdetails
                    WHERE
                        playerId="'.$main_player_id.'" AND
                        country="'.$dup_country.'"
                    LIMIT 1
                ) as dup_country,
                (
                    SELECT rate_exact
                    FROM duplicate_account_setting
                    WHERE item_id = '.Duplicate_account_setting::ITEM_COUNTRY.'
                ) as rate
        ';

        $run = $this->db->query("$query");
        return $run->row_array();
    }

    /**
     * get duplicate city info by two playerIds
     *
     * @param   string
     * @param   string
     * @return  array
     */
    public function getDupCityInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_player_id, $dup_city) {
        $query = '
            SELECT
                CONCAT("combined-", '.$main_player_id.', "-", '.$check_dup_data_player_id.') as combined_player_id,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$main_player_id.'"
                ) as username,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$check_dup_data_player_id.'"
                ) as dup_userName,
                (
                    SELECT city
                    FROM playerdetails
                    WHERE
                        playerId="'.$main_player_id.'" AND
                        city="'.$dup_city.'"
                    LIMIT 1
                ) as dup_city,
                (
                    SELECT rate_exact
                    FROM duplicate_account_setting
                    WHERE item_id = '.Duplicate_account_setting::ITEM_CITY.'
                ) as rate
        ';

        $run = $this->db->query("$query");
        return $run->row_array();
    }

    /**
     * get duplicate cookie info by two playerIds and certain cookie
     *
     * @param   string
     * @param   string
     * @return  array
     */
    public function getDupCookieInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_player_id, $cookie) {
        $this->load->model(array('http_request'));
        $query = '
            SELECT
                CONCAT("combined-", '.$main_player_id.', "-", '.$check_dup_data_player_id.') as combined_player_id,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$main_player_id.'"
                ) as username,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$check_dup_data_player_id.'"
                ) as dup_userName,
                (
                    SELECT cookie
                    FROM http_request
                    WHERE
                        playerId="'.$main_player_id.'" AND
                        cookie=\''.$cookie.'\'
                    LIMIT 1
                ) as dup_cookie,
                (
                    SELECT rate_exact
                    FROM duplicate_account_setting
                    WHERE item_id = '.Duplicate_account_setting::ITEM_COOKIES.'
                ) as rate
        ';

        $run = $this->db->query("$query");
        return $run->row_array();
    }

    /**
     * get duplicate referrer info by two playerIds and certain referrer
     *
     * @param   string
     * @param   string
     * @return  array
     */
    public function getDupReferrerInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_player_id, $referrer) {
        $this->load->model(array('http_request'));
        $query =
            'SELECT
                CONCAT("combined-", '.$main_player_id.', "-", '.$check_dup_data_player_id.') as combined_player_id,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$main_player_id.'"
                ) as username,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$check_dup_data_player_id.'"
                ) as dup_userName,
                (
                    SELECT referrer
                    FROM http_request
                    WHERE
                        playerId="'.$main_player_id.'" AND
                        referrer="'.$referrer.'"
                    LIMIT 1
                ) as dup_referrer,
                (
                    SELECT rate_exact
                    FROM duplicate_account_setting
                    WHERE item_id = '.Duplicate_account_setting::ITEM_REFERER.'
                ) as rate
        ';

        $run = $this->db->query("$query");
        return $run->row_array();
    }

    /**
     * get duplicate device info by two playerIds and certain device
     *
     * @param   string
     * @param   string
     * @return  array
     */
    public function getDupDeviceInfoByPlayerIdsAndIp($main_player_id, $check_dup_data_player_id, $device) {
        $this->load->model(array('http_request'));

        $query = '
            SELECT
                CONCAT("combined-", '.$main_player_id.', "-", '.$check_dup_data_player_id.') as combined_player_id,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$main_player_id.'"
                ) as username,
                (
                    SELECT username
                    FROM player
                    WHERE playerId="'.$check_dup_data_player_id.'"
                ) as dup_userName,
                (
                    SELECT device
                    FROM http_request
                    WHERE
                        playerId="'.$main_player_id.'" AND
                        device="'.$device.'"
                    LIMIT 1
                ) as dup_device,
                (
                    SELECT rate_exact
                    FROM duplicate_account_setting
                    WHERE item_id = '.Duplicate_account_setting::ITEM_DEVICE.'
                ) as rate
        ';

        $run = $this->db->query("$query");
        return $run->row_array();
    }

    public function getDupAccountTotalInfo() {

        $current_date = date("Y-m-d H:i:s");

        $sql = '
            SELECT
                player.playerId,
                dupUserRate.userName,
                dupUserRate.total_rate,
                "' . $current_date . '" as created_at,
                "' . $current_date . '" as updated_at
            FROM
                (
                    SELECT
                        userName,
                        SUM(total_rate) as total_rate
                    FROM duplicate_account_info
                    GROUP BY userName
                ) as dupUserRate
            INNER JOIN player on player.username = dupUserRate.userName
        ';

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function createDupIpInfo($dup_ip_info_for_two_players) {
        if(!empty($dup_ip_info_for_two_players)) {
            $combined_player_id = $dup_ip_info_for_two_players['combined_player_id'];
            $this->initValueForRow($combined_player_id);

            $this->assignParamsForRow($combined_player_id, $dup_ip_info_for_two_players, [
                'username', 'dup_userName', 'dup_regIp', 'dup_loginIp', 'dup_depositIp', 'dup_withDrawIp', 'dup_TranMain2SubIp', 'dup_TranSub2MainIp'
            ]);

            $this->assignRateForRow($combined_player_id, $dup_ip_info_for_two_players['rate']);
        }
        // $this->utils->debug_log('=============updateDupReport rrr', $this->result_duplicate_account_info);
    }

    public function createDupRealNameInfo($dup_realname_info_for_two_players) {
        if(!empty($dup_realname_info_for_two_players)) {
            $combined_player_id = $dup_realname_info_for_two_players['combined_player_id'];
            $this->initValueForRow($combined_player_id);

            $this->assignParamsForRow($combined_player_id, $dup_realname_info_for_two_players, [
                'username', 'dup_userName', 'dup_realName'
            ]);

            $this->assignRateForRow($combined_player_id, $dup_realname_info_for_two_players['rate']);
        }
        // $this->utils->debug_log('=============updateDupReport rrr', $this->result_duplicate_account_info);
    }

    public function createDupPasswordInfo($dup_passwd_info_for_two_players) {
        if(!empty($dup_passwd_info_for_two_players)) {
            $combined_player_id = $dup_passwd_info_for_two_players['combined_player_id'];
            $this->initValueForRow($combined_player_id);

            $this->assignParamsForRow($combined_player_id, $dup_passwd_info_for_two_players, [
                'username', 'dup_userName', 'dup_passwd'
            ]);

            $this->assignRateForRow($combined_player_id, $dup_passwd_info_for_two_players['rate']);
        }
        // $this->utils->debug_log('=============updateDupReport rrr', $this->result_duplicate_account_info);
    }

    public function createDupEmailInfo($dup_email_info_for_two_players) {
        if(!empty($dup_email_info_for_two_players)) {
            $combined_player_id = $dup_email_info_for_two_players['combined_player_id'];
            $this->initValueForRow($combined_player_id);

            $this->assignParamsForRow($combined_player_id, $dup_email_info_for_two_players, [
                'username', 'dup_userName', 'dup_email'
            ]);

            $this->assignRateForRow($combined_player_id, $dup_email_info_for_two_players['rate']);
        }
        // $this->utils->debug_log('=============updateDupReport rrr', $this->result_duplicate_account_info);
    }

    public function createDupMobileInfo($dup_mobile_info_for_two_players) {
        if(!empty($dup_mobile_info_for_two_players)) {
            $combined_player_id = $dup_mobile_info_for_two_players['combined_player_id'];
            $this->initValueForRow($combined_player_id);

            $this->assignParamsForRow($combined_player_id, $dup_mobile_info_for_two_players, [
                'username', 'dup_userName', 'dup_mobile'
            ]);

            $this->assignRateForRow($combined_player_id, $dup_mobile_info_for_two_players['rate']);
        }
        // $this->utils->debug_log('=============updateDupReport rrr', $this->result_duplicate_account_info);
    }

    public function createDupAddressInfo($dup_address_info_for_two_players) {
        if(!empty($dup_address_info_for_two_players)) {
            $combined_player_id = $dup_address_info_for_two_players['combined_player_id'];
            $this->initValueForRow($combined_player_id);

            $this->assignParamsForRow($combined_player_id, $dup_address_info_for_two_players, [
                'username', 'dup_userName', 'dup_address'
            ]);

            $this->assignRateForRow($combined_player_id, $dup_address_info_for_two_players['rate']);
        }
        // $this->utils->debug_log('=============updateDupReport rrr', $this->result_duplicate_account_info);
    }

    public function createDupCountryInfo($dup_country_info_for_two_players) {
        if(!empty($dup_country_info_for_two_players)) {
            $combined_player_id = $dup_country_info_for_two_players['combined_player_id'];
            $this->initValueForRow($combined_player_id);

            $this->assignParamsForRow($combined_player_id, $dup_country_info_for_two_players, [
                'username', 'dup_userName', 'dup_country'
            ]);

            $this->assignRateForRow($combined_player_id, $dup_country_info_for_two_players['rate']);
        }
        // $this->utils->debug_log('=============updateDupReport rrr', $this->result_duplicate_account_info);
    }

    public function createDupCityInfo($dup_city_info_for_two_players) {
        if(!empty($dup_city_info_for_two_players)) {
            $combined_player_id = $dup_city_info_for_two_players['combined_player_id'];
            $this->initValueForRow($combined_player_id);

            $this->assignParamsForRow($combined_player_id, $dup_city_info_for_two_players, [
                'username', 'dup_userName', 'dup_city'
            ]);

            $this->assignRateForRow($combined_player_id, $dup_city_info_for_two_players['rate']);
        }
        // $this->utils->debug_log('=============updateDupReport rrr', $this->result_duplicate_account_info);
    }

    public function createDupCookieInfo($dup_cookie_info_for_two_players) {
        if(!empty($dup_cookie_info_for_two_players)) {
            $combined_player_id = $dup_cookie_info_for_two_players['combined_player_id'];
            $this->initValueForRow($combined_player_id);

            $this->assignParamsForRow($combined_player_id, $dup_cookie_info_for_two_players, [
                'username', 'dup_userName', 'dup_cookie'
            ]);

            $this->assignRateForRow($combined_player_id, $dup_cookie_info_for_two_players['rate']);
        }
        // $this->utils->debug_log('=============updateDupReport rrr', $this->result_duplicate_account_info);
    }

    public function createDupReferrerInfo($dup_referrer_info_for_two_players) {
        if(!empty($dup_referrer_info_for_two_players)) {
            $combined_player_id = $dup_referrer_info_for_two_players['combined_player_id'];
            $this->initValueForRow($combined_player_id);

            $this->assignParamsForRow($combined_player_id, $dup_referrer_info_for_two_players, [
                'username', 'dup_userName', 'dup_referrer'
            ]);

            $this->assignRateForRow($combined_player_id, $dup_referrer_info_for_two_players['rate']);
        }

        // $this->utils->debug_log('=============updateDupReport rrr', $this->result_duplicate_account_info);
    }

    public function createDupDeviceInfo($dup_device_info_for_two_players) {
        if(!empty($dup_device_info_for_two_players)) {
            $combined_player_id = $dup_device_info_for_two_players['combined_player_id'];
            $this->initValueForRow($combined_player_id);

            $this->assignParamsForRow($combined_player_id, $dup_device_info_for_two_players, [
               'username', 'dup_userName', 'dup_device'
            ]);

            $this->assignRateForRow($combined_player_id, $dup_device_info_for_two_players['rate']);
        }
        // $this->utils->debug_log('=============updateDupReport rrr', $this->result_duplicate_account_info);
    }

    public function createDupReport($data)
    {
        $this->db->trans_start();
        $this->createDupInfoReport($data);
        $this->createDupTotalReport();
        $this->db->trans_complete();
    }

    private function createDupInfoReport($duplicate_account_info_arr) {
        if(!empty($duplicate_account_info_arr)) {
            $chunk_size = ceil((count($duplicate_account_info_arr) / 1000));
            $chunk_data = array_chunk($duplicate_account_info_arr, $chunk_size);
            $i= 1;
            foreach ($chunk_data as $data) {
                $this->db->insert_batch('duplicate_account_info', $data);
            }
        }
        $this->utils->debug_log("==================" . __FUNCTION__ . '================== chunk size: ' . $chunk_size );
    }

    private function createDupTotalReport()
    {
        $duplicate_account_total_data = $this->getDupAccountTotalInfo();
        $chunk_size = ceil((count($duplicate_account_total_data) / 1000));
        $chunk_data = array_chunk($duplicate_account_total_data, $chunk_size);
        foreach ($chunk_data as $data) {
            $this->db->insert_batch('duplicate_account_total', $data);
        }
        $this->utils->debug_log("==================" . __FUNCTION__ . '================== chunk size: ' . $chunk_size );
    }

    public function clearDuplicateRelationTable()
    {
        $this->db->empty_table('duplicate_account_total');
        $this->db->empty_table('duplicate_account_info');
    }

    private function assignParamsForRow($combined_player_id, $dupData, array $params_key)
    {
        foreach ($params_key as $column_key) {
            if ($column_key == 'username' && !$dupData[$column_key]) {
                unset($this->result_duplicate_account_info[$combined_player_id]);
                break;
            }
            $this->result_duplicate_account_info[$combined_player_id][$column_key] =
                empty($this->result_duplicate_account_info[$combined_player_id][$column_key]) ?
                    is_null($dupData[$column_key]) ? '' : $dupData[$column_key]
                    :
                    $this->result_duplicate_account_info[$combined_player_id][$column_key];
        }
    }

    private function assignRateForRow($combined_player_id, $rate)
    {
        if (!isset($this->result_duplicate_account_info[$combined_player_id]['username'])) {
            return;
        }

        $this->result_duplicate_account_info[$combined_player_id]['total_rate'] =
        empty($this->result_duplicate_account_info[$combined_player_id]['total_rate']) ?
            $rate
            :
            $this->result_duplicate_account_info[$combined_player_id]['total_rate'] + $rate;
    }

    private function groupConcat($data, $keyword, $groupBy, $groupByAlias, $is_duplication = false)
    {
        $groupDataByKeyWord = [];
        foreach ($data as $row) {
            if (!isset($groupDataByKeyWord[$row[$keyword]])) {
                $groupDataByKeyWord[$row[$keyword]] = [];
            }
            $groupDataByKeyWord[$row[$keyword]][] = $row[$groupBy];
        }

        $_data = [];
        foreach ($groupDataByKeyWord as $key => $array) {
            if ($is_duplication && count($array) <= 1) {
                continue;
            }
            $_data[] = [
                $keyword => $key,
                $groupByAlias => implode(",", $array)
            ];
        }

        return $_data;
    }
}