<?php

namespace App\Services\Admins;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use App\Library\Stripe\StripeLibrary;

class DebugService
{
    protected StripeClient $stripe;

    /**
     * create PermissionsService instance
     * @return void
     */
    public function __construct()
    {
        $this->stripe = new StripeClient(Config::get('stripe.apiKey.private'));
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
