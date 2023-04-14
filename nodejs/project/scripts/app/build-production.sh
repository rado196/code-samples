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
## paths
ROOT_SOURCE="$PWD/src"
ROOT_DIST="$PWD/dist"
ROOT_BUILD="$PWD/build"

## ----------------------------------------------------------------------------------
## check node modules installed
if [[ ! -d "node_modules" ]]; then
  npm install
  check_exit $? "Failed to install node modules."
fi

## ----------------------------------------------------------------------------------
## start production build
"$APP_NPM_CLI_BIN/babel" \
  --out-dir "$ROOT_BUILD" \
  --copy-files \
  --extensions '.ts,.js' \
  --source-maps false \
  "$ROOT_SOURCE"
check_exit $? "Failed to build production release."

if [[ ! -d "$ROOT_BUILD/middlewares" ]]; then
  cp -r "$ROOT_SOURCE/middlewares" "$ROOT_BUILD/middlewares"
fi
if [[ ! -d "$ROOT_BUILD/public" ]]; then
  cp -r "$ROOT_SOURCE/public" "$ROOT_BUILD/public"
fi
if [[ ! -d "$ROOT_BUILD/views" ]]; then
  cp -r "$ROOT_SOURCE/views" "$ROOT_BUILD/views"
fi

## ----------------------------------------------------------------------------------
## update artifacts
rm -rf "$ROOT_DIST"
mv "$ROOT_BUILD" "$ROOT_DIST"
