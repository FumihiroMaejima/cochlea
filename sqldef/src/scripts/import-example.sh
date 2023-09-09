#!/bin/sh

# CURRENT_DIR=$(cd $(dirname $0); pwd)
DELIMITER_LINE='------------------------------------------------------'
START_MESSAGE='start import database ddl.'

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
OUTPUT_FILE=dump.sql # 存在するディレクトリである必要がある(scripts/databaseなど)
OUTPUT_CSV_FILE=scripts/database/dump_${TIME_STAMP}.csv
DRY_RUN_OPTION=--dry-run

# @param {string} message
showMessage() {
  echo ${DELIMITER_LINE}
  echo $1
}

# process start
showMessage ${START_MESSAGE}

# parameter check
if [ "$1" != '' ]; then
  if [ "$2" == 'dryRun' ]; then
    docker-compose exec -T ${SERVICE_NAME} ${SERVICE_BIN_PATH} -h ${DATABASE_HOST} -P ${DATABASE_PORT} -u ${DATABASE_USER} -p ${DATABASE_PASSWORD} $1 ${DRY_RUN_OPTION} < ${OUTPUT_PATH}${OUTPUT_FILE}
  else
    # -T is required to avoid error: the input device is not a TTY
    docker-compose exec -T ${SERVICE_NAME} ${SERVICE_BIN_PATH} -h ${DATABASE_HOST} -P ${DATABASE_PORT} -u ${DATABASE_USER} -p ${DATABASE_PASSWORD} $1 < ${OUTPUT_PATH}${OUTPUT_FILE}
  fi
else
  # command.
  docker-compose exec -T ${SERVICE_NAME} ${SERVICE_BIN_PATH} -h ${DATABASE_HOST} -P ${DATABASE_PORT} -u ${DATABASE_USER} -p ${DATABASE_PASSWORD} ${DATABASE_NAME} < ${OUTPUT_PATH}${OUTPUT_FILE}
fi

showMessage 'import data base.'

