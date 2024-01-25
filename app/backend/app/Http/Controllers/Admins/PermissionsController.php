<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admins;

use App\Exceptions\MyApplicationHttpException;
use App\Http\Controllers\Controller;
use App\Library\Message\StatusCodeMessages;
use App\Library\Response\ResponseLibrary;
use App\Services\Admins\PermissionsService;
use App\Trait\CheckHeaderTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class PermissionsController extends Controller
{
    use CheckHeaderTrait;
    private $service;

    /**
     * Create a new MembersController instance.
     *
     * @return void
     */
    public function __construct(PermissionsService $permissionsService)
    {
        $this->middleware('customAuth:api-admins');
        $this->service = $permissionsService;
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
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function list(Request $request): JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.permissions'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return ResponseLibrary::jsonResponse($this->service->getPermissionsAsList($request));
        // return $this->service->getPermissionsAsList($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
