<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

// use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Response\ResponseLibrary;
use App\Http\Controllers\Controller;
use App\Services\Users\CoinsService;

// use App\Trait\CheckHeaderTrait;

class CoinsController extends Controller
{
    private CoinsService $service;

    /**
     * Create a new RolesController instance.
     *
     * @return void
     */
    public function __construct(CoinsService $coinsService)
    {
        $this->middleware('auth:api-users', ['except' => ['index']]);
        $this->service = $coinsService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function index(): JsonResponse
    {
        // サービスの実行
        return ResponseLibrary::jsonResponse($this->service->getCoins());
    }
}
