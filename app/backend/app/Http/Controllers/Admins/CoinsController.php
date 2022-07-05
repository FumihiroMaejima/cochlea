<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Admins\CoinsService;
use App\Services\Admins\RolesService;
use App\Http\Requests\Admins\RoleCreateRequest;
use App\Http\Requests\Admins\RoleUpdateRequest;
use App\Http\Requests\Admins\RoleDeleteRequest;
use App\Trait\CheckHeaderTrait;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        $this->middleware('auth:api-admins');
        $this->service = $coinsService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.coins'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->getCoins($request);
    }

    /**
     * download a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function download(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.coins'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->downloadCSV($request);
    }

    /**
     * creating a new resource.
     *
     * @param  RoleCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function create(RoleCreateRequest $request)
    {
        // サービスの実行
        return $this->service->createCoin($request);
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
     * @param  RoleUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleUpdateRequest $request, int $id)
    {
        // サービスの実行
        return $this->service->updateCoin($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  RoleDeleteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(RoleDeleteRequest $request)
    {
        // サービスの実行
        return $this->service->deleteCoin($request);
    }
}
