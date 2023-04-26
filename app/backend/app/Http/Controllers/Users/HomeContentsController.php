<?php

namespace App\Http\Controllers\Users;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Users\HomeContentsService;
use App\Trait\CheckHeaderTrait;

class HomeContentsController extends Controller
{
    use CheckHeaderTrait;
    private HomeContentsService $service;

    /**
     * Create a new controller instance.
     *
     * @param HomeContentsService $service
     * @return void
     */
    public function __construct(HomeContentsService $service)
    {
        $this->middleware('auth:api-users', ['except' => ['index']]);
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
        return $this->service->getHomeContents($request);
    }
}
