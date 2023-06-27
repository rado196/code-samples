#!/bin/bash

echo "🚚 Executing 'aws deploy push' command ..."

aws deploy push \
  --application-name "$CODE_BUILD_DEPLOY_APP_NAME" \
  --s3-location s3://$CODE_BUILD_S3_BUCKET/$CODE_BUILD_S3_FOLDER/web-source.zip

if [[ $? -eq 0 ]]; then
  echo "✅ Command 'aws deploy push' successfully completed."
else
  echo "❌ Could not execute command 'aws deploy push'."
  exit 10006
fi

echo "🚚 Executing 'aws deploy create-deployment' command ..."
aws deploy create-deployment \
  --application-name "$CODE_BUILD_DEPLOY_APP_NAME" \
  --s3-location bucket=$CODE_BUILD_S3_BUCKET,key=$CODE_BUILD_S3_FOLDER/web-source.zip,bundleType=zip \
  --deployment-group-name $CODE_BUILD_DEPLOY_GROUP \
  --deployment-config-name CodeDeployDefault.AllAtOnce \
  --description "Deploy to EC2 instance(s)."

if [[ $? -eq 0 ]]; then
  echo "✅ Command 'aws deploy create-deployment' successfully completed."
else
  echo "❌ Could not execute command 'aws deploy create-deployment'."
  exit 10007
fi
