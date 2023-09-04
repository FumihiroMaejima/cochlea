#!/bin/sh

# CURRENT_DIR=$(cd $(dirname $0); pwd)
DELIMITER_LINE='------------------------------------------------------'
START_MESSAGE='start export database dump.'

# dateコマンド結果を指定のフォーマットで出力
TIME_STAMP=$(date "+%Y%m%d_%H%M%S")

# CHANGE Variable.
DATABASE_CONTAINER_NAME=database_container_name
DATABASE_HOST=host.docker.internal
DATABASE_PORT=3306
DATABASE_USER=database_user
DATABASE_PASSWORD=database_password
DATABASE_NAME=database_name
OUTPUT_FILE=sample/dump/dump_${TIME_STAMP}.sql # 存在するディレクトリである必要がある(scripts/databaseなど)
SECURE_FILE_PRIV_DIR=/var/lib/mysql-files
OUTPUT_CSV_FILE=scripts/database/dump_${TIME_STAMP}.csv

# @param {string} message
showMessage() {
  echo ${DELIMITER_LINE}
  echo $1
}

# process start
showMessage ${START_MESSAGE}

# command.
docker-compose exec sqldef /mysqldef -h ${DATABASE_HOST} -P ${DATABASE_PORT} -u ${DATABASE_USER} -p ${DATABASE_PASSWORD} ${DATABASE_NAME} --export

showMessage 'export data base.'

