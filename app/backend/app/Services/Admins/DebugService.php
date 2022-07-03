<?php

namespace App\Services\Admins;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\Admins\Permissions\PermissionsRepositoryInterface;
use App\Http\Resources\Admins\PermissionsResource;
use Stripe\StripeClient;

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
        // $this->stripe->getApiBase();
        $resource = ['getApiBase' => $this->stripe->getApiBase()];

        return response()->json($resource, 200);
    }
}
