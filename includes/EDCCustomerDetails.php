<?php
namespace HWH;

use HWH\HWHException;

class EDCCustomerDetails {

    /**
     * Email address as registered at EDC
     */
    private $email;

    /**
     * EDC Account API Key
     */
    private $api_key;

    /**
     * 'simple' or 'advanced'
     */
    private $output_mode;

    public function __construct($email, $api_key, $output_mode = 'simple') {
        if ($email === null) {
            throw new HWHException('Cannot create EDCCustomerDetails: no email provided');
        } else if ($api_key === null) { 
            throw new HWHException('Cannot create EDCCustomerDetails: no email provided');
        } else if ($output_mode === null) {
            throw new HWHException('Cannot create EDCCustomerDetails: no email provided');
        }

        $this->email = $email;
        $this->api_key = $api_key;
        $this->output_mode = $output_mode;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getApiKey() {
        return $this->api_key;
    }

    public function getOutputMode() {
        return $this->output_mode;
    }

