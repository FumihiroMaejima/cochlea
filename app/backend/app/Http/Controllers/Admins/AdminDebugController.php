<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Services\Admins\DebugService;
use App\Trait\CheckHeaderTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AdminDebugController extends Controller
{
    use CheckHeaderTrait;
    private DebugService $service;

    /**
     * Create a new AdminDebugController instance.
     *
     * @param DebugService $debugService
     * @return void
     */
    public function __construct(DebugService $debugService)
    {
        $this->service = $debugService;
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
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        // サービスの実行
        return $this->service->getList($request);
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
