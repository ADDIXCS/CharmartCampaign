#!/bin/bash

export PATH=/usr/local/bin:$PATH

echo "yumパッケージを更新します..."
sudo yum update -y

echo "nodejs, npm, ruby, gem, compassをインストールします..."
sudo yum install -y --enablerepo=epel nodejs npm
npm cache clean -f
sudo npm install -g n
sudo n stable
sudo npm install -g grunt-cli
sudo yum -y install ruby ruby-devel rubygems
sudo gem install compass --no-ri --no-rdoc

echo "Apacheの設定を行います..."
sudo cp /vagrant/provision/etc/ssl.conf /etc/httpd/conf.d/ssl.conf

echo "MySQLの設定を行います..."
sudo cp /vagrant/provision/etc/my.cnf /etc/my.cnf

echo "xdebugをインストールします..."
sudo yum install -y php56-php-pecl-xdebug
sudo cp /vagrant/provision/etc/50-xdebug.ini /etc/php.d/50-xdebug.ini

echo "Apacheを再起動します..."
sudo service httpd restart

echo "MySQLを再起動します..."
sudo service mysqld restart

echo "データベースを作成します..."
mysql -uroot -pvagrant -e 'CREATE DATABASE faro_campaign DEFAULT CHARACTER SET utf8'

echo "設定ファイルをコピーします..."
cp --no-clobber /vagrant/provision/Config/* /vagrant/app/Config/

echo "依存パッケージをインストールします..."
cd /vagrant
composer install
npm install --no-bin-links
grunt build

echo "データベースのマイグレーションを行います..."
app/Console/cake Migrations.migration run all

echo "セットアップが完了しました！"

echo "開発を始める前に app/Config/settings.php を確認し、各項目を正しく設定してください"
