#!/bin/sh

# CURRENT_DIR=$(cd $(dirname $0); pwd)
DELIMITER_LINE='------------------------------------------------------'
START_MESSAGE='start database initialization.'

# CHANGE Variable.

# @param {string} message
showMessage() {
  echo ${DELIMITER_LINE}
  echo $1
}

# process start
showMessage ${START_MESSAGE}

# mysql -u ${DB_USER} -p${MYSQL_ROOT_PASSWORD} ${DB_DATABASE}

databaseList=(
cochlea
cochlea_logs
cochlea_user1
cochlea_user2
cochlea_user3
cochlea_testing
)

for d in ${databaseList[@]}; do
  # database setting
  mysqlCommand="CREATE DATABASE IF NOT EXISTS ${d} CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
  createUserCommand="CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED BY '${MYSQL_ROOT_PASSWORD}';"
  grantCommand="GRANT ALL PRIVILEGES ON ${d}.* TO 'root'@'%';"
  mysql -u ${DB_USER} -p${MYSQL_ROOT_PASSWORD} ${DB_DATABASE} -e $mysqlCommand
  # CREATE DATABASE IF NOT EXISTS cochlea CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

  # user setting
  mysql -u ${DB_USER} -p${MYSQL_ROOT_PASSWORD} ${DB_DATABASE} -e $createUserCommand
  mysql -u ${DB_USER} -p${MYSQL_ROOT_PASSWORD} ${DB_DATABASE} -e $grantCommand
  mysql -u ${DB_USER} -p${MYSQL_ROOT_PASSWORD} ${DB_DATABASE} -e "FLUSH PRIVILEGES;"
done

## slaveアカウント
createSlaveUserCommand="CREATE USER IF NOT EXISTS 'repl'@'%' IDENTIFIED BY '${MYSQL_ROOT_PASSWORD}';"
mysql -u ${DB_USER} -p${MYSQL_ROOT_PASSWORD} ${DB_DATABASE} -e $createSlaveUserCommand
grantSlaveUserCommand="GRANT REPLICATION SLAVE ON *.* TO 'repl'@'%';"

showMessage 'initialize database.'

