.PHONY: help
.DEFAULT_GOAL := help

# load test
WOKER=1
LOCUST_FILE=./loadTest/locust/locustfile.py
LOCUST_SAMPLE_FILE=./loadTest/locust/samples/locustfileTest.py

##############################
# make docker environmental
##############################
up:
	docker-compose up -d

stop:
	docker-compose stop

down:
	docker-compose down

down-rmi:
	docker-compose down --rmi all
ps:
	docker-compose ps

rebuild: # 個別のコンテナを作り直し
	docker-compose build --no-cache $(CONTAINER)

dev:
	sh ./scripts/container.sh && \
	${SHELL} ./scripts/change-db-host.sh db-next db

# ssr:
# 	sh ./scripts/container-nextjs.sh && \
# 	${SHELL} ./scripts/change-db-host.sh db db-next

##############################
# make frontend production in nginx container
##############################
frontend-install:
	docker-compose exec nginx ash -c 'cd /var/www/frontend && yarn install'

frontend-build:
	docker-compose exec nginx ash -c 'cd /var/www/frontend && yarn build'

##############################
# backend
##############################
migrate:
	docker-compose exec app php artisan migrate

migrate-wipe:
	docker-compose exec app php artisan db:wipe --database mysql && \
	docker-compose exec app php artisan db:wipe --database mysql_logs && \
	docker-compose exec app php artisan db:wipe --database mysql_user1 && \
	docker-compose exec app php artisan db:wipe --database mysql_user2 && \
	docker-compose exec app php artisan db:wipe --database mysql_user3 && \
	docker-compose exec app php artisan migrate:fresh --seed

# データベースから全テーブルをドロップし、その後migrateを行う
migrate-fresh:
	docker-compose exec app php artisan migrate:fresh --seed

# 全部のデータベースマイグレーションを最初にロールバックし,その後migrateを行う
migrate-refresh:
	docker-compose exec app php artisan migrate:refresh --seed

migrate-rollback:
	docker-compose exec app php artisan migrate:rollback

# migrationを全てロールバックする
migrate-reset:
	docker-compose exec app php artisan migrate:reset

seed:
	docker-compose exec app php artisan db:seed

tinker:
	docker-compose exec app php artisan tinker

artisan-list:
	docker-compose exec app php artisan list

composer-install:
	docker-compose exec app composer install

composer-update:
	docker-compose exec app composer update

dump-autoload:
	docker-compose exec app composer dump-autoload

cache-clear:
	docker-compose exec app php artisan cache:clear

view-clear:
	docker-compose exec app php artisan view:clear

config-clear:
	docker-compose exec app php artisan config:clear

phpunit:
	docker-compose exec app vendor/bin/phpunit --testdox

phpunit-cov:
	docker-compose exec app php -dxdebug.mode=coverage vendor/bin/phpunit --coverage-text --colors=never > app/backend/storage/logs/coverage.log

phpunit-cov-html-report:
	docker-compose exec app php -dxdebug.mode=coverage vendor/bin/phpunit --coverage-html=storage/coverage

phpcsfix:
	docker-compose exec app vendor/bin/php-cs-fixer fix -v --diff --config=.php-cs-fixer.php

phpcs:
	docker-compose exec app vendor/bin/phpcs --standard=phpcs.xml --extensions=php .

phpmd:
	docker-compose exec app vendor/bin/phpmd . text ruleset.xml --suffixes php --exclude node_modules,resources,storage,vendor,app/Console, database/seeds

php-insights:
	docker-compose exec app php artisan insights -s

phpstan:
	docker-compose exec app vendor/bin/phpstan analyze app tests

# local server
backend-serve:
	cd app/backend && php artisan serve

server-cache-clear:
	docker-compose exec app php artisan cache:clear && \
	docker-compose exec app php artisan config:clear

server-full-cache-clear:
	docker-compose exec app php artisan cache:clear && \
	docker-compose exec app php artisan config:clear && \
	docker-compose exec app composer dump-autoload

# set partitions in databases
logs-partitions:
	docker-compose exec app php artisan admins:add-logs-partitions

users-partitions:
	docker-compose exec app php artisan admins:add-users-partitions

remove-logs-partitions:
	docker-compose exec app php artisan admins:remove-logs-partitions

remove-users-partitions:
	docker-compose exec app php artisan admins:remove-users-partitions

debug-seed-user-coin-histories:
	docker-compose exec app php artisan debug:seed-user-coin-histories

##############################
# web server(nginx)
##############################
nginx-t:
	docker-compose exec nginx ash -c 'nginx -t'

nginx-reload:
	docker-compose exec nginx ash -c 'nginx -s reload'

nginx-stop:
	docker-compose exec nginx ash -c 'nginx -s stop'


##############################
# db container(mysql)
##############################
mysql:
	docker-compose exec db bash -c 'mysql -u $$DB_USER -p$$MYSQL_PASSWORD $$DB_DATABASE'

mysql-dump:
	sh ./scripts/get-dump.sh

mysql-restore:
	sh ./scripts/restore-dump.sh

##############################
# prometheus docker container
##############################
prometheus-up:
	docker-compose -f ./docker-compose.prometheus.yml up -d && \
	echo 'prometheus : http://localhost:9090' && \
	echo 'node-exporter : http://localhost:9100/metrics' && \
	echo 'grafana : http://localhost:3200' && \
	echo 'alertmanager : http://localhost:9093/#/status' && \
	echo 'promtail : http://localhost:9080/targets'

prometheus-down:
	docker-compose -f ./docker-compose.prometheus.yml down

prometheus-ps:
	docker-compose -f ./docker-compose.prometheus.yml ps

prometheus-dev:
	sh ./scripts/prometheus-container.sh

##############################
# locust docker environmental
##############################
locust-up:
	docker-compose -f ./docker-compose.locust.yml up -d

locust-down:
	docker-compose -f ./docker-compose.locust.yml down

locust-down-rmi:
	docker-compose -f ./docker-compose.locust.yml down --rmi all

locust-ps:
	docker-compose -f ./docker-compose.locust.yml ps

create: # create locustfile.py
ifeq ("$(wildcard $(LOCUST_FILE))", "") # ファイルが無い場合
	cp $(LOCUST_SAMPLE_FILE) $(LOCUST_FILE)
else
	@echo file already exist.
endif

locust-dev:
#	 sh ./scripts/locust-dev.sh
	sh ./scripts/locust-dev.sh $(WOKER)

##############################
# circle ci
##############################
circleci:
	cd app/backend && circleci build

ci:
	circleci build

##############################
# mock-server docker container
##############################
mock-up:
	docker-compose -f ./docker-compose.mock.yml up -d

mock-down:
	docker-compose -f ./docker-compose.mock.yml down

mock-ps:
	docker-compose -f ./docker-compose.mock.yml ps

##############################
# swagger docker container
##############################
swagger-up:
	docker-compose -f ./docker-compose.swagger.yml up -d

swagger-down:
	docker-compose -f ./docker-compose.swagger.yml down

swagger-ps:
	docker-compose -f ./docker-compose.swagger.yml ps

swagger-dev:
	sh ./scripts/swagger-container.sh

##############################
# swagger codegen mock-server
##############################
codegen-mock:
	rm -rf api/node-mock/* && \
	swagger-codegen generate -i api/api.yaml -l nodejs-server -o api/node-mock && \
	sed -i -e "s/serverPort = 8080/serverPort = 3200/g" api/node-mock/index.js && \
	cd api/node-mock && npm run prestart

codegen-changeport:
	sed -i -e "s/serverPort = 8080/serverPort = 3200/g" api/node-mock/index.js

codegen-prestart:
	cd api/node-mock && npm run prestart

codegen-start:
	cd api/node-mock && npm run start

##############################
# etc
##############################
help:
	@cat Makefile
