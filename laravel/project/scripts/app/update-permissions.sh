#!/usr/bin/env bash
cd "$(dirname $0)/../../" || exit 1

function change_mode() {
  CHANGE_MODE=$1
  CHANGE_PATH=$2

  CURRENT_MODE=$(perl -e 'printf "%03o\n", (stat)[2] & 07777, $_ for @ARGV' "$CHANGE_PATH")
  if [[ -d "$CHANGE_PATH" ]]; then
    echo "Changing permission [$CHANGE_MODE > $CURRENT_MODE, d]: $CHANGE_PATH"
    sudo chmod -R 777 "$CHANGE_PATH"
  fi

  if [[ -f "$CHANGE_PATH" ]]; then
    if [[ "$CHANGE_MODE" != "$CURRENT_MODE" ]]; then
      echo "Changing permission [$CURRENT_MODE -> $CHANGE_MODE, f]: $CHANGE_PATH"
      sudo chmod 777 "$CHANGE_PATH"
    fi
  fi
}

MACHINE="unknown"
case "$(uname -s)" in
  Linux*) MACHINE="Linux" ;;
  Darwin*) MACHINE="macOS" ;;
  CYGWIN*) MACHINE="Win Cygwin" ;;
  MINGW*) MACHINE="Windows MinGw" ;;
  *) MACHINE="unknown" ;;
esac

if [[ "Linux" == "$MACHINE" || "macOS" == "$MACHINE" ]]; then
  if [[ "$(command -v sudo)" != "" ]]; then
    sudo -v

    change_mode 777 ./storage/logs
    change_mode 777 ./storage/framework
    change_mode 777 ./storage/app
    change_mode 777 ./public/js
    change_mode 777 ./public/css
    change_mode 777 ./public/svg
  else
    echo "Sorry, we can't find sudo for file/directory changing permission."
  fi
else
  echo "Sorry, for Windows we can't change files or directories permission."
fi
