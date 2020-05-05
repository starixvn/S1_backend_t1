<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rows_to_registration_fields extends CI_Migration {

	public function up() {
		$this->db->query("DELETE FROM `registration_fields`");
		
		$this->db->query("INSERT INTO `registration_fields` (`registrationFieldId`, `type`, `field_name`, `alias`, `visible`, `required`, `updatedOn`, `can_be_required`) 
VALUES 
(1, '1', 'First Name', 'firstName', '0', '0', '2015-07-06 06:16:14', '0'),
(2, '1', 'Last Name', 'lastName', '0', '0', '2015-07-06 06:16:14', '0'),
(3, '1', 'Birthday', 'birthdate', '0', '0', '2015-07-06 06:16:14', '0'),
(4, '1', 'Gender', 'gender', '0', '0', '2015-07-06 06:16:14', '0'),
(5, '1', 'Nationality', 'citizenship', '0', '1', '2015-07-06 06:16:14', '0'),
(6, '1', 'BirthPlace', 'birthplace', '0', '1', '2015-07-06 06:16:14', '0'),
(7, '1', 'Language', 'language', '0', '0', '2015-07-06 06:16:14', '0'),
(8, '1', 'Contact Number', 'contactNumber', '0', '1', '2015-07-06 06:16:14', '0'),
(9, '1', 'Instant Message 1', 'imAccount', '0', '1', '2015-07-06 06:16:14', '0'),
(10, '1', 'Instant Message 2', 'imAccount2', '0', '1', '2015-07-06 06:16:14', '0'),
(11, '1', 'Security Question', 'secretQuestion', '0', '0', '2015-07-06 06:16:14', '0'),
(12, '1', 'Security Answer', 'secretAnswer', '0', '0', '2015-07-06 06:16:14', '0'),
(13, '1', 'Referral Code', 'invitationCode', '0', '1', '2015-07-06 06:16:14', '1'),
(14, '1', 'Affiliate Code', 'affiliateCode', '0', '1', '2015-07-06 06:16:14', '1'),
(15, '2', 'First Name', '', '0', '1', '2015-06-08 14:49:11', '0'),
(16, '2', 'Last Name', '', '0', '1', '2015-06-08 14:49:11', '0'),
(17, '2', 'Birthday', '', '0', '1', '2015-06-08 14:49:11', '0'),
(18, '2', 'Gender', '', '0', '1', '2015-06-08 14:49:11', '0'),
(19, '2', 'Company', '', '0', '1', '2015-06-08 14:49:11', '0'),
(20, '2', 'Occupation', '', '0', '1', '2015-06-08 14:49:11', '0'),
(21, '2', 'Mobile Phone', '', '0', '1', '2015-06-08 14:49:11', '0'),
(22, '2', 'Phone', '', '0', '1', '2015-06-08 14:49:11', '0'),
(23, '2', 'City', '', '0', '1', '2015-06-08 14:49:11', '0'),
(24, '2', 'Address', '', '0', '1', '2015-06-08 14:49:11', '0'),
(25, '2', 'Zip Code', '', '0', '1', '2015-06-08 14:49:11', '0'),
(26, '2', 'State', '', '0', '1', '2015-06-08 14:49:11', '0'),
(27, '2', 'Country', '', '0', '1', '2015-06-08 14:49:11', '0'),
(28, '2', 'Website', '', '0', '1', '2015-06-08 14:49:11', '0'),
(29, '2', 'Instant Message 1', '', '0', '1', '2015-06-08 14:49:11', '0'),
(30, '2', 'Instant Message 2', '', '0', '1', '2015-06-08 14:49:11', '0'),
(31, '1', 'At Least 18 Yrs. Old and Accept Terms and Conditions', '', '0', '0', '2015-07-06 06:16:14', '0');
");
	}

	public function down() {
		$this->db->query("DELETE FROM `registration_fields`");
	}
}