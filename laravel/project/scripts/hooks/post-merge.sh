#!/usr/bin/env bash
cd "$(dirname $0)/../../" || exit 1

# git post-pull command

bash ./scripts/app/update-permissions.sh
bash ./scripts/app/deploy.sh
