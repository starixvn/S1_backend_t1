#!/usr/bin/env bash
# apt-get install php-pear
# sudo pear channel-discover pear.phpdoc.org
# sudo pear install phpdoc/phpDocumentor

OG_DIR="$(dirname $(readlink -f $0))/../.."
DOC_DIR="$OG_DIR/../doc/og"
mkdir -p $DOC_DIR
phpdoc -d $OG_DIR -t $DOC_DIR --ignore */views/*,*/vendor/*,*/testing/*,*/sites/*,*/secret_keys/*,*/migrations/*,*/phpexcel/*,*/PHPMailer/*,*/gcharts/*,*/public/*,*/third_party/*,*/system/*,*/api/*,*/integration/*,*/config/*,*/doc/*,*/language/*
