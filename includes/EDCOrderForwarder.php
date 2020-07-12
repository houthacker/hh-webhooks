<?php
namespace HWH;

use HWH\EDCXmlOrder;
use HWH\HWHException;

class EDCOrderForwarder {

    private $order;

    private $endpoint;

    public function __construct($json_order, $edc_email, $edc_api_key
        , $edc_packing_slip_id, $endpoint) {
        if ($json_order === null) {
            throw new HWHException('Cannot forward empty order');
        } else if ($edc_email === null) {
            throw new HWHException('Cannot forward order: edc_email is required');
        } else if ($edc_api_key === null) {
            throw new HWHException('Cannot forward order: edc_api_key is required');
        } else if ( ! $json_order->date_paid) {
            throw new HWHException('Cannot forward order: no payment information');
        }

        $this->endpoint = $endpoint;
        $this->order = EDCXmlOrder::from_wc_order($json_order, $edc_email
            , $edc_api_key, $edc_packing_slip_id);
    }

    /**
     * @param $forward_type 'local_file' or 'http'
     */
    public function forward($forward_type) {
        switch($forward_type) {
            case 'http':
                $this->forward_to_http(); break;
            case 'local_file':
            default:
                $this->forward_to_local_file(); break;
        } 
    }

    private function forward_to_local_file() {
        try {
            $xml = $this->to_edc_xml();
            $file = WP_PLUGIN_DIR . '/hh-webhooks/exports/' . $this->order->getReceiver()->getOwnOrderNumber() . '.xml';

            $this->set_order_status(
                $this->order->getReceiver()->getOwnOrdernumber(),
                'stored',
                'OK',
                $xml
            );
        } catch (\Exception $e) {
            $this->set_order_status(
                $this->order->getReceiver()->getOwnOrdernumber(),
                'error',
                $e->getmessage(),
                $xml
            );
        }
    }

    private function forward_to_http() {
        $xml = $this->to_edc_xml();

        // get pretty printed xml string
        $dom = \dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        $pretty_xml = $dom->saveXML();

        $post_data = \http_build_query(array('data' => $pretty_xml));
        $options = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $post_data
            )
        );
        $context = \stream_context_create($options);
        $result = \file_get_contents($this->endpoint, false, $context);

        if ($result === false) {
            $this->set_order_status(
                $this->order->getReceiver()->getOwnOrdernumber(),
                'error',
                'Could not send order ro EDC',
                $pretty_xml
            );
            return;
        }

        $json = \json_decode($result, true);
        $status = 'forwarded';
        if (\strcmp('OK', $json['result']) !== 0) {
            $status = 'error';
        }

        $message = $json['errorcode'] . ' - ' . $json['message'];
        $this->set_order_status(
            $this->order->getReceiver()->getOwnordernumber(),
            $status,
            $message,
            $pretty_xml
        );
    }

    private function set_order_status($order_id, $status, $status_message, $xml) {
        global $wpdb;
        $wpdb->replace($wpdb->prefix . 'hwh_orders',
            array(
                'order_id'              => $order_id,
                'order_status'          => $status,
                'order_status_message'  => $status_message,
                'order_xml'             => $xml
            ), 
            array(
                '%d',
                '%s',
                '%s',
                '%s'
            )
        );
    }

    private function to_edc_xml() {
        $xml = new \SimpleXMLElement('<?xml version="1.0"?><orderdetails></orderdetails>');

        $cd = $this->order->getCustomerDetails();
        $customerdetails = $xml->addChild('customerdetails');
        $this->add_child($customerdetails, 'email', $cd->getEmail());
        $this->add_child($customerdetails, 'apikey', $cd->getApiKey());
        $this->add_child($customerdetails, 'output', $cd->getOutputMode());

        $r = $this->order->getReceiver();
        $receiver = $xml->addChild('receiver');
        $this->add_child($receiver, 'name', $r->getName());
        $this->add_child($receiver, 'street', $r->getStreet());
        $this->add_child($receiver, 'house_nr', $r->getHouseNr());
        $this->add_child($receiver, 'house_nr_ext', $r->getHouseNrExt());
        $this->add_child($receiver, 'postalcode', $r->getPostalCode());
        $this->add_child($receiver, 'city', $r->getCity());
        $this->add_child($receiver, 'country', $r->getCountry());
        $this->add_child($receiver, 'extra_email', $r->getExtraEmail());
        $this->add_child($receiver, 'own_ordernumber', $r->getOwnOrdernumber());
        $this->add_child($receiver, 'pakjegemak', $r->getPakjegemak());
        $this->add_child($receiver, 'pakjegemak_consumer_housenr', $r->getPakjegemakConsumerHousenr());
        $this->add_child($receiver, 'pakjegemak_consumer_postalcode', $r->getPakjegemakConsumerPostalcode());
        $this->add_child($receiver, 'bpostpickup', $r->getbPostpickup());
        $this->add_child($receiver, 'cod_amount', $r->getCodAmount());
        $this->add_child($receiver, 'phone', $r->getPhone());
        $this->add_child($receiver, 'pickup', $r->getPickup());
        $this->add_child($receiver, 'processing_date', $r->getProcessingDate());
        $this->add_child($receiver, 'dhl_postid', $r->getDhlPostid());
        $this->add_child($receiver, 'carrier', $r->getCarrier());
        $this->add_child($receiver, 'carrier_service', $r->getCarrierService());
        $this->add_child($receiver, 'packing_slip_id', $r->getPackingSlipId());

        $products = $xml->addChild('products');
        foreach ($this->order->getProducts() as $product) {
            $this->add_child($products, 'artnr', $product);
        }

        return $xml;
    }

    private function add_child($element, $name, $value) {
        if ($value !== null) {
            $element->addChild($name, $value);
        }
    }
}

