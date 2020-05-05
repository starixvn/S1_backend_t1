#!/bin/bash
if [ "$(uname)" == "Darwin" ]; then
OGHOME=$(dirname $_)
else
OGHOME=$(dirname $( readlink -f $0 ))
fi
echo $OGHOME
# tailf-with-colors () {
#     if [ -z "$1" ] ; then
#         echo "Please specify a file for monitoring"
#         return
#     fi
#     tail -f $1 $2 | awk '
#                 {matched=0}
#                 /INFO:/    {matched=1; print "\033[0;37m" $0 "\033[0m"}   # WHITE
#                 /NOTICE:/  {matched=1; print "\033[0;36m" $0 "\033[0m"}   # CYAN
#                 /WARNING:/ {matched=1; print "\033[0;34m" $0 "\033[0m"}   # BLUE
#                 /ERROR:/   {matched=1; print "\033[0;31m" $0 "\033[0m"}   # RED
#                 /ALERT:/   {matched=1; print "\033[0;35m" $0 "\033[0m"}   # PURPLE
#                 matched==0            {print "\033[0;33m" $0 "\033[0m"}   # YELLOW
#         '
# }

tail -f $OGHOME/admin/application/logs/*.log
