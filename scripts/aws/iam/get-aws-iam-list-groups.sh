#!/bin/sh

# CURRENT_DIR=$(cd $(dirname $0); pwd)
DELIMITER_LINE='------------------------------------------------------'
START_MESSAGE='start Get AWS IAM Group List'

# 事前にaws-cliのconfig設定が必要
AWS_CLI_PATH=/usr/local/bin/aws


# @param {string} message
showMessage() {
  echo ${DELIMITER_LINE}
  echo $1
}

# process start
showMessage "$START_MESSAGE"

$AWS_CLI_PATH iam list-groups

showMessage "Get AWS IAM List Groups"

