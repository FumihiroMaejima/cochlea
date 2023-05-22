from locust import HttpUser, task
# ログのパーセンタイルを変更する場合
# import locust.stats
# locust.stats.PERCENTILES_TO_REPORT = [0.95]

# ヘッダー作成用の処理
def createHeader(id: str):
    # 文字列で渡す必要がある
    if id == "":
        headers = { 'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Accept-Encoding': '' }
    else:
        headers = { 'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Accept-Encoding': '',
                    'X-Test-Id': id }
    return headers

class DebugUser(HttpUser):
    # 2〜5秒間隔で各種テストを実行
    min_wait = 2000 # 待ち時間の最小(ms)
    max_wait = 5000 # 待ち時間の最大(ms)

    @task
    def debug_test(self):
        self.client.get("/api/v1/debug/test")
