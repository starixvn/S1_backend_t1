<form class="form-horizontal" id="search-form" method="get" role="form">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h4 class="panel-title"><i class="fa fa-list"></i>&nbsp;<?=lang('Test SMTP API Response Logs'); ?> </h4>
		</div>
	    <div class="panel-body">
	        <pre><textarea rows="30" style="width: 100%;resize: none;" disabled> <?php

	        $CI =& get_instance();
	        
			$smtp_api = $CI->utils->getConfig('current_smtp_api');
            $CI->load->library('smtp/'.$smtp_api);
            $CI->load->model('operatorglobalsettings');

            $smtp_api = strtolower($smtp_api);
            $api = $CI->$smtp_api;

            $subject = 'test ' . random_string() . ' at ' . $CI->utils->getNowForMysql();
            $body = 'test ' . random_string() . ' date:' . $CI->utils->getNowForMysql();

            $SMTP_API_RESULT = $api->sendEmail($email, $from_email, $from_name, $subject, $body, null, null,TRUE);

            $rlt = $api->isSuccess($SMTP_API_RESULT);

            $CI->utils->debug_log("SMTP API RESPONSE: " . var_export($rlt, true));

            if(!$rlt) $CI->utils->debug_log("SMTP API ERROR RESPONSE: " . var_export($api->getErrorMessages($SMTP_API_RESULT), true));

			?></textarea></pre>
			<?php
				$show_message = array(
					'result' => 'success',
					'message' => lang('smtp.setting.test.success'),
				);

				if (!$rlt)  {
					$message = str_replace("\n", "<br><br>\n", lang('smtp.setting.test.failed').".\nError: ".var_export($api->getErrorMessages($SMTP_API_RESULT), true));
					$show_message = array(
						'result' => 'danger',
						'message' => $message,
					);
				} 

				$CI->session->set_userdata($show_message);
			?>
			<br>

			<a class="btn btn-primary pull-right" href="/cms_management/smtp_setting"> Back </a>
	    </div>

	</div>
</form>