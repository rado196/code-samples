#!/bin/bash

cd "/var/www/web"

export NVM_DIR="$HOME/.nvm"
if [[ ! -d "$NVM_DIR" ]]; then
  exit 10007
fi

source "$HOME/.bashrc"

echo ">>> Rebuilding node_modules/.bin symlinks ..."
npm rebuild
if [[ $? != 0 ]]; then
  exit 10009
fi

if [[ "$(command -v pm2)" != "" ]]; then
  echo ">>> Starting app ..."
  pm2 start ecosystem.config.js \
    && pm2 save --force \
    && pm2 startup
  exit $?
else
  echo ">>> No PM2 were found, existing with error."
  exit 10011
fi
