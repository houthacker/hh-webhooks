<?php
namespace HWH;

use HWH\EDCXmlOrder;
use HWH\HWHException;

class EDCOrderForwarder {

    private $order;

    public function __construct($json_order, $edc_email, $edc_api_key
        , $edc_packing_slip_id) {
        if ($json_order === null) {
            throw new HWHException('Cannot forward empty order');
        } else if ($edc_email === null) {
            throw new HWHException('Cannot forward order: edc_email is required');
        } else if ($edc_api_key === null) {
            throw new HWHException('Cannot forward order: edc_api_key is required');
        } else if ( ! $json_order->date_paid) {
            throw new HWHException('Cannot forward order: no payment information');
        }

        $this->order = EDCXmlOrder::from_wc_order($json_order, $edc_email
            , $edc_api_key, $edc_packing_slip_id);

    }

    /**
     * @param $forward_type 'local_file' or 'http'
     */
    public function forward($forward_type) {
        switch($forward_type) {
            case 'local_file':
                $this->forward_to_local_file(); break;
            case 'http':
                $this->forward_to_http(); break;
            default:
                return;
        } 
    }

    private function forward_to_local_file() {
        $xml = $this->to_edc_xml();
    }

    private function forward_to_http() {
        $xml = $this->to_edc_xml();
    }

    private function to_edc_xml() {
        return null;
    }
}

