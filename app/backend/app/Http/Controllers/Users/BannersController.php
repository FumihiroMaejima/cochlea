<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Response\ResponseLibrary;
use App\Http\Controllers\Controller;
use App\Services\Users\BannersService;
use App\Trait\CheckHeaderTrait;

class BannersController extends Controller
{
    use CheckHeaderTrait;
    private BannersService $service;

    /**
     * Create a new controller instance.
     *
     * @param BannersService $service
     * @return void
     */
    public function __construct(BannersService $service)
    {
        $this->middleware('auth:api-users', ['except' => ['index', 'getImage']]);
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // サービスの実行
        // return $this->service->getBanners($request);
        return ResponseLibrary::jsonResponse($this->service->getBanners($request));
    }

    /**
     * Get Banner Image.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return BinaryFileResponse
     * @throws MyApplicationHttpException
     */
    public function getImage(Request $request, string $uuid): BinaryFileResponse
    {
        // バリデーションチェック
        $validator = Validator::make(
            ['uuid' => $uuid],
            [
                'uuid' => ['required','uuid'],
            ]
        );

        if ($validator->fails()) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
            );
        }

        // サービスの実行
        // return $this->service->getImage($request->uuid);
        return ResponseLibrary::fileResponse($this->service->getImage($request->uuid));
    }
}
