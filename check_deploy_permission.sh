echo "run cli to check permission of $GITLAB_USER_EMAIL on $CI_JOB_NAME, $CI_JOB_STAGE"

php ./admin/shell/Cli.php -e $GITLAB_USER_EMAIL -N $CI_JOB_NAME -S $CI_JOB_STAGE -R $CI_COMMIT_REF_NAME check_deploy_permission
