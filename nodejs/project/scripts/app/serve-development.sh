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
## check node modules installed
if [[ ! -d "node_modules" ]]; then
  npm install
  check_exit $? ${ERROR_INSTALL_MODULES[@]}
fi

## ----------------------------------------------------------------------------------
## start development server
"$APP_NPM_CLI_BIN/concurrently" \
  --restart-tries -1 \
  --restart-after 200 \
  --kill-others \
  --prefix "[{name}] " \
  --prefix-colors "magenta,cyan" \
  --names "jobs,http" \
  "npm run start:jobs" \
  "npm run start:http"

check_exit $? ${ERROR_APP_SERVE_DEVELOPMENT[@]}
