#!/bin/sh

# CURRENT_DIR=$(cd $(dirname $0); pwd)
DELIMITER_LINE='------------------------------------------------------'
START_MESSAGE='start Get AWS S3 List'

AWS_CLI_PATH=/usr/local/bin/aws


# @param {string} message
showMessage() {
  echo ${DELIMITER_LINE}
  echo $1
}

# process start
showMessage "$START_MESSAGE"

# iam users
$AWS_CLI_PATH s3 ls

showMessage "Get AWS S3 Users"

