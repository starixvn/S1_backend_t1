#!/usr/bin/env python
# Purpose: Send email and attachment
# Syntax: send_mail.py -u <user> -p <password> -s <subject> -t <to_users> [-f files]

import os
import smtplib
import datetime
from email.mime.multipart import MIMEMultipart
from email.mime.base import MIMEBase
from email.mime.text import MIMEText
#from email.utils import COMMASPACE, formatdate
from email import encoders

def send_mail(subject, user, password, smtp_server, to_users=[], files=[]):
    msg = MIMEMultipart()
    msg['Subject'] = subject
    msg['From'] = user
    msg['To'] = ','.join(to_users)

    for f in files:
        if f == '':
            break
        part = MIMEBase('application', 'octet-stream')
        try:
            with open(f, 'rb') as attach_file:
                part.set_payload(attach_file.read())
                encoders.encode_base64(part)
                part.add_header('Content-Disposition', 'attachment; filename="{}"'.format(os.path.basename(f)))
                msg.attach(part)
        except IOError as e:
            pass

    server = smtplib.SMTP_SSL(host=smtp_server)
    server.login(user, password)
    server.sendmail(user, to_users, msg.as_string())
    server.quit()

if __name__ == '__main__':
    import sys
    from optparse import OptionParser

    usage = """send_mail.py -u <user> -p <password> -s <subject> -t <to_users> [-f files] [--server smtp_server]"""
    parser = OptionParser(usage='Usage: {}'.format(usage))
    parser.add_option("-u", "--user", dest="user", help="Send e-mail's user.", metavar="USER")
    parser.add_option("-p", "--password", dest="password", help="Send e-mail user's password.", metavar="PASSWORD")
    parser.add_option("-s", "--subject", dest="subject", help="The mail subject.", metavar="SUBJECT")
    parser.add_option("-t", "--to_user", dest="to_users", help="Post to users, use comma separated.", metavar="TO_USER")
    parser.add_option("-f", "--files", dest="files", help="Attach files location, use comma separated.", default='', metavar="FILES")
    parser.add_option("--server", dest="smtp_server", help="SMTP server name, default use 'smtp.gmail.com'", default='smtp.gmail.com', metavar="SERVER")
    option, args = parser.parse_args()

    if None in [option.subject, option.user, option.password, option.to_users]:
        sys.exit(parser.error('Arguments error.'))

    send_mail(option.subject, option.user, option.password, option.smtp_server, option.to_users.split(','), option.files.split(','))
