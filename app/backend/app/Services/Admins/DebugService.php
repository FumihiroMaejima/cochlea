<?php

namespace App\Services\Admins;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Library\Stripe\StripeLibrary;

class DebugService
{
    protected string $prop;

    /**
     * create PermissionsService instance
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
