<form class="form-horizontal" id="search-form" method="get" role="form">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h4 class="panel-title"><i class="fa fa-list"></i>&nbsp;Test Email Response Logs <?//=lang('player.ui48');?> </h4>
		</div>
	    <div class="panel-body">
	        <pre><textarea rows="30" style="width: 100%;resize: none;" disabled> <?php
			$this->load->library('email_setting');
			$mail_config=$this->input->post();
			$mail_config['is_debug']=true;
			$var = $this->email_setting->sendEmail($this->input->post('email'), array(
				'from_name' => $this->input->post('mail_from'),
				'from' => $this->input->post('mail_from_email'),
				'subject' => 'test ' . random_string() . ' at ' . $this->utils->getNowForMysql(),
				'body' => 'test ' . random_string() . ' date:' . $this->utils->getNowForMysql(),
			), $mail_config);
			?></textarea></pre>
			<?php
			if ($var != 1)  {
				$message = str_replace("\n", "<br><br>\n", lang('smtp.setting.test.failed').".\nError: ".$var);
				$show_message = array(
				'result' => 'danger',
				'message' => $message,
			);
			$this->session->set_userdata($show_message);
			} else if($var == 1) {
				$show_message = array(
				'result' => 'success',
				'message' => lang('smtp.setting.test.success'),
			);
			$this->session->set_userdata($show_message);

			}

			?>
			<br>

			<a class="btn btn-primary pull-right" href="/cms_management/smtp_setting"> Back </a>
	    </div>

	</div>
</form>