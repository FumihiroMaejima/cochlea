# Application Name

My Application.

# 構成

## backend

| 名前 | バージョン |
| :--- | :---: |
| PHP | 8.0.15(php:8.0.15-fpm-alpine) |
| MySQL | 5.7 |
| Nginx | 1.19(nginx:1.19-alpine) |
| Laravel | 9.* |

[backend/README](./app/backend/README.md)

## frontend

| 名前 | バージョン |
| :--- | :---: |
| npm | 8.1.0 |
| node | 16.13.0 |
| react | 17.0.2 |
| TypeScript | 4.5.2 |

[frontend/README](./frontend/README.md)

---

## volumeとnetworkの作成

networkは`gateway`と`subnet`を必ず指定する。(値は任意。)

```shell
docker volume create ${PROJECT_NAME}-db-store
docker volume create ${PROJECT_NAME}-redis-store
docker volume create ${PROJECT_NAME}-mail-store
docker network create --gateway=172.19.0.1 --subnet=172.19.0.0/16 ${PROJECT_NAME}-net
```


---

## メールサーバーについて

[mailhog](https://github.com/mailhog/MailHog)を利用する。

データの永続化の為に専用のvolumeを新規で作成する。

最低限下記の形でdocker-compose.ymlに記載すれば良い。

コンテナ起動後は`http://localhost:8025/`でブラウザ上からメール情報を確認出来る。

```yaml
  mail:
    image: mailhog/mailhog
    container_name: container_name
    volumes:
      - volumeName:/tmp
    ports:
      - "8025:8025"
    environment:
      MH_MAILDIR_PATH: /tmp
```

`app/backend/.env`のメール設定を下記の通りに設定すること。

`MAIL_HOST`はデフォルトの値が`mailhog`になっているがDockerコンテナ名を設定する必要がある。

* 実際のSMTPでは、port:1025で受け付けている為8025ではなく1025にする必要がある。

```shell
MAIL_MAILER=smtp
MAIL_HOST=mail
MAIL_PORT=1025
```

---

# Swaggerの設定

 ### ローカル環境にswagger-codegenのインストール(mockサーバーのコード出力)

```shell-session
 $ brew install swagger-codegen
```

### API仕様から出力するmockサーバーについて

API仕様からmockサーバーの出力

```shell-session
 $ swagger-codegen generate -i api/api.yaml -l nodejs-server -o api/nodejs
```

node.jsのサーバーなので、`node_modules`のインストールが必要

`npm run install`と`npm run prestart`を実行後に起動出来る。

```shell-session
 $ npm run prestart
```

mockサーバーの起動

```shell-session
 $ npm run start
```

---

# 構成



---

