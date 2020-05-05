#!/usr/bin/env bash
# Purpose: This script for auto backup remote config data.
# Description: This script will schedule to backup on remote hosts.
#              Remote hosts configuration by file conf/remote_backup.conf.
#              Example: <remote address>;<backup dir1>,<backup dir2>,...
#              Mean:
#                 <remote address>: remote_address address or domain name.
#                 <backup dir>: Example /etc/nginx,/etc/postfix.
#
#    echo "argument error" >&2
#    echo "$0 [key path]" >&2
#    echo "    key path: ssh private key path" >&2
#    echo "    example: $0 /home/vagrant/my.key" >&2
#    exit 1

config_dir="conf"
config_file="remote_backup.conf"
current_dir=$(dirname $(readlink -f $0))
config_path="$current_dir/$config_dir/$config_file"
timestrap="$(date +'%Y_%m_%d_%s')"
temporary="/tmp"
backup_dir_name="backup_$timestrap"
temporary_backup_dir="$temporary/$backup_dir_name"
backup_dir="$HOME/config_backup"
backup_tarball="$backup_dir/$backup_dir_name.tar.bz2"

if [ ! -f $config_path ]; then
    echo "Config file not exists." >&2
    exit 1
fi

schedule_list=($(cat $config_path | awk -F: '{print $1":"$2}' |  sed 's/ //g' | sed 's/\#.*//g'))
key_path=$(readlink -f $1)
key_args=""
if [ ! -z "$key_path" ]; then
    key_args=" -i $key_path"
    echo $key_args
fi
user="vagrant"
auto_deploy_script="/home/vagrant/Code/og_git/admin/shell/auto_deploy.sh"
code_dir="/home/vagrant/Code"
echo "This script will backup remote config."
echo "remote info:"

for list in ${schedule_list[@]}; do
    echo "    backup target => $(echo $list | awk -F\; '{print $1}')"
    echo "                     └── $(echo $list | awk -F\; '{print $2}')"
done

echo "Starting schedule"

if [ ! -d "$temporary_backup_dir" ]; then
    mkdir -p $temporary_backup_dir
fi

cd $temporary_backup_dir

config_file_list=()
config_file_list+=('/etc/nginx')

for schedule in ${schedule_list[@]}; do
    ip_port=$(echo $schedule | awk -F\; '{print $1}')
    ip=$(echo $ip_port | awk -F: '{print $1}')
    echo "Start backup $ip"
    config_file_list=$(echo $schedule | awk -F\; '{print $2}' | sed 's/,/ /g')

    if [ -z $ip ]; then
        continue
    fi

    port=$(echo $ip_port | awk -F: '{print $2}')
    port_args=""

    if [ ! -z "$port" ]; then
        port_args=" -P $port "
    fi

    if [ ! -d "$ip" ]; then
        mkdir $ip
    fi

    for config_file in ${config_file_list[@]}; do
        scp -C $port_args $key_args -r vagrant@$ip:$config_file $temporary_backup_dir/$ip/$(basename $config_file)
    done
done

if [ ! -d "$backup_dir" ]; then
    mkdir -p $backup_dir
fi

cd $(dirname $temporary_backup_dir)
test -f $backup_tarball && rm -f $backup_tarball
tar jcf $backup_tarball $(basename $temporary_backup_dir) && rm -fr $temporary_backup_dir
