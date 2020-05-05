#!/bin/bash
if [ -z ${OG_BASEPATH+'x'} ]; then
	echo set OG_BASEPATH

  	export OG_BASEPATH=/home/vagrant/Code/og/admin
fi


php $OG_BASEPATH/public/index.php cli/html_template_processer/fix_template
