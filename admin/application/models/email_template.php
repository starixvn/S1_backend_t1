<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Email_template extends CI_Model {

	/*
	CREATE TABLE `og`.`email_template` (
	  `email_template_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	  `template_name` VARCHAR(45) NOT NULL,
	  `template_subject` TEXT NOT NULL,
	  `template_message` TEXT NOT NULL,
	  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  `updated_at` TIMESTAMP NULL,
	  PRIMARY KEY (`email_template_id`),
	  UNIQUE INDEX `template_name_UNIQUE` (`template_name` ASC));
	*/

	const DATABASE_TABLE = 'email_template';

	function __construct() {
		parent::__construct();
	}

	function add_email_template($template_name, $subject, $message) {
		$this->db->insert('email_template', array(
			'template_name' => $template_name,
			'template_subject' => json_encode($subject),
			'template_message' => json_encode($message),
		));
	}

	function get_email_template($template_name, $language, $data) {

		$query = $this->db->get_where(self::DATABASE_TABLE, array('template_name' => $template_name));
		$template = $query->row_array();

		$subject = json_decode($template['template_subject'], true);
		$subject = isset($subject[$language]) ? $subject[$language] : reset($subject);
		$subject = $this->process_template($subject, $data);

		$message = json_decode($template['template_message'], true);
		$message = isset($message[$language]) ? $message[$language] : reset($message);
		$message = $this->process_template($message, $data);

		return isset($subject, $message) ? array(
			'subject' => $subject, 
			'message' => $message,
		) : false;

	}

	function process_template($template, $data) {
		$result = preg_replace_callback("#\[([^\]]+)\]#", function($match) use ($data) {
			return isset($data[$match[1]]) ? $data[$match[1]] : 'N/A';
		}, $template);
		return $result;
	}

	function migrate() {

		$this->db->from('operator_settings');
		$this->db->where('value', 'email');
		$query = $this->db->get();
		$tempates_result = $query->result_array();

		$templates = array();
		foreach ($tempates_result as $template_row) {

			$template_name = $template_row['name'];
			$language = '1';

			if (substr($template_row['name'], -3) == '_cn') {
				$template_name = substr($template_row['name'], 0, -3);
				$language = '2';
			}

			$templates[$template_name][$language] = $template_row['template'];

		}

		foreach ($templates as $key => $value) {
			$this->add_email_template($key, array('1' => $key, '2' => $key), $value);
		}
		
	}

}