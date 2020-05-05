#!/usr/bin/env bash
# Purpose: This script for auto update remote deploy by the schedule.
# Description: This script will schedule to execute script file on remote hosts.
#              Remote hosts configuration by file conf/remote.conf.
#              Example: <remote address>:<og platform dir1>,<og platform dir2>,...
#              Mean:
#                 <remote address>: IP address or domain name.
#                 <og platform dir>: og platform directory.
#                              Only support directory under the /home/vagrant/Code/

if [ -z "$1" ] || [ -z "$2" ]; then
    echo "argument error" >&2
    echo "$0 <repo> <branch> [key path]" >&2
    echo "    repo: git repository name" >&2
    echo "    branch: branch name" >&2
    echo "    key path: ssh private key path" >&2
    echo "    example: $0 bitbucket 8.8.8 /home/vagrant/my.key" >&2
    exit 1
fi

config_dir="conf"
config_file="remote.conf"
current_dir=$(dirname $(readlink -f $0))
config_path="$current_dir/$config_dir/$config_file"

if [ ! -f $config_path ]; then
    echo "Config file not exists." >&2
    exit 1
fi

schedule_list=($(cat $config_path | awk '{print $1}'))
repo=$1
branch=$2
key_path=$3
user="vagrant"
auto_deploy_script="/home/vagrant/Code/og_git/admin/shell/auto_deploy.sh"
code_dir="/home/vagrant/Code"
echo "This script will update all remote og platform."
echo -e "Update info repository \e[31m$repo\e[0m branch \e[31m$branch\e[0m"
echo "Update info:"

for list in ${schedule_list[@]}; do
    echo "    update target => $list"
done

echo -n "Are you sure run this remote schedule script (Y/N) "
read anser

if [ "$anser" != "Y" ]; then
    echo "Do nothing"
    exit 0
fi

echo "Starting schedule"
sleep 5

for schedular in ${schedule_list[@]}; do
    ip=$(echo $schedular | grep -Po ".*?:" | sed 's/://' )
    dir_list=$(echo $schedular | grep -Po ":.*" | sed 's/://' | sed 's/,/ /g')
    echo "Execute command to remote $ip"

    for target_dir in $dir_list; do
        echo "Start auto deploy to this path \"$ip $target_dir\""
        test_cmd="ls -ld $code_dir/$target_dir"

        if [ -z "$key_path" ]; then
            ssh $user@$ip "$test_cmd" >/dev/null
        else
            ssh -i $key_path $user@$ip "$test_cmd" >/dev/null
        fi

        if [ "$?" != 0 ]; then
            echo "This directory $target_dir not exists!!" >&2
            continue
        fi

        deploy_cmd="$auto_deploy_script $repo $branch $code_dir/$target_dir"
        echo "ssh -i $key_path $user@$ip \"$deploy_cmd\""

        if [ -z "$key_path" ]; then
            ssh $user@$ip "$deploy_cmd"
        else
            ssh -i $key_path $user@$ip "$deploy_cmd"
        fi

        if [ "$?" != 0 ]; then
            echo "Error" >&2
            exit 1
        fi
    done
done
