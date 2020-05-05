#!/usr/bin/env bash
# Purpose: Automatic login MySQL database interact interface.
# Descript: Parse og's config files obtain login database information.
#           The priority config_local.php big than config_secret_local.php.

config_files=()
adminshell_dir=$(dirname $(readlink -f $0))
og_root=$(dirname $(dirname $adminshell_dir))
local_config="config_local.php"
apps=("admin" "aff" "player")
under_app_config_path="application/config"

for app in ${apps[*]}; do
    config_files+=("$og_root/$app/$under_app_config_path/$local_config")
done

secret_dir="secret_keys"
secret_file="config_secret_local.php"
secret_file_path="$og_root/$secret_dir/$secret_file"
config_files+=($secret_file_path)

function search_string () {
    config_file=$1
    s_pattern=$2
    cat $config_file | grep "$s_pattern" | awk -F= '{print $2}' | sed "s/.*'\(.*\)'.*/\1/"
}

for config_file in ${config_files[@]}; do
    echo $config_file
    test -f $config_file || break

    if [ "$1" == "readonly" ]; then
        passwd=$(search_string $config_file 'db.readonly.password')
        user=$(search_string $config_file 'db.readonly.username')
        host=$(search_string $config_file 'db.readonly.hostname')
        port=$(search_string $config_file 'db.readonly.port')
        database=$(search_string $config_file 'db.readonly.database')
    else
        passwd=$(search_string $config_file 'db.default.password')
        user=$(search_string $config_file 'db.default.username')
        host=$(search_string $config_file 'db.default.hostname')
        port=$(search_string $config_file 'db.default.port')
        database=$(search_string $config_file 'db.default.database')
    fi
done

if [ -z "$passwd" ] || [ -z "$user" ] || [ -z "$host" ]; then
    echo "Cannot obtain password or user info" >&2
    exit 1
fi

echo "Login MySQL use user: $user@$host use db='$database'"
if [ -z $port ]; then
    port=3306
fi
mysql -u $user -p$passwd -h $host $database --port $port
