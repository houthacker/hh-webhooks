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

    public function no_orders() {
        __('No orders available.', 'default');
    }

    public function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}hwh_orders";

        return $wpdb->get_var($sql);
    }

    public function get_orders($per_page = 20, $page_number = 1) {
        global $wpdb;

        $per_page = (int)$per_page;
        $page_number = (int)$page_number;

        $sql = "SELECT order_id, order_status, order_status_message FROM {$wpdb->prefix}hwh_orders";
        $sql .= " LIMIT $per_page";
        $sql .= " OFFSET " . ($page_number - 1) * $per_page;

        return $wpdb->get_results($sql, 'ARRAY_A');
    }

    public function column_default($order, $column_name) {
        switch($column_name) {
            case 'order_id':
            case 'order_status':
            case 'order_status_message':
                return $order[$column_name];
            default:
                return \print_r($order, true); // for troubles
        }
    }

    public function get_columns() {
        return [
            'order_id'              => __('Order ID', 'default'),
            'order_status'          => __('Status', 'default'),
            'order_status_message'  => __('Message', 'default')
        ];
    }

    public function get_sortable_columns() {
        return [
            'order_id'      => array('order_id', true),
            'order_status'  => array('order_status', true)
        ];
    }

    /**
     * Handles data query and filter, sorting, and pagination
     */
    public function prepare_items() {
        $this->_column_headers = $this->get_column_info();

        $per_page       = $this->get_items_per_page('orders_per_page', 20);
        $current_page   = $this->get_pagenum();
        $total_items    = $this->record_count();

        $this->set_pagination_args([
            'total_items'   => $total_items,
            'per_page'      => $per_page
        ]);

        $this->items    = $this->get_orders($per_page, $current_page);
    }
}
