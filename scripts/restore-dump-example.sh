#!/bin/sh

# CURRENT_DIR=$(cd $(dirname $0); pwd)
DELIMITER_LINE='------------------------------------------------------'
START_MESSAGE='start restore database dump.'

# dateコマンド結果を指定のフォーマットで出力
TIME_STAMP=$(date "+%Y%m%d_%H%M%S")

# CHANGE Variable.
DATABASE_CONTAINER_NAME=database_container_name
DATABASE_USER=database_user
DATABASE_PASSWORD=database_password
DATABASE_NAME=database_name
OUTPUT_FILE=sample/dump/dump.sql
# TIME_STAMPを使う場合
# OUTPUT_FILE=sample/dump/dump_${TIME_STAMP}.sql

# @param {string} message
showMessage() {
  echo ${DELIMITER_LINE}
  echo $1
}

# process start
showMessage ${START_MESSAGE}

# dump command.
# docker exec -it ${DATABASE_CONTAINER_NAME} mysqldump -u ${DATABASE_USER} -p${DATABASE_PASSWORD} -D ${DATABASE_NAME} < ${OUTPUT_FILE}
docker exec -i ${DATABASE_CONTAINER_NAME} mysql -h localhost -u ${DATABASE_USER} -p${DATABASE_PASSWORD} -D ${DATABASE_NAME} < ${OUTPUT_FILE}

# メッセージ出力
showMessage 'restore data base dump.'

