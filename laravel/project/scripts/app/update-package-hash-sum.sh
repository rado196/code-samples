#!/usr/bin/env bash
source "$(dirname $0)/../.helpers.sh"

HASH_SHA256="$(sha256sum composer.json | awk '{print $1;}')"
HASH_SHA512="$(sha512sum composer.json | awk '{print $1;}')"
HASH_MD5="$(md5sum composer.json | awk '{print $1;}')"

CURRENT_HASH="SHA256:$HASH_SHA256|SHA512:$HASH_SHA512|MD5:$HASH_MD5"
LATEST_HASH=""

if [[ -f "package-hash.txt" ]]; then
  LATEST_HASH="$(cat package-hash.txt)"
fi

if [[ "$CURRENT_HASH" != "$LATEST_HASH" ]]; then
  echo "$CURRENT_HASH" > package-hash.txt
fi
