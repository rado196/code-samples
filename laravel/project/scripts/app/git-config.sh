#!/usr/bin/env bash
cd "$(dirname $0)/../../" || exit 1
source "$(dirname $0)/../.helpers.sh"

git config branch.autosetuprebase always
git config pull.ff false

git config branch.development.mergeOptions "--no-ff --no-commit"
git config branch.master.mergeOptions "--no-commit"
git config branch.staging.mergeOptions "--no-commit"
