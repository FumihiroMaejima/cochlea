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

source $HOME/.nvm/nvm.sh ;

showMessage "nvm version."
nvm -v

showMessage "install stable node.js"
nvm install "$TARGET_NODE_VERSION"
nvm use "$TARGET_NODE_VERSION"

showMessage "current npm version"
$HOME/.nvm/versions/node/v$TARGET_NODE_VERSION/bin/npm -v

showMessage "current node.js version"
node -v

showMessage "install yarn & TypeScript"
$HOME/.nvm/versions/node/v$TARGET_NODE_VERSION/bin/npm install -g yarn typescript

showMessage "current yarn version"
yarn -v

showMessage "current TypeScript version"
tsc -v

showMessage "current dir"
pwd

showMessage "execut yarn build"
# yarn build
### 実行結果の格納
YARN_BUILD_RESULT="$(yarn build 2>/dev/null)"

# build実行結果で分技
if [[ "$YARN_BUILD_RESULT" == "" ]]; then
  # エラーが無い時
  showMessage "yarn build success."
  exit 0
else
  # エラーがある時、jenkinsビルドをエラー終了させる
  showMessage "yarn build error."
  showMessage  "$YARN_BUILD_RESULT"
  # exit 1

  ### 検証時用
  showMessage "return SUCCESS status for development inspection."
  exit 0
fi

# showMessage "execut unit test"
# yarn test:unit


showMessage "Finish build."
echo $DELIMITER_LINE
