<?php

namespace App\Library\Stripe;

use Illuminate\Http\Request;
use App\Trait\CheckHeaderTrait;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Stripe\StripeClient;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\AuthenticationException;
use Stripe\Util\RequestOptions;

class StripeLibrary
{
    // request methods
    private const REQUEST_METHOD_GET = 'GET';
    private const REQUEST_METHOD_POST = 'POST';
    private const REQUEST_METHOD_DELETE = 'DELETE';

    // api key config name
    private const CONFIG_KEY_NAME = 'stripe.apiKey.private';

    /**
     * get current date time.
     *
     * @param string $format datetime format
     * @return string
     */
    public static function getCurrentDateTime(string $format = self::REQUEST_METHOD_GET): string
    {
        return Carbon::now()->timezone(Config::get('app.timezone'))->format($format);
    }


    /**
     * get permissions data for frontend parts
     *
     * @param  \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public static function getTestList(): JsonResponse
    {
        $stripe = new StripeClient(Config::get(self::CONFIG_KEY_NAME));
        // $stripe->getApiBase();
        // $stripe->request(self::REQUEST_METHOD_GET, '/');
        // TODO 確認したいものからコメントアウトを外す。
        $resource = [
            'getApiBase' => $stripe->getApiBase(),
            // 'customers' => $stripe->customers,
            // 'api_customers' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/customers', [], []),
            //'api_customers' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/customers', ['limit' => 3], []),
            // 'api_balance' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/balance', [], []),
            // 'api_balance_transactions' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/balance_transactions', [], []),
            // 'api_charges' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/charges', [], []),
            // 'api_disputes' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/disputes', [], []),
            // 'api_events' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/events', [], []),
            // 'api_files' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/files', [], []),
            // 'api_file_links' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/file_links', [], []),
            // 'api_payment_intents' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/payment_intents', [], []),
            // 'api_setup_intents' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/setup_intents', [], []),
            // 'api_setup_attempts' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/setup_attempts', [], []),
            // 'api_payouts' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/payouts', [], []),
            // 'api_payment_methods' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/payment_methods', [], []),
            // 'api_products' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/products', [], []),
            // 'api_prices' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/prices', [], []),
            // 'api_coupons' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/coupons', [], []),
            // 'api_promotion_codes' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/promotion_codes', [], []),
            // 'api_tax_codes' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/tax_codes', [], []),
            // 'api_invoices' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/invoices', [], []),
            // 'api_invoiceitems' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/invoiceitems', [], []),
            // 'api_subscriptions' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/subscriptions', [], []),
            'api_orders' => $stripe->request(self::REQUEST_METHOD_GET, '/v1/orders', [], []),
        ];

        return response()->json($resource, 200);
    }
}
