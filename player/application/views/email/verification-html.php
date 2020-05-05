<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>Welcome to <?php echo $site_name; ?>!</title>
    </head>

    <body>
        <div style="max-width: 800px; margin: 0; padding: 30px 0;">
            <table width="80%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="5%"></td>
                    <td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
                        <h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">Welcome to <?php echo $site_name; ?>!</h2>

                            Thanks for joining <?php echo $site_name; ?>.To verify your account you can click/copy the link below.<br />
                            Follow this link to verify your account:<br />
                        <br />

                        <big style="font: 16px/18px Arial, Helvetica, sans-serif;"><b><a href="http://www.lg.com/auth/verify/<?= $random_verification_code ?>" style="color: #3366cc;">Click me to verify your account</a></b></big><br />
                        <br />
                        Link doesn't work? Copy the following link to your browser address bar:<br />
                        <nobr><a href="http://www.lg.com/auth/verify/<?= $random_verification_code ?>" style="color: #3366cc;">http://www.lg.com/auth/verify/<?= $random_verification_code ?></a></nobr><br />
                        <br />
                        <br />
                        Thank you for choosing us. For further questions, suggestions and inquiries don't hesitate to contact us!<br />
                        The <?php echo $site_name; ?> Team
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>