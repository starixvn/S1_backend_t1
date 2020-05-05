#!/usr/bin/env bash

pwd
find_php_syntax_error=`find -name "*.php" -type f | egrep -v "/vendor/" | awk '{print "php -l "$1}' | bash | grep -v 'No syntax errors detected'`

echo $find_php_syntax_error

if [ ! -z "$find_php_syntax_error" ]; then
  echo "php syntax error" >&2
  exit 1
fi
