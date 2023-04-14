#!/usr/bin/env bash

# -----------------------------------------------------
# init script
cd "$(dirname "$0")/../../" || exit 1

if [[ "$(command -v realpath)" != "" ]]; then
  ROOT_DIR="$(realpath "$PWD")"
else
  ROOT_DIR="$PWD"
fi

source "$(dirname "$0")/../.helpers.sh"

# -----------------------------------------------------
# format code - scripts
print_row_wait "Formatting script"
prettier \
  --config .prettierrc \
  --loglevel error \
  --write \
  'scripts/**/*.(bash|sh)' > /dev/null 2>&1
exit_fail_or_done $? 30002
