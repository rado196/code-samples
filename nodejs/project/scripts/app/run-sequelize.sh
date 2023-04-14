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
## functions
function get_config() {
  local CONFIG_NAME="$1"

  node --eval "
    const configs = require('./.sequelizerc');
    console.log(configs['$CONFIG_NAME']);
  "
}

## ----------------------------------------------------------------------------------
## import environment variables

source "$ROOT_DIR/.env"

## ----------------------------------------------------------------------------------
## run command
OTHER_ARGS="${@:1}"

"$APP_NPM_CLI_BIN/babel-node" \
  --extensions '.ts,.js' \
  "$APP_NPM_CLI_BIN/sequelize" $OTHER_ARGS \
  --config "$(get_config "config")" \
  --models-path "$(get_config "models-path")" \
  --migrations-path "$(get_config "migrations-path")" \
  --seeders-path "$(get_config "seeders-path")" \
  --debug

check_exit $? "Failed to run sequelize command with following arguments: $OTHER_ARGS"
