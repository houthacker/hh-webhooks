<?php
namespace HWH;

require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

class OrderList extends \WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular'  => __('Order', 'default'),
            'plural'    => __('Orders', 'default'),
            'ajax'      => false
        ]);
    }
}
