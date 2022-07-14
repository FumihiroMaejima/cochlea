<?php

namespace App\Services\Users;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Stripe\CheckoutLibrary;
use App\Library\String\UnidLibrary;
use App\Library\Time\TimeLibrary;

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
    public function getCheckout(): JsonResponse
    {
        $lineItems = [
            [
              CheckoutLibrary::REQUEST_KEY_LINE_ITEM_NAME => 'test product',
              CheckoutLibrary::REQUEST_KEY_LINE_ITEM_DESCRIPTION => 'test description',
              CheckoutLibrary::REQUEST_KEY_LINE_ITEM_AMOUNT => 600,
              CheckoutLibrary::REQUEST_KEY_LINE_ITEM_CURRENCY => CheckoutLibrary::CURRENCY_TYPE_JPY,
              CheckoutLibrary::REQUEST_KEY_LINE_ITEM_QUANTITY => 2,
            ],
        ];
        $session = CheckoutLibrary::createSession($lineItems);

         return response()->json(
            [
                'code' => 200,
                'message' => 'Successfully Create Session',
                'data' => $session->toArray(),
            ]
        );
    }

    /**
     * get permissions data for frontend parts
     *
     * @param  \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function cancelCheckout(): JsonResponse
    {
         return response()->json(
            [
                'code' => 200,
                'message' => 'Successfully Cancel Create Session',
                'data' => [],
            ]
        );
    }

    /**
     * get permissions data for frontend parts
     *
     * @param  \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function completeCheckout(): JsonResponse
    {
         return response()->json(
            [
                'code' => 200,
                'message' => 'Successfully Payment!',
                'data' => [],
            ]
        );
    }
}
