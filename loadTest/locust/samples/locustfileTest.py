from locust import HttpUser, task
# ログのパーセンタイルを変更する場合
# import locust.stats
# locust.stats.PERCENTILES_TO_REPORT = [0.95]

class HelloWorldUser(HttpUser):
    @task
    def hello_world(self):
        self.client.get("/hello")
        self.client.get("/world")
