<?php

namespace App\Library\Stripe;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\JsonResponse;
use App\Library\Stripe\StripeLibrary;
use Stripe\Checkout\Session;
use Stripe\StripeClient;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\AuthenticationException;
use Stripe\Util\RequestOptions;
use Stripe\StripeObject;

class CheckoutLibrary extends StripeLibrary
{

    // mode
    private const CHECKOUT_MODE_PAYMENT = 'mode'; // Accept one-time payments for cards, iDEAL, and more.
    private const CHECKOUT_MODE_SET_UP = 'setup'; // Save payment details to charge your customers later.
    private const CHECKOUT_MODE_SUBSCRIPTION = 'subscription'; // Use Stripe Billing to set up fixed-price subscriptions.

    // payment method types
    private const PAYMENT_TYPE_CARD = 'card';
    private const PAYMENT_TYPE_ACSS_DEBIT = 'acss_debit';
    private const PAYMENT_TYPE_ALIPAY = 'alipay';
    private const PAYMENT_TYPE_KONBINI = 'konbini';
    private const PAYMENT_TYPE_P24 = 'p24';
    private const PAYMENT_TYPE_PAYNOW = 'paynow';
    private const PAYMENT_TYPE_SEPA_DEBIT = 'sepa_debit';
    private const PAYMENT_TYPE_US_BANK_ACCOUNT = 'us_bank_account';
    private const PAYMENT_TYPE_WECHAT_PAY = 'wechat_pay';

    // request param
    private const REQUEST_KEY_SUCCESS_URL = 'success_url'; // require
    private const REQUEST_KEY_CANCEL_URL = 'cancel_url'; // require
    private const REQUEST_KEY_MODE = 'mode'; // require
    private const REQUEST_KEY_LINE_ITEMS = 'line_items';
    private const REQUEST_KEY_LINE_ITEM_PRICE = 'price';
    private const REQUEST_KEY_LINE_ITEM_QUANTITY = 'quantity';
    private const REQUEST_KEY_CUSTOMER = 'customer';
    private const REQUEST_KEY_CUSTOMER_EMAIL = 'customer_email';
    private const REQUEST_KEY_PAYMENT_METHOD_TYPES = 'payment_method_types';

    // response param
    private const RESPONSE_KEY_ID_LOCAL = 'id'; // 重複を避ける為に`LOCAL`をつけている
    private const RESPONSE_KEY_OBJECT = 'object';
    private const RESPONSE_KEY_AFTER_EXPIRATION = 'after_expiration';
    private const RESPONSE_KEY_ALLOW_PROMOTION_CODES = 'allow_promotion_codes';
    private const RESPONSE_KEY_AMOUNT_SUBTOTAL = 'amount_subtotal';
    private const RESPONSE_KEY_AMMOUNT_TOTAL = 'amount_total';
    private const RESPONSE_KEY_AUTOMATIC_TAX = 'automatic_tax';
    private const RESPONSE_KEY_ENABLED = 'enabled';
    private const RESPONSE_KEY_STATUS = 'status';
    private const RESPONSE_KEY_BILLING_ADDRESS_COLLECTION = 'billing_address_collection';
    private const RESPONSE_KEY_CANCEL_URL = 'cancel_url';
    private const RESPONSE_KEY_CLIENT_REFERENCE_ID = 'client_reference_id';
    private const RESPONSE_KEY_CONSENT = 'consent';
    private const RESPONSE_KEY_CONSENT_COLLECTION = 'consent_collection';
    private const RESPONSE_KEY_CURRENCY = 'currency';
    private const RESPONSE_KEY_CUSTOMER = 'customer';
    private const RESPONSE_KEY_CUSTOMER_CREATION = 'customer_creation';
    private const RESPONSE_KEY_CUSTOMER_DETAILS = 'customer_details';
    private const RESPONSE_KEY_CUSTOMER_EMAIL = 'customer_email';
    private const RESPONSE_KEY_EXPIRES_AT = 'expires_at';
    private const RESPONSE_KEY_LIVEMODE = 'livemode';
    private const RESPONSE_KEY_LOCALE = 'locale';
    private const RESPONSE_KEY_MODE = 'mode';
    private const RESPONSE_KEY_PAYMENT_INTENT= 'payment_intent';
    private const RESPONSE_KEY_PAYMENT_LINK = 'payment_link';
    private const RESPONSE_KEY_PAYMENT_METHOD_OPTIONS = 'payment_method_options';
    private const RESPONSE_KEY_PAYMENT_METHOD_TYPES = 'payment_method_types';
    private const RESPONSE_KEY_PAYMENT_METHOD_TYPES_CARD = 'card';
    private const RESPONSE_KEY_PAYMENT_STATUS = 'payment_status';
    private const RESPONSE_KEY_PHONE_NUMBER_COLLECTION = 'phone_number_collection';
    private const RESPONSE_KEY_PHONE_NUMBER_COLLECTION_ENABLED = 'enabled';
    private const RESPONSE_KEY_RECOVERD_FROM= 'recovered_from';
    private const RESPONSE_KEY_SETUP_INTENT = 'setup_intent';
    private const RESPONSE_KEY_SNIPPING = 'shipping';
    private const RESPONSE_KEY_SNIPPING_ADDRESS_COLLECTION = 'shipping_address_collection';
    private const RESPONSE_KEY_SNIPPING_OPTIONS = 'shipping_options';
    private const RESPONSE_KEY_SNIPPING_RATE = 'shipping_rate';
    private const RESPONSE_KEY_STATUS_LOCAL = 'status'; // 重複を避ける為に`LOCAL`をつけている
    private const RESPONSE_KEY_SUBMIT_TYPE = 'submit_type';
    private const RESPONSE_KEY_SUBSCRIPTION = 'subscription';
    private const RESPONSE_KEY_SUCCESS_URL = 'success_url';
    private const RESPONSE_KEY_TOTAL_DETAILS = 'total_details';
    private const RESPONSE_KEY_URL = 'url';

    /**
     * exec stripe api request for POST
     *
     * @return Session
     */
    public static function createSession(): Session {
        $stripe = self::getStripeClient();

        return $stripe->checkout->sessions->create([
            self::REQUEST_KEY_SUCCESS_URL => 'https://example.com/success',
            self::REQUEST_KEY_CANCEL_URL => 'https://example.com/cancel',
            self::REQUEST_KEY_LINE_ITEMS => [
              [
                self::REQUEST_KEY_LINE_ITEM_PRICE => 600,
                self::REQUEST_KEY_LINE_ITEM_QUANTITY => 2,
              ],
            ],
            self::REQUEST_KEY_MODE => self::CHECKOUT_MODE_PAYMENT,
        ]);
    }
}
