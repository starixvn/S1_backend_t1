#!/usr/bin/env python3
# Purpose: Analysis nginx access log for find out large access ip and auto block it.
# Descript: First this get nginx's access, then statistic IP info and make simple judge
#           these IPs whether have threat, notification threat IP and information.
# SYNOPSIS: find_ip.py -f <FILE> -n <NUMBER> -m <block|check> -k <nickname> -t <minute> [-c config]
# Options:
#      -h, --help            show this help message and exit
#      -f <FILE>, --file=<FILE>
#                            Parse log file.
#      -n <NUMBER>, --number=<NUMBER>
#                            Will read log file lines from reciprocal number.
#      -m <block|check>, --mode=<block|check>
#                            Choose mode [block|check], If use check will "not"
#                            block IP just prompt else block will block.
#      -k <nikename>, --nikename=<nikename>
#                            Salck notification's user name
#      -t <minute>, --time-minute=<minute>
#                            Scan before <minute> minute
#      -c [config], --config=[config]
#                            Config whitelist file format 'json'
# Whitelist json format: {'comment': {'ip1': 'comment', 'ip2': 'comment'}, whitelist: [ 'ip1', 'ip2', 'ip3', 'ip4']}
# TODO: two level example 10 alert 100 block

import sys; sys.dont_write_bytecode = True
import urllib.request
import urllib.parse
import json
import ipaddress
import subprocess
import re
import os
import time
import logging
import datetime
import send_slack as slack

if not os.geteuid() == 0:
    sys.exit("\nOnly root can run this script\n")

logger = logging.getLogger()
handler = logging.StreamHandler(sys.stdout)
formatter = logging.Formatter('%(asctime)s %(name)-12s %(levelname)-8s %(message)s')
handler.setFormatter(formatter)
logger.addHandler(handler)
logger.setLevel(logging.INFO)

WHITELIST = set([
    "121.58.225.10", # Customer company IP
    "10.176.97.165",
    "119.9.110.56",
    "10.176.98.210",
    "119.9.110.160",
    "104.98.3.14",
    "165.254.102.160",
    "165.254.102.166",
    "165.254.27.77",
    "165.254.27.92",
    "184.50.87.103",
    "184.50.87.116",
    "184.50.87.29",
    "184.50.87.36",
    "206.160.170.14",
    "206.160.170.60",
    "208.185.115.116",
    "208.185.115.125",
    "209.18.46.100",
    "63.243.242.86",
    "63.243.242.95",
    "96.17.68.108",
    "96.17.68.110",
    "10.176.67.15",
    "10.176.69.193",
    "119.9.95.69",
    "139.162.16.136",
    "180.232.133.50",
    "10.176.67.15",
    "10.176.69.193",
    "103.28.248.0/22",
    "119.9.95.69",
    "127.0.0.1",
    "149.126.72.0/21",
    "185.11.124.0/22",
    "192.230.64.0/18",
    "198.143.32.0/19",
    "199.83.128.0/21",
    "45.64.64.0/22"
])

def run_shell(cmd):
    logger.debug(cmd)
    for i in range(3):
        p = subprocess.Popen("{}".format(cmd),
                shell=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        outs, errs = p.communicate(timeout=15)
        if len(errs) > 0:
            logger.error(errs)
            time.sleep(1)
        else:
            break
    return outs

IGNORE_END_PATH = ('.png', 'jpg', 'gif', '.js', '.css', 'favicon.ico')
ASCII = 'ascii'
UFW_GET_DENY_CMD = "/usr/sbin/ufw status | grep DENY | awk '{print $3}'"
THREAT_IPS = [t_ip.decode(ASCII) for t_ip in run_shell(UFW_GET_DENY_CMD).splitlines()]
QUERY_TIMES = 300
HTTP_METHOD = ("POST", "GET", "HEAD")
BLOCK_MODE = "block"
CHECK_MODE = "check"
RULE_LARGE_QUERY = "large_query"
RULE_MANY_POST = "many_post"
RULE_ONLY_POST = "only_post"
RULE_IN_WHITELIST = "in_whitelist_not_block"

class ThreatAnalyzer:
    def __init__(self, filename, last_lines, scan_minutes):
        self.origin_data = run_shell("tail -n {} {}".format(last_lines, filename)).splitlines()
        self.time_flag = datetime.datetime.now().replace(second=0, microsecond=0) - datetime.timedelta(minutes=scan_minutes)
        self.block_ips = {}
        self.ips_info = {}
        self.url_total = {}
        self.whitelist = WHITELIST
        logger.debug(self.whitelist)
        self._parse_raw_data()

    def set_whitelist(self, whitelist_file):
        self.whitelist.update(set(json.loads(open(whitelist_file, "r").read())['whitelist']))
        logger.debug(self.whitelist)

    def _parse_raw_data(self):
        """Parse raw data for data structure."""
        date_pattern = re.compile(r".*\[(.*?):\d\d\s+\+.*\]")
        url_pattern = re.compile(r".*\"(GET|POST|HEAD)\s+?(.*?) HTTP/\d\.\d\"")
        datetime_pattern = "%d/%b/%Y:%H:%M"
        # Reversed read file list.
        for line in reversed(self.origin_data):
            line = line.decode(ASCII)
            ip = line.split()[0]
            try:
                divide_date_string = date_pattern.match(line).groups()[0]
                http_method, url_path = url_pattern.match(line).groups()
                access_time = datetime.datetime.strptime(divide_date_string, datetime_pattern)
                if access_time < self.time_flag:
                    logger.info("break access by {}".format(access_time))
                    break
            except AttributeError as e:
                """Ignore non-matches string."""
                logger.error(e)
                logger.error(line)
                continue
            self._record_url_info(divide_date_string, http_method, url_path)
            self._record_ips_info(ip, divide_date_string, http_method, url_path)

    def _record_url_info(self, key, method, url_path):
        """Recode all url info."""
        if key not in self.url_total:
            self.url_total[key] = {}
        u_m_key = "{} {}".format(method, url_path)
        if u_m_key not in self.url_total[key]:
            self.url_total[key][u_m_key] = 0
        self.url_total[key][u_m_key] += 1

    def _record_ips_info(self, ip, divide_date, method, url_path):
        if divide_date not in self.ips_info:
            self.ips_info[divide_date] = {}
        if ip not in self.ips_info[divide_date]:
            self.ips_info[divide_date][ip] = {'count': 0, 'url': dict((method, []) for method in HTTP_METHOD)}
        self.ips_info[divide_date][ip]['count'] += 1
        self.ips_info[divide_date][ip]['url'][method].append(url_path)

    def judge_threat(self):
        """Start judge threat"""
        for divide_date in sorted(self.ips_info):
            for ip in sorted(self.ips_info[divide_date]):
                if ip in THREAT_IPS:
                    continue
                self.__rule_large_query(divide_date, ip)
                self.__rule_many_post(divide_date, ip)
                self.__rule_only_post(divide_date, ip)

    def __rule_large_query(self, divide_date, ip):
        if self.ips_info[divide_date][ip]['count'] > QUERY_TIMES:
            self._record_thread_ip_info(ip, divide_date, RULE_LARGE_QUERY)

    def __rule_many_post(self, divide_date, ip):
        if self.ips_info[divide_date][ip]['count'] > QUERY_TIMES / 4 and len(self.ips_info[divide_date][ip]['url']['GET']) == 0:
            self._record_thread_ip_info(ip, divide_date, RULE_MANY_POST)

    def __rule_only_post(self, divide_date, ip):
        """If one IP address only use POST.
        But I think packet must big than 10 only have threat.
        """
        if len(self.ips_info[divide_date][ip]['url']['GET']) == 0 and len(self.ips_info[divide_date][ip]['url']['POST']) > 10:
            self._record_thread_ip_info(ip, divide_date, RULE_ONLY_POST)

    def __in_whitelist(self, ip):
        is_cdn = False
        for cdn in self.whitelist:
            if ipaddress.ip_address(ip) in ipaddress.ip_network(cdn):
                logger.debug("{} in {}".format(ip, cdn))
                is_cdn = True
                break
        return is_cdn

    def _record_thread_ip_info(self, ip, divide_date, match_rule):
        if ip not in self.block_ips:
            self.block_ips[ip] = {}
        if divide_date not in self.block_ips[ip]:
            self.block_ips[ip][divide_date] = {}
            ip_info = {
                    'count': self.ips_info[divide_date][ip]['count'],
                    'url': self.ips_info[divide_date][ip]['url'],
                    'rule': set([match_rule])
            }
            self.block_ips[ip][divide_date] = ip_info
        else:
            # Update dictionary
            self.block_ips[ip][divide_date]['rule'].add(match_rule)

        if RULE_IN_WHITELIST not in self.block_ips[ip][divide_date]['rule'] and self.__in_whitelist(ip):
            self.block_ips[ip][divide_date]['rule'].add(RULE_IN_WHITELIST)

    def generate_threat_ip_message(self):
        """Generate threat IP's message by count the IP one minute query times."""
        message = ""
        if len(self.url_total) == 0:
            return message
        if len(self.block_ips.keys()) > 0:
            message += "Please check following IP:\n"
            for ip in self.block_ips:
                message += "*Alert* `{}`\n".format(ip)
                for d in self.block_ips[ip]:
                    t_ip_info = self.block_ips[ip][d]
                    message += "  - {} = {} \n".format(d, t_ip_info['count'])
                    message += "    - Match rule = {}\n".format(",".join(t_ip_info['rule']))
                    for method in HTTP_METHOD:
                        message += "    - {} = {}\n".format(method, len(t_ip_info['url'][method]))
        return message
    def block_threat_ip(self):
        """Block threat IPs use ufw."""
        ips = []
        if len(self.block_ips.keys()) > 0:
            for ip in self.block_ips:
                for d in self.block_ips[ip]:
                    hit_rules = self.block_ips[ip][d]['rule']
                    if RULE_IN_WHITELIST in hit_rules:
                        break
                    if RULE_LARGE_QUERY in hit_rules and RULE_ONLY_POST in hit_rules:
                        cmd = "/usr/sbin/ufw insert 1 deny from {}".format(ip)
                        run_shell(cmd)
                        logger.info(cmd)
                        ips.append(ip)
                        break
        return ips

    def generate_top_path_usage_message(self):
        """Generate the query largest number path in limited time."""
        message = ""
        if len(self.url_total) == 0:
            return message
        max_times_url = ""
        max_times = 0
        url_total_count, start, end = self._url_total_count_dict()
        start_divide_date = ""
        end_divide_date = ""

        for url in url_total_count:
            if url_total_count[url] > max_times:
                max_times = url_total_count[url]
                max_times_url = "`{}`".format(url)
            elif url_total_count[url] == max_times:
                max_times_url += "\n"
                max_times_url = "`{}`".format(url)
        message += "Top path from {} - {}\n".format(start, end)
        message += "Times = {}\n".format(max_times)
        message += "URL path = \n{}\n".format(max_times_url)
        return message

    def generate_top_url_list(self, number):
        message = ""
        if len(self.url_total) == 0:
            return message
        top_list, start, end = self._get_top_url_list(number)
        message += "Top {} URL info from `{}` - `{}`\n".format(number, start, end)
        for top_info in top_list:
            amount, url = top_info.split(':')
            message += "{:<5}:`{}`\n".format(amount, url)
        return message

    def _get_top_url_list(self, number):
        """Sort by URL path count exam [{"2": "url1"},{"1", "url2"}]}"""
        url_total_dict, start, end = self._url_total_count_dict()
        sort_dict = {}
        for url in url_total_dict:
            key = url_total_dict[url]
            if key not in sort_dict:
                sort_dict[key] = []
            sort_dict[key].append(url)
        top_list = []
        counter = 0
        for i in sorted(sort_dict, reverse=True):
            for url in sort_dict[i]:
                counter += 1
                top_info = "{}:{}".format(i, url)
                top_list.append(top_info)
                if counter >= number:
                    break
            if counter >= number:
                break
        return (top_list, start, end)

    def _url_total_count_dict(self):
        """Arrange IP info by {"path": "path count"}"""
        counter = 0
        total_count = {}
        for divide_date in sorted(self.url_total, reverse=True):
            counter += 1
            if counter == 1:
                end_divide_date = divide_date
            else:
                start_divide_date = divide_date

            for url in self.url_total[divide_date]:
                if self._url_path_ignore(url):
                    continue
                if url not in total_count:
                    total_count[url] = 0
                total_count[url] += self.url_total[divide_date][url]

        start_divide_date = divide_date
        return (total_count, start_divide_date, end_divide_date)

    def _url_path_ignore(self, url_path):
        """Ignore some special path pattern."""
        for ig in IGNORE_END_PATH:
            if urllib.parse.urlparse(url_path).path.endswith(ig):
                return True
        return False

if __name__ == '__main__':
    from optparse import OptionParser

    mode_usage="""Choose mode [block|check], If use check will "not" block IP just prompt else block will block."""
    parser = OptionParser(usage='Usage: %prog -f <FILE> -n <NUMBER> -m <block|check> -k <nickname> -t <minute> [-c config]')
    parser.add_option("-f", "--file", dest="filename",
                      help="Parse log file.", metavar="<FILE>")
    parser.add_option("-n", "--number", dest="number", type='int', metavar="<NUMBER>",
                      help="Will read log file lines from reciprocal number.")
    parser.add_option("-m", "--mode", dest="mode", help=mode_usage, metavar="<block|check>")
    parser.add_option("-k", "--nikename", dest="nikename", help="Salck notification's user name", metavar="<nikename>")
    parser.add_option("-t", "--time-minute", type='int', dest="time_minute", help="Scan before <minute> minute", metavar="<minute>")
    parser.add_option("-c", "--config", dest="config_filename", help="Config whitelist file format 'json'", metavar="[config]")

    option, args = parser.parse_args()
    logger.debug(option)
    file_path = option.filename
    last_lines = option.number
    mode = option.mode
    slack_nickname = option.nikename
    scan_minutes = option.time_minute
    whitelist = option.config_filename

    if file_path == None or last_lines == None or mode == None or slack_nickname == None or scan_minutes == None:
        sys.exit(parser.error('Arguments error.'))

    slack_user = 'Network Monitor {}'.format(slack_nickname)
    blockedip_channel = "#blockedip"
    top_ten_channel = "#parse_log"
    logger.info('Start run mode: {}'.format(mode))

    ter = ThreatAnalyzer(file_path, last_lines, scan_minutes)
    if whitelist:
        ter.set_whitelist(whitelist)

    if mode == BLOCK_MODE:
        # Try to find therat IP address info, and notification use slack for show it.
        ter.judge_threat()
        threat_ip_message = ter.generate_threat_ip_message()
        if threat_ip_message != '':
            logger.info(threat_ip_message)
            slack.send_slack(blockedip_channel, slack_user, threat_ip_message)

        ips = ter.block_threat_ip()
        # Block threat IP and notification it.
        if len(ips) > 0:
            block_message = "Block `{}`".format(', '.join(ips))
            logger.info(block_message)
            slack.send_slack(blockedip_channel, slack_user, block_message)
    else:
        # Find out top 10 useage URL path, and notification it.
        top_ten_message = ter.generate_top_url_list(10)
        if top_ten_message != '':
            logger.info(top_ten_message)
            slack.send_slack(top_ten_channel, slack_user, top_ten_message)

    logger.info('End')
