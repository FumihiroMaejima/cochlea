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

showMessage "Finish build."

