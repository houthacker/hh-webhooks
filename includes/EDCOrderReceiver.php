<?php
namespace HWH;

class EDCOrderReceiver {

    private $name;

    private $street;

    private $house_nr;

    private $house_nr_ext;

    private $postalcode;

    private $city;

    private $country;

    private $extra_email;

    private $own_ordernumber;

    private $pakjegemak;

    private $pakjegemak_consumer_housenr;

    private $pakjegemak_consumer_postalcode;

    private $bpostpickup;

    private $cod_amount;

    private $phone;

    private $pickup;

    private $processing_date;

    private $dhl_postid;

    private $carrier;

    private $carrier_service;

    private $packing_slip_id;

    public function __construct($name, $street, $house_nr, $house_nr_ext, $postalcode
        , $city, $country, $extra_email, $own_ordernumber, $pakjegemak
        , $pakjegemak_consumer_housenr, $pakjegemak_consumer_postalcode
        , $bpostpickup, $cod_amount, $phone, $pickup, $processing_date
        , $dhl_postid, $carrier, $carrier_service, $packing_slip_id) {

        $this->name                             = $name;
        $this->street                           = $street;
        $this->house_nr                         = $house_nr;
        $this->house_nr_ext                     = $house_nr_ext;
        $this->postalcode                       = $postalcode;
        $this->city                             = $city;
        $this->country                          = $country;
        $this->extra_email                      = $extra_email;
        $this->own_ordernumber                  = $own_ordernumber;
        $this->pakjegemak                       = $pakjegemak;
        $this->pakjegemak_consumer_housenr      = $pakjegemak_consumer_housenr;
        $this->pakjegemak_consumer_postalcode   = $pakjegemak_consumer_postalcode;
        $this->bpostpickup                      = $bpostpickup;
        $this->cod_amount                       = $cod_amount;
        $this->phone                            = $phone;
        $this->pickup                           = $pickup;
        $this->processing_date                  = $processing_date;
        $this->dhl_postid                       = $dhl_postid;
        $this->carrier                          = $carrier;
        $this->carrier_service                  = $carrier_service;
        $this->packing_slip_id                  = $packing_slip_id;
    }

    public function getName() {
        return $this->name;
    }

    public function getStreet() {
        return $this->street;
    }

    public function getHouseNr() {
        return $this->house_nr;
    }

    public function getHouseNrExt() {
        return this->house_nr_ext;
    }

    public function getPostalcode() {
        return $this->postalcode;
    }

    public function getCity() {
        return $this->city;
    }

    public function getCountry() {
        return $this->country;
    }

    public function getExtraEmail() {
        return $this->extra_email;
    }

    public function getOwnOrdernumber() {
        return $this->own_ordernumber;
    }

    public function getPakjegemak() {
        return $this->pakjegemak;
    }

    public function getPakjegemakConsumerHousenr() {
        return $this->pakjegemak_consumer_housenr;
    }

    public function getPakjegemakConsumerPostalcode() {
        return $this->pakjegemak_consumer_postalcode;
    }

    public function getBpostpickup() {
        return $this->bpostpickup;
    }

    public function getCodAmount() {
        return $this->cod_amount;
    }
    
    public function getPhone() {
        return $this->phone;
    }

    public function getPickup() {
        return $this->pickup;
    }

    public function getProcessingDate() {
        return $this->processing_date;
    }

    public function getDhlPostid() {
        return $this->dhl_postid;
    }

    public function getCarrier() {
        return $this->carrier;
    }

    public function getCarrierService() {
        return $this->carrier_service;
    }

    public function getPackingSlipId() {
        return $this->packing_slip_id;
    }

}
