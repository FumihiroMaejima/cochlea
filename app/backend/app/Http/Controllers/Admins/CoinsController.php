<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admins;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Response\ResponseLibrary;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Coins\CoinCreateRequest;
use App\Http\Requests\Admin\Coins\CoinDeleteRequest;
use App\Http\Requests\Admin\Coins\CoinsImportRequest;
use App\Http\Requests\Admin\Coins\CoinUpdateRequest;
use App\Services\Admins\CoinsService;
use App\Trait\CheckHeaderTrait;

class CoinsController extends Controller
{
    use CheckHeaderTrait;
    private CoinsService $service;

    /**
     * Create a new RolesController instance.
     *
     * @return void
     */
    public function __construct(CoinsService $coinsService)
    {
        $this->middleware('customAuth:api-admins');
        $this->service = $coinsService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function index(Request $request): JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.coins'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        // return $this->service->getCoins();
        return ResponseLibrary::jsonResponse($this->service->getCoins($request));
    }

    /**
     * download a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws MyApplicationHttpException
     */
    public function download(Request $request): BinaryFileResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.coins'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return $this->service->downloadCSV();
    }

    /**
     * download import template for import the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws MyApplicationHttpException
     */
    public function template(Request $request): BinaryFileResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.coins'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return $this->service->downloadTemplate();
    }

    /**
     * import record data by file.
     *
     * @param CoinsImportRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function uploadTemplate(CoinsImportRequest $request): JsonResponse
    {
        // サービスの実行
        $this->service->importTemplate($request->file);
        return ResponseLibrary::jsonResponse(status: StatusCodeMessages::STATUS_201);
    }

    /**
     * creating a new resource.
     *
     * @param  CoinCreateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function create(CoinCreateRequest $request): JsonResponse
    {
        // サービスの実行
        // return $this->service->createCoin($request);
        $this->service->createCoin(
            $request->{CoinCreateRequest::KEY_NAME},
            $request->{CoinCreateRequest::KEY_DETAIL},
            $request->{CoinCreateRequest::KEY_PRICE},
            $request->{CoinCreateRequest::KEY_COST},
            $request->{CoinCreateRequest::KEY_START_AT},
            $request->{CoinCreateRequest::KEY_END_AT},
            $request->{CoinCreateRequest::KEY_IMAGE} ?? ''
        );
        return ResponseLibrary::jsonResponse(status: StatusCodeMessages::STATUS_201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CoinUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function update(CoinUpdateRequest $request, int $id): JsonResponse
    {
        // サービスの実行
        $this->service->updateCoin(
            $id,
            $request->{CoinUpdateRequest::KEY_NAME},
            $request->{CoinUpdateRequest::KEY_DETAIL},
            $request->{CoinUpdateRequest::KEY_PRICE},
            $request->{CoinUpdateRequest::KEY_COST},
            $request->{CoinUpdateRequest::KEY_START_AT},
            $request->{CoinUpdateRequest::KEY_END_AT},
            $request->{CoinUpdateRequest::KEY_IMAGE} ?? ''
        );
        return ResponseLibrary::jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  CoinDeleteRequest  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function destroy(CoinDeleteRequest $request): JsonResponse
    {
        // サービスの実行
        $this->service->deleteCoin($request->{CoinUpdateRequest::KEY_COINS});
        return ResponseLibrary::jsonResponse();
    }
}
