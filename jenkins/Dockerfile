FROM jenkins/jenkins:latest

USER root

### パッケージをインストールするのに最低限必要なパッケージを指定しておく
RUN apt-get update && \
    apt-get install -y \
    unzip \
    sudo

# aws-cli
RUN curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
RUN unzip awscliv2.zip
RUN sudo ./aws/install

USER jenkins

