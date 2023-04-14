#!/usr/bin/env bash

## ----------------------------------------------------------------------------------
## init script
cd "$(dirname "$0")/../../" || exit 1
if [[ "$(command -v realpath)" != "" ]]; then
  ROOT_DIR="$(realpath "$PWD")"
else
  ROOT_DIR="$PWD"
fi
source "$(dirname "$0")/../includes.sh"

## ----------------------------------------------------------------------------------
## start development server
SERVICE_NAME="${1:15}"

"$APP_NPM_CLI_BIN/nodemon" \
  --cwd "$ROOT_DIR" \
  --exec "'$APP_NPM_CLI_BIN/babel-node' ./app.js $SERVICE_NAME" \
  --config "$ROOT_DIR/nodemon.json" \
  --watch src

exit $?
