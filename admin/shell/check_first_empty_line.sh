#!/usr/bin/env bash

# filename=$(find ./ -name "*.php" -type f | egrep -v "/views/|/vendor/" )
# for i in $filename
# do
#     seq=0
#     file=$i
#     while read line
#     do
#         lines[$seq]=$line
#         ((seq++))
#     done < $file
#     if [ ! -n "${lines[0]}"  ]; then
#         echo $file has empty line on first >&2
#         exit 1
#     fi
# done

filename=$(find ./ -name "*.php" -type f | egrep -v "/views/|/vendor/" )
for file in $filename
do
    seq=0
    code=$(head -1 $file | cat -A | sed 's/\^I//g' |sed 's/" "//g' | sed 's/\^M\$/\$/g' )
    echo $code  > /tmp/tmp.txt
    head=$(head -1 /tmp/tmp.txt )
    rm -rf /tmp/tmp.txt
    if [ "$head" = "\$"  ]; then
        echo $file has empty line on first >&2
        exit 1
    fi
    ((sum++))
done
echo $sum

