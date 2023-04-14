__PROCESS_START=""
__PROCESS_START=""
__PRINT_NO_COLOR=0

function print_deployment_started() {
  clear
  sudo ls
  clear

  echo ""
  if [[ "$__PRINT_NO_COLOR" == "0" ]]; then
    echo -e "\033[0;32m ===================================================================== \033[0m"
    echo -e "\033[0;32m ======          AUTODPROC - DEPLOYMENT SCRIPT STARTED          ====== \033[0m"
    echo -e "\033[0;32m ===================================================================== \033[0m"
  else
    echo -e " ===================================================================== "
    echo -e " ======          AUTODPROC - DEPLOYMENT SCRIPT STARTED          ====== "
    echo -e " ===================================================================== "
  fi
  echo ""
}

function print_finished_successful() {
  local MESSAGE="$1"
  echo ""
  if [[ "$__PRINT_NO_COLOR" == "0" ]]; then
    echo -e " \033[0;32m Yu-hu, $MESSAGE 🎉 🎉 🎉 \033[0m"
  else
    echo -e " Yu-hu, $MESSAGE 🎉 🎉 🎉"
  fi
  echo ""
}

function pad_dots() {
  local MESSAGE="$1"

  local LENGTH_MSG=${#MESSAGE}
  local LENGTH_DOT=$((50 - $LENGTH_MSG))

  for INDEX in $(seq $LENGTH_DOT); do
    echo -en "."
  done
}

function get_env() {
  local ENV_KEY="$1"
  local LENGTH=${#ENV_KEY}
  local LENGTH=$((LENGTH + 2))

  local ENV_VALUE="$(cat ".env" | grep $ENV_KEY= | tail -n 1 | cut -c$LENGTH-1000 | tr -d \")"
  echo "$ENV_VALUE"
}

function print_diff() {
  local PROCESS_END="$(date +%s%N | cut -b1-13)"
  local RUNTIME=$((PROCESS_END - __PROCESS_START))

  if [[ $RUNTIME -gt 1000 ]]; then
    RUNTIME=$((RUNTIME / 1000))
    TYPE="sec"
  else
    TYPE="ms"
  fi

  if [[ "$__PRINT_NO_COLOR" == "0" ]]; then
    echo -e "\033[0;37m $RUNTIME $TYPE\033[0m"
  else
    echo -e " $RUNTIME $TYPE"
  fi
}

function print_row_wait() {
  __PROCESS_START="$(date +%s%N | cut -b1-13)"

  local MESSAGE="$1"
  local DOTS="$2"

  local DOTS="$(pad_dots "$MESSAGE")"

  if [[ "$__PRINT_NO_COLOR" == "0" ]]; then
    echo -en " \033[1;34m* $MESSAGE\033[0m $DOTS "
    echo -en "\033[0;100;5m WAIT \033[0m "
  else
    echo -en " * $MESSAGE $DOTS "
  fi
}

function print_done() {
  if [[ "$__PRINT_NO_COLOR" == "0" ]]; then
    echo -en "\b\b\b\b\b\b\b\033[0;42m DONE \033[0m"
  else
    echo -en " DONE "
  fi
  print_diff
}

function print_fail() {
  if [[ "$__PRINT_NO_COLOR" == "0" ]]; then
    echo -en "\b\b\b\b\b\b\b\033[0;41m FAIL \033[0m"
  else
    echo -en " FAIL "
  fi
  print_diff
}

function print_kill() {
  if [[ "$__PRINT_NO_COLOR" == "0" ]]; then
    echo -en "\b\b\b\b\b\b\b\033[0;43m KILL \033[0m"
  else
    echo -en " KILL "
  fi
  print_diff
}

function print_skip() {
  if [[ "$__PRINT_NO_COLOR" == "0" ]]; then
    echo -en "\b\b\b\b\b\b\b\033[0;44m SKIP \033[0m"
  else
    echo -en " SKIP "
  fi
  print_diff
}

function exit_fail_or_done() {
  local EXIT_CODE="$1"
  local ERROR_CODE="$2"

  if [[ $? != 0 ]]; then
    print_fail
    exit $1
  fi

  print_done
}

if [[ "$*" == *--no-print-color* ]]; then
  __PRINT_NO_COLOR=1
fi
