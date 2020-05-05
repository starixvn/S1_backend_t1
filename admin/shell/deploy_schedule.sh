#!/usr/bin/env bash

if [ -z "$1" ] || [ -z "$2" ] || [ -z "$3" ]; then
    echo "argument error" >&2
    echo "$0 <repo> <branch> <path list>" >&2
    echo "    repo: git repository name" >&2
    echo "    branch: branch name" >&2
    echo "    path list: use comma separated path" >&2
    echo "    example: $0 bitbucket 8.8.8 og_staging,og,og_sync" >&2
    exit 1
fi

repo=$1
branch=$2
current_dir=$(dirname $(readlink -f $0))
og_root_dir=$(readlink -f $current_dir/../..)
og_parent_dir=$(readlink -f $og_root_dir/..)
path_list=$3
abs_path=$(dirname $(readlink -f $0))

for p in `echo $path_list | sed 's/,/ /g'`; do
    path="$og_parent_dir/$p"
    if [ -d "$path" ]; then
        echo "Start auto deploy to this path \"$path\""
        $abs_path/auto_deploy.sh $repo $branch $path
        if [ "$?" != 0 ]; then
            echo "Error" >&2
            exit 1
        fi
    else
        echo "Error: This $path not exists." >&2
    fi
done
