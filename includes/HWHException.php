<?php
namespace HWH;

class HWHException extends \Exception {
    
    public function __construct($message, $code = 0, \Exception $cause = null) {
        parent::__construct($message, $code, $cause);
    }    

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
