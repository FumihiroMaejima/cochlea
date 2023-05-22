import logging, json
from locust import HttpUser, task, TaskSet
# ログのパーセンタイルを変更する場合
# import locust.stats
# locust.stats.PERCENTILES_TO_REPORT = [0.95]

# ヘッダー作成用の処理
def createHeader(userId: str, token: str):
    # 文字列で渡す必要がある
    if userId == "":
        headers = { 'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Accept-Encoding': '' }
    else:
        headers = { 'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Accept-Encoding': '',
                    'X-User-ID': userId,
                    'X-Auth-ID': token
                    }
    return headers

# セッション作成
def startSession(self, request):
    path = "/api/v1/auth/login"
    response = self.client.post(path, request)
    return response

def debug_test(self):
    # self.client.get("/api/v1/debug/test")
    self.client.get("/api/v1/debug/test", headers=createHeader(str(self.id), self.sessionToken))

def banners_test(self):
    # self.client.get("/api/v1/banners")
    self.client.get("/api/v1/banners", headers=createHeader(str(self.id), self.sessionToken))

def home_contents_test(self):
    self.client.get("/api/v1/home/contents/list", headers=createHeader(str(self.id), self.sessionToken))

# ログ作成処理(現状locustでloggingが機能しない)
def getLogger():
    # ログの出力名を設定
    # logger = logging.getLogger('LoggingTest')
    logger = logging.getLogger('locust')
    #ログレベルを設定
    logger.setLevel(20)
    # ログのファイル出力先を設定
    fileHandler = logging.FileHandler('/home/locust/log/test.log')
    logger.addHandler(fileHandler)
    return logger

# シナリオ用のクラス(emailとpasswordは適時変更)
class UserBehavior(TaskSet):
    # tasks = {debug_test: 1}
    tasks = {debug_test: 1, banners_test: 1, banners_test: 1, home_contents_test: 1}
    email = 'test@example.com'
    password = 'password';

    # このメソッド名で無いと実行されない
    def on_start(self):
        loginRequestBody = {'email':self.email, 'password': self.password}
        print(loginRequestBody)

        # ログインリクエストを実行してセッション情報を取得
        response = startSession(self, loginRequestBody)
        responseJson = response.json()
        logging.info(response.json())
        self.id = responseJson['user']['id']
        self.sessionToken = responseJson['access_token']

# 最初に呼ばれるクラス(UserBehaviorをtaskとして実行)
class User(HttpUser):
    tasks = {UserBehavior: 1}
    min_wait = 2000
    max_wait = 5000

# class HelloWorldUser(HttpUser):
#     tasks = {UserBehavior: 1}
#     min_wait = 2000
#     max_wait = 5000
#     # @task
#     # def hello_world(self):
#     #     self.client.get("/hello")
#     #     self.client.get("/world")
#
#     @task
#     def debug_test(self):
#         # self.client.get("/hello")
#         # self.client.get("/world")
#         self.client.get("/api/v1/debug/test")
#         self.client.get("/api/v1/debug/phpinfo")
#
#     @task
#     def banners_test(self):
#         self.client.get("/api/v1/banners")
#
#     @task
#     def home_contents_test(self):
#         # self.client.get("/api/v1/home/contents/list")
#         self.client.get("/api/v1/home/contents/list", headers=createHeader("99"))
#         # self.client.get("/api/v1/home/contents/list", {'X-Test-Id': 99})
