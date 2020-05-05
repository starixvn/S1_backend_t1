<?php

/*
 *  Return Key Use Template Name
 *
 *  Template Key Use Language Is As Following [
 *      english,
 *      chinese,
 *      indonesian,
 *      vietnamese,
 *      korean,
 *      thai
 * ]
 */

# Verify email

$script['player_verify_email']['english']['subject'] = "Please Verify Your Email";
$script['player_verify_email']['english']['content'] = "
Hi [player_username] :

Welcome and thanks for joining us.

Verify first your E-mail address by clicking the link below:

[link]

Thank you for choosing us. For further questions, suggestions and inquiries don't hesitate to reply to this email or contact support and we will do our best to assist you.

Enjoy our thrilling games and always remember, our platform is where winning happens!

Regards.
";

$script['player_verify_email']['chinese']['subject'] = "请验证您的电子邮箱";
$script['player_verify_email']['chinese']['content'] = "
您好 [player_username] :

欢迎和感谢您的加入。

点击下面按钮以验证你的电子邮箱。

[link]

如果你无法通过上面链接验证电子邮箱，请复制到浏览器地址栏后操作。

请您妥善保存您的邮箱，任何情况下不要透露给其他人士，如有任何疑问，请随时联系官网的24小时在线客服。

祝您好运连连，盈利多多！

谢谢
";

# Change Login Password successfully

$script['player_change_login_password_successfully']['english']['subject'] = "Login Password Changed Successfully.";
$script['player_change_login_password_successfully']['english']['content'] = "
Hello [player_username] :

We have successfully changed your withdrawal password as your request.

Your new password is: [login_password]

We are happy to be at your service. If you have other inquiries, please do not hesitate to contact our customer service.

Best regards.
";

$script['player_change_login_password_successfully']['chinese']['subject'] = "登入密码变更成功";
$script['player_change_login_password_successfully']['chinese']['content'] = "
您好 [player_username] :

您的账号登入密码已经重置成功。

新密码为: [login_password]

请视情相使用您的新密码登录并立即修改为您的个人密码，如有任何疑问，请随时联系官网的24小时在线客服。

祝您好运连连，盈利多多！
";

# Forgot login password

$script['player_forgot_login_password']['english']['subject'] = "Reset Login Password";
$script['player_forgot_login_password']['english']['content'] = "
Hello [player_username] :

Your verification code is: [verify_code]

To complete your login password reset procedure, please input this verification code.

We are happy to be at your service. If you have other inquiries, please do not hesitate to contact our customer service.

Best regards.
";

$script['player_forgot_login_password']['chinese']['subject'] = "重置登入密码";
$script['player_forgot_login_password']['chinese']['content'] = "
您好 [player_username] :

您的重置密码用验证代码为 : [verify_code]

请输入此验证码以完成您的重置手续。

请您保存好此代碼，任何情况下不要透露给第三方人员，如有任何疑问，请随时联系官网的24小时在线客服。

祝您好运连连，盈利多多！
";

# Change Withdrawal Password Successfully

$script['player_change_withdrawal_password_successfully']['english']['subject'] = "Withdrawal Password Changed Successfully.";
$script['player_change_withdrawal_password_successfully']['english']['content'] = "
Hello [player_username] :

We have successfully changed your withdrawal password as your request.

Your new password is: [withdrawal_password]

We are happy to be at your service. If you have other inquiries, please do not hesitate to contact our customer service.

Best regards.
";

$script['player_change_withdrawal_password_successfully']['chinese']['subject'] = "取款密码变更成功";
$script['player_change_withdrawal_password_successfully']['chinese']['content'] = "
您好 [player_username] :

您的取款密码已经重置成功。

新密码为: [withdrawal_password]

请视情相使用您的新密码登录并立即修改为您的个人密码，如有任何疑问，请随时联系官网的24小时在线客服。

祝您好运连连，盈利多多！
";

# Verify Email Success

$script['player_verify_email_success']['english']['subject'] = "Verify Email Successfully";
$script['player_verify_email_success']['english']['content'] = "
Hi [player_username] :

Welcome and thanks for joining us.

Your E-mail address has been successfully verified.

Thank you for choosing us. For further questions, suggestions and inquiries don't hesitate to contact our customer service and we will do our best to assist you.

Enjoy our thrilling games and always remember,our platform is where winning happens!

Regards.
";

$script['player_verify_email_success']['chinese']['subject'] = "信箱验证成功";
$script['player_verify_email_success']['chinese']['content'] = "
您好 [player_username] :

您已完成验证您的邮箱。非常感谢您的支持与配合。

请您妥善保存您的邮箱，任何情况下不要透露给其他人士，如有任何疑问，请随时联系官网的24小时在线客服。

祝您好运连连，盈利多多！

谢谢
";

return $script;
