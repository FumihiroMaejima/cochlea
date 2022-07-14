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
use App\Library\String\UuidLibrary;
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
     * get stripe chceckout session.
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

        $session = CheckoutLibrary::createSession($lineItems, UuidLibrary::uuidVersion4());

         return response()->json(
            [
                'code' => 200,
                'message' => 'Successfully Create Session',
                'data' => $session->toArray(),
            ]
        );
    }

    /**
     * cancel stripe chceckout session.
     *
     * @param string $orderId order id.
     * @return JsonResponse
     */
    public function cancelCheckout(string $orderId): JsonResponse
    {
         return response()->json(
            [
                'code' => 200,
                'message' => 'Successfully Cancel Create Session. ' . $orderId,
                'data' => [],
            ]
        );
    }

    /**
     * complete stripe chceckout session.
     *
     * @param string $orderId order id.
     * @return JsonResponse
     */
    public function completeCheckout(string $orderId): JsonResponse
    {
         return response()->json(
            [
                'code' => 200,
                'message' => 'Successfully Payment! ' . $orderId,
                'data' => [],
            ]
        );
    }
}
