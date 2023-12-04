#!/bin/bash

DEFAULT_AWS_PROFILE=default

# aws-cli
echo "export AWS_PROFILE=$DEFAULT_AWS_PROFILE" >> $HOME/.bash_profile

source $HOME/.bash_profile

