#!/usr/bin/env bash

## ----------------------------------------------------------------------------------
## init script
cd "$(dirname "$0")/../../" || exit 1
if [[ "$(command -v realpath)" != "" ]]; then
  ROOT_DIR="$(realpath "$PWD")"
else
  ROOT_DIR="$PWD"
fi
source "$ROOT_DIR/scripts/includes.sh"

## ----------------------------------------------------------------------------------
## format code
npm run tool:format:code
check_exit $? "Failed to execute 'npm run tool:format:code' command."

## ----------------------------------------------------------------------------------
## execute lint
npm run tool:lint:execute:prod
check_exit $? "Failed to execute 'npm run tool:lint:execute:prod' command."

## ----------------------------------------------------------------------------------
## add re-formatted files to git
git add .
