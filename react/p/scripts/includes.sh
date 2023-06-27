## ----------------------------------------------------------------------------------
## variables - errors
ERROR_INSTALL_MODULES=("1000001" "Failed to clear old lint cache.")
ERROR_APP_ENV_FILE_MISSING=("1000002" "Make sure .env file exists in root directory.")
ERROR_APP_LINT_UNCACHE=("1000003" "Failed to clear old lint cache.")
ERROR_APP_LINT_EXECUTE=("1000004" "Failed to lint app.")
ERROR_APP_FORMAT_CODE=("1000005" "Failed to format your code.")
ERROR_APP_SERVE_DEVELOPMENT=("1000006" "Failed to start development server.")
ERROR_APP_BUILD_PRODUCTION=("1000007" "Failed to build production release.")

## ----------------------------------------------------------------------------------
## variables - declarations
APP_NPM_ROOT="$ROOT_DIR/node_modules"
APP_NPM_CLI_BIN="$APP_NPM_ROOT/.bin"
APP_NPM_CACHE="$APP_NPM_ROOT/.cache"

## ----------------------------------------------------------------------------------
## functions
function check_exit() {
  local FUNC_PASSED_ARGS=("$@")
  local CMD_EXIT_CODE="${FUNC_PASSED_ARGS[0]}"
  local ERROR_EXIT_CODE="${FUNC_PASSED_ARGS[1]}"
  local ERROR_EXIT_MSG="${FUNC_PASSED_ARGS[@]:2}"

  if [[ $CMD_EXIT_CODE != 0 ]]; then
    echo ""
    echo -e "\033[0;31m ☠️   Oh nooo\033[0m"
    echo -e "\033[0;31m ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m"
    echo -e "\033[0;31m $ERROR_EXIT_MSG\033[0m"
    echo ""

    exit $ERROR_EXIT_CODE
  fi
}

function exit_on_fail() {
  local CMD_EXIT_CODE="$1"

  if [[ $CMD_EXIT_CODE != 0 ]]; then
    exit $CMD_EXIT_CODE
  fi
}

function check_env_file() {
  # if [[ ! -f .env ]]; then
  #   check_exit 1 ${ERROR_APP_ENV_FILE_MISSING[@]}
  # fi

  echo ""
}

function get_env() {
  local ENV_KEY="$1"
  local LENGTH=${#ENV_KEY}
  local LENGTH=$((LENGTH + 2))

  local ENV_VALUE="$(cat ".env" | grep "^$ENV_KEY=" | tail -n 1 | cut -c$LENGTH-1000 | tr -d \")"
  echo "$ENV_VALUE"
}
