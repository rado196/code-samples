#!/usr/bin/env bash
cd "$(dirname $0)" || exit 1

bash ./scripts/app/deploy.sh "$@"
