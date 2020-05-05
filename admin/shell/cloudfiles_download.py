#!/usr/bin/env python3
# Purpose: Download access log from rackspace
# Description: Use rackspace URL API by authentication get cloudfiles's URL info.
#              Use specific container get file list, and download these file.
# SYNOPSIS: cloudfiles_download.py <download to local directory> <api authentication file> [specific date]
#           authentication file: format example <user>:=<api key>
#           specific date: format example "2016/01/01"
# Example: cloudfiles_download.py /tmp /home/user/mykey 2016/01/08
import time
import urllib.request
import urllib.parse
import json
import os
import sys
import threading
from queue import Queue
#import pprint
#pp = pprint.PrettyPrinter(indent=4)

q = Queue()

def worker():
    while True:
        item = q.get()
        try:
            delete_url_from_endpoint(**item)
        except Exception as e:
            print(e)
        q.task_done()

for i in range(100):
    t = threading.Thread(target=worker)
    t.daemon = True
    t.start()

def parse_api_auth_info_file(filename):
    with open(filename, 'r') as f:
        user, api_key = f.readline().split(':=')
    return (user, api_key.strip())

def get_url_info(rs_user, rs_apikey, region):
    rackspace_url = "https://identity.api.rackspacecloud.com/v2.0/tokens"
    print("Getting URL info use region '{}'".format(region))
    headers = {"Content-type": "application/json"}
    data = {"auth": {
                        "RAX-KSKEY:apiKeyCredentials": {
                            "username": rs_user,
                            "apiKey": rs_apikey
                        }
                    }
          }
    data = json.dumps(data).encode('utf8')
    req = urllib.request.Request(rackspace_url, data=data, headers=headers)
    response = urllib.request.urlopen(req)
    response_data = json.loads(response.read().decode('ascii'))
    token = response_data['access']['token']['id']
    data_url = ''
    for d in response_data['access']['serviceCatalog']:
        if d['name'] == 'cloudFiles':
            for dd in d['endpoints']:
                if dd['region'] == region:
                    data_url = dd['publicURL']
    return (token, data_url)

def get_container_data_path_list(token, endpoint_url, container):
    print("Getting container '{}' path list".format(container))
    headers = {'X-Auth-Token': token, "Accept": "application/json"}
    req = urllib.request.Request("{}/{}".format(endpoint_url, container), headers=headers)
    s = urllib.request.urlopen(req)
    cdn_list = s.read().decode('ascii')
    container_data_json = json.loads(cdn_list)
    url_list = []
    for row in container_data_json:
        if row['content_type'] == 'application/octet-stream':
            url_list.append("{}".format(row['name']))
    return url_list

def delete_url_from_endpoint(token, endpoint_url, container, file_path):
    print('start delete {}'.format(endpoint_url))
    headers = {'X-Auth-Token': token}
    req = urllib.request.Request(endpoint_url, headers=headers, method='DELETE')
    s = urllib.request.urlopen(req)

def download_url_from_endpoint(token, endpoint_url, container, file_path, folder):
    headers = {'X-Auth-Token': token}
    req = urllib.request.Request(endpoint_url, headers=headers)
    s = urllib.request.urlopen(req)
    download_to_path = "{}/{}".format(folder, file_path.replace('/', '_'))
    store_folder = os.path.dirname(folder)
    if not os.path.exists(folder):
        os.makedirs(folder)
    with open(download_to_path, 'wb') as f:
        print("Downloading {} to {}".format(endpoint_url, download_to_path))
        f.write(s.read())

def download_container_all_data(rs_user, rs_apikey, container, region, folder, specific_date):
    token, endpoint_url = get_url_info(rs_user, rs_apikey, region)
    path_list = get_container_data_path_list(token, endpoint_url, container)
    for path in path_list:
        if path.find(specific_date) >= 0:
            download_url_from_endpoint(token, endpoint_url, container, path, folder)

def delete_container_all_data(rs_user, rs_apikey, region, container):
    token, endpoint_url = get_url_info(rs_user, rs_apikey, region)
    counter = 1
    path_set = set()
    while True:
        path_list = get_container_data_path_list(token, endpoint_url, container)
        if len(path_list) == 0:
            break
        for path in path_list:
            url = "{}/{}/{}".format(endpoint_url, container, path)
            if path in path_set:
                continue
            path_set.add(path)
            item = {'token': token, 'endpoint_url': url, 'container': container, 'file_path': path}
            q.put(item)
            counter += 1
        time.sleep(20)
    q.join()
    print("Delete {} files".format(counter))

if __name__ in '__main__':
    container = '.CDN_ACCESS_LOGS'
    region = 'IAD'
    if len(sys.argv) < 3:
        usage = "<download to local directory> <api authentication file> [specific date]\n"
        usage += "\tspecific date: format 2016/01/01"
        msg = "Argument error!\n"
        msg += "{} {}".format(sys.argv[0], usage)
        sys.exit(msg)
    folder = sys.argv[1]
    rs_user, rs_apikey = parse_api_auth_info_file(sys.argv[2])
    try:
        specific_date = sys.argv[3]
    except:
        specific_date = '/'
    download_container_all_data(rs_user, rs_apikey, container, region, folder, specific_date)
    #container = 'z_DO_NOT_DELETE_CloudBackup_v2_0_3862dcbb-79a2-4146-b9b1-be105ed5a09e'
    #delete_container_all_data(rs_user, rs_apikey, 'HKG', container)
