<?php
namespace HWH;

use HWH\HWHException;
use HWH\EDCXmlOrder;
use HWH\EDCCustomerDetails;
use HWH\EDCReceiver;

class EDCXmlOrder {

    private $customer_details;

    private $receiver;

    private $products;

    private function __construct($customer_details, $receiver, $products) {
        $this->customer_details = $customer_details;
        $this->receiver = $receiver;
        $this->products = $products;
    }

    public function getCustomerDetails() {
        return $this->customer_details;
    }

    public function getReceiver() {
        return $this->receiver;
    }

    public function getProducts() {
        return $this->products;
    }

    public static function from_wc_order($json_order, $email, $api_key, $packing_slip_id) {
        if (!isset($json_order) || $json_order === null) {
            throw new HWHException('Cannot convert empty object to EDCXmlOrder');
        }

        $customer_details = self::create_customer_details($email, $api_key, 'advanced');
        $receiver = self::create_receiver($json_order, $packing_slip_id);
        $products = self::create_products($json_order);

        return new EDCXmlOrder($customer_details, $receiver, $products);
    }

    private static function create_customer_details($email, $api_key) {
        return new EDCCustomerDetails($email, $api_key, 'advanced');
    }

    private static function create_receiver($json_order, $packing_slip_id) {
        return new EDCOrderReceiver(
            self::extract_name($json_order),
            self::extract_street($json_order),
            self::extract_house_nr($json_order),
            self::extract_house_nr_ext($json_order),
            self::extract_postalcode($json_order),
            self::extract_city($json_order),
            self::extract_country($json_order),
            self::extract_extra_email($json_order),
            self::extract_own_ordernumber($json_order),
            null, // pakjegemak
            null, // pakjegemak_consumer_housenr
            null, // pakjegemak_consumer_postalcode
            null, // bpostpickup
            null, // cod_amount
            self::extract_phone($json_order),
            self::extract_pickup($json_order),
            null, // processing_date (future orders)
            null, // dhl_postid
            null, // carrier, automatically filled by EDC
            null, // carrier_service
            $packing_slip_id
        );
    }

    private static function create_products($json_order) {
        $products = array();

        foreach ($json_order->line_items as $line_item) {
            for ($i = 0; $i < $line_item->quantity; $i++) {
                \array_push($products, $line_item->sku);
            }
        }

        return $products;
    }

    /*** Helper functions for create_receiver  ***/

    private static function extract_name($json_order) {
        return $json_order->shipping->first_name . ' ' . $json_order->shipping->last_name;
    }

    private static function extract_street($json_order) {
        return $json_order->shipping->address_1;
    }

    private static function extract_house_nr($json_order) {
        $raw = $json_order->shipping->address_2;
        $nr = (int)$raw;
        return (string)$nr;
    }

    private static function extract_house_nr_ext($json_order) {
        $raw = $json_order->shipping->address_2;
        $nr = (int)$raw;
        $start_idx = \strlen((string)$nr);
        return \trim(\substr($raw, $start_idx));
    }

    private static function extract_postalcode($json_order) {
        return \str_replace(' ', '', $json_order->shipping->postcode);
    }

    private static function extract_city($json_order) {
        return $json_order->shipping->city;
    }

    private static function extract_country($json_order) {
        switch($json_order->shipping->country) {
            case 'NL':
                return 1;
            default:
                throw new HWHException('Unsupported country: ' . $json_order->shipping->country);
        }
    }

    private static function extract_extra_email($json_order) {
        return $json_order->billing->email;
    }

    private static function extract_own_ordernumber($json_order) {
        return (string)$json_order->id;
    }

    private static function extract_phone($json_order) {
        return $json_order->billing->phone; 
    }

    private static function extract_pickup($json_order) {
        return 'N';
    }

}
