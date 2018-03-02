#!/bin/bash

BASEDIR=$(dirname "$0")
PATH_LIVE="ssh-v166574@fusca.de:/www/htdocs/v166574/qlu/login"
rsync -avz -e ssh --exclude '.git*' --exclude '.idea/*' --exclude '.idea' --exclude 'application/cache/*' --exclude 'system' --safe-links $BASEDIR/ $PATH_LIVE/