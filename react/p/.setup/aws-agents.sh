#!/usr/bin/env bash
cd "$(dirname "$0")/../" || exit 1
if [[ "$(command -v realpath)" != "" ]]; then
  ROOT_DIR="$(realpath "$PWD")"
else
  ROOT_DIR="$PWD"
fi

## ========================================================================
## SSM Agent

mkdir /tmp/ssm
cd /tmp/ssm

if [[ "$(command -v snap)" != "" ]]; then
  sudo snap remove amazon-ssm-agent
fi

wget https://s3.amazonaws.com/ec2-downloads-windows/SSMAgent/latest/debian_amd64/amazon-ssm-agent.deb
sudo dpkg --install amazon-ssm-agent.deb
sudo systemctl start amazon-ssm-agent

cd "$ROOT"
rm -rf /tmp/ssm

## ========================================================================
## Ruby 2.6

mkdir /tmp/ruby2.6
cd /tmp/ruby2.6

echo \
  "deb https://ppa.launchpadcontent.net/brightbox/ruby-ng/ubuntu/ focal main" |
  sudo tee /etc/apt/sources.list.d/brightbox.list

sudo apt-key adv \
  --keyserver keyserver.ubuntu.com \
  --recv-keys 80F70E11F0F0D5F10CB20E62F5DA5F09C3173AA6

sudo apt update -y

echo \
  "deb http://security.ubuntu.com/ubuntu focal-security main" |
  sudo tee /etc/apt/sources.list.d/focal-security.list

sudo apt update -y
sudo apt install -y libssl1.1
sudo apt install -y ruby2.6
sudo apt install -y ruby2.6-dev

cd "$ROOT"
rm -rf /tmp/ruby2.6

## ========================================================================
## CodeDeploy Agent

mkdir /tmp/codedeploy_agent
cd /tmp/codedeploy_agent

wget https://aws-codedeploy-us-east-2.s3.us-east-2.amazonaws.com/latest/install
sudo chmod +x ./install
sudo ./install auto

cd "$ROOT"
rm -rf /tmp/codedeploy_agent

## ========================================================================
## AWS CLI Tool

mkdir /tmp/aws_cli
cd /tmp/aws_cli

curl \
  https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip \
  --output "aws-cli-v2.zip"
unzip awscliv2.zip
sudo ./aws/install

cd "$ROOT"
rm -rf /tmp/aws_cli

## ========================================================================
## Cleaning up

sudo apt -y update
sudo apt -y autoremove
sudo apt -y autoclean
