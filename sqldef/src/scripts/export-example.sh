#!/bin/sh

# CURRENT_DIR=$(cd $(dirname $0); pwd)
DELIMITER_LINE='------------------------------------------------------'
START_MESSAGE='start export database dump.'

# dateコマンド結果を指定のフォーマットで出力
TIME_STAMP=$(date "+%Y%m%d_%H%M%S")

# CHANGE Variable.
SERVICE_NAME=sqldef
SERVICE_BIN_PATH=/mysqldef
DATABASE_HOST=host.docker.internal
DATABASE_PORT=3306
DATABASE_USER=database_user
DATABASE_PASSWORD=database_password
DATABASE_NAME=database_name
OUTPUT_PATH=sqldef/src/sqls/
OUTPUT_FILE=dump_${TIME_STAMP}.sql # 存在するディレクトリである必要がある(scripts/databaseなど)
SECURE_FILE_PRIV_DIR=/var/lib/mysql-files
OUTPUT_CSV_FILE=scripts/database/dump_${TIME_STAMP}.csv

# @param {string} message
showMessage() {
  echo ${DELIMITER_LINE}
  echo $1
}

# process start
showMessage ${START_MESSAGE}

# parameter check
if [ "$1" != '' ]; then
  docker-compose exec ${SERVICE_NAME} ${SERVICE_BIN_PATH} -h ${DATABASE_HOST} -P ${DATABASE_PORT} -u ${DATABASE_USER} -p ${DATABASE_PASSWORD} $1 --export > ${OUTPUT_PATH}"$1_"${OUTPUT_FILE}
else
  # command.
  docker-compose exec ${SERVICE_NAME} ${SERVICE_BIN_PATH} -h ${DATABASE_HOST} -P ${DATABASE_PORT} -u ${DATABASE_USER} -p ${DATABASE_PASSWORD} ${DATABASE_NAME} --export > ${OUTPUT_PATH}${DATABASE_NAME}_${OUTPUT_FILE}
fi

showMessage 'export data base.'

