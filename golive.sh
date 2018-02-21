#!/bin/bash

BASEDIR=$(dirname "$0")
PATH_LIVE="ssh-v166574@fusca.de:/var/www/htdocs/v166574/qlu/login"
echo rsync -avz -e ssh -delete --exclude '.git*' --exclude '.idea/*' --exclude '.idea' --exclude 'application/cache/*' $BASEDIR/ $PATH_LIVE/