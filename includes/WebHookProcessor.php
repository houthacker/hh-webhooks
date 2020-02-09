<?php
namespace HWH;

class WebHookProcessor {

    public function process($request) {
        $response = new \WP_REST_Response(array(
            'status' => 200,
            'response' => 'OK'
        ));
        $response->set_headers([ 'Cache-Control' => 'must-revalidate, no-cache, no-store, private']);
        return $response;
    }
}
