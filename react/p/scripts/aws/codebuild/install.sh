#!/bin/bash

echo "📦 Installing NPM packages ..."

rm -rf node_modules
npm install

if [[ $? -eq 0 ]]; then
  echo "✅ Packages installed successfully."
else
  echo "❌ Could not install packages."
  exit 10002
fi
