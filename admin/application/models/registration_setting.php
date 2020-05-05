<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Player
 *
 * This model represents ip data. It operates the following tables:
 * - player
 * - playerdetails
 *
 * @author	Johann Merle
 */

class Registration_setting extends BaseModel {

	const CACHE_TTL = 3600; # 1 hour

	const TABLE = 'registration_fields';

	const PLAYER = 1;
	const AFFILIATE = 2;

	const VISIBLE = 0;
	const HIDDEN = 1;
	const REQUIRED = 0;
	const OPTIONAL = 1;
	const CAN_BE_REQUIRED = 0;

	// OGP-16730: Moved from Marketing_management
	const PASSWORD_MINIMUM_LENGTH = 6;
	const PASSWORD_MAXIMUM_LENGTH = 20;

	private $registration_fields = array();
	private $registration_fields_by_alias = array();
	private $registration_fields_bare = [];

	private function getCacheKey($key) {
		return PRODUCTION_VERSION.'|'.self::TABLE.'|'.$key;
	}

	public function getRegistrationFields($type = self::PLAYER) {
		if (!isset($this->registration_fields[$type])) {
			$this->db->order_by('field_order', 'ASC');
			$query = $this->db->get_where(self::TABLE, array('type' => $type));
			$result = $query->result_array();
			$result = array_combine(array_column($result, 'field_name'), $result);
			$this->registration_fields[$type] = $result;
		}
		$regFields = $this->registration_fields[$type];

		return $regFields;
	}

    public function getRegistrationFieldsByAlias($type = self::PLAYER) {

        if (!isset($this->registration_fields_by_alias[$type])) {
			$this->db->order_by('field_order', 'ASC');
            $query = $this->db->get_where(self::TABLE, array('type' => $type));
            $result = $query->result_array();
            $result = array_combine(array_column($result, 'alias'), $result);
            $this->registration_fields_by_alias[$type] = $result;
        }

        return $this->registration_fields_by_alias[$type];
    }

    /**
     * Reads bare rows from registration_fields
     * @param	int		$type	self::PLAYER or self::AFFILIATE
     * @return	array of rows
     */
    public function getRegistrationFieldsBare($type = self::PLAYER) {
		if (!isset($this->registration_fields_bare[$type])) {
			$this->db->order_by('field_order', 'ASC');
            $query = $this->db->get_where(self::TABLE, array('type' => $type));
            $result = $query->result_array();
            $this->registration_fields_bare[$type] = $result;
        }

        return $this->registration_fields_bare[$type];
    }

    public function checkAccountInfoFieldAllowEdit($player, $fieldName, $disable_edit = false) {
	    $registrationFields = $this->getRegistrationFieldsByAlias();
	    # empty > disable_edit > account_info setting

	    $allowed = false;

	    if (isset($player) && empty($player[$fieldName])) {
	        $allowed = true;
	    } else {
	        if (isset($registrationFields[$fieldName]) && $registrationFields[$fieldName]['account_edit'] == 0) { # 0 is can edit ( Registration Settings )
	            $allowed = !$disable_edit;
	        } else {
	            $allowed = false;
	        }
	    }

	    return $allowed;
    }

	public function isRegistrationFieldVisible($field_name, $type = self::PLAYER) {
		if (isset($this->getRegistrationFields($type)[$field_name]['visible'])) {
			return @$this->getRegistrationFields($type)[$field_name]['visible'] == self::VISIBLE;
		}
		return false;
	}

	public function isRegistrationFieldRequired($field_name, $type = self::PLAYER) {
		return $this->isRegistrationFieldVisible($field_name, $type) &&
		isset($this->getRegistrationFields($type)[$field_name]['required']) &&
		@$this->getRegistrationFields($type)[$field_name]['required'] == self::REQUIRED;
	}

	public function updateRegistrationField($registrationField, $registrationFieldId) {
		$this->utils->deleteCache($this->getCacheKey(self::PLAYER));
		$this->utils->deleteCache($this->getCacheKey(self::AFFILIATE));
		$this->db->update(self::TABLE, $registrationField, array('registrationFieldId' => $registrationFieldId));
	}

	public function displayPlaceholderHint($field_name){
		$required = '';
		if ( $this->system_feature->isEnabledFeature('enabled_display_placeholder_hint_require') ) {
			if ( $this->isRegistrationFieldRequired($field_name) ){
				$required = '(' . lang('Required') . ')';
			}
		}
		return $required;
	}

	public function displaySymbolHint($field_name){
		$required = '';

		if ( $this->isRegistrationFieldRequired($field_name) ){
			$required = '<span class="required_hint"><i class="text-danger register required">*</i></span>';
		}
		return $required;
	}

	public function player_profile_field_requirement() {
		$reg_fields = $this->getRegistrationFields();
		$fields = [];
		foreach ($reg_fields as $row) {
			$fields[$row['alias']] = !(bool) $row['account_required'];
		}

		return $fields;
	}

	# TODO:
	# IS USERNAME AVAILABLE

}

/* End of file ip.php */
/* Location: ./application/models/registration_setting.php */