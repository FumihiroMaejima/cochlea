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
docker network create --gateway=172.19.0.1 --subnet=172.19.0.0/16 ${PROJECT_NAME}-net
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

