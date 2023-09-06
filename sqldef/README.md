# Sqldef Memo

Sqldef Memo

[official](https://github.com/k0kubun/sqldef)

---

## 環境

Ubuntuサーバーにsqldefをインストールして同network内のDBコンテナにアクセスしてDB情報を取得する形式。

sqldefのバイナリはroot直下に置かれ、その後移動出来なかった為現状パス指定で実行している。

---

## 現状のDDLのexport

```shell
/mysqldef -h ${DATABASE_HOST} -P ${DATABASE_PORT} -u ${DATABASE_USER} -p ${DATABASE_PASSWORD} ${DATABASE_NAME} --export > ${OUTPUT_FILE}
```

---

## DDLのimport

DDLにカラムを追加した状態で下記の通りコマンドを打てば差分を拾ってカラムを追加する。

```sql
-- 定義をしないテーブルは何も記載しなくて良い
CREATE TABLE `test_1` (
  `user_id` int(10) unsigned NOT NULL COMMENT 'ユーザーID',
  `type` tinyint(3) unsigned NOT NULL COMMENT '種類',
  `count` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '試行回数',
  `code` int(10) unsigned NOT NULL COMMENT 'コード',
  `is_used` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '使用済みか',
  `test_id` int(11) NOT NULL DEFAULT '0' COMMENT 'testID',　-- 追加したカラム
  `expired_at` datetime NOT NULL COMMENT '有効期限日時',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日時',
  `updated_at` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  `deleted_at` datetime DEFAULT NULL COMMENT '削除日時', -- 追加したカラム
  PRIMARY KEY (`user_id`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='test_1';

CREATE TABLE `test_2` (
  `user_id` int(10) unsigned NOT NULL COMMENT 'ユーザーID',
  `test_id` int(11) NOT NULL DEFAULT '0' COMMENT 'testID',　-- 追加したカラム
  `type` tinyint(3) unsigned NOT NULL COMMENT '種類',
  `count` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '試行回数',
  `code` int(10) unsigned NOT NULL COMMENT 'コード',
  `price` int(10) unsigned NOT NULL COMMENT '価格',　-- 追加したカラム
  `is_used` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '使用済みか',
  `expired_at` datetime NOT NULL COMMENT '有効期限日時',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日時',
  `updated_at` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`user_id`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='test_2';

```

```shell
/mysqldef -h ${DATABASE_HOST} -P ${DATABASE_PORT} -u ${DATABASE_USER} -p ${DATABASE_PASSWORD} ${DATABASE_NAME} < ${IMPORT_FILE}


-- Apply --
ALTER TABLE `test_1` ADD COLUMN `test_id` int(11) NOT NULL DEFAULT '0' COMMENT 'testID' AFTER `is_used`;
ALTER TABLE `test_1` ADD COLUMN `deleted_at` datetime DEFAULT null COMMENT '削除日時' AFTER `updated_at`;
ALTER TABLE `test_2` ADD COLUMN `test_id` int(11) NOT NULL DEFAULT '0' COMMENT 'testID' AFTER `user_id`;
ALTER TABLE `test_2` ADD COLUMN `price` int(10) UNSIGNED NOT NULL COMMENT '価格' AFTER `code`;
-- Skipped: DROP TABLE `test_3`;
-- Skipped: DROP TABLE `test_4`;
```

UNIQUE KEYを設定する時も同様

```sql
CREATE TABLE `test_2` (
  `user_id` int(10) unsigned NOT NULL COMMENT 'ユーザーID',
  `test_id` int(11) NOT NULL DEFAULT '0' COMMENT 'testID',　-- 追加したカラム
  `type` tinyint(3) unsigned NOT NULL COMMENT '種類',
  `count` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '試行回数',
  `code` int(10) unsigned NOT NULL COMMENT 'コード',
  `price` int(10) unsigned NOT NULL COMMENT '価格',　-- 追加したカラム
  `is_used` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '使用済みか',
  `expired_at` datetime NOT NULL COMMENT '有効期限日時',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日時',
  `updated_at` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`user_id`,`code`),
  UNIQUE KEY `user_test_user_id_expired_at_unique` (`user_id`,`expired_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='test_2';

```

```shell
/mysqldef -h ${DATABASE_HOST} -P ${DATABASE_PORT} -u ${DATABASE_USER} -p ${DATABASE_PASSWORD} ${DATABASE_NAME} < ${IMPORT_FILE}

-- Apply --
ALTER TABLE `test_2` ADD UNIQUE KEY `user_test_user_id_expired_at_unique` (`user_id`, `expired_at`);
-- Skipped: DROP TABLE `test_1`;
-- Skipped: DROP TABLE `test_3`;
-- Skipped: DROP TABLE `test_4`;
```

UNIQUE KEYを設定する前のDDLを読み込めばUNIQUE KEYを外せる。

カラム追加等も追加前のDDLをimportすれば元に戻せる。

```shell
/mysqldef -h ${DATABASE_HOST} -P ${DATABASE_PORT} -u ${DATABASE_USER} -p ${DATABASE_PASSWORD} ${DATABASE_NAME} < ${IMPORT_FILE}

-- Apply --
-- Skipped: DROP TABLE `test_1`;
ALTER TABLE `test_2` DROP INDEX `user_test_user_id_expired_at_unique`;
-- Skipped: DROP TABLE `test_3`;
-- Skipped: DROP TABLE `test_4`;
```

---
