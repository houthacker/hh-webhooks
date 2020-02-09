<?php
namespace HWH;

use HWH\EDCXmlOrder;
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

class WebHookProcessor {

    private $wc_client;

    public function __construct() {
        $wc_private_key = \get_option('hwh-wc-private');
        $wc_public_key = \get_option('hwh-wc-public');
        $wc_api_host = \get_option('hwh-wc-api-host');

        if ($wc_private_key === null || $wc_public_key === null && $wc_api_host === null) {
            throw new \Exception('Invalid WooCommerce settings');
        }

        $this->wc_client = new Client($wc_api_host,
            $wc_public_key,
            $wc_private_key,
            [ 'version' => 'wc/v3' ]
        );
    }

    public function process($request) {
        $status = 400;
        $message = 'Invalid Request';

        if ($this->validate_request($request) === true) {
            try {
                $params = $request->get_json_params();
                $order_nr = $params['arg'];
                if (\is_numeric($order_nr)) {
                    $order = $wc_client->get('orders/' . $order_nr);
                    $edc_order = EDCXmlOrder::from_wc_order($order);
                    $forwarder = new EDCOrderForwarder($edc_order);
                    $forwarder->forward();

                    $status = 201;
                    $message = 'Created';
                }

                // TODO log cause of HTTP 400
            } catch (Exception $e) {
                // TODO log exception
                $status = 500;
                $message = 'Internal Server Error';
            }
        }

        $response = new \WP_REST_Response(array(
            'status' => $status,
            'response' => $message
        ));
        $response->set_headers([ 'Cache-Control' => 'must-revalidate, no-cache, no-store, private']);
        return $response;
    }

    /**
     * Validates if the request is an instancef WP_REST_Request,
     * and if it contains the header x-wc-webhook-signature.
     *
     * If this header is found, its value must be the SHA256-HMAC
     * of the body, signed with an accepted key.
     */
    private function validate_request($request) {
        if ($request === null || !\is_a($request, 'WP_REST_Request')) {
            // TODO log cause
            return false;
        }

        $action_header = $request->get_header('x-wc-webhook-event');
        if (\strcmp('woocommerce_payment_complete', $action_header) !== 0) {
            // TODO log cause
            return false;
        }

        $signature_header = $request->get_header('x-wc-webhook-signature');

        if ($signature_header !== null) {
            $raw_secrets = \get_option('hwh-accepted-secrets');

            if (isset($raw_secrets) && $raw_secrets !== null) {
                $secrets = explode(PHP_EOL, $raw_secrets);
                foreach ($secrets as $secret) {
                    $trimmed_secret = \trim($secret);
                    $hash = \base64_encode(\hash_hmac('sha_256',
                        $request->get_body(), $trimmed_secret, true));

                    if (\strcmp($signature_header, $hash) === 0) {
                        return true;
                    }
                }
            }
        }

        // TODO log cause
        return false;
    }
}
