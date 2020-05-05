#!/usr/bin/env python3
# Purpose: Show top 10 traffic IP address.
# Description: This script parse "iptraf" tool log
#              Statistic peer IP address traffic,
#              aften sort show the top traffic IP address,
#              by indication.
# SYNOPSIS: show_top_traffic_ip.py <filename> <top number>
#           filename: iptraf log file path.
#           top number: want show how much top number.
import sys; sys.dont_write_bytecode = True
import time
import os
import socket
import fcntl
import struct
import send_slack
import math

destrnation_ip = ''

def convertSize(size):
    size_name = ("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB")
    i = int(math.floor(math.log(size, 1024)))
    p = math.pow(1024, i)
    s = round(size / p, 2)
    if (s > 0):
        return '%s %s' % (s,size_name[i])
    else:
        return '0B'

"""Following is iptraf log example.
Sun Jan 24 13:48:08 2016; TCP; Connection 120.7.55.19:51268 to 119.9.95.69:80 timed out, 7 packets, 2206 bytes, avg flow rate 0.02 kbits/s; opposite direction 7 packets, 2100 bytes, avg flow rate 0.02 kbits/s
Sun Jan 24 08:00:02 2016; TCP; eth0; 40 bytes; from 119.9.95.69:80 to 61.240.234.139:61707; FIN sent; 4 packets, 1019 bytes, avg flow rate 1.33 kbits/s
Mon Jan 25 08:00:01 2016; TCP; eth0; active; from 183.94.217.192:19967 to 119.9.95.69:80; 6 packets, 2846 bytes
Fri Feb  5 08:00:13 2016; UDP; eth0; 71 bytes; from 119.9.95.69:48092 to 120.136.32.63:53
Fri Feb  5 09:08:17 2016; UDP; eth0; 1163 bytes; from 201.187.104.26 to 119.9.95.69; fragment

"""
def statistic_ip_traffic(filename):
    statistic_ip = {}
    start_date = ""
    end_date = ""
    with open(filename, 'r') as f:
        counter = 0
        while True:
            line = f.readline()
            if len(line) == 0:
                break
            elif line.find('packets') >= 0 and line.find('bytes') >= 0 or line.find('UDP') >= 0:
                counter += 1
                tmp = line.split('; ')
                d = tmp[0]
                if counter == 1:
                    start_date = d
                else:
                    end_date = d
                try:
                    protocol = tmp[1].strip()
                    if protocol == 'UDP':
                        interface = tmp[2]
                        size = tmp[3]
                        from_ip_port, to_ip_port = tmp[4].strip('from ').split(' to ')
                        try:
                            from_ip, from_port = from_ip_port.split(':')
                            to_ip, to_port = to_ip_port.split(':')
                            amt_bytes = int(size.strip(' bytes'))
                        except ValueError:
                            from_ip = from_ip_port
                            from_port = ''
                            to_ip = to_ip_port
                            to_port = ''
                    else:
                        if tmp[2].find('Connection') >= 0:
                            state = 'Connection'
                            interface = ''
                            size = ''
                            from_ip_port, to_ip_port = tmp[2].strip('Connection ').strip(' timed out').split(' to ')
                            amt_packets, amt_size, avg_rate = tmp[3].split(', ')
                        else:
                            interface = tmp[2]
                            if tmp[3] == 'active':
                                status = tmp[3]
                                size = ''
                                amt_packets, amt_size = tmp[5].strip().split(', ')
                                avg_rate = ''
                            else:
                                size = tmp[3]
                                state = tmp[5]
                                amt_packets, amt_size, avg_rate = tmp[6].split(', ')
                            from_ip_port, to_ip_port = tmp[4].strip('from ').split(' to ')
                        from_ip, from_port = from_ip_port.split(':')
                        to_ip, to_port = to_ip_port.split(':')
                        amt_bytes = int(amt_size.strip(' bytes'))
                except Exception as e:
                    print(e)
                    print(line)

                if to_ip == destination_ip:
                    try:
                        statistic_ip[from_ip] += amt_bytes
                    except KeyError:
                        statistic_ip[from_ip] = amt_bytes
    return (statistic_ip, start_date, end_date)

def get_top_num_ip_message(num, statistic_ip, start_date, end_date):
    message = ""
    if len(statistic_ip):
        message += "Total IPs {} `{}` - `{}`\n".format(len(statistic_ip), start_date, end_date)
        sort_statistic = ((k, statistic_ip[k]) for k in sorted(statistic_ip, key=statistic_ip.get, reverse=True))
        counter = 0
        for k, v in sort_statistic:
            counter += 1
            message += "{:<3}:`{:<17}` `{}`\n".format(counter, k, convertSize(v))
            if counter >= num:
                break
    return message

if __name__ == '__main__':
    if not os.geteuid() == 0:
        sys.exit("\nOnly root can run this script\n")
    try:
        destination_ip = sys.argv[1]
        filename = sys.argv[2]
        top_number = int(sys.argv[3])
    except:
        msg = "show_top_traffic_ip.py <destination IP> <filename> <top number>"
        sys.exit(msg)
    statistic_ip, start_date, end_date = statistic_ip_traffic(filename)
    msg = get_top_num_ip_message(top_number, statistic_ip, start_date, end_date)
    print(msg)
    send_slack.send_slack("#parse_log", "IP traffic statistic", msg)
