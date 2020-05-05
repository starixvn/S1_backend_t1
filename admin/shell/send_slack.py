#!/usr/bin/env python3
import urllib.request
import urllib.parse
import json

ASCII = 'ascii'

def send_slack(channel, name, message):
    slack_url = "https://talk.chatchat365.com/hooks/hyzzcabhy7g93j71brmyarhjwy"
    post_data = {"payload": json.dumps({"channel": "{}".format(channel), "username": "{}".format(name), "text": "{}".format(message), "icon_emoji": "{}".format('')})}
    params = urllib.parse.urlencode(post_data)
    params = params.encode(ASCII)
    f = urllib.request.urlopen(slack_url, params)


if __name__ == '__main__':
    import sys
    print(sys.argv)
    channel = sys.argv[1]
    bot_name = sys.argv[2]
    massage = sys.argv[3]
    send_slack(channel, bot_name, massage);
