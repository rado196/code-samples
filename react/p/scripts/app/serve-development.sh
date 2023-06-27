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
## check .env file existence
check_env_file

## ----------------------------------------------------------------------------------
## check node modules installed
if [[ ! -d "node_modules" ]]; then
  npm install
  check_exit $? ${ERROR_INSTALL_MODULES[@]}
fi

## ----------------------------------------------------------------------------------
## clean old data
rm -rf "$ROOT_DIT/.next"

## ----------------------------------------------------------------------------------
## export node options
export NODE_OPTIONS=""
export NODE_OPTIONS="$NODE_OPTIONS --trace-deprecation"
# export NODE_OPTIONS="$NODE_OPTIONS --inspect"
export NODE_OPTIONS="$NODE_OPTIONS --no-warnings"

## ----------------------------------------------------------------------------------
## start development server
"$APP_NPM_CLI_BIN/next" dev
check_exit $? ${ERROR_APP_SERVE_DEVELOPMENT[@]}
