<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Get template data
 * * Create/update template
 * * Sync template from json file
 * * Fix default templates
 *
 * @category promo_rules_template
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Promo_rule_templates extends BaseModel {

	protected $tableName = 'promo_rule_templates';

	/**
	 * overview : Promo_rule_templates constructor.
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * overview : get template
	 *
	 * @param  int	$id
	 * @return array
	 */
	function getTemplateById($id) {
		$this->db->from($this->tableName)->where('id', $id)->where('status', self::STATUS_NORMAL);
		return $this->runOneRowArray();
	}

	/**
	 * overview : get template list
	 *
	 * @return array
	 */
	function getTemplateList() {
		$this->db->from($this->tableName)->where('status', self::STATUS_NORMAL);
		return $this->runMultipleRowArray();
	}

	/**
	 * overview : create template
	 *
	 * @param string $template_name
	 * @param string $template_parameters
	 * @param string $template_content
	 * @return mixed
	 */
	function createTemplate($template_name, $template_parameters, $template_content) {
		$data = array(
			'template_name' => $template_name,
			'template_parameters' => $template_parameters,
			'template_content' => $template_content,
			'status' => self::STATUS_NORMAL,
			'created_at' => $this->utils->getNowForMysql(),
			'updated_at' => $this->utils->getNowForMysql(),
		);

		return $this->insertData($this->tableName, $data);
	}

	/**
	 * overview : update template
	 *
	 * @param int 	 $id
	 * @param string $template_name
	 * @param string $template_parameters
	 * @param string $template_content
	 * @return bool
	 */
	function updateTemplate($id, $template_name, $template_parameters, $template_content) {
		$data = array(
			'template_name' => $template_name,
			'template_parameters' => $template_parameters,
			'template_content' => $template_content,
			'updated_at' => $this->utils->getNowForMysql(),
		);

		$this->db->where('id', $id)->set($data);
		return $this->runAnyUpdate($this->tableName);
	}

	/**
	 * overview : $template
	 * @param $template_name
	 * @param $template_parameters
	 * @param $template_content
	 * @return bool|mixed
	 */
	function syncTemplate($template_name, $template_parameters, $template_content) {
		$this->db->from($this->tableName)->where('template_name', $template_name);
		$row = $this->runOneRowArray();
		if (!empty($row)) {
			return $this->updateTemplate($row['id'], $template_name, $template_parameters, $template_content);
		} else {
			return $this->createTemplate($template_name, $template_parameters, $template_content);
		}
	}

	/**
	 * overivew : sync template from json file
	 *
	 * @param $jsonFile
	 * @return bool|mixed
	 */
	function syncTemplateFromJsonFile($jsonFile) {
		$jsonObj = json_decode(file_get_contents($jsonFile), true);
		return $this->syncTemplate(
			$jsonObj['template_name'],
			json_encode($jsonObj['template_parameters']),
			json_encode(array('json_info' => $jsonObj['json_info'], 'formula' => $jsonObj['formula']))
		);
	}

	/**
	 * overview : fix default templates
	 */
	function fixDefaultTemplates() {

		$templatesList = array(
			"first_deposit.json",
			"every_deposit.json",
			"rescue.json",
		);

		foreach ($templatesList as $template) {
			$jsonFile = APPPATH . 'config/promorules/' . $template;
			$this->syncTemplateFromJsonFile($jsonFile);
		}
	}
}

///END OF FILE///////////////////