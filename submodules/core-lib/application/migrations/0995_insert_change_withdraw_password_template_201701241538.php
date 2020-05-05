<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_change_withdraw_password_template_201701241538 extends CI_Migration {

	private $tableName = 'operator_settings';



	public function up() {


// $template_ch = <<<EOD

// <html><body>
// <p style='color:#222;font-size:13px;font-family:Verdana;'> 您好 [lastname] [firstname]!</p><br/>
// <p style='color:#222;font-size:13px;font-family:Verdana;'> 您的密码已经重设 </p>
// <p style='color:#222;font-size:13px;font-family:Verdana;'> 您的新密码是: <b> [password] </b></p>
// <p style='color:#222;font-size:13px;font-family:Verdana;'> 很高兴为您服务 如果你疑问，请直接联系我们的客服. </p><br/>
// <p style='color:#222;font-size:13px;font-family:Verdana;'> 致敬, </p>
// <p style='color:#222;font-size:13px;font-family:Verdana;'> 网站管理员 </p><br/>
// <p style='color:rgb(57, 132, 198);font-size:13px;font-family:Verdana;'> 这是一条自动信息，请不要回复. </p>
// </body></html>

// EOD
// ;

// $template_en = <<<EOD

// <html><body>
// <p style='color:#222;font-size:13px;font-family:Verdana;'> Hello [lastname] [firstname]!</p><br/>
// <p style='color:#222;font-size:13px;font-family:Verdana;'> We have successfully changed your password as your request. </p>
// <p style='color:#222;font-size:13px;font-family:Verdana;'> Your new password is: <b> [password] </b></p>
// <p style='color:#222;font-size:13px;font-family:Verdana;'> We are happy to be at your service. If you have other inquiries, please do not hesitate to contact our customer service.</p><br/>
// <p style='color:#222;font-size:13px;font-family:Verdana;'> Best regards, </p>
// <p style='color:#222;font-size:13px;font-family:Verdana;'> Admin </p><br/>
// <p style='color:rgb(57, 132, 198);font-size:13px;font-family:Verdana;'> This is an automated message, please don't reply. </p>
// </body></html>

// EOD
// ;



// $data = array(
//    array(
// 		"name" => 'email_change_withdrawal_password_template_cn' ,
// 		"value" => 'email',
// 		"note" => 'Change Withdrawal password chinese' ,
// 		"template" => $template_ch,
// 		'description_json' => '{"type":"text","default_value":""}'
//    ),
//    array(
// 		"name" => 'email_change_withdrawal_password_template_en',
// 		"value" => 'email',
// 		"note" => 'Change Withdrawal password english',
// 		"template" => $template_en ,
// 		'description_json' => '{"type":"text","default_value":""}'
//    )
// );


// $this->db->insert_batch($this->tableName, $data);


}

	public function down() {
		// $this->db->delete($this->tableName, array('name' => 'email_change_withdrawal_password_template_cn'));
		// $this->db->delete($this->tableName, array('name' => 'email_change_withdrawal_password_template_en'));
	}

}
