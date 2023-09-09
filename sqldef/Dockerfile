FROM ubuntu:latest

# ubuntuの場合、aptリポジトリの設定が必要。バージョン確認は下記から。
# https://dev.mysql.com/downloads/repo/apt/
# dpkg -iで対話形式で設定が必要な為テキストファイルから選択値を入力させる
# mysqldefをwgetで取得してgzファイルを解凍(root直下)

RUN apt-get update && \
  apt-get install -y vim \
  lsb-release \
  gnupg \
  wget && \
  wget https://dev.mysql.com/get/mysql-apt-config_0.8.26-1_all.deb && \
  wget -O - https://github.com/k0kubun/sqldef/releases/latest/download/mysqldef_linux_amd64.tar.gz | tar xvz

COPY ./apt.txt /tmp/apt.txt

# 下記のバージョン指定では不可
RUN dpkg -i mysql-apt-config_0.8.26-1_all.deb < /tmp/apt.txt && \
  apt -y update && \
  apt install -y mysql-client
  # apt install -y mysql-client-5.7 \
  # mysql-community-server-5.7 \
  # mysql-server-5.7

WORKDIR /usr/local/src


 CMD ["/bin/bash"]
