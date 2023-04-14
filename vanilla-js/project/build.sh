#!/usr/bin/env bash
cd "$(dirname "$0")"

# clear terminal 
clear
echo ""

FOLDER=$(date +%Y%m%d-%H%M)

# install node modules
echo -n " * Installing node modules ........... "
if [[ ! -d "node_modules" ]]; then
  npm install --silent >/dev/null 2>&1
  if [[ $? == 0 ]]; then
    echo "DONE"
  else
    echo "FAIL"
    exit
  fi
else
  echo "SKIP"
fi

# execute build command
if [[ $* == "--dev" || $* == "-d" ]]; then
  echo -n " * Building TCF2 script [dev] ........ "
  npm run build:dev >/dev/null 2>&1
else
  echo -n " * Building TCF2 script [prod] ....... "
  npm run build:prod >/dev/null 2>&1
fi

if [[ $? == 0 ]]; then
  echo "DONE"
else
  echo "FAIL"
  exit
fi

# cleaning up
echo -n " * Cleaning outputs .................. "

rm ./.dist/$FOLDER/tcf2.js.LICENSE.txt >/dev/null 2>&1
sed -i "s|For license information please see tcf2.js.LICENSE.txt|TCF2 + GoogleTag @ $FOLDER|g" ./.dist/$FOLDER/tcf2.js >/dev/null 2>&1

echo "DONE"
echo ""

# print file path
echo " File: $PWD/.dist/$FOLDER/tcf2.js"
echo ""
