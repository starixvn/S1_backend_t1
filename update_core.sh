PROJECT_HOME=$(dirname $( readlink -f $0 ))
cd $PROJECT_HOME

if [ -f "$HOME/Code/relative_link" ]; then
    OGHOME="."
else
    OGHOME=$(dirname $( readlink -f $0 ))
fi

d1=$(dirname $(readlink -f $0))
d2=$(dirname $d1)
split_symbol=$(basename $d2)/$(basename $d1)

function real_path {
    if [ "$OGHOME" == "." ]; then
        p=$(pwd | awk -F"$split_symbol" '{print $2}')
        n=$(expr $(echo $p | sed 's/[^\/]//g' | tr --delete '\n'  | wc -c) + $(echo $2 | sed 's/^\.\///' | sed 's/[^\/]//g' | tr --delete '\n'  | wc -c))
        parents_path=$(for (( c=1; c<=n; c++)) ; do echo -n "../" ; done)
        echo "$parents_path$1 $2"
    else
        echo "$1 $2"
    fi
}

function ln {
#    pwd
#    echo /usr/bin/ln $1 $(real_path $2 $3)
    /bin/ln $1 $(real_path $2 $3)
}

# OGHOME=$(pwd)
#/home/vagrant/Code/og
OGADMIN_HOME=$OGHOME/admin
OGPLAYER_HOME=$OGHOME/player
OGAFF_HOME=$OGHOME/aff
OGAGENCY_HOME=$OGHOME/agency

echo "create links in $OGHOME"
echo "OGADMIN_HOME=$OGADMIN_HOME"
echo "OGPLAYER_HOME=$OGPLAYER_HOME"
echo "OGAFF_HOME=$OGAFF_HOME"
echo "OGAGENCY_HOME=$OGAGENCY_HOME"

#check core-lib dir
if [ -d $PROJECT_HOME/core-lib ]; then
  echo "core-lib ready"
else
  echo "clone core-lib"
  git clone git@git.smartbackend.com:core/core-lib.git core-lib
fi

cd $PROJECT_HOME/core-lib
git pull
cd $PROJECT_HOME

echo "done"

