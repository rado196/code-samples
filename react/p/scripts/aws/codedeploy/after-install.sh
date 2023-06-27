#!/bin/bash

export NVM_DIR="$HOME/.nvm"
if [[ ! -d "$NVM_DIR" ]]; then
  sudo apt install curl -y
  curl https://raw.githubusercontent.com/creationix/nvm/master/install.sh | bash
  source ~/.bashrc

  echo ">>> Installing Node v16 using NVM ..."
  nvm install 16
  nvm alias default 16
  nvm use 16

  echo 'export PATH="$PATH:$(npm config get prefix)/bin"' >> ~/.bashrc
  source ~/.bashrc
fi

if [[ -s "$NVM_DIR/nvm.sh" ]]; then
  echo ">>> Sourcing file $NVM_DIR/nvm.sh"
  source "$NVM_DIR/nvm.sh"
else
  echo ">>> File not found $NVM_DIR/nvm.sh"
fi

if [[ -s "$NVM_DIR/bash_completion" ]]; then
  echo ">>> Sourcing file $NVM_DIR/bash_completion"
  source "$NVM_DIR/bash_completion"
else
  echo ">>> File not found $NVM_DIR/bash_completion"
fi

if [[ "$(command -v npm)" != "" ]]; then
  echo ">>> Node installed (node: $(node -v), npm: $(npm -v))"

  if [[ "$(command -v pm2)" == "" ]]; then
    echo ">>> Installing pm2 globally."
    npm install --location=global pm2
  else
    echo ">>> pm2 installed $(pm2 -v)"
  fi
else
  echo ">>> No NPM were found, exiting with error."
  exit 10006
fi
