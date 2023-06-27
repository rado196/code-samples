#!/bin/bash

function update_env_file() {
  local ENV_KEY="$1"
  local ENV_VAL="$(printf '%s' "${!ENV_KEY}")"

  if [[ ! -f .env ]]; then
    cp .env.example .env
  fi

  local EXISTS_WITH_EXPORT=$(cat .env | grep "^export $ENV_KEY=" | wc -l)
  local EXISTS_NON_EXPORT=$(cat .env | grep "^$ENV_KEY=" | wc -l)

  if [[ $EXISTS_WITH_EXPORT != 0 || $EXISTS_NON_EXPORT != 0 ]]; then
    if [[ $EXISTS_WITH_EXPORT != 0 ]]; then
      sed -i "s|^export $ENV_KEY=.*|export $ENV_KEY=$ENV_VAL|g" .env
    fi
    if [[ $EXISTS_NON_EXPORT != 0 ]]; then
      sed -i "s|^$ENV_KEY=.*|$ENV_KEY=$ENV_VAL|g" .env
    fi
  else
    echo '' >> .env
    echo "export $ENV_KEY=$ENV_VAL" >> .env
  fi
}

echo "✒️ Generating environment variables ..."

ENV_KEY_LIST="$(cat .env.example | grep -v '^#' | grep -v '^$' | sed 's|=.*||g' | sed 's|^export ||g' | xargs)"
for ENV_KEY in ${ENV_KEY_LIST[@]}; do
  update_env_file "$ENV_KEY"
done

if [[ $? -eq 0 ]]; then
  echo "✅ Environment variables generated successfully."
else
  echo "❌ Could not generate environment variables."
  exit 10003
fi
