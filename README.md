# FARO CAMPAIGN

<https://faro-campaign.com/>

## Getting Started

[Composer](https://getcomposer.org/download/)

```bash
composer install
```

[Vagrant](https://www.vagrantup.com/)
と
[VirtualBox](https://www.virtualbox.org/)
をインストールする.

vagrant-vbguestプラグインをインストールする

```bash
vagrant plugin install vagrant-vbguest
```

Macならvagrant-hostsupdaterプラグインもインストールすると良い

```bash
vagrant plugin install vagrant-hostsupdater
```

boxをダウンロードする（数十分かかるかもしれない）

```bash
vagrant box add addix/centos67amp http://addix:3hgq1EVLarMv45Fw@dev.addix.tokyo/boxes/addix/centos67amp.json
```

boxを起動する。初回はプロビジョニングが実行される（これも数十分かかるかもしれない）

```bash
vagrant up
```

設定ファイル `app/Config/settings.php` を書き換える。

`vagrant-hostsupdater` がインストールされていないなら, hostsファイルに以下の行を追加する:

```
192.168.33.10 local.faro-campaign.com
```

<https://local.faro-campaign.com> で動作する.

ログイン画面 <https://local.faro-campaign.com/admin/login> から

- ユーザ名: `admin`
- パスワード: `adminpass`

でログインできる.

----

## Fabricによるデプロイなどの操作

ローカルマシンへのFabricのインストール, APサーバへのSSH公開鍵の登録は完了しているものとする。

### ステージング環境へのデプロイ

```bash
fab -R staging deploy
```

### 本番環境へのデプロイ

```bash
fab -R production deploy
```

### 本番環境で `git status` を実行

```bash
fab -R production status
```

### ステージング環境で `git checkout master` を実行

```bash
fab -R staging checkout:master
```


