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
prettier \
  --config .prettierrc \
  --loglevel error \
  --write \
  "src/**/*.(ts|js|json)"

check_exit $? "Failed to format source code."
