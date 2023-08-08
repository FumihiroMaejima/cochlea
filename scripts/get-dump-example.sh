#!/bin/sh

# CURRENT_DIR=$(cd $(dirname $0); pwd)
DELIMITER_LINE='------------------------------------------------------'
START_MESSAGE='start getting database dump.'

# dateコマンド結果を指定のフォーマットで出力
TIME_STAMP=$(date "+%Y%m%d_%H%M%S")

# CHANGE Variable.
DATABASE_CONTAINER_NAME=database_container_name
DATABASE_USER=database_user
DATABASE_PASSWORD=database_password
DATABASE_NAME=database_name
OUTPUT_FILE=sample/dump/dump_${TIME_STAMP}.sql
SECURE_FILE_PRIV_DIR=/var/lib/mysql-files
OUTPUT_CSV_FILE=scripts/dump_${TIME_STAMP}.csv

# @param {string} message
showMessage() {
  echo ${DELIMITER_LINE}
  echo $1
}

# process start
showMessage ${START_MESSAGE}

# parameter check
if [ "$1" != '' ]; then
  if [ "$1" == 'gz' ]; then
    docker exec -it ${DATABASE_CONTAINER_NAME} mysqldump -u ${DATABASE_USER} -p${DATABASE_PASSWORD} ${DATABASE_NAME} | gzip > ${OUTPUT_FILE}.gz
  elif [ "$1" == 'csv' ]; then
    # only ouput in docker container
    docker exec -it ${DATABASE_CONTAINER_NAME} mysqldump -u ${DATABASE_USER} -p${DATABASE_PASSWORD} --tab=${SECURE_FILE_PRIV_DIR} --fields-terminated-by=, ${DATABASE_NAME}
  fi
else
  # dump command.
  docker exec -it ${DATABASE_CONTAINER_NAME} mysqldump -u ${DATABASE_USER} -p${DATABASE_PASSWORD} ${DATABASE_NAME} > ${OUTPUT_FILE}
fi

# dump command.
# docker exec -it ${DATABASE_CONTAINER_NAME} mysqldump -u ${DATABASE_USER} -p${DATABASE_PASSWORD} ${DATABASE_NAME} > ${OUTPUT_FILE}

# 現在のDocker コンテナの状態を出力
showMessage 'get data base dump.'

