MM_WEBHOOK_URL="https://talk.chatchat365.com/hooks/gewtki6nkp8j3xf4xgjyax1n5e"
GITLAB_USER_LOGIN=$1
CLIENT_NAME=$2
CI_COMMIT_REF_NAME=$3
CI_JOB_ID=$4
CI_JOB_URL="https://git.smartbackend.com/sbs/og/-/jobs/$CI_JOB_ID"
CI_COMMIT_SHA=$5
CI_COMMIT_URL="https://git.smartbackend.com/sbs/og/commit/$CI_COMMIT_SHA"



curl -i -X POST --data-urlencode 'payload={"username":"'"$GITLAB_USER_LOGIN"'","text":"'"Deploy #$CLIENT_NAME $CI_COMMIT_REF_NAME $CI_JOB_URL \n $CI_COMMIT_URL"'"}' $MM_WEBHOOK_URL 
