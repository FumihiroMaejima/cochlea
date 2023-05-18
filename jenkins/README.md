# Jenkinsest

ローカルのDockerでjenkins環境を構築する為の手順書

## 構成

| 名前 | バージョンなど |
| :--- | :---: |
| jenkins/jenkins | latest |

---

## ローカル環境の構築(Mac)

```shell
docker-compose up -d

# 設定したポートを指定してlocalhostにアクセス
http://localhost:8280

```

container上の`/var/jenkins_home`のデータはローカルの`./jenkinst/home`に反映される様にしている。

`/var/jenkins_home`にsshキーを作成しておく。

```shell
docker exec -it jenkins-master bash

cd /var/jenkins_home

ssh-keygen -t rsa -C ""
```

*コンテナ再起動時もやや時間がかかる。

コンテナ再起動によってクライアントにアクセス出来なくなったら、下記のコマンドでイメージごと作り直すのが良い。

`/var/jenkins_hom/.cache`は削除した方が良さそう。

```shell
docker-compose down --rmi all
```

---

## sshキー(公開鍵の設定)

`/var/jenkins_hom/.ssh/id_rsa.pub`の情報をクライアントに貼り付けておく。

```shell
cat /var/jenkins_hom/.ssh/id_rsa.pub | pbcopy
```

---

## コンテナの再起動について

コンテナのダウン時にvolumeの削除とcacheの削除を実行するとコンテナ再立ち上げが上手くいく。

```shell
docker-compose -f ${DOCKER_COMPOSE_FILE} down -v
# cacheも削除する
rm -r src/.cache
```

---

## jobの作成について

jobの具体的な内容はシェルスクリプトなどに書いてjob内のコンソールで呼び出す形が良さそう。

```shell
# jobで実行するスクリプトを実行
sh /usr/local/scripts/test-job-script.sh
# パラメーターを渡す場合(設定したパラメーター名を指定する)
sh /usr/local/scripts/test-job-script.sh $testParameter1
```

---
