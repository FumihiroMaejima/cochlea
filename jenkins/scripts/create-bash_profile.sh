#!/bin/bash

# ls
# cat /etc/os-release

DELIMITER_LINE='------------------------------------------------------'
START_MESSAGE="Start Create .bash_profile."
DEFAULT_AWS_PROFILE=default

# aws-cli
echo "export AWS_PROFILE=$DEFAULT_AWS_PROFILE" >> $HOME/.bash_profile

source $HOME/.bash_profile

