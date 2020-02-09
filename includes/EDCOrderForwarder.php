<?php
namespace HWH;

use HWH\EDCXmlOrder;

class EDCOrderForwarder {

    private $order;

    public function __construct($order) {
        if (!\is_a($order, 'HWH\EDCXmlOrder') {
            throw new \Exception('Cannot forward if order is not an EDCXmlOrder');
        }

    }

    public function forward() {
    }
}

