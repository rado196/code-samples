#!/usr/bin/env bash
cd "$(dirname $0)/../../" || exit 1

# git pre-commit command

npm run format
git add .
