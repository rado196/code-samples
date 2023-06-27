#!/usr/bin/env bash
cd "$(dirname "$0")/../" || exit 1
if [[ "$(command -v realpath)" != "" ]]; then
  ROOT_DIR="$(realpath "$PWD")"
else
  ROOT_DIR="$PWD"
fi

# ================================================================================
# == Inputs & Variables

function print_usage() {
  local ERROR_MESSAGE="$1"

  echo ""
  echo "ERROR: $ERROR_MESSAGE"
  echo ""
  echo "Invalid command usage, your command must be like this:"
  echo "  bash ./.setup/aws-deploy.sh \\"
  echo "    --account-id=123456789012 \\"
  echo "    --profile=default \\"
  echo "    --environment=staging \\"
  echo "    --artifact-s3=deploy-artifacts \\"
  echo "    --ec2-instance=web-t2.micro \\"
  echo ""
  echo "Parameter descriptions:"
  echo "  --account-id   - Your AWS 12-digit Account ID."
  echo "  --profile      - Configured AWS profile name, to generate new one"
  echo "                   just run \"aws configure\" command  and see result"
  echo "                   in ~/.aws/credentials and ~/.aws/config files."
  echo "  --environment  - Environment name that you want to setup for, possible values:"
  echo "                   * dev, develop, development"
  echo "                   * test, testing"
  echo "                   * stage, staging"
  echo "                   * prod, production, release"
  echo "  --artifact-s3  - The S3 bucket name thaw will be used to upload a generated"
  echo "                   artifacts for deployment."
  echo "  --ec2-instance - The Amazon EC2 instance name that deployment artifacts"
  echo "                   must be downloaded into."
  echo ""

  exit 1
}

echo ""
echo "================================================================================"
echo "== Inputs & Variables"
echo ""

for ARGUMENT in ${@}; do
  if [[ "$ARGUMENT" == --account-id=* ]]; then
    AWS_ACCOUNT_ID="${ARGUMENT:13}"
  elif [[ "$ARGUMENT" == --profile=* ]]; then
    AWS_PROFILE_NAME="${ARGUMENT:10}"
  elif [[ "$ARGUMENT" == --environment=* ]]; then
    ENV_NAME="${ARGUMENT:14}"
  elif [[ "$ARGUMENT" == --artifact-s3=* ]]; then
    AWS_ARTIFACT_BUCKET="${ARGUMENT:14}"
  elif [[ "$ARGUMENT" == --ec2-instance=* ]]; then
    AWS_EC2_INSTANCE_NAME="${ARGUMENT:15}"
  fi
done

if [[ "$AWS_ACCOUNT_ID" == "" || "${#AWS_ACCOUNT_ID}" != 12 ]]; then
  print_usage "Account ID must be 12-digit value."
fi

if [[ "$AWS_PROFILE_NAME" == "" ]]; then
  print_usage "Profile name must be provided."
fi

aws configure --profile "$AWS_PROFILE_NAME" list > /dev/null 2>&1
if [[ $? != 0 ]]; then
  print_usage "Profile \"$AWS_PROFILE_NAME\" does not exits.."
fi

if [[ "$ENV_NAME" == "dev" || "$ENV_NAME" == "develop" || "$ENV_NAME" == "development" ]]; then
  GITHUB_BRANCH="develop"
elif [[ "$ENV_NAME" == "test" || "$ENV_NAME" == "testing" ]]; then
  GITHUB_BRANCH="test"
elif [[ "$ENV_NAME" == "stage" || "$ENV_NAME" == "staging" ]]; then
  GITHUB_BRANCH="staging"
elif [[ "$ENV_NAME" == "prod" || "$ENV_NAME" == "production" || "$ENV_NAME" == "release" ]]; then
  GITHUB_BRANCH="main"
else
  print_usage "The deployment environment must be one of from provided list."
fi

if [[ "$AWS_ARTIFACT_BUCKET" == "" ]]; then
  print_usage "The S3 bucket name for artifacts must be provided."
fi

if [[ "$AWS_EC2_INSTANCE_NAME" == "" ]]; then
  print_usage "The EC2 instance name must be provided."
fi

AWS_ROLE_NAME="deploy-Web-$ENV_NAME-role"
AWS_CUSTOM_POLICY_NAME="deploy-Web-$ENV_NAME-policy"
AWS_CODEBUILD_PROJECT="deploy-Web-$ENV_NAME-CodeBuild-project"
AWS_CODEDEPLOY_APP="deploy-Web-$ENV_NAME-CodeDeploy-app"
AWS_CODEDEPLOY_GROUP="deploy-Web-$ENV_NAME-CodeDeploy-group"

# ================================================================================
# == IAM - Roles and Policies

echo ""
echo "================================================================================"
echo "== IAM - Roles and Policies"
echo ""

sleep 15

echo -n "Creating policy: $AWS_CUSTOM_POLICY_NAME ... "
aws iam create-policy \
  --policy-name "$AWS_CUSTOM_POLICY_NAME" \
  --policy-document "{
    \"Version\": \"2012-10-17\",
    \"Statement\": {
      \"Effect\": \"Allow\",
      \"Action\": [
        \"logs:CreateLogGroup\",
        \"logs:CreateLogStream\",
        \"logs:PutLogEvents\",
        \"s3:PutObject\",
        \"s3:GetObject\",
        \"s3:GetObjectVersion\",
        \"s3:GetBucketAcl\",
        \"s3:GetBucketLocation\",
        \"ec2:DescribeInstances\",
        \"ec2:DescribeInstanceStatus\",
        \"ec2:TerminateInstances\",
        \"sts:AssumeRole\"
      ],
      \"Resource\": \"*\"
    }
  }" \
  --profile "$AWS_PROFILE_NAME"

if [[ $? == 0 ]]; then
  echo "DONE"
else
  echo "FAIL"
  exit 1
fi

echo -n "Creating role: $AWS_ROLE_NAME ... "
aws iam create-role \
  --role-name "$AWS_ROLE_NAME" \
  --assume-role-policy-document "{
    \"Version\": \"2012-10-17\",
    \"Statement\": {
      \"Effect\": \"Allow\",
      \"Action\": \"sts:AssumeRole\",
      \"Principal\": {
        \"AWS\": \"$AWS_ACCOUNT_ID\",
        \"Service\": [
          \"codebuild.amazonaws.com\",
          \"codedeploy.amazonaws.com\"
        ]
      }
    }
  }" \
  --profile "$AWS_PROFILE_NAME"

if [[ $? == 0 ]]; then
  echo "DONE"
else
  echo "FAIL"
  exit 1
fi

echo -n "Attaching policy to role: $AWS_CUSTOM_POLICY_NAME ... "
aws iam attach-role-policy \
  --role-name "$AWS_ROLE_NAME" \
  --policy-arn "arn:aws:iam::$AWS_ACCOUNT_ID:policy/$AWS_CUSTOM_POLICY_NAME" \
  --profile "$AWS_PROFILE_NAME"

if [[ $? == 0 ]]; then
  echo "DONE"
else
  echo "FAIL"
  exit 1
fi

echo -n "Attaching policy to role: AWSCodeDeployDeployerAccess ... "
aws iam attach-role-policy \
  --role-name "$AWS_ROLE_NAME" \
  --policy-arn "arn:aws:iam::aws:policy/AWSCodeDeployDeployerAccess" \
  --profile "$AWS_PROFILE_NAME"

if [[ $? == 0 ]]; then
  echo "DONE"
else
  echo "FAIL"
  exit 1
fi

echo -n "Attaching policy to role: AmazonS3FullAccess ... "
aws iam attach-role-policy \
  --role-name "$AWS_ROLE_NAME" \
  --policy-arn "arn:aws:iam::aws:policy/AmazonS3FullAccess" \
  --profile "$AWS_PROFILE_NAME"

if [[ $? == 0 ]]; then
  echo "DONE"
else
  echo "FAIL"
  exit 1
fi

# ================================================================================
# == CodeBuild - Project

echo ""
echo "================================================================================"
echo "== CodeBuild - Project"
echo ""

ENV_VARIABLES_STRING=""

ENV_VARIABLES_LIST="$(cat .env.example | grep -v '^#' | grep -v '^$' | sed 's|export ||g')"
for ENV_VARIABLE in ${ENV_VARIABLES_LIST[@]}; do
  ENV_VAR_KEY="$(echo "$ENV_VARIABLE" | sed 's/=.*//g')"
  ENV_VAR_VAL="$(echo "$ENV_VARIABLE" | sed 's/^.*=//g')"

  if [[ "$ENV_VAR_VAL" == '""' ]]; then
    ENV_VAR_VAL=""
  fi

  if [[ "$ENV_VARIABLES_STRING" != "" ]]; then
    ENV_VARIABLES_STRING="$ENV_VARIABLES_STRING,"
  fi

  ENV_VARIABLES_STRING="${ENV_VARIABLES_STRING}{name=\"$ENV_VAR_KEY\",value=\"$ENV_VAR_VAL\",type=\"PLAINTEXT\"}"
done

ENV_VARIABLES_STRING="${ENV_VARIABLES_STRING},{name=\"NPM_TOKEN\",value=\"\",type=\"PLAINTEXT\"}"
ENV_VARIABLES_STRING="${ENV_VARIABLES_STRING},{name=\"CODE_BUILD_DEPLOY_APP_NAME\",value=\"$AWS_CODEDEPLOY_APP\",type=\"PLAINTEXT\"}"
ENV_VARIABLES_STRING="${ENV_VARIABLES_STRING},{name=\"CODE_BUILD_DEPLOY_GROUP\",value=\"$AWS_CODEDEPLOY_GROUP\",type=\"PLAINTEXT\"}"
ENV_VARIABLES_STRING="${ENV_VARIABLES_STRING},{name=\"CODE_BUILD_S3_BUCKET\",value=\"$AWS_ARTIFACT_BUCKET\",type=\"PLAINTEXT\"}"
ENV_VARIABLES_STRING="${ENV_VARIABLES_STRING},{name=\"CODE_BUILD_S3_FOLDER\",value=\"$GITHUB_BRANCH\",type=\"PLAINTEXT\"}"

sleep 15

echo -n "Creating CodeBuild project: $AWS_CODEBUILD_PROJECT ... "
aws codebuild create-project \
  --name "$AWS_CODEBUILD_PROJECT" \
  --environment "type=LINUX_CONTAINER,computeType=BUILD_GENERAL1_SMALL,image=aws/codebuild/standard:6.0,imagePullCredentialsType=CODEBUILD,environmentVariables=[$ENV_VARIABLES_STRING]" \
  --source "type=GITHUB,gitCloneDepth=1,location=https://github.com/443-how/web.git" \
  --source-version "refs/heads/$GITHUB_BRANCH" \
  --artifacts "type=S3,location=443-deploy,path=$ENV_NAME,packaging=ZIP,name=web-$ENV_NAME.zip,overrideArtifactName=true" \
  --logs-config "cloudWatchLogs={status=ENABLED,groupName="$AWS_CODEBUILD_PROJECT"},s3Logs={status=DISABLED}" \
  --service-role "arn:aws:iam::$AWS_ACCOUNT_ID:role/$AWS_ROLE_NAME" \
  --profile "$AWS_PROFILE_NAME"

if [[ $? == 0 ]]; then
  echo "DONE"
else
  echo "FAIL"
  exit 1
fi

echo -n "Creating webhook for CodeBuild project: $AWS_CODEBUILD_PROJECT ... "
aws codebuild create-webhook \
  --project-name "$AWS_CODEBUILD_PROJECT" \
  --filter-groups "[[{\"type\":\"EVENT\",\"pattern\":\"PUSH\"}]]" \
  --build-type BUILD \
  --profile "$AWS_PROFILE_NAME"

if [[ $? == 0 ]]; then
  echo "DONE"
else
  echo "FAIL"
  exit 1
fi

# ================================================================================
# == CodeDeploy Application

echo ""
echo "================================================================================"
echo "== CodeDeploy Application"
echo ""

sleep 15

echo -n "Creating CodeDeploy application: $AWS_CODEDEPLOY_APP ... "
aws deploy create-application \
  --application-name "$AWS_CODEDEPLOY_APP" \
  --compute-platform "Server" \
  --profile "$AWS_PROFILE_NAME"

if [[ $? == 0 ]]; then
  echo "DONE"
else
  echo "FAIL"
  exit 1
fi

# ================================================================================
# == CodeDeploy - Deployment Group

echo ""
echo "================================================================================"
echo "== CodeDeploy - Deployment Group"
echo ""

sleep 15

echo -n "Creating CodeDeploy deployment group: $AWS_CODEDEPLOY_GROUP ... "
aws deploy create-deployment-group \
  --application-name "$AWS_CODEDEPLOY_APP" \
  --deployment-group-name "$AWS_CODEDEPLOY_GROUP" \
  --service-role-arn "arn:aws:iam::$AWS_ACCOUNT_ID:role/$AWS_ROLE_NAME" \
  --deployment-config-name "CodeDeployDefault.AllAtOnce" \
  --ec2-tag-filters "Key=Name,Value=$AWS_EC2_INSTANCE_NAME,Type=KEY_AND_VALUE" \
  --profile "$AWS_PROFILE_NAME"

if [[ $? == 0 ]]; then
  echo "DONE"
else
  echo "FAIL"
  exit 1
fi

# ================================================================================
# == Completed

echo ""
echo "================================================================================"
echo "== Completed"
echo ""

sleep 3

echo ""
echo ""
echo " >>> Please go to CodeBuild console and do following steps manually."
echo "     1) Authorize AWS to connect to GitHub"
echo "     2) Write environment variables"
echo ""
echo ""
