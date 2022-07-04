<?php

namespace App\Services\Admins;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\AuthenticationException;
use Stripe\Util\RequestOptions;

class DebugService
{
    protected StripeClient $stripe;

    private const REQUEST_METHOD_GET = 'GET';
    private const REQUEST_METHOD_POST = 'POST';

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
        // $this->stripe->request(self::REQUEST_METHOD_GET, '/');
        // TODO 確認したいものからコメントアウトを外す。
        $resource = [
            'getApiBase' => $this->stripe->getApiBase(),
            // 'customers' => $this->stripe->customers,
            // 'api_customers' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/customers', [], []),
            //'api_customers' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/customers', ['limit' => 3], []),
            // 'api_balance' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/balance', [], []),
            // 'api_balance_transactions' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/balance_transactions', [], []),
            // 'api_charges' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/charges', [], []),
            // 'api_disputes' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/disputes', [], []),
            // 'api_events' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/events', [], []),
            // 'api_files' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/files', [], []),
            // 'api_file_links' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/file_links', [], []),
            // 'api_payment_intents' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/payment_intents', [], []),
            // 'api_setup_intents' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/setup_intents', [], []),
            // 'api_setup_attempts' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/setup_attempts', [], []),
            // 'api_payouts' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/payouts', [], []),
            // 'api_payment_methods' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/payment_methods', [], []),
            // 'api_products' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/products', [], []),
            // 'api_prices' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/prices', [], []),
            // 'api_coupons' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/coupons', [], []),
            // 'api_promotion_codes' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/promotion_codes', [], []),
            // 'api_tax_codes' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/tax_codes', [], []),
            // 'api_invoices' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/invoices', [], []),
            // 'api_invoiceitems' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/invoiceitems', [], []),
            'api_subscriptions' => $this->stripe->request(self::REQUEST_METHOD_GET, '/v1/subscriptions', [], []),
        ];

        return response()->json($resource, 200);
    }
}
