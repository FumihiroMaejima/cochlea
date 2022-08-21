<?php

namespace App\Services\Admins;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Http\Requests\Admin\Debug\DebugFileUploadRequest;
use App\Library\Stripe\StripeLibrary;
use App\Library\Time\TimeLibrary;
use App\Library\String\UuidLibrary;

class DebugService
{
    protected string $prop;

    /**
     * create DebugService instance
     *
     * @return void
     */
    public function __construct()
    {
        $this->prop = 'debug propaty';
    }

    /**
     * get permissions data for frontend parts
     *
     * @param  \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function getList(): JsonResponse
    {
        return StripeLibrary::getTestList();
    }
}
