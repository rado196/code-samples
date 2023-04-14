#!/usr/bin/env bash
cd "$(dirname "$0")"

# clear terminal 
clear
echo ""

npm run build:runtime
if [[ $? != 0 ]]; then
  exit 1
fi

rm -rf .runtime
mkdir .runtime

cp -r example .runtime/public
cp .dist/runtime/tcf2.js .runtime/public/tcf2.js
cd .runtime

npm install express

echo "const express = require('express');" >> _serve.js
echo "const path = require('path');" >> _serve.js
echo "" >> _serve.js
echo "const app = express();" >> _serve.js
echo "app.use(express.static(path.join(__dirname, 'public')));" >> _serve.js
echo "" >> _serve.js
echo "const port = 3000;" >> _serve.js
echo "app.listen(port, function () {" >> _serve.js
echo "  console.log(\`App listening on port \${port}\`)" >> _serve.js
echo "});" >> _serve.js
echo "" >> _serve.js

node _serve.js
