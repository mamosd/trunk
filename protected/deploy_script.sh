#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

echo "/****************************************************************/" >> $DIR/deployments.txt
echo "$(date) - $2 deploying revision $1 ..." >> $DIR/deployments.txt;
php $DIR/yiic migrate --interactive=0 >> $DIR/deployments.txt
##rm -r $DIR/runtime/cache ## NOT REQUIRED ON STG
echo "$(date) - $2 deploying revision $1 ... DONE.-" >> $DIR/deployments.txt;
