from locust import HttpUser, task

class DebugUser(HttpUser):
    # 2〜5秒間隔で各種テストを実行
    min_wait = 2000 # 待ち時間の最小(ms)
    max_wait = 5000 # 待ち時間の最大(ms)

    @task
    def debug_test(self):
        self.client.get("/api/v1/debug/test")
