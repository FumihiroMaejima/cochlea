<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Database\DatabaseLibrary;
use App\Library\Database\ShardingLibrary;
use App\Library\Database\TableMemoryLibrary;
use App\Library\File\PdfLibrary;
use App\Library\File\QRCodeLibrary;
use App\Library\Encrypt\EncryptLibrary;
use App\Library\JWT\JwtLibrary;
use App\Library\Log\LogLibrary;
use App\Library\Message\StatusCodeMessages;
use App\Library\Time\TimeLibrary;
use App\Http\Controllers\Controller;
use App\Services\Users\DebugService;
use App\Services\Admins\ImagesService;
use App\Trait\CheckHeaderTrait;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DebugController extends Controller
{
    use CheckHeaderTrait;
    private DebugService $service;
    private ImagesService $imagesService;

    /**
     * Create a new AdminDebugController instance.
     *
     * @param DebugService $debugService
     * @param ImagesService $imagesService
     * @return void
     */
    public function __construct(DebugService $debugService, ImagesService $imagesService)
    {
        // 認証が必要なメソッドのみ指定する
        $this->middleware('auth:api-users', ['only' => ['assignCoins']]);
        $this->service = $debugService;
        $this->imagesService = $imagesService;
    }

    /**
     * Display test Debug message.
     *
     * @return JsonResponse
     */
    public function test(): JsonResponse
    {
        return response()->json(['message' => 'test debug message.'], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * デバッグ関連情報取得
     *
     * @param Request $request
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function getDebugStatus(Request $request): JsonResponse
    {
        $userId = self::getUserId($request, true);
        $sessionId = self::getSessionId($request);
        $fakerTimeStamp = self::getFakerTimeStamp($request);
        return $this->service->getDebugStatus(
            $userId,
            $sessionId,
            $fakerTimeStamp,
            $request->getClientIp(),
            $request->userAgent()
        );
    }

    /**
     * Create Stipe Checkout Session.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function checkout(Request $request): JsonResponse
    {
        // サービスの実行
        return $this->service->getCheckout($request);
    }

    /**
     * Cancel Stipe Checkout Session.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function cancelCheckout(Request $request): JsonResponse
    {
        // バリデーションチェック
        $validator = Validator::make(
            $request->all(),
            [
                'orderId' => ['required','uuid'],
            ]
        );

        if ($validator->fails()) {
            // $validator->errors()->toArray();
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
            );
        }

        // サービスの実行
        return $this->service->cancelCheckout($request->orderId);
    }

    /**
     * Complete Stipe Checkout Session.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function completeCheckout(Request $request): JsonResponse
    {
        // バリデーションチェック
        $validator = Validator::make(
            $request->all(),
            [
                'orderId' => ['required','uuid'],
            ]
        );

        if ($validator->fails()) {
            // $validator->errors()->toArray();
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
            );
        }

        // サービスの実行
        return $this->service->completeCheckout($request->orderId);
    }

    /**
     * assign coins.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function assignCoins(Request $request): JsonResponse
    {
        // バリデーションチェック
        $validator = Validator::make(
            $request->all(),
            [
                'freeCoins' => ['int', 'min:1'],
                'paidCoins' => ['int', 'min:1'],
                'limitedTimeCoins' => ['int', 'min:1'],
                'expiredAt' => [
                    'required_with_all:limitedTimeCoins',
                    'date',
                    'date_format:'.TimeLibrary::DEFAULT_DATE_TIME_FORMAT_SLASH,
                    'after:'.TimeLibrary::getCurrentDateTime()
                ],
            ]
        );

        if ($validator->fails()) {
            // $validator->errors()->toArray();
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'validation error.',
                $validator->errors()->toArray()
            );
        }

        // ユーザーIDの取得
        $userId = self::getUserId($request);

        // サービスの実行
        return $this->service->assignCoins(
            $userId,
            $request->freeCoins ?? 0,
            $request->paidCoins ?? 0,
            $request->limitedTimeCoins ?? 0,
            $request->expiredAt ?? null,
        );
    }

    /**
     * Complete Stipe Checkout Session.
     *
     * @return JsonResponse
     */
    public function debugRandomValue(): JsonResponse
    {
        return $this->service->gacha();
    }

    /**
     * Complete Stipe Checkout Session.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkIsEmoji(Request $request): JsonResponse
    {
        return $this->service->checkIsEmoji($request->emoji);
    }

    /**
     * テスト用PDFファイルの表示
     *
     * @param DebugFileUploadRequest $request
     * @return BinaryFileResponse|JsonResponse
     * @throws MyApplicationHttpException
     */
    public function getSamplePDF(): BinaryFileResponse|JsonResponse
    {
        return response()->file(PdfLibrary::getSamplePDF());
    }

    /**
     * テスト用PDFファイルの表示(コイン履歴用)
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid uuid
     * @return BinaryFileResponse
     */
    public function getSampleCoinHistoryDesignPDF(Request $request, string $uuid): BinaryFileResponse
    {
        if ($uuid === '') {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
            );
        }

        // テストユーザーIDの取得
        $userId = 1;

        // サービスの実行
        return $this->service->getCoinHistoryPdfByUuid($userId, $request->uuid);
    }

    /**
     * テスト用QRLコードの表示
     *
     * @param DebugFileUploadRequest $request
     * @return Response
     * @throws MyApplicationHttpException
     */
    public function getSampleQRCode(): Response
    {
        // SVGのQRコードをHTMLとして返却
        return response(QRCodeLibrary::getSampleQrCode());
    }

    /**
     * JWTトークンヘッダーのデコード
     *
     * @param Request $request
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function decodeTokenHeader(Request $request): JsonResponse
    {
        return response()->json(JwtLibrary::decodeTokenHeader($request->tokenHeader ?? ''));
    }

    /**
     * JWTトークンペイロードのデコード
     *
     * @param Request $request
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function decodeTokenPayload(Request $request): JsonResponse
    {
        return response()->json(JwtLibrary::decodeTokenPayload($request->tokenPayload ?? ''));
    }

    /**
     * メールアドレスの暗号化
     *
     * @param Request $request
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function encryptMail(Request $request): JsonResponse
    {
        return response()->json(EncryptLibrary::encrypt($request->email ?? '', false));
    }

    /**
     * メールアドレスの複合化
     *
     * @param Request $request
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function decryptMail(Request $request): JsonResponse
    {
        return response()->json(EncryptLibrary::decrypt($request->email ?? '', false));
    }

    /**
     * 指定された日付からタイムスタンプを取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTimeStampByDateTime(Request $request): JsonResponse
    {
        return response()->json(TimeLibrary::strToTimeStamp($request->datetime ?? ''));
    }

    /**
     * 指定されたタイムスタンプから日付を取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDateTimeByTimeStamp(Request $request): JsonResponse
    {
        return response()->json(TimeLibrary::timeStampToDate($request->timestamp ?? 0));
    }

    /**
     * ログファイルの取得(1日ごと)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDateLog(Request $request): JsonResponse
    {
        return response()->json(
            ['data' => LogLibrary::getLogFileContentAsAssociative($request->date ?? null, $request->name ?? null, $request->sort ?? SORT_ASC)]
        );
    }

    /**
     * テーブル一覧の取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getSchemaList(Request $request): JsonResponse
    {
        return response()->json(
            ['data' => DatabaseLibrary::getSchemaListByConnection($request->connection ?? null)]
        );
    }

    /**
     * DBごとのサイズの取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getSchemaSizeList(Request $request): JsonResponse
    {
        return response()->json(
            ['data' => TableMemoryLibrary::getDatabaseMemories($request->connection ?? 'mysql')]
        );
    }

    /**
     * テーブルごとのサイズの取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTableSizeList(Request $request): JsonResponse
    {
        return response()->json(
            ['data' => TableMemoryLibrary::getTableMemories($request->connection ?? 'mysql')]
        );
    }

    /**
     * テーブル情報の取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTableStatus(Request $request): JsonResponse
    {
        return response()->json(
            ['data' => DatabaseLibrary::getTableStatusByConnection($request->table, $request->connection ?? null)]
        );
    }

    /**
     * シャード番号と対照データベースの取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getShardId(Request $request): JsonResponse
    {
        // ログイン中の設定が無い場合はリクエストデータからも指定出来る様にする
        $sessionUserId = $this->getUserId($request, true);
        if ($sessionUserId === 0) {
            $requstUserId = $request->userId;
        } else {
            $requstUserId = $sessionUserId;
        }

        $shardId = ShardingLibrary::getShardIdByUserId($requstUserId);
        return response()->json(
            [
                'data' => [
                    'shardId' => $shardId,
                    'databaseNumber' => ShardingLibrary::getUserDataBaseConnection($shardId),
                ],
            ]
        );
    }
}
