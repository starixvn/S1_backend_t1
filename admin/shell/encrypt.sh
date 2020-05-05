#!/usr/bin/env bash

if [ -z "$1" ] || [ -z "$2" ] || [ -z "$3" ] || [ -z "$4" ] || [ -z "$5" ]; then
    echo "ERROR: argument error" >&2
    echo "$0 <repo> <trigger_flag_file> <encrypt_source> <encrypt_destination> <encrypt_folders> [not_push]" >&2
    echo "    encrypt_folders: use comma separated." >&2
    exit 1
fi
if [ "$3" == "/" ] || [ "$4" == "/" ]; then
    echo "ERROR: can not encrypt root directory." >&2
    exit 1
fi

repo=$1
trigger_flag_file=$2
encrypt_source=$3
encrypt_destination=$4
not_push_flag=$6
encrypt_folders=()
encrypt_folder=$encrypt_destination/$5
for d in `echo $5 | sed 's/,/ /g'`; do
    encrypt_folders+=("$encrypt_destination/$d")
done
exclude_file="$encrypt_source/.gitignore"
exclude_dir=".git"
ENCRYPT_REPO='bitbucket_encrypt'

if [ ! -f $trigger_flag_file ]; then
    exit 0
fi

encrypt_tool='/home/vagrant/ZendGuard/bin/zendenc56'
encrypt_cmds=()
for encrypt_dir in ${encrypt_folders[@]}; do
    encrypt_tool_args="--obfuscation-level 1 --no-header --recursive --ignore-errors --ignore *PHPMailer/*,*gcharts/*,*vendor/*,*cli/*,*phpexcel/* --delete-source $encrypt_dir"
    encrypt_cmds+=("$encrypt_tool $encrypt_tool_args")
done

cd $encrypt_source

if [ "$(cat $trigger_flag_file | wc -l)" -gt "0" ]; then
    branchs=$(cat $trigger_flag_file | awk '{print $2}' | sort | uniq)
    echo rm -f $trigger_flag_file
    test -f $trigger_flag_file && rm -f $trigger_flag_file
    for branch in $branchs; do
        if [[ ! "$branch" =~ .*_encrypt ]]; then
            encrypt_branch=${branch}_encrypt
            echo "Encrypt branch: $branch"
            echo "git fetch $repo $branch"
            git fetch $repo $branch
            if [ ! $? == 0 ]; then echo 'fetch error' >&2 ; exit 1; fi
            git checkout $branch
            if [ ! $? == 0 ]; then echo 'branch error' >&2 ; exit 1; fi
            git pull $repo $branch
            if [ ! $? == 0 ]; then echo 'pull error' >&2 ; exit 1; fi
            git fetch --tags
            git describe --tags --long > $encrypt_destination/version
            # Change to encrypt directory start encrypt
            cd $encrypt_destination || exit 1

            git checkout -b $encrypt_branch || git checkout $encrypt_branch
            echo rsync --delete -av $encrypt_source/ $encrypt_destination/
            rsync --delete -av $encrypt_source/ $encrypt_destination/ --exclude-from=$exclude_file --exclude=$exclude_dir --exclude=version
            if [ ! $? == 0 ]; then echo 'sync error' >&2 ; exit 1; fi
            echo "Start encrypt $encrypt_folder"
            for (( i = 0; i < ${#encrypt_cmds[@]}; i++ )); do
                echo "${encrypt_cmds[$i]}"
                ${encrypt_cmds[$i]}
            done
            if [ ! $? == 0 ]; then echo "ERROR: encrypt" >&2; exit 1; fi
            echo "encrypt successful."
            if [ -z "$not_push_flag" ]; then
                echo " push to bitbucket"
                git add --all .
                if [ ! $? == 0 ]; then echo "ERROR: add error" >&2; exit 1; fi
                git commit -a -m "$(date) encrypt"
                git push $ENCRYPT_REPO $encrypt_branch
                if [ ! $? == 0 ]; then echo "ERROR: push $encrypt_branch" >&2; exit 1; fi
            fi
        fi
    done
fi
