#!/bin/sh

# CURRENT_DIR=$(cd $(dirname $0); pwd)
SEPARATOPION='---------------------------'
START_MESSAGE='start getting database dump.'

# dateコマンド結果を指定のフォーマットで出力
TIME_STAMP=$(date "+%Y%m%d_%H%M%S")

# CHANGE Variable.
DATABASE_CONTAINER_NAME=database_container_name
DATABASE_USER=database_user
DATABASE_NAME=database_name
DATABASE_PASSWORD=database_password
OUTPUT_FILE=sample/dump/dump_${TIME_STAMP}.sql

# @param {string} message
showMessage() {
  echo ${SEPARATOPION}
  echo $1
}

# process start
showMessage ${START_MESSAGE}

# dump command.
docker exec -it ${DATABASE_CONTAINER_NAME} mysqldump -u ${DATABASE_USER} -p${DATABASE_PASSWORD} ${DATABASE_NAME} > ${OUTPUT_FILE}

# 現在のDocker コンテナの状態を出力
showMessage 'get data base dump.'

