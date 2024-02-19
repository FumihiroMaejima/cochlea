<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Response\ResponseLibrary;
use App\Http\Controllers\Controller;
use App\Services\Users\EventsService;
use App\Trait\CheckHeaderTrait;

class EventsController extends Controller
{
    use CheckHeaderTrait;
    private EventsService $service;

    /**
     * Create a new controller instance.
     *
     * @param EventsService $service
     * @return void
     */
    public function __construct(EventsService $service)
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
        return ResponseLibrary::jsonResponse($this->service->getEvents($request));
    }
}
