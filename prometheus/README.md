## Grafana


`http://localhost:3100`にアクセス


初回はユーザー名とパスワードに「admin」を入力する。

初回ユーザーパスワードの設定もある。


`http://localhost:3100/datasources/new`にアクセスしてPrometheusのインテグレーションを追加

Prometheusの設定として下記の入力してSave&Testボタンを押下

```shell
# 下記は古い設定っぽい
# http: http://localhost:9090
# Access: Browser

# 下記を設定する(prometheusのコンテナ名)
http: http://prometheus:9090
```

`Dashborads`で下記をimportする

```shell
### 最低限Prometheus 2.0 StatsをインポートすればGUIでメトリクスを確認出来る。
Prometheus Stats
Prometheus 2.0 Stats
Grafana metrics
```


---

## Alertmanager


`http://localhost:9093/#/status`にアクセス

通知先設定が反映されている事を確認する事。

---
## 検証


検証用に`node-exporter`のコンテナを削除する。

```shell
docker container stop node-exporter
```

---

## MySQLコンテナとGrafanaを接続させる方法

リポジトリを分ける場合はまずnetworkを合わせる必要がある。

その値は`prometheus`の設定と同様に`service_name:port`で接続出来る。

ユーザーrootではアクセス出来ない為専用のユーザーを作成する。

```shell
db:3306
```

---

## Grafana Loki

Grafana Lokiについて

```shell

```

---

### LogQLについて

```logql
# jobに該当する全てのログを取得
{job="app-access-logs"} |= ``

# ファイルを指定するには絶対パスを指定する必要がある
{filename="/var/log/app/access-2023-05-04.log"} |= ``

# パターンが決まっているログに対して特定部分をラベルに設定。スペースで区切った5つ目移行を「line」と言うラベルを設定
{job="app-access-logs"} | pattern "<_> <_> <_> <_> <line>"

# 設定したラベルを利用してログのフォーマットを編集
{job="app-access-logs"} | pattern "<_> <_> <_> <_> <line>" | line_format "line is here {{.line}}"
{job="app-access-logs"} | pattern "<_> <_> <_> <_> <line>" | line_format "{{.line}}"

### 不要部分を削除したログをjson形式として扱う。keyをラベルとして扱う様にする。
{job="app-access-logs"} | pattern "<_> <_> <_> <_> <line>" | line_format "{{.line}}" | json

### JSON parseでエラーになるがリクエスト日時を出力できる。
{job="app-access-logs"} | pattern "<log_date> <log_time> <_> <_> <line>" | line_format "{{.log_date}} {{.log_time}} {{.line}}" | json

# メソッドがPOSTのログを取得
{job="app-access-logs"} | pattern "<_> <_> <_> <_> <line>" | line_format "{{.line}}" | json | method="POST"

```

#### ログからメトリクスの生成

```logql
# rate:指定した時間で発生した1秒間の平均値
# sum:全体合計
sum(rate({job="app-access-logs"} | pattern "<_> <_> <_> <_> <line>" | line_format "{{.line}}" | json[1m]))

# ラベルごとの合計
sum(rate({job="app-access-logs"} | pattern "<_> <_> <_> <_> <line>" | line_format "{{.line}}" | json[1m])) by (url)
sum(rate({job="app-access-logs"} | pattern "<_> <_> <_> <_> <line>" | line_format "{{.line}}" | json[1m])) by (method)
sum(rate({job="app-access-logs"} | pattern "<_> <_> <_> <_> <line>" | line_format "{{.line}}" | json[1m])) by (host)
# 平均
avg((rate({job="app-access-logs"} | pattern "<_> <_> <_> <_> <line>" | line_format "{{.line}}" | json[1m])))
avg((rate({job="app-access-logs"} | pattern "<_> <_> <_> <_> <line>" | line_format "{{.line}}" | json[1m]))) by (method)

# ステータスコードごとのカウント
count(rate({job="app-access-logs"} | pattern "<_> <_> <_> <_> <line>" | line_format "{{.line}}" | json[1m])) by (status_code)

# 値が大きいor小さい順、*個まで表示
topk(10, sum(rate({job="app-access-logs"} | pattern "<_> <_> <_> <_> <line>" | line_format "{{.line}}" | json[1m])) by (url))
bottomk(10, sum(rate({job="app-access-logs"} | pattern "<_> <_> <_> <_> <line>" | line_format "{{.line}}" | json[1m])) by (url))
```

---

---
