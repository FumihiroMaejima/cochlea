#!/bin/bash

# ls
# cat /etc/os-release

DELIMITER_LINE='------------------------------------------------------'
START_MESSAGE="Start build."
TARGET_NODE_VERSION='19.2.0'

# @param {string} message
showMessage() {
  echo ${DELIMITER_LINE}
  echo $1
}

showMessage ${START_MESSAGE}

showMessage "Build Execution Logic."

# parameter count ($#) check。
if [ $# -ne 1 ]; then
  echo ${DELIMITER_LINE}
  echo "You had set $# parameters." 1>&2
  echo "You need 1 parameters to exectute this batch." 1>&2
  exit 1
fi
showMessage $1

showMessage "Finish build."

