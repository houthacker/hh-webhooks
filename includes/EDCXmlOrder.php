<?php
namespace HWH;

class EDCXmlOrder {

    private function __construct() {
    }

    public static function from_wc_order($order) {
        return new HWH\EDCXmlOrder();
    }
}
