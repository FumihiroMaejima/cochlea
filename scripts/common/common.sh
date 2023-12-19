#!/bin/bash

# {string} message
function showMessage() {
  echo '------------------------------------------------------'
  echo $1
}

# {unknown} parameter
function isParamterNotEmpty() {
if [ "$1" == '' ]; then
  showMessage 'Invalid Parameter. \nNo parameter.'
  exit
fi
}

# {string} parameter
# {string} target
function isContainTargetString() {
if [[ "$1" != *$2* ]]; then
  showMessage "Invalid Parameter. \nNo contains '$2'."
  exit
fi
}

