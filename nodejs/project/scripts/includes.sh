APP_NPM_CLI_BIN="$ROOT_DIR/node_modules/.bin"
APP_NPM_CACHE="$ROOT_DIR/node_modules/.cache"

if [[ -f "$ROOT_DIR/.env.global" ]]; then
  source "$ROOT_DIR/.env.global"
fi

if [[ -f "$ROOT_DIR/.env" ]]; then
  source "$ROOT_DIR/.env"
fi

function check_exit() {
  local FUNC_PASSED_ARGS=("$@")
  local CMD_EXIT_CODE="${FUNC_PASSED_ARGS[0]}"
  local ERROR_EXIT_MSG="${FUNC_PASSED_ARGS[@]:1}"

  if [[ $CMD_EXIT_CODE != 0 ]]; then
    echo ""
    echo -e "\033[0;31m Oh nooooooooooooooooooooooo, ☠️\033[0m"
    echo -e "\033[0;31m ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m"
    echo -e "\033[0;31m ❌ $ERROR_EXIT_MSG\033[0m"
    echo ""

    exit $CMD_EXIT_CODE
  fi
}
