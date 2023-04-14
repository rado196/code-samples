#!/bin/bash

echo "🚧 Building node project ..."
npm run build

if [[ $? -eq 0 ]]; then
  echo "✅ Build completed successfully."
else
  echo "❌ Could not build project."
  exit 10005
fi
