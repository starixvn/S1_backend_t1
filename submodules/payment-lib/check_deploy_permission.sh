
# test
# GITLAB_USER_EMAIL=""

function contains() {
    local n=$#
    local value=${!n}
    for ((i=1;i < $#;i++)) {
        if [ "${!i}" == "${value}" ]; then
            echo "y"
            return 0
        fi
    }
    echo "n"
    return 1
}

echo "checking permission of $GITLAB_USER_EMAIL on $CI_JOB_NAME, $CI_JOB_STAGE"

# "james@tripleonetech.net"
emails=( "james.cto@tripleonetech.net" "noke.php.tw@tripleonetech.net" "yunfei.dev@tripleonetech.net" "aris.php.ph@tripleonetech.net" "andy.php.ph@tripleonetech.net" "kaiser.php.ph@tripleonetech.net" "ned.sa.tw@tripleonetech.net" "jouan.php.tw@tripleonetech.net" "jhunel.php.ph@tripleonetech.net" "zuma.sa.tw@tripleonetech.net")

NOT_LIVE_CLIENTS=("laba360" "ecg" "entaplayth" "newrainbow" "entaplay" "a888caishen" "caishen888" "lequ" "lotterydemo" "firstgenpact" "rainbow" "e888")

if [ $(contains "${NOT_LIVE_CLIENTS[@]}" "$CI_JOB_NAME") == "y" ]; then

  echo "permission is granted on not live client"

  exit 0;

fi

if [ $(contains "${emails[@]}" "$GITLAB_USER_EMAIL") == "y" ]; then

  echo "permission is granted"

  exit 0

else

  echo "permission is declined"

  exit 1

fi
