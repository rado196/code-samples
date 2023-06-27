#!/usr/bin/env bash
cd "$(dirname "$0")/../" || exit 1
if [[ "$(command -v realpath)" != "" ]]; then
  ROOT_DIR="$(realpath "$PWD")"
else
  ROOT_DIR="$PWD"
fi

## ========================================================================
## Tools & Libraries

sudo apt-get -y update
sudo apt-get -y install make \
  cmake \
  gcc \
  g++ \
  build-essential \
  software-properties-common \
  python2 \
  python2-dev \
  python2-minimal \
  python3 \
  python3-dev \
  python3-minimal \
  curl \
  openssl \
  vim \
  git \
  htop \
  nano \
  unzip \
  net-tools \
  wget \
  vim \
  cron \
  gnupg2

echo "colorscheme default" >> ~/.vimrc
echo "syntax on" >> ~/.vimrc
echo "set autoindent" >> ~/.vimrc
echo "set tabstop=4" >> ~/.vimrc
echo "set shiftwidth=4" >> ~/.vimrc
echo "set expandtab" >> ~/.vimrc
echo "set cursorline" >> ~/.vimrc
echo "hi CursorLine cterm=NONE ctermbg=black guibg=darkred guifg=white" >> ~/.vimrc

sudo swapoff -a
sudo dd if=/dev/zero of=/swapfile bs=256M count=16
sudo chmod 0600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile

## ========================================================================
## Redis

sudo apt-get -y install redis\
  redis-server

sudo cat /etc/redis/redis.conf | sed 's/^supervised no/supervised systemd/g' > /tmp/redis.conf.temp
sudo rm /etc/redis/redis.conf
sudo mv /tmp/redis.conf.temp /etc/redis/redis.conf

sudo systemctl enable redis-server@
sudo systemctl start redis-server@

## ========================================================================
## Nginx

sudo apt-get -y install nginx

sudo systemctl stop apache2.service
sudo systemctl disable apache2.service
sudo systemctl enable nginx.service
sudo systemctl start nginx.service

mkdir -p /var/www
cd /var/www
sudo rm -rf html
sudo chmod -R 777 .

## ========================================================================
## NodeJS

curl -sL https://deb.nodesource.com/setup_16.x | sudo bash -
sudo apt-get -y update
sudo apt-get -y install nodejs

mkdir "$HOME/.npm-cache"
npm config set prefix "$HOME/.npm-cache"
EXPORT_NPM_PATH="export PATH=\"$HOME/.npm-cache/bin:\$PATH\""
cat "$HOME/.profile" | grep -q "$EXPORT_NPM_PATH"
if [[ $? != 0 ]]; then
  echo "$EXPORT_NPM_PATH" >>"$HOME/.profile"
  source "$HOME/.profile"
fi

npm install --location=global pm2
pm2 startup
sudo env \
  PATH=$PATH:/usr/bin \
  "$HOME/.npm-cache/lib/node_modules/pm2/bin/pm2" \
  startup systemd \
  -u "$USER" \
  --hp "$HOME"

sudo systemctl enable "pm2-$USER"
sudo systemctl start "pm2-$USER"

## ========================================================================
## GoLang

cd "/tmp"
wget https://dl.google.com/go/go1.20.5.linux-amd64.tar.gz
rm -rf /usr/local/go
sudo tar -C /usr/local -xzf go1.20.5.linux-amd64.tar.gz
rm -rf go1.20.5.linux-amd64.tar.gz

EXPORT_NPM_PATH="export PATH=\"/usr/local/go/bin:\$PATH\""
cat "$HOME/.profile" | grep -q "$EXPORT_NPM_PATH"
if [[ $? != 0 ]]; then
  echo "$EXPORT_NPM_PATH" >>"$HOME/.profile"
  source "$HOME/.profile"
fi

cd "$ROOT_DIR"

## ========================================================================
## Cleaning up

sudo apt -y update
sudo apt -y autoremove
sudo apt -y autoclean
