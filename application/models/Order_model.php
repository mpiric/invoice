<?php
class Order_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function validateOrder()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('table_detail_id', 'table_detail_id', 'required');
        $this->form_validation->set_rules('order_date_time', 'order_date_time', 'required');
        
        // $this->form_validation->set_rules('total_items','total_items','required');
        
        $this->form_validation->set_rules('tax', 'tax', 'required');
        $this->form_validation->set_rules('discount', 'discount', 'required');
        $this->form_validation->set_rules('total_amount', 'total_amount', 'required');
        if ($this->form_validation->run() === FALSE) {
            return false;
        } else {
            return true;
        }
    }
    
    public function set_data()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        $data         = array(
            'branch_id' => $branch_id,
            'table_detail_id' => $this->input->post('table_detail_id'),
            
            // 'order_date_time'=> $this->input->post('order_date_time'),
            // 'total_items'=> $this->input->post('total_items'),
            // 'tax'=> $this->input->post('tax'),
            
            'discount' => $this->input->post('discount'),
            'total_amount' => $this->input->post('total_amount'),
            'customer_name' => $this->input->post('customer_name'),
            'customer_contact' => $this->input->post('customer_contact'),
            'customer_email' => $this->input->post('customer_email'),
            'notes' => $this->input->post('notes'),
            'discount_type' => $this->input->post('discount_type'),
            'discount_amount' => $this->input->post('discount_amount'),
            'payment_type' => $this->input->post('payment_type'),
            'payment_card_number' => $this->input->post('payment_card_number')
        );
        return $data;
    }
    
    public function get_data()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        $branch_type  = $session_data['branch_type'];
        $cond         = "";
        if ($branch_type != 1) {
            $cond = "AND order_detail.branch_id = '" . $branch_id . "' ";
        }
        
        $sql = " SELECT waiter.firstname AS waiter_name, total_amount, order_detail.* FROM order_detail LEFT JOIN waiter ON waiter.waiter_id = order_detail.waiter_id WHERE order_detail.deleted IS NULL " . $cond . " ";
        
        // echo $sql;
        
        $query  = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }
    
    public function get_data_list()
    {
        $date         = date('Y-m-d', strtotime('-2 months'));
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        $branch_type  = $session_data['branch_type'];
        $cond         = "";
        if ($branch_type != 1) {
            $cond .= "AND order_detail.branch_id = '" . $branch_id . "' ";
        }
        
        $cond .= "AND DATE(order_detail.order_date_time) >= '" . $date . "' ";
        $sql = " SELECT waiter.firstname AS waiter_name, total_amount, branch.name AS branch_name, order_detail.*,case when order_type=1 then 'Table order' when order_type=2 then 'Delivery' when order_type=3 then 'Parcel' end as orderType  FROM order_detail LEFT JOIN waiter ON waiter.waiter_id = order_detail.waiter_id LEFT JOIN branch ON branch.branch_id = order_detail.branch_id WHERE order_detail.deleted IS NULL " . $cond . " ";
        
        // echo $sql; die;
        
        $query  = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }
    
    public function get_details_by_id($id)
    {
        
        // left join on branch added for admin print
        
        $query  = $this->db->query(" 
             SELECT waiter.firstname AS waiter_name,b.name AS branch_name, b.address AS branch_address, total_amount, order_detail.*,t.`table_number`,t.`max_capacity`,
            DATE_FORMAT(`order_date_time`,'%d/%m/%Y') AS order_date, DATE_FORMAT(`order_date_time`, '%h:%i %p') AS order_time, order_type 
            FROM order_detail 
            LEFT JOIN waiter ON waiter.waiter_id = order_detail.waiter_id 
            LEFT JOIN `table_detail` t ON (order_detail.`table_detail_id` = t.`table_detail_id`)
            LEFT JOIN branch b ON b.branch_id = order_detail.branch_id 
            WHERE order_detail.deleted IS NULL AND order_detail.order_id = $id");
        $result = $query->row_array();
        return $result;
    }
    
    public function insert_data()
    {
        $data   = $this->set_data();
        $result = $this->db->insert('order_detail', $data);
        return $result;
    }
    
    public function update_data($id)
    {
        
        // echo "model";die;
        
        $data            = $this->set_data();
        $data['updated'] = date("Y-m-d H:i:s");
        $this->db->where('order_id', $id);
        $result = $this->db->update('order_detail', $data);
        return $result;
    }
    
    public function update_order_data_by_id($data, $id)
    {
        $this->db->where('order_id', $id);
        $result = $this->db->update('order_detail', $data);
        return $result;
    }
    
    public function delete($id)
    {
        $data['deleted'] = date("Y-m-d H:i:s");
        $this->db->where('order_id', $id);
        $this->db->update('order_detail');
    }
    
    // get max id
    
    public function getMaxOrderId()
    {
        $result = $this->db->query(" SELECT MAX(`order_id`) AS max_order_id FROM `order_detail` ")->row_array();
        return $result['max_order_id'] + 1;
    }
    
    public function get_next_bill_code()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        $result       = $this->db->query(" SELECT `last_order_id` FROM `branch_order_code` WHERE branch_id='" . $branch_id . "' AND code_type=0 ")->row_array();
        
        // return $result['last_order_id']+1;
        
        return $result['last_order_id'];
    }
    
    public function get_next_complementary_bill_code()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        $result       = $this->db->query(" SELECT `last_order_id` FROM `branch_order_code` WHERE branch_id='" . $branch_id . "' AND code_type=1 ")->row_array();
        return $result['last_order_id'] + 1;
    }
    
    public function order_list_for_dd()
    {
        $this->db->select('order_detail.order_id,order_detail.order_code');
        $this->db->where('order_detail.deleted', null);
        $query  = $this->db->get('order_detail');
        $result = $query->result_array();
        return $result;
    }
    
    public function getDailyincome($id)
    {
        $result = $this->db->query("
            SELECT SUM(order_detail.total_amount) AS daily_income 
            FROM order_detail 
            LEFT JOIN table_detail ON table_detail.table_detail_id = order_detail.table_detail_id
            WHERE order_detail.branch_id = $id AND order_detail.is_print=1 GROUP BY order_detail.branch_id")->row_array();
        return $result['daily_income'];
    }
    
    public function getDailyincomebyBranch($id)
    {
        $result = $this->db->query(" SELECT SUM(order_detail.total_amount) AS daily_income, order_detail.order_date_time FROM order_detail LEFT JOIN table_detail ON table_detail.table_detail_id = order_detail.table_detail_id WHERE order_detail.branch_id = $id AND order_detail.is_print=1 AND date(order_detail.order_date_time) = CURDATE()")->row_array();
        return $result;
    }
    
    public function getMonthlyincome($id)
    {
        $result = $this->db->query("SELECT SUM(order_detail.total_amount) AS monthly_income FROM order_detail LEFT JOIN table_detail ON table_detail.table_detail_id = order_detail.table_detail_id WHERE order_detail.branch_id = $id AND order_detail.is_print=1 AND YEAR(order_detail.order_date_time) = YEAR(CURRENT_DATE()) AND MONTH(order_detail.order_date_time) = MONTH(CURRENT_DATE());")->row_array();
        return $result;
    }
    
    public function getTotalincome($id)
    {
        $result = $this->db->query("SELECT SUM(order_detail.total_amount) AS total_income FROM order_detail LEFT JOIN table_detail ON table_detail.table_detail_id = order_detail.table_detail_id WHERE order_detail.branch_id = $id AND order_detail.is_print=1 ;")->row_array();
        return $result;
    }
    
    public function last_week_sale($id, $date)
    {
        $result = $this->db->query(" SELECT SUM(order_detail.total_amount) AS income, order_detail.order_date_time FROM order_detail LEFT JOIN table_detail ON table_detail.table_detail_id = order_detail.table_detail_id WHERE order_detail.branch_id = $id AND order_detail.is_print=1 AND date(order_detail.order_date_time) = '" . $date . "' ")->row_array();
        return $result;
    }
    
    public function monthly_sales($id, $start_month, $end_month)
    {
        $result = $this->db->query("SELECT SUM(order_detail.total_amount) AS monthly_sales FROM order_detail LEFT JOIN table_detail ON table_detail.table_detail_id = order_detail.table_detail_id WHERE order_detail.branch_id = $id AND order_detail.is_print=1 AND date(order_detail.order_date_time) >= '" . $start_month . "' AND date(order_detail.order_date_time) <= '" . $end_month . "' ")->row_array();
        /*$sql = "SELECT SUM(order_detail.total_amount) AS monthly_sales FROM order_detail LEFT JOIN table_detail ON table_detail.table_detail_id = order_detail.table_detail_id WHERE order_detail.branch_id = $id AND order_detail.is_print=1 AND date(order_detail.order_date_time) >= '".$start_month."' AND date(order_detail.order_date_time) <= '".$end_month."' ";
        $query = $this->db->query( $sql );
        $result = $query->result_array();*/
        return $result;
    }
    
    public function top_selling_items_of_current_month($branch_id)
    {
        $date   = date('Y-m-01'); //first day of current month
        $result = $this->db->query("SELECT * FROM 
                                    ( SELECT ot.product_id, p.name as product_name, p.product_code, SUM(ot.quantity) as qty 
                                    FROM order_detail od 
                                    LEFT JOIN order_items ot ON ot.order_id = od.order_id 
                                    LEFT JOIN product p ON p.product_id = ot.product_id 
                                    WHERE od.is_print = 1 AND od.order_date_time >= '" . $date . "' AND od.branch_id = '" . $branch_id . "' 
                                    GROUP BY ot.product_id ) AS temp ORDER BY qty DESC LIMIT 0,10")->result_array();
        return $result;
    }
    
    public function getDetailsByOrderIdForLive($order_id)
    {
        $rowcount = $this->db->query("SELECT order_id from order_detail_live
                                    WHERE order_id = $order_id ")->num_rows();
        return $rowcount;
    }
    
    public function getAllDetailsByOrderIdForLive($order_id)
    {
        $result = $this->db->query("SELECT * from order_detail_live
                                    WHERE order_id = $order_id ")->row_array();
        return $result;
    }
    
    public function getRowByOrderId($order_id)
    {
        $result = $this->db->query("SELECT * from order_detail
                                    WHERE order_id = $order_id ")->row_array();
        return $result;
    }
    
    public function getDetailsByOrderAndProductForLive($order_id, $product_id)
    {
        $result = $this->db->query("SELECT * from order_items_live
                                    WHERE order_id = $order_id AND product_id = $product_id ")->row_array();
        return $result;
    }
    
    public function insert_into_order_live($post_data)
    {
        if (!empty($post_data['table_id'])) {
            $data = array(
                'order_id' => $post_data['order_id'],
                'table_detail_id' => isset($post_data['table_id']) ? $post_data['table_id'] : null
                
                // 'waiter_id'=> $post_data['waiter_id'],
                // 'order_date_time'=> date("Y-m-d H:i:s"),
                // 'total_items'=> $post_data['total_items'],
                // 'tax'=> $post_data['tax'],
                // 'total_amount'=> $post_data['total_amount'],
                // 'return_amount'=> $post_data['return_amount'],
                
            );
            
            // check before insert
            // if order_id is available in table data
            
            $is_order_id_available = $this->getDetailsByOrderIdForLive($data['order_id']);
            if ($is_order_id_available > 0) {
                
                // update
                // $data['updated']= date("Y-m-d H:i:s");
                
                $this->db->where('order_id', $data['order_id']);
                $result = $this->db->update('order_detail_live', $data);
            } else {
                
                // insert
                // $data['created']= date("Y-m-d H:i:s");
                
                $result = $this->db->insert('order_detail_live', $data);
            }
            
            return $result;
        } else {
            return false;
        }
    }
    
    public function getDetailsById($order_id)
    {
        $rowcount = $this->db->query("SELECT * from order_detail
                                    WHERE order_id = $order_id ")->num_rows();
        return $rowcount;
    }
    
    public function insert_into_order($post_data)
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        
        // find brand_id associated with branch
        
        $this->load->model('branch_model');
        $branch_details = $this->branch_model->branch_details_by_id($branch_id);
        
        // $brand_id = $branch_details['brand_id'];
        // $order_id= $post_data['order_id'];
        
        $waiter_id        = null;
        $table_detail_id  = null;
        $number_of_person = null;
        if (isset($post_data['order_type']) && $post_data['order_type'] != '') {
            $order_type = $post_data['order_type'];
            if ($post_data['order_type'] == 1 || $post_data['order_type'] == "1") {
                $waiter_id        = $post_data['waiter_id'];
                $table_detail_id  = $post_data['table_id'];
                $number_of_person = $post_data['number_of_person'];
            }
            
            if ($post_data['order_type'] == 3 || $post_data['order_type'] == "3") {
                
                // parcel
                
                $waiter_id = $post_data['waiter_id'];
            }
        } else {
            $order_type = 1;
        }
        
        // if($post_data['discount_type']=="1" || $post_data['discount_type']==1)
        // {
        //     // get complementary order code
        //     $post_data['order_code'] = "COMP".($this->get_next_complementary_bill_code());
        // }
        
        $data = array(
            'table_detail_id' => $table_detail_id,
            'waiter_id' => $waiter_id,
            'branch_id' => $branch_id,
            'order_type' => $order_type,
            'number_of_person' => $number_of_person,
            
            // 'order_date_time'=> date("Y-m-d H:i:s"),
            // 'total_items'=> $post_data['total_items'],
            // 'tax'=> $post_data['tax'],
            
            'order_code' => $post_data['order_code'],
            'sub_total' => $post_data['sub_total'],
            'total_amount' => $post_data['total_amount'],
            'round_off_total_amount' => round($post_data['total_amount']),
            'given_amount' => $post_data['given_amount'],
            'return_amount' => $post_data['return_amount'],
            'discount_type' => $post_data['discount_type'],
            'discount_amount' => $post_data['discount_amount'],
            'payment_type' => $post_data['payment_type'],
            'payment_card_number' => $post_data['discount_amount']
        );
        
        // check before insert
        // if order_id is available in table data
        
        if (isset($post_data['order_id']) && $post_data['order_id'] != "") {
            $order_id = $post_data['order_id'];
            
            // update data
            
            $is_order_id_available = $this->getDetailsById($order_id);
            if ($is_order_id_available > 0) {
                
                // update
                
                $data['updated'] = date("Y-m-d H:i:s");
                $this->db->where('order_id', $order_id);
                $result = $this->db->update('order_detail', $data);
                return $order_id;
            }
        } else {
            
            // insert
            
            $data['created'] = date("Y-m-d H:i:s");
            $result          = $this->db->insert('order_detail', $data);
            $order_id        = $this->db->insert_id();
        }
        
        if ($order_id > 0) {
            if ($post_data['order_type'] == 2 || $post_data['order_type'] == "2") {
                
                // insert customer details
                
                $customer_data               = array();
                $customer_data['firstname']  = $post_data['customer_name'];
                $customer_data['contact']    = $post_data['customer_contact'];
                $customer_data['address']    = $post_data['customer_address'];
                $customer_data['order_type'] = $post_data['order_type'];
                $customer_data['order_id']   = $order_id;
                $this->load->model('customer_model');
                $this->customer_model->insert_into_customer($customer_data);
            }
            
            // update branch order code
            // $this->update_branch_code();
            
        }
        
        return $order_id;
        
        // return $result;
        
    }
    
    function update_branch_code($order_code)
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        $this->db->set('last_order_id', $order_code, FALSE);
        $this->db->where('branch_id', $branch_id);
        $result = $this->db->update('branch_order_code');
        return $result;
    }
    
    function decrease_branch_code()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        $this->db->set('last_order_id', 'last_order_id-1', FALSE);
        $this->db->where('branch_id', $branch_id);
        $result = $this->db->update('branch_order_code');
        return $result;
    }
    
    function add_items_order_live($order_id, $post_data)
    {
        $data = array(
            'order_id' => $order_id,
            'product_id' => $post_data['product_id'],
            'price' => $post_data['price'],
            'quantity' => $post_data['quantity']
        );
        
        // check before insert
        // if order_id is available in table data
        
        $order_item_row = $this->getDetailsByOrderAndProductForLive($data['order_id'], $data['product_id']);
        if (!empty($order_item_row)) {
            
            // update
            
            $data['updated']  = date("Y-m-d H:i:s");
            $data['quantity'] = $data['quantity'];
            
            // $this->db->where('order_id', $data['order_id']);
            // $this->db->where('product_id', $data['product_id']);
            
            $this->db->where('order_item_live_id', $order_item_row['order_item_live_id']);
            $result = $this->db->update('order_items_live', $data);
        } else {
            
            // insert
            
            $data['created'] = date("Y-m-d H:i:s");
            $result          = $this->db->insert('order_items_live', $data);
        }
        
        return $result;
    }
    
    public function getDetailsByOrderAndProduct($order_id, $product_id)
    {
        $result = $this->db->query("SELECT * from order_items
                                    WHERE order_id = $order_id AND product_id = $product_id ")->row_array();
        return $result;
    }
    
    function add_items_order($order_id, $post_data)
    {
        if (isset($post_data['print_kot']) && $post_data['print_kot'] != '') {
            $print_kot = $post_data['print_kot'];
        } else {
            $print_kot = 0;
        }
        
        $data = array(
            'order_id' => $order_id,
            'product_id' => $post_data['product_id'],
            'price' => $post_data['price'],
            'quantity' => $post_data['quantity'],
            'print_kot' => $print_kot
        );
        
        // check before insert
        // if order_id is available in table data
        
        $order_item_row = $this->getDetailsByOrderAndProduct($data['order_id'], $data['product_id']);
        if (!empty($order_item_row) && false) {
            
            // update
            
            $data['updated']  = date("Y-m-d H:i:s");
            $data['quantity'] = $data['quantity'];
            
            // $this->db->where('order_id', $data['order_id']);
            // $this->db->where('product_id', $data['product_id']);
            
            $this->db->where('order_item_id', $order_item_row['order_item_id']);
            $result = $this->db->update('order_items', $data);
        } else {
            
            // insert
            
            $data['created'] = date("Y-m-d H:i:s");
            $result          = $this->db->insert('order_items', $data);
        }
        
        // print_r($this->db->last_query());
        
        return $result;
    }
    
    public function update_order_live($post_data)
    {
        $order_id        = $post_data['order_id'];
        $data            = array(
            
            // 'tax'=> $post_data['tax'],
            
            'total_amount' => $post_data['total_amount'],
            'round_off_total_amount' => round($post_data['total_amount']),
            'return_amount' => $post_data['return_amount']
        );
        $data['updated'] = date("Y-m-d H:i:s");
        $this->db->where('order_id', $order_id);
        $result = $this->db->update('order_detail_live', $data);
        return $result;
    }
    
    public function update_order($post_data)
    {
        $order_id        = $post_data['order_id'];
        $data            = array(
            
            // 'tax'=> $post_data['tax'],
            
            'total_amount' => $post_data['total_amount'],
            'round_off_total_amount' => round($post_data['total_amount']),
            'return_amount' => $post_data['return_amount']
        );
        $data['updated'] = date("Y-m-d H:i:s");
        $this->db->where('order_id', $order_id);
        $result = $this->db->update('order_detail', $data);
        return $result;
    }
    
    public function update_order_items_live($post_data)
    {
        $product_id      = $post_data['product_id'];
        $order_id        = $post_data['order_id'];
        $data            = array(
            'quantity' => $post_data['quantity']
        );
        $data['updated'] = date("Y-m-d H:i:s");
        $this->db->where('order_id', $order_id);
        $this->db->where('product_id', $product_id);
        $result = $this->db->update('order_items_live', $data);
        return $result;
    }
    
    public function update_order_items($post_data)
    {
        $product_id      = $post_data['product_id'];
        $order_id        = $post_data['order_id'];
        $data            = array(
            'quantity' => $post_data['quantity']
        );
        $data['updated'] = date("Y-m-d H:i:s");
        $this->db->where('order_id', $order_id);
        $this->db->where('product_id', $product_id);
        if (isset($post_data['order_item_id'])) {
            $this->db->where('order_item_id', $post_data['order_item_id']);
        }
        
        $result = $this->db->update('order_items', $data);
        return $result;
    }
    
    public function delete_order_items_live($order_items_data, $tax, $given_amount)
    {
        $product_id = $order_items_data['product_id'];
        $order_id   = $order_items_data['order_id'];
        $this->db->where('order_id', $order_id);
        $this->db->where('product_id', $product_id);
        $query              = $this->db->get('order_items_live');
        $order_item_details = $query->row_array();
        
        // echo '<pre>';print_r($order_item_details);
        
        if (!empty($order_item_details)) {
            $price    = $order_item_details['price'];
            $quantity = $order_item_details['quantity'];
            
            // get details by order_id
            
            $order_details = $this->getAllDetailsByOrderIdForLive($order_id);
            
            // echo '<pre>';print_r($order_details);
            
            $total_amount  = $order_details['total_amount'] - (($price * $quantity) + ((($price * $quantity) * $tax) / 100));
            $return_amount = ($given_amount == null ? 0 : $given_amount) - $total_amount;
            number_format(abs($total_amount), 2);
            $data = array(
                
                // 'total_items'=> $order_details['total_items']-1,
                // 'tax'=> number_format(abs($order_details['tax']-$tax),2),
                
                'total_amount' => $total_amount,
                'return_amount' => $return_amount
            );
            
            // echo '<pre>';print_r($data);die;
            
            $data['updated'] = date("Y-m-d H:i:s");
            $this->db->where('order_id', $order_id);
            $order_result = $this->db->update('order_detail_live', $data);
            if ($order_result == true) {
                $this->db->where('order_id', $order_id);
                $this->db->where('product_id', $product_id);
                $result = $this->db->delete('order_items_live');
                return $result;
            }
        }
        
        return false;
    }
    
    public function delete_order_items($order_items_data, $tax, $given_amount)
    {
        $product_id = $order_items_data['product_id'];
        $order_id   = $order_items_data['order_id'];
        
        // $order_item_id = $order_items_data['order_item_id'];
        
        $this->db->where('order_id', $order_id);
        $this->db->where('product_id', $product_id);
        
        // $this->db->where('order_item_id', $order_item_id);
        
        $query              = $this->db->get('order_items');
        $order_item_details = $query->row_array();
        
        // echo '<pre>Order item d:';print_r($order_item_details);
        
        if (!empty($order_item_details)) {
            $price    = $order_item_details['price'];
            $quantity = $order_item_details['quantity'];
            
            // get details by order_id
            
            $order_details = $this->getRowByOrderId($order_id);
            
            // echo '<pre>';print_r($order_details);
            
            $sub_total_amount = (($price * $quantity) + ((($price * $quantity) * $tax) / 100));
            
            // subtract branch specific tax too
            
            $this->load->model('tax_model');
            $branch_specific_taxes = $this->tax_model->branch_specific_tax_list();
            
            // echo '<pre>';print_r($branch_specific_taxes);die;
            
            $bs_tx_total_percent = 0;
            if (!empty($branch_specific_taxes)) {
                foreach ($branch_specific_taxes as $bstx) {
                    $bs_tx_total_percent += $bstx['tax_percent'];
                }
            }
            
            // count percentage of subtotal
            
            $sub_total_amount += ($sub_total_amount * $bs_tx_total_percent) / 100;
            $total_amount  = $order_details['total_amount'] - $sub_total_amount;
            $return_amount = ($given_amount == null ? 0 : $given_amount) - $total_amount;
            number_format(abs($total_amount), 2);
            $data = array(
                
                // 'total_items'=> $order_details['total_items']-1,
                // 'tax'=> number_format(abs($order_details['tax']-$tax),2),
                
                'total_amount' => $total_amount,
                'return_amount' => $return_amount
            );
            
            // echo '<pre>';print_r($data);die;
            
            $data['updated'] = date("Y-m-d H:i:s");
            $this->db->where('order_id', $order_id);
            $order_result = $this->db->update('order_detail', $data);
            if ($order_result == true) {
                $this->db->where('order_id', $order_id);
                $this->db->where('product_id', $product_id);
                
                // $this->db->where('order_item_id', $order_item_id);
                
                $result = $this->db->delete('order_items');
                return $result;
            }
        }
        
        return false;
    }
    
    public function get_order_details_by_id_live($id)
    {
        $query  = $this->db->get_where('order_detail_live', array(
            'order_id' => $id
        ));
        $result = $query->row_array();
        return $result;
    }
    
    public function insert_order($live_data)
    {
        
        // echo '<pre>';print_r($live_data);
        
        $session_data    = $this->session->userdata('logged_in');
        $branch_id       = $session_data['branch_id'];
        $data            = array(
            
            // 'order_id'=> $live_data['order_id'],
            
            'table_detail_id' => $live_data['table_detail_id'],
            'waiter_id' => $live_data['waiter_id'],
            'branch_id' => $branch_id,
            
            // 'order_date_time'=> date("Y-m-d H:i:s"),
            // 'total_items'=> $live_data['total_items'],
            // 'tax'=> $live_data['tax'],
            
            'total_amount' => $live_data['total_amount'],
            'return_amount' => $live_data['return_amount']
        );
        $data['created'] = date("Y-m-d H:i:s");
        $result          = $this->db->insert('order_detail', $data);
        return $result;
    }
    
    public function get_order_items_by_id_live($id)
    {
        $query  = $this->db->get_where('order_items_live', array(
            'order_id' => $id
        ));
        $result = $query->result_array();
        return $result;
    }
    
    public function insert_order_items($items)
    {
        $data      = array(
            'order_id' => $items['order_id'],
            'product_id' => $items['product_id'],
            'price' => $items['price'],
            'quantity' => $items['quantity'],
            'created' => date("Y-m-d H:i:s")
        );
        $result    = $this->db->insert('order_items', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    
    public function get_all_data_order_items_live($order_id)
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        
        // get tax details
        
        $this->load->model('tax_model');
        $tax_list = $this->tax_model->get_tax_by_branch();
        $tax_1    = $tax_list[0]['tax_id'];
        $tax_2    = $tax_list[1]['tax_id'];
        $this->db->select('oil.*,p.*
                            ,( SELECT (`tax_master_id`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = ' . $tax_1 . ' limit 1) AS service_tax_id 
                            ,( SELECT (`tax_master_id`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = ' . $tax_2 . ' limit 1) AS other_tax_id 
                            ,( SELECT (`tax_percent`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = ' . $tax_1 . ' limit 1 ) AS service_tax_percent 
                            ,( SELECT (`tax_percent`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = ' . $tax_2 . ' limit 1) AS other_tax_percent  ');
        $this->db->join('product p', 'p.`product_id` = oil.`product_id`', 'left');
        $this->db->join('branch_products bp', 'bp.`product_id` = p.`product_id`', 'left');
        $this->db->join('product_category pc', 'p.`product_category_id` = pc.`product_category_id`', 'left');
        $this->db->join('tax_master tm', 'pc.`product_category_id` = tm.`product_category_id`', 'left');
        $this->db->where('p.deleted', null);
        $this->db->where('bp.branch_id', $branch_id);
        $this->db->where('oil.order_id', $order_id);
        $this->db->group_by('p.product_id');
        $query = $this->db->get('order_items_live oil');
        
        // $str = $this->db->last_query();
        // echo '<pre>';print_r($str);
        
        $result = $query->result_array();
        return $result;
    }
    
    public function get_all_data_order_items($order_id)
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        
        // get tax details
        
        $this->load->model('tax_model');
        $tax_list    = $this->tax_model->get_tax_by_branch();
        $select_tax1 = '';
        $select_tax2 = '';
        if (!empty($tax_list)) {
            if (isset($tax_list[0]['tax_id']) && $tax_list[0]['tax_id'] != '') {
                $tax_1       = $tax_list[0]['tax_id'];
                $select_tax1 = ',( SELECT (`tax_master_id`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = ' . $tax_1 . ' limit 1) AS service_tax_id
            ,( SELECT (`tax_percent`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = ' . $tax_1 . ' limit 1 ) AS service_tax_percent  ';
            }
            
            if (isset($tax_list[1]['tax_id']) && $tax_list[1]['tax_id'] != '') {
                $tax_2       = $tax_list[1]['tax_id'];
                $select_tax2 = ',( SELECT (`tax_master_id`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = ' . $tax_2 . ' limit 1) AS other_tax_id                             
                            ,( SELECT (`tax_percent`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = ' . $tax_2 . ' limit 1) AS other_tax_percent';
            }
        }
        
        $this->db->select('oi.*,p.*,bp.product_price as price ' . $select_tax1 . $select_tax2 . ' ');
        $this->db->join('product p', 'p.`product_id` = oi.`product_id`', 'left');
        $this->db->join('branch_products bp', 'bp.`product_id` = p.`product_id`', 'left');
        $this->db->join('product_category pc', 'p.`product_category_id` = pc.`product_category_id`', 'left');
        $this->db->join('tax_master tm', 'pc.`product_category_id` = tm.`product_category_id`', 'left');
        $this->db->where('p.deleted', null);
        $this->db->where('bp.branch_id', $branch_id);
        $this->db->where('oi.order_id', $order_id);
        
        // $this->db->group_by('p.product_id');
        
        $query = $this->db->get('order_items oi');
        
        // $str = $this->db->last_query();
        // echo '<pre>';print_r($str);
        
        $result = $query->result_array();
        return $result;
    }
    
    function update_order_on_change_of_given_amount($order_id, $data)
    {
        $data['updated'] = date("Y-m-d H:i:s");
        $this->db->where('order_id', $order_id);
        $result = $this->db->update('order_detail', $data);
        return $result;
    }
    
    function delete_order_from_live_tbl($order_id)
    {
        $this->db->where('order_id', $order_id);
        $result = $this->db->delete('order_detail_live');
        return $result;
    }
    
    function reset_order($order_id)
    {
        $this->db->where('order_id', $order_id);
        $result = $this->db->delete('order_detail');
        $this->reset_order_from_live_tbl($order_id);
        $this->decrease_branch_code();
        return $result;
    }
    
    function reset_order_from_live_tbl($order_id)
    {
        
        // delete record from order_detail_live table too
        
        $this->db->where('order_id', $order_id);
        $result = $this->db->delete('order_detail_live');
        return $result;
    }
    
    function reset_order_items($order_id)
    {
        $this->db->where('order_id', $order_id);
        $result = $this->db->delete('order_items');
        return $result;
    }
    
    public function recent_orders()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        
        //       $this->db->select('od.order_id,od.order_code,od.total_amount,od.sub_total,od.discount,td.table_number');
        //       $this->db->join('table_detail td', 'od.`table_detail_id` = td.`table_detail_id`','left');
        //       $this->db->where('td.branch_id',$branch_id);
        //        $this->db->where('od.is_print','1');
        //       $this->db->order_by('od.order_id','desc');
        // $this->db->limit(200);
        //       $query = $this->db->get('order_detail od');
        
        $query  = $this->db->query("SELECT `od`.`order_id`, `od`.`order_code`, ROUND(`od`.`total_amount`) AS total_amount, `od`.`sub_total`, `td`.`table_number`, ( (SELECT SUM(quantity * price) FROM order_items WHERE order_id = `od`.`order_id`) * `od`.`discount_amount` / 100 ) AS discount FROM `order_detail` `od` LEFT JOIN `table_detail` `td` ON od.`table_detail_id` = td.`table_detail_id` WHERE `td`.`branch_id` = '" . $branch_id . "' AND `od`.`is_print` = '1' ORDER BY `od`.`order_code` DESC LIMIT 20 ");
        $result = $query->result_array();
        return $result;
        
        // $sql = " SELECT `od`.`order_id`, `od`.`order_code`, ROUND(`od`.`total_amount`) AS total_amount, `od`.`sub_total`, `td`.`table_number`, ( (SELECT SUM(quantity * price) FROM order_items WHERE order_id = `od`.`order_id`) * `od`.`discount_amount` / 100 ) AS discount FROM `order_detail` `od` LEFT JOIN `table_detail` `td` ON od.`table_detail_id` = td.`table_detail_id` WHERE `td`.`branch_id` = '".$branch_id."' AND `od`.`is_print` = '1' ORDER BY `od`.`order_id` DESC LIMIT 200  ";
        // $query = $this->db->query($sql);
        // $str = $this->db->last_query();
        //  echo $str; die;
        
    }
    
    function update_order_discount($order_id, $data)
    {
        $data['updated'] = date("Y-m-d H:i:s");
        $this->db->where('order_id', $order_id);
        $result = $this->db->update('order_detail', $data);
        return $result;
    }
    
    function get_order_tax_by_order_id_and_tax_id($order_id, $tax_id)
    {
        $this->db->select('*');
        $this->db->where('order_id', $order_id);
        $this->db->where('tax_id', $tax_id);
        $query  = $this->db->get('order_tax');
        $result = $query->row_array();
        return $result;
    }
    
    function add_order_tax($data)
    {
        
        // check if the order tax already exists
        
        $order_tax_details = $this->get_order_tax_by_order_id_and_tax_id($data['order_id'], $data['tax_id']);
        if (!empty($order_tax_details)) {
            
            // $data['tax_percent'] = $data['tax_percent']+$order_tax_details['tax_percent'];
            // $this->db->set('tax_percent', $data['tax_percent']);
            // $this->db->where('order_id', $data['order_id']);
            // $this->db->where('tax_id', $data['tax_id']);
            // $result = $this->db->update('order_tax');
            
        } else {
            $result = $this->db->insert('order_tax', $data);
        }
        
        // $str = $this->db->last_query();
        // echo $str; die;
        
        return $result;
    }
    
    function get_items_by_order_id($order_id)
    {
        /*$this->db->select('order_items.*,p.name as product_name');
        $this->db->where('order_id', $order_id);
        $this->db->join('product p', 'p.`product_id` = order_items.`product_id`');
        $query = $this->db->get('order_items');*/
        $sql    = "SELECT SUM(o.quantity) as quantity, o.price, p.name as product_name 
                FROM order_items o 
                LEFT JOIN product p ON p.product_id = o.product_id
                WHERE o.order_id = '" . $order_id . "'  GROUP BY p.product_id";
        $query  = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }
    
    public function waiter_report_all()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        if ($session_data['branch_type'] == 1) {
            $query = $this->db->query(" SELECT o.* ,
                                    (SELECT (SUM(bp.waiter_commission_branch) * oi.quantity) FROM order_items oi
                                    LEFT JOIN branch_products bp ON (oi.product_id = bp.product_id)
                                    WHERE oi.order_id=o.order_id)
                                    AS waiter_commision
                                    FROM order_detail o WHERE o.is_print=1 ");
        } else {
            $query = $this->db->query(" SELECT o.* ,
                                    (SELECT (SUM(bp.waiter_commission_branch) * oi.quantity) FROM order_items oi
                                    LEFT JOIN branch_products bp ON (oi.product_id = bp.product_id)
                                    WHERE oi.order_id=o.order_id)
                                    AS waiter_commision
                                    FROM order_detail o
                                    WHERE o.branch_id='" . $branch_id . "' AND o.is_print=1");
        }
        
        $result = $query->result_array();
        return $result;
    }
    
    public function waiter_report_by_waiter($waiter_id)
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        $query        = $this->db->query(" SELECT o.* ,(SELECT (SUM(bp.waiter_commission_branch) * oi.quantity) FROM order_items oi LEFT JOIN branch_products bp ON (oi.product_id = bp.product_id) WHERE oi.order_id=o.order_id) AS waiter_commision 
            FROM order_detail o 
            WHERE o.branch_id= $branch_id 
            AND o.waiter_id=$waiter_id AND o.is_print=1 ");
        $result       = $query->result_array();
        return $result;
    }
    
    public function waiter_report($branch_id, $waiter_id, $fromdate, $todate)
    {
        
        // $select = 'SELECT o.* ,
        //             (SELECT (SUM(bp.waiter_commission_branch) * oi.quantity) FROM order_items oi
        //             LEFT JOIN branch_products bp ON (oi.product_id = bp.product_id)
        //             WHERE oi.order_id=o.order_id)
        //             AS waiter_commision
        //             FROM order_detail o
        //             WHERE o.branch_id= "'.$branch_id.'" ';
        // if($waiter_id!='')
        // {
        //     $select .= ' AND o.waiter_id = "'.$waiter_id.'" ';
        // }
        // if($fromdate!='' && $todate!='')
        // {
        //     $select .= ' AND (o.order_date_time BETWEEN "'.$fromdate.'" AND "'.$todate.'") ';
        // }
        // else if($fromdate!="" && $todate=="")
        // {
        //     $select .= ' AND (o.order_date_time >="'.$fromdate.'" ) ';
        // }
        // else if($todate!="" && $fromdate=="")
        // {
        //     $select .= ' AND (o.order_date_time <="'.$todate.'" ) ';
        // }
        // $query = $this->db->query( $select );
        // // $str = $this->db->last_query();
        // // echo $str;die;
        // $result = $query->result_array();
        // return $result;
        
        $condition   = '';
        $query       = '';
        $condition[] = 'o.is_print=1';
        if ($branch_id != '') {
            $condition[] = ' o.branch_id = "' . $branch_id . '" ';
        } else {
            $session_data = $this->session->userdata('logged_in');
            $branch_id    = $session_data['branch_id'];
            if ($session_data['branch_type'] != 1) {
                $condition[] = ' o.branch_id = "' . $session_data['branch_id'] . '" ';
            }
        }
        
        if ($waiter_id != '') {
            $condition[] = ' o.waiter_id = "' . $waiter_id . '" ';
        }
        
        if ($fromdate != '' && $todate != '') {
            $condition[] = ' (o.order_date_time BETWEEN "' . $fromdate . '" AND "' . $todate . '") ';
        } else if ($fromdate != "" && $todate == "") {
            $condition[] = ' (o.order_date_time >="' . $fromdate . '" ) ';
        } else if ($todate != "" && $fromdate == "") {
            $condition[] = ' (o.order_date_time <="' . $todate . '" ) ';
        }
        
        // $condition[] = 'ABS(o.order_code) desc';
        
        if (!empty($condition)) {
            $query = implode(' AND ', $condition);
        }
        
        $where_qry = '';
        if ($query != '') {
            $where_qry = ' WHERE ' . $query;
        }
        
        $select = 'SELECT o.* ,
                   (SELECT SUM((bp.waiter_commission_branch) * oi.quantity) FROM order_items oi
                     LEFT JOIN branch_products bp ON (oi.product_id = bp.product_id) AND bp.branch_id="' . $branch_id . '"
                     WHERE oi.order_id=o.order_id) AS waiter_commision FROM order_detail o
                      ' . $where_qry . ' ORDER BY ABS(o.order_code) desc';
        $query  = $this->db->query($select);
        
        // $str = $this->db->last_query();
        // echo $str;die;
        
        $result = $query->result_array();
        return $result;
    }
    
    public function sales_report_all()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        
        // (select sum(otx.tax_percent) from order_detail sod left join order_tax otx on (sod.order_id = otx.order_id) where sod.order_id=o.order_id ) as totalTax,(o.sub_total * o.discount_amount/100) as discount,
        
        if ($session_data['branch_type'] == 1) {
            $query = $this->db->query(' SELECT  CASE 
                    WHEN (ROUND(o.total_amount)) < o.total_amount 
                           THEN ROUND(o.total_amount) - o.total_amount
                    WHEN (ROUND(o.total_amount)) > o.total_amount
                           THEN Concat("+", ROUND(o.total_amount) - o.total_amount) 
                    WHEN (ROUND(o.total_amount)) = o.total_amount
                           THEN  ROUND(o.total_amount) - o.total_amount
                       end  AS roundoff_value,o.order_id,o.total_amount as bill_amount,  
                ROUND(o.total_amount) as roundoff,case when order_type=1 then "Table order" when order_type=2 then "Delivery" when order_type=3 then "Parcel" end as orderType,CASE WHEN payment_type = 1 THEN "Cash" WHEN payment_type = 2 THEN "Credit Card" WHEN payment_type = 3 THEN "Debit Card" END AS paymentType,DATE_FORMAT(o.created, "%d-%m-%Y %H:%i") AS created ,b.brand_id,
                (SELECT sum(quantity*price) from order_items where order_id=o.order_id) as sub_total, (SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id AND
                ( o.order_type=2 )) AS tax_free , ((SELECT sum(quantity*price) from order_items where order_id=o.order_id) * o.discount_amount/100) as discount,o.sub_total,o.order_code, o.number_of_person, (SELECT table_number FROM table_detail WHERE table_detail_id = o.table_detail_id) AS table_no
                FROM order_detail o
                left join branch b on b.branch_id = o.branch_id
                WHERE o.is_print=1
                order by ABS(o.order_code) desc limit 50');
            
            // (SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id AND ( o.order_type=2 OR o.order_type=3 )) AS tax_free ,
            
        } else {
            $query = $this->db->query(' SELECT  CASE 
                    WHEN (ROUND(o.total_amount)) < o.total_amount 
                           THEN ROUND(o.total_amount) - o.total_amount
                    WHEN (ROUND(o.total_amount)) > o.total_amount
                           THEN Concat("+", ROUND(o.total_amount) - o.total_amount) 
                    WHEN (ROUND(o.total_amount)) = o.total_amount
                           THEN  ROUND(o.total_amount) - o.total_amount
                       end  AS roundoff_value,o.order_id,o.total_amount as bill_amount,  
                ROUND(o.total_amount) as roundoff,case when order_type=1 then "Table order" when order_type=2 then "Delivery" when order_type=3 then "Parcel" end as orderType,CASE WHEN payment_type = 1 THEN "Cash" WHEN payment_type = 2 THEN "Credit Card" WHEN payment_type = 3 THEN "Debit Card" END AS paymentType,DATE_FORMAT(o.created, "%d-%m-%Y %H:%i") AS created,b.brand_id,
                (SELECT sum(quantity*price) from order_items where order_id=o.order_id) as sub_total, (SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id AND ( o.order_type=2 )) AS tax_free , ((SELECT sum(quantity*price) from order_items where order_id=o.order_id) * o.discount_amount/100) as discount,o.sub_total,o.order_code, o.number_of_person, (SELECT table_number FROM table_detail WHERE table_detail_id = o.table_detail_id) AS table_no
                FROM order_detail o
                left join branch b on b.branch_id = o.branch_id 
                where o.branch_id= "' . $branch_id . '" AND o.is_print=1
                order by ABS(o.order_code) desc limit 50');
            
            // (SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id AND ( o.order_type=2 OR o.order_type=3 )) AS tax_free
            
        }
        
        $result = $query->result_array();
        
        // $str = $this->db->last_query();
        //  echo $str; die;
        
        return $result;
    }
    
    public function sales_report($branch_id, $fromdate, $todate)
    {
        
        // $session_data = $this->session->userdata('logged_in');
        // $branch_id = $session_data['branch_id'];
        
        $select_cond = '';
        if ($branch_id != '') {
            $select_cond .= ' AND b.branch_id = "' . $branch_id . '" ';
        } else {
            $session_data = $this->session->userdata('logged_in');
            if ($session_data['branch_type'] != 1) {
                $branch_id = $session_data['branch_id'];
                $select_cond .= ' AND b.branch_id = "' . $session_data['branch_id'] . '" ';
            }
        }
        
        $select = 'SELECT  CASE  
                    WHEN (ROUND(o.total_amount)) < o.total_amount 
                           THEN ROUND(o.total_amount) - o.total_amount
                    WHEN (ROUND(o.total_amount)) > o.total_amount
                           THEN Concat("+", ROUND(o.total_amount) - o.total_amount) 
                    WHEN (ROUND(o.total_amount)) = o.total_amount
                           THEN  ROUND(o.total_amount) - o.total_amount
                       end  AS roundoff_value,case when order_type=1 then "Table order" when order_type=2 then "Delivery" when order_type=3 then "Parcel" end as orderType,CASE WHEN payment_type = 1 THEN "Cash" WHEN payment_type = 2 THEN "Credit Card" WHEN payment_type = 3 THEN "Debit Card" END AS paymentType, o.order_id,o.total_amount as bill_amount,  
                ROUND(o.total_amount) as roundoff,DATE_FORMAT(o.created, "%d-%m-%Y %H:%i") AS created,b.brand_id,(SELECT sum(quantity*price) from order_items where order_id=o.order_id) as sub_total, (SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id AND ( o.order_type=2 )) AS tax_free , ((SELECT sum(quantity*price) from order_items where order_id=o.order_id) * o.discount_amount/100) as discount,
                (select sum(otx.tax_percent) from order_detail sod left join order_tax otx on (sod.order_id = otx.order_id) where sod.order_id=o.order_id ) as totalTax,o.order_code, o.number_of_person, (SELECT table_number FROM table_detail WHERE table_detail_id = o.table_detail_id) AS table_no
                FROM order_detail o
                left join branch b on b.branch_id = o.branch_id 
                where o.branch_id ="' . $branch_id . '" AND o.is_print=1  ';
        
        // (SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id AND ( o.order_type=2 OR o.order_type=3 )) AS tax_free
        
        if ($fromdate != '' && $todate != '') {
            $select_cond .= ' AND (o.order_date_time BETWEEN "' . $fromdate . '" AND "' . $todate . '") ';
        } else if ($fromdate != "" && $todate == "") {
            $select_cond .= ' AND (o.order_date_time >="' . $fromdate . '" ) ';
        } else if ($todate != "" && $fromdate == "") {
            $select_cond .= ' AND (o.order_date_time <="' . $todate . '" ) ';
        }
        
        if ($select_cond != '') {
            $select .= $select_cond;
        }
        
        $select .= 'order by ABS(o.order_code) desc';
        $session_data = $this->session->userdata('logged_in');
        if ($session_data['branch_type'] == 1) {
            if ($branch_id == '' && $fromdate == "" && $todate == "") {
                $select = 'SELECT  CASE  
                    WHEN (ROUND(o.total_amount)) < o.total_amount 
                           THEN ROUND(o.total_amount) - o.total_amount
                    WHEN (ROUND(o.total_amount)) > o.total_amount
                           THEN Concat("+", ROUND(o.total_amount) - o.total_amount) 
                    WHEN (ROUND(o.total_amount)) = o.total_amount
                           THEN  ROUND(o.total_amount) - o.total_amount
                       end  AS roundoff_value,o.order_id,o.total_amount as bill_amount,  
                ROUND(o.total_amount) as roundoff,o.created,b.brand_id,(SELECT sum(quantity*price) from order_items where order_id=o.order_id) as sub_total, ((SELECT sum(quantity*price) from order_items where order_id=o.order_id) * o.discount_amount/100) as discount,
                (select sum(otx.tax_percent) from order_detail sod left join order_tax otx on (sod.order_id = otx.order_id) where sod.order_id=o.order_id ) as totalTax,o.order_code, o.number_of_person, (SELECT table_number FROM table_detail WHERE table_detail_id = o.table_detail_id) AS table_no
                FROM order_detail o
                left join branch b on b.branch_id = o.branch_id 
                WHERE o.is_print=1
                order by ABS(o.order_code) desc ';
            }
        } else {
            $select = $select;
        }
        
        $query = $this->db->query($select);
        
        //  $str = $this->db->last_query();
        // echo $str;die;
        
        $result = $query->result_array();
        return $result;
    }
    
    public function branchwise_report_all()
    {
        
        // $query = $this->db->query(" SELECT  CASE
        //                             WHEN (ROUND(o.total_amount)) < o.total_amount
        //                                THEN ROUND(o.total_amount) - o.total_amount
        //                             WHEN (ROUND(o.total_amount)) > o.total_amount
        //                                THEN Concat('+', ROUND(o.total_amount) - o.total_amount)
        //                             WHEN (ROUND(o.total_amount)) = o.total_amount
        //                                THEN  ROUND(o.total_amount) - o.total_amount
        //                             end  AS roundoff_value,o.order_id,o.total_amount as total_bill_amount, ROUND(o.total_amount) as roundoff,b.brand_id,(select sum(otx.tax_percent) from order_detail sod left join order_tax otx on (sod.order_id = otx.order_id) where sod.order_id=o.order_id ) as totalTax,o.created,o.sub_total,(o.sub_total * o.discount_amount/100) as discount
        //                             FROM order_detail o
        //                             left join branch b on b.branch_id = o.branch_id where b.brand_id is not null group by o.order_id");
        
        $sql = "
        SELECT  CASE  
        WHEN (ROUND(o.total_amount)) < o.total_amount 
        THEN ROUND(o.total_amount) - o.total_amount
        WHEN (ROUND(o.total_amount)) > o.total_amount
        THEN Concat('+', ROUND(o.total_amount) - o.total_amount) 
        WHEN (ROUND(o.total_amount)) = o.total_amount
        THEN  ROUND(o.total_amount) - o.total_amount
        end  AS roundoff_value,o.order_id,o.total_amount as total_bill_amount, ROUND(o.total_amount) as roundoff,b.brand_id,
        o.created,(SELECT sum(quantity*price) from order_items where order_id=o.order_id) as sub_total, ((SELECT sum(quantity*price) from order_items where order_id=o.order_id) * o.discount_amount/100) as discount,o.order_code
        FROM order_detail o
        left join branch b on b.branch_id = o.branch_id where b.brand_id is not null AND o.is_print=1 order by ABS(o.order_code) desc limit 50 ";
        
        // order by o.order_id desc
        
        $query  = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }
    
    public function branch_sales_report($branch_id, $brand_id, $fromdate, $todate)
    {
        $session_data = $this->session->userdata('logged_in');
        $condition    = '';
        $condition[]  = 'o.is_print=1';
        if ($branch_id != '') {
            $condition[] = ' b.branch_id = "' . $branch_id . '" ';
        } else {
            
            // $branch_id = $session_data['branch_id'];
            // if($session_data['branch_type']!=1)
            // {
            
            $condition[] = ' b.branch_id = "' . $session_data['branch_id'] . '" ';
            
            // }
            // else
            // {
            //     $condition[]= '';
            // }
            
        }
        
        if ($brand_id != '') {
            $condition[] = ' b.brand_id = "' . $brand_id . '" ';
        }
        
        if ($fromdate != '' && $todate != '') {
            $condition[] = ' (o.order_date_time BETWEEN "' . $fromdate . '" AND "' . $todate . '") ';
        } else if ($fromdate != "" && $todate == "") {
            $condition[] = ' (o.order_date_time >="' . $fromdate . '" ) ';
        } else if ($todate != "" && $fromdate == "") {
            $condition[] = ' (o.order_date_time <="' . $todate . '" ) ';
        }
        
        $query = implode(' AND ', $condition);
        
        // if($condition!='')
        // {
        //     $select .= 'WHERE '.$condition;
        // }
        // $select.= 'group by o.order_id';
        
        $select = 'SELECT  CASE 
        WHEN (ROUND(o.total_amount)) < o.total_amount 
           THEN ROUND(o.total_amount) - o.total_amount
        WHEN (ROUND(o.total_amount)) > o.total_amount
           THEN Concat("+", ROUND(o.total_amount) - o.total_amount) 
        WHEN (ROUND(o.total_amount)) = o.total_amount
           THEN  ROUND(o.total_amount) - o.total_amount
        end  AS roundoff_value,o.order_id,o.total_amount as total_bill_amount,  
        ROUND(o.total_amount) as roundoff,b.brand_id, (select sum(otx.tax_percent) from order_detail sod left join order_tax otx on (sod.order_id = otx.order_id) where sod.order_id=o.order_id ) as totalTax,o.created,(SELECT sum(quantity*price) from order_items where order_id=o.order_id) as sub_total, ((SELECT sum(quantity*price) from order_items where order_id=o.order_id) * o.discount_amount/100) as discount,o.order_code
        FROM order_detail o
        left join branch b on b.branch_id = o.branch_id WHERE ' . $query . ' order by ABS(o.order_code) desc ';
        if ($branch_id == "" && $brand_id == "" && $fromdate == "" && $todate == "") {
            if ($session_data['branch_type'] == 1) {
                $select = "
                            SELECT  CASE  
                            WHEN (ROUND(o.total_amount)) < o.total_amount 
                            THEN ROUND(o.total_amount) - o.total_amount
                            WHEN (ROUND(o.total_amount)) > o.total_amount
                            THEN Concat('+', ROUND(o.total_amount) - o.total_amount) 
                            WHEN (ROUND(o.total_amount)) = o.total_amount
                            THEN  ROUND(o.total_amount) - o.total_amount
                            end  AS roundoff_value,o.order_id,o.total_amount as total_bill_amount, ROUND(o.total_amount) as roundoff,b.brand_id,
                            o.created,(SELECT sum(quantity*price) from order_items where order_id=o.order_id) as sub_total, ((SELECT sum(quantity*price) from order_items where order_id=o.order_id) * o.discount_amount/100) as discount,o.order_code
                            FROM order_detail o
                            left join branch b on b.branch_id = o.branch_id where b.brand_id is not null AND o.is_print=1 order by ABS(o.order_code) desc";
            }
        }
        
        $query = $this->db->query($select);
        
        // echo $str = $this->db->last_query();
        // die;
        
        $result = $query->result_array();
        return $result;
    }
    
    public function get_total_order_items_by_order_id($order_id)
    {
        $query  = $this->db->query('select sum(quantity) as total_order_items from order_items where order_id="' . $order_id . '"');
        $result = $query->row_array();
        return $result['total_order_items'];
    }
    
    public function order_tax_data($order_id, $sub_total, $discount)
    {
        $amnt  = $sub_total - $discount;
        $query = $this->db->query('select  ot.*,tm.tax_name,ot.order_id,CAST((ot.tax_percent*"' . $amnt . '")/100 AS DECIMAL(6,2)) as tax_amount from order_tax ot  
                                    left join tax_main tm on tm.tax_id = ot.tax_id 
                                    left join order_detail od on od.order_id = ot.order_id
                                    where  ot.order_id="' . $order_id . '"
                                    group by ot.tax_id');
        
        //    echo $str = $this->db->last_query();
        // die;
        
        $result = $query->result_array();
        return $result;
    }
    
    public function order_tax_data_all()
    {
        $query  = $this->db->query('select ot.*,tm.tax_name from order_tax ot 
                                    left join tax_main tm on tm.tax_id = ot.tax_id 
                                    order by ot.order_id');
        $result = $query->result_array();
        
        // $str=$this->db->last_query();
        // echo $str;
        
        return $result;
    }
    
    public function daily_sales_all()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        if ($session_data['branch_type'] == 1) {
            $query = $this->db->query(' SELECT  CASE 
                    WHEN (ROUND(o.total_amount)) < o.total_amount 
                           THEN ROUND(o.total_amount) - o.total_amount
                    WHEN (ROUND(o.total_amount)) > o.total_amount
                           THEN Concat("+", ROUND(o.total_amount) - o.total_amount) 
                    WHEN (ROUND(o.total_amount)) = o.total_amount
                           THEN  ROUND(o.total_amount) - o.total_amount
                       end  AS roundoff_value,o.order_id,(o.sub_total * o.discount_amount/100) as discount,o.sub_total,o.order_code,o.total_amount as bill_amount,  
                ROUND(o.total_amount) as roundoff,o.created,b.brand_id,
                (select sum(otx.tax_percent) from order_detail sod left join order_tax otx on (sod.order_id = otx.order_id) where sod.order_id=o.order_id ) as totalTax
                FROM order_detail o
                left join branch b on b.branch_id = o.branch_id
                WHERE o.is_print=1
                group by o.order_id ');
        } else {
            $query = $this->db->query(' SELECT  CASE 
                    WHEN (ROUND(o.total_amount)) < o.total_amount 
                           THEN ROUND(o.total_amount) - o.total_amount
                    WHEN (ROUND(o.total_amount)) > o.total_amount
                           THEN Concat("+", ROUND(o.total_amount) - o.total_amount) 
                    WHEN (ROUND(o.total_amount)) = o.total_amount
                           THEN  ROUND(o.total_amount) - o.total_amount
                       end  AS roundoff_value,o.order_id,(o.sub_total * o.discount_amount/100) as discount,o.sub_total,o.order_code,o.total_amount as bill_amount,  
                ROUND(o.total_amount) as roundoff,o.created,b.brand_id,
                (select sum(otx.tax_percent) from order_detail sod left join order_tax otx on (sod.order_id = otx.order_id) where sod.order_id=o.order_id ) as totalTax
                FROM order_detail o
                left join branch b on b.branch_id = o.branch_id 
                where o.branch_id ="' . $branch_id . '" AND o.is_print=1
                group by o.order_id ');
        }
        
        $result = $query->result_array();
        return $result;
    }
    
    public function dailySalesList($cal_date)
    {
        $branch_cond  = '';
        $session_data = $this->session->userdata('logged_in');
        if ($session_data['branch_type'] != 1) {
            $branch_id   = $session_data['branch_id'];
            $branch_cond = ' AND o.branch_id="' . $branch_id . '" ';
        }
        
        $sql = 'SELECT CAST( SUM(  CASE
                  WHEN (ROUND(o.total_amount)) < o.total_amount
                          THEN ROUND(o.total_amount) - o.total_amount
                   WHEN (ROUND(o.total_amount)) > o.total_amount
                          THEN CONCAT("+", ROUND(o.total_amount) - o.total_amount)
                   WHEN (ROUND(o.total_amount)) = o.total_amount
                          THEN  ROUND(o.total_amount) - o.total_amount
                      END ) AS DECIMAL(10,2) )  AS roundoff_value,        
                SUM((SELECT sum(quantity*price) from order_items where order_id=o.order_id)) as sub_total,
                ( SUM((SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id AND
                ( o.order_type=2 ) )) ) AS tax_free,
                SUM(((SELECT sum(quantity*price) from order_items where order_id=o.order_id) * o.discount_amount/100)) as discount,      
                       SUM(o.total_amount) AS bill_amount,  
                       SUM(ROUND(o.total_amount)) AS roundoff
                FROM order_detail o                
                LEFT JOIN branch b ON b.branch_id = o.branch_id              
                WHERE DATE( o.order_date_time ) = "' . $cal_date . '" ' . $branch_cond . ' AND o.is_print=1
                GROUP BY DATE(o.order_date_time)';
        
        // ( SUM((SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id AND ( o.order_type=2 OR o.order_type=3 ) )) ) AS tax_free,
        
        $query = $this->db->query($sql);
        
        // $str = $this->db->last_query();
        // echo $str;die;
        
        $result = $query->row_array();
        return $result;
    }
    
    public function get_daily_sales_by_branch($cal_date, $branch_id)
    {
        $branch_cond = '';
        if ($branch_id != '') {
            $branch_id   = $branch_id;
            $branch_cond = ' AND o.branch_id="' . $branch_id . '" ';
        } else {
            $session_data = $this->session->userdata('logged_in');
            if ($session_data['branch_type'] != 1) {
                $branch_id   = $session_data['branch_id'];
                $branch_cond = ' AND o.branch_id="' . $branch_id . '" ';
            }
        }
        
        $sql = 'SELECT CAST( SUM(  CASE
                   WHEN (ROUND(o.total_amount)) < o.total_amount
                          THEN ROUND(o.total_amount) - o.total_amount
                   WHEN (ROUND(o.total_amount)) > o.total_amount
                          THEN CONCAT("+", ROUND(o.total_amount) - o.total_amount)
                   WHEN (ROUND(o.total_amount)) = o.total_amount
                          THEN  ROUND(o.total_amount) - o.total_amount
                      END ) AS DECIMAL(10,2) )  AS roundoff_value,        
                SUM((SELECT sum(quantity*price) from order_items where order_id=o.order_id)) as sub_total,
                ( SUM((SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id AND
                ( o.order_type=2 ) )) ) AS tax_free,
                SUM(((SELECT sum(quantity*price) from order_items where order_id=o.order_id) * o.discount_amount/100)) as discount,      
                       SUM(o.total_amount) AS bill_amount,  
                       SUM(ROUND(o.total_amount)) AS roundoff
                FROM order_detail o                
                LEFT JOIN branch b ON b.branch_id = o.branch_id              
                WHERE DATE( o.order_date_time ) = "' . $cal_date . '" ' . $branch_cond . ' AND o.is_print=1
                GROUP BY DATE(o.order_date_time)';
        
        // ( SUM((SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id AND ( o.order_type=2 OR o.order_type=3 ) )) ) AS tax_free,
        
        $query = $this->db->query($sql);
        
        //          $str = $this->db->last_query();
        // echo $str;die;
        
        $result = $query->row_array();
        return $result;
    }
    
    public function get_tax_data_by_date_and_tax_id($cal_date, $tax_id)
    {
        $branch_cond  = '';
        $session_data = $this->session->userdata('logged_in');
        if ($session_data['branch_type'] != 1) {
            $branch_id   = $session_data['branch_id'];
            $branch_cond = " AND o.branch_id='" . $branch_id . "' ";
        }
        
        // $sql = "select ot.*,(SELECT sum(quantity*price) from order_items where order_id=o.order_id) as subTotal,
        // sum( ( ((SELECT sum(quantity*price) from order_items where order_id=o.order_id)* o.discount_amount/100) *ot.tax_percent) ) as tax_amount, tm.tax_name from order_detail o left join order_tax ot on (o.order_id = ot.order_id) left join tax_main tm on tm.tax_id = ot.tax_id where DATE(o.order_date_time)='".$cal_date."' and ot.tax_id='".$tax_id."' ";
        
        $sql   = "SELECT ot.*,(SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id) AS subTotal,
SUM( ( ((SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id)-((SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id) * o.discount_amount/100)) *ot.tax_percent)/100 ) AS tax_amount, tm.tax_name FROM order_detail o 
LEFT JOIN order_tax ot ON (o.order_id = ot.order_id) 
LEFT JOIN tax_main tm ON tm.tax_id = ot.tax_id where DATE(o.order_date_time)='" . $cal_date . "' and ot.tax_id='" . $tax_id . "' " . $branch_cond . "  ";
        $query = $this->db->query($sql);
        
        //            $str = $this->db->last_query();
        // echo $str;die;
        
        $result = $query->row_array();
        return $result;
    }
    
    public function get_tax_data_by_date_and_tax_id_daily_sales($cal_date, $tax_id, $branch_id)
    {
        $branch_cond = '';
        if ($branch_id != '') {
            $branch_id   = $branch_id;
            $branch_cond = ' AND o.branch_id="' . $branch_id . '" ';
        } else {
            $session_data = $this->session->userdata('logged_in');
            if ($session_data['branch_type'] != 1) {
                $branch_id   = $session_data['branch_id'];
                $branch_cond = ' AND o.branch_id="' . $branch_id . '" ';
            }
        }
        
        // $session_data = $this->session->userdata('logged_in');
        // if($session_data['branch_type']!=1)
        // {
        //     $branch_id = $session_data['branch_id'];
        //     $branch_cond = " AND o.branch_id='".$branch_id."' ";
        // }
        // $sql = "select ot.*,(SELECT sum(quantity*price) from order_items where order_id=o.order_id) as subTotal,
        // sum( ( ((SELECT sum(quantity*price) from order_items where order_id=o.order_id)* o.discount_amount/100) *ot.tax_percent) ) as tax_amount, tm.tax_name from order_detail o left join order_tax ot on (o.order_id = ot.order_id) left join tax_main tm on tm.tax_id = ot.tax_id where DATE(o.order_date_time)='".$cal_date."' and ot.tax_id='".$tax_id."' ";
        
        $sql   = "SELECT order_tax_id, order_id, tax_id, tax_percent, SUM(tax_amount) AS tax_amount, tax_name FROM (
                SELECT ot.*, 
                (SELECT Sum(quantity * price) 
                    FROM   order_items 
                    WHERE  order_id = o.order_id)      AS subTotal, 
                (( ( (SELECT Sum(quantity * price) 
                            FROM   order_items 
                            WHERE  order_id = o.order_id) - ( 
                        (SELECT Sum(quantity * price) 
                            FROM   order_items 
                            WHERE  order_id = o.order_id) * 
                        o.discount_amount / 100 ) ) 
                            * ot.tax_percent ) / 100) AS tax_amount, 
                tm.tax_name 
            FROM   order_detail o 
            LEFT JOIN order_tax ot ON ( o.order_id = ot.order_id ) 
            LEFT JOIN tax_main tm ON tm.tax_id = ot.tax_id 
            WHERE  Date(o.order_date_time) = '" . $cal_date . "' and ot.tax_id='" . $tax_id . "' " . $branch_cond . " GROUP BY o.order_id
            ) t ";
        $query = $this->db->query($sql);
        
        //            $str = $this->db->last_query();
        // echo $str;die;
        
        $result = $query->row_array();
        return $result;
    }
    
    public function daily_sales_report($brand_id, $fromdate)
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        $select       = ' SELECT CAST( SUM(  CASE
                   WHEN (ROUND(o.total_amount)) < o.total_amount
                          THEN ROUND(o.total_amount) - o.total_amount
                   WHEN (ROUND(o.total_amount)) > o.total_amount
                          THEN CONCAT("+", ROUND(o.total_amount) - o.total_amount)
                   WHEN (ROUND(o.total_amount)) = o.total_amount
                          THEN  ROUND(o.total_amount) - o.total_amount
                      END ) AS DECIMAL(10,2) )  AS roundoff_value, SUM((SELECT sum(quantity*price) from order_items where order_id=o.order_id)) as sub_total,SUM((sub_total * o.discount_amount/100)) as discount, SUM(o.total_amount) AS bill_amount, SUM(ROUND(o.total_amount)) AS roundoff FROM order_detail o LEFT JOIN branch b ON b.branch_id = o.branch_id WHERE DATE( o.order_date_time ) = "' . $fromdate . '" AND o.brand_id="' . $brand_id . '" AND o.is_print=1 GROUP BY DATE(o.order_date_time) ';
        
        // if($brand_id!='')
        // {
        //     $select .= ' AND b.brand_id = "'.$brand_id.'" ';
        // }
        // if($fromdate!='')
        // {
        //     $select .= ' AND (date(o.created) = "'.$fromdate.'" ) ';
        // }
        // $select.='group by o.order_id';
        
        $query = $this->db->query($select);
        
        // $str = $this->db->last_query();
        // echo $str;die;
        
        $result = $query->result_array();
        return $result;
    }
    
    public function updateOrder($order_id)
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        $this->load->model('tax_model');
        $tax_list    = $this->tax_model->get_tax_by_branch();
        $select_tax1 = '';
        $select_tax2 = '';
        if (!empty($tax_list)) {
            if (isset($tax_list[0]['tax_id']) && $tax_list[0]['tax_id'] != '') {
                $tax_1       = $tax_list[0]['tax_id'];
                $select_tax1 = ',( SELECT (`tax_master_id`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = ' . $tax_1 . ' limit 1) AS service_tax_id
            ,( SELECT (`tax_percent`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = ' . $tax_1 . ' limit 1 ) AS service_tax_percent  ';
            }
            
            if (isset($tax_list[1]['tax_id']) && $tax_list[1]['tax_id'] != '') {
                $tax_2       = $tax_list[1]['tax_id'];
                $select_tax2 = ',( SELECT (`tax_master_id`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = ' . $tax_2 . ' limit 1) AS other_tax_id                             
                            ,( SELECT (`tax_percent`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = ' . $tax_2 . ' limit 1) AS other_tax_percent';
            }
        }
        
        $this->db->select('o.*,oi.quantity,oi.price,p.* ' . $select_tax1 . $select_tax2 . ' ');
        $this->db->join('order_items oi', 'oi.`order_id` = o.`order_id` ', 'left');
        $this->db->join('product p', 'p.`product_id` = oi.`product_id` ', 'left');
        
        // $this->db->where('o.branch_id',$branch_id);
        
        $this->db->where('o.order_id', $order_id);
        $this->db->order_by('oi.product_id');
        $query = $this->db->get('order_detail o');
        
        // $str = $this->db->last_query();
        // echo $str; die;
        
        $result = $query->result_array();
        return $result;
        
        // $query = $this->db->query(' SELECT o.order_id,o.order_code,p.product_id,p.name,p.product_code,oi.quantity,oi.price
        //     FROM order_detail o
        //     LEFT JOIN order_items oi ON oi.order_id = o.order_id
        //     LEFT JOIN product p ON p.product_id = oi.product_id
        //     WHERE branch_id= "'.$branch_id.'" AND o.order_id="'.$order_id.'"
        //     ORDER BY oi.product_id ');
        
    }
    
    public function get_live_parcel_order()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        $sql          = " SELECT * FROM order_detail_live odl
                LEFT JOIN order_detail o ON (odl.order_id = o.order_id)
                WHERE o.order_type=3 AND o.branch_id = $branch_id AND is_print=0 ";
        $query        = $this->db->query($sql);
        $result       = $query->row_array();
        return $result;
    }
    
    public function get_live_delivery_order()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        $sql          = " SELECT o.*,c.firstname AS customer_name,c.contact, c.address FROM order_detail_live odl
                    LEFT JOIN order_detail o ON (odl.order_id = o.order_id)
                    LEFT JOIN customer c ON (o.order_id = c.order_id)
                    WHERE o.order_type=2 AND o.branch_id = $branch_id AND is_print=0
                     ";
        $query        = $this->db->query($sql);
        $result       = $query->row_array();
        return $result;
    }
    
    public function get_latest_printed_bill_code()
    {
        /*$session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];
        $sql = " SELECT MAX(order_code) AS order_code FROM order_detail WHERE is_print=1 AND branch_id = '".$branch_id."' ";
        $query = $this->db->query( $sql );
        $result = $query->row_array();
        if(!empty($result))
        {
        return $result['order_code']+1;
        }
        else
        {
        return 1;
        }
        
        // return $result;*/
        
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        
        // $sql = " SELECT MAX(order_code) AS order_code FROM order_detail WHERE is_print=1 AND branch_id = '".$branch_id."' AND sub_brand_id = '".$brand_id."' ";
        // Financial year
        
        $current_month = date('m');
        if ($current_month >= 4) {
            $year_start = date('Y-04-01');
            $year_end   = date("Y-m-d", strtotime($year_start . " + 1 year - 1 day"));
        } else {
            $temp_year_start = date('Y-04-01');
            $temp_year_end   = date("Y-m-d", strtotime($temp_year_start . " + 1 year - 1 day"));
            $year_start      = date("Y-m-d", strtotime($temp_year_start . " - 1 year"));
            $year_end        = date("Y-m-d", strtotime($temp_year_end . " - 1 year"));
        }
        //SELECT order_code FROM order_detail WHERE is_print=1 AND branch_id = '5' AND DATE(created) >= '2017-04-01' AND DATE(created) <= '2018-03-31'  ORDER BY order_code DESC LIMIT 0,1
        $sql    = " SELECT MAX(order_code) as order_code FROM order_detail WHERE is_print=1 AND branch_id = '" . $branch_id . "' AND DATE(created) >= '" . $year_start . "' AND DATE(created) <= '" . $year_end . "' ";
        $query  = $this->db->query($sql);
        $result = $query->row_array();
        if (!empty($result['order_code'])) {
            $bill_code = $result['order_code'] + 1;
        } else {
            $bill_code = 1;
        }
        
        return $bill_code;
        
    }
    
    public function updateBillAmountOrdersQry()
    {
        $sql    = 'SELECT  CASE  
                    WHEN (ROUND(o.total_amount)) < o.total_amount 
                           THEN ROUND(o.total_amount) - o.total_amount
                    WHEN (ROUND(o.total_amount)) > o.total_amount
                           THEN CONCAT("+", ROUND(o.total_amount) - o.total_amount) 
                    WHEN (ROUND(o.total_amount)) = o.total_amount
                           THEN  ROUND(o.total_amount) - o.total_amount
                       END  AS roundoff_value,CASE WHEN order_type=1 THEN "Table order" WHEN order_type=2 THEN "Delivery" WHEN order_type=3 THEN "Parcel" END AS orderType,
CASE WHEN payment_type = 1 THEN "Cash" WHEN payment_type = 2 THEN "Credit Card" WHEN payment_type = 3 THEN "Debit Card" END AS paymentType, o.order_id,o.total_amount AS bill_amount,  
                ROUND(o.total_amount) AS roundoff,o.created,b.brand_id,(SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id) AS sub_total, ((SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id) * o.discount_amount/100) AS discount,
                (SELECT SUM(otx.tax_percent) FROM order_detail sod LEFT JOIN order_tax otx ON (sod.order_id = otx.order_id) WHERE sod.order_id=o.order_id ) AS totalTax,o.order_code
                FROM order_detail o
                LEFT JOIN branch b ON b.branch_id = o.branch_id 
                WHERE o.branch_id ="5" AND o.is_print=1   AND b.branch_id = "5"  AND (o.order_date_time BETWEEN "2016-10-20 00:00:00" AND "2017-01-06 23:59:59") ORDER BY ABS(o.order_code) DESC';
        $query  = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }
    
    public function updateBillAmountByOrderId($data, $id)
    {
        $this->db->where('order_id', $id);
        $result = $this->db->update('order_detail', $data);
        return $result;
    }
    
    public function check_for_previous_order($order_code)
    {
        
        // return false;
        
        $less_order_code = $order_code - 1;
        $session_data    = $this->session->userdata('logged_in');
        $branch_id       = $session_data['branch_id'];
        if ($order_code != 1 && $order_code != "1") {
            $sql    = 'SELECT order_code FROM order_detail WHERE order_code < "' . $order_code . '" AND is_print="1" AND branch_id="' . $branch_id . '" ORDER BY order_code DESC LIMIT 1 ';
            $query  = $this->db->query($sql);
            $result = $query->row_array();
            if (!empty($result)) {
                if ($result['order_code'] == $less_order_code) {
                    return true;
                }
            }
        } else {
            return true;
        }
    }
    
    function get_items_by_order_id_brand_wise($order_id, $brand_id)
    {
        
        // fetch items for KOT with kot variable 0
        
        $sql    = "SELECT SUM(o.quantity) as quantity, o.price, p.name as product_name FROM order_items o 
                LEFT JOIN product p ON p.product_id = o.product_id
                LEFT JOIN product_category pc ON pc.product_category_id = p.product_category_id
                WHERE o.order_id = " . $order_id . " AND o.print_kot = 0 AND pc.brand_id = " . $brand_id . "  GROUP BY p.product_id";
        $query  = $this->db->query($sql);
        /*$sql = 'SELECT `order_items`.*, `p`.`name` AS `product_name`
        FROM `order_items`
        JOIN `product` `p` ON p.`product_id` = order_items.`product_id`
        LEFT JOIN product_category pc ON pc.product_category_id = p.product_category_id
        WHERE `order_id` = "'.$order_id.'" AND `print_kot` = 0 AND pc.brand_id = "'.$brand_id.'" ';
        $query = $this->db->query( $sql );*/
        $result = $query->result_array();
        
        // update : set kot variable to
        
        $update = "UPDATE order_items SET print_kot = '1' WHERE order_id = '" . $order_id . "'";
        $query  = $this->db->query($update);
        return $result;
    }
    
    function getOrdersByDateAndBranch($order_date, $branch_id)
    {
        $branch_cond = '';
        if ($branch_id != '') {
            $branch_id   = $branch_id;
            $branch_cond = ' AND o.branch_id="' . $branch_id . '" ';
        } else {
            $session_data = $this->session->userdata('logged_in');
            if ($session_data['branch_type'] != 1) {
                $branch_id   = $session_data['branch_id'];
                $branch_cond = ' AND o.branch_id="' . $branch_id . '" ';
            }
        }
        
        $sql    = 'SELECT o.order_id
                FROM order_detail o                
                LEFT JOIN branch b ON b.branch_id = o.branch_id              
                WHERE DATE( o.order_date_time ) = "' . $order_date . '" ' . $branch_cond . '  AND o.is_print=1 ';
        $query  = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }
    
    function getOrdersByDateAndBranchForAll($order_date)
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        $branch_type  = $session_data['branch_type'];
        $cond         = "";
        if ($branch_type != 1) {
            $cond = 'AND o.branch_id = "' . $branch_id . '" ';
        }
        
        $sql    = 'SELECT o.order_id
                FROM order_detail o                
                LEFT JOIN branch b ON b.branch_id = o.branch_id              
                WHERE DATE( o.order_date_time ) = "' . $order_date . '" ' . $cond . '  AND o.is_print=1 ';
        $query  = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }
    
    function getOrderItemsByOrderId($order_id)
    {
        $sql    = 'SELECT SUM(oi.quantity*oi.price) AS sub_total , ( (SUM(oi.quantity*oi.price)) * o.discount_amount/100) AS discount
                FROM order_items oi
                JOIN order_detail o ON (oi.order_id = o.order_id)
                WHERE o.order_id = "' . $order_id . '" ';
        $query  = $this->db->query($sql);
        $result = $query->row_array();
        return $result;
    }
    
    public function live_table_total_amount()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        $branch_type  = $session_data['branch_type'];
        $sql          = "SELECT SUM(o.total_amount) as live_amount FROM order_detail o
                        LEFT JOIN order_detail_live l ON l.order_id = o.order_id
                        WHERE o.is_print = '0' AND o.branch_id = '" . $branch_id . "' AND o.order_type = '1' AND l.table_detail_id !=0 ";
        $query        = $this->db->query($sql);
        $result       = $query->result_array();
        return $result[0]['live_amount'];
    }
    
    public function change_table($order_id, $new_table_id)
    {
        if(!empty($new_table_id)){
            $result = array();
            $sql    = "SELECT COUNT(*) as count FROM order_detail_live WHERE table_detail_id = '" . $new_table_id . "'";
            $query  = $this->db->query($sql);
            $result = $query->result_array();
            if ($result[0]['count'] == 0) {
                $update = "UPDATE order_detail SET table_detail_id = '" . $new_table_id . "' WHERE order_id = '" . $order_id . "'";
                $query  = $this->db->query($update);
                $update = "UPDATE order_detail_live SET table_detail_id = '" . $new_table_id . "' WHERE order_id = '" . $order_id . "'";
                $query  = $this->db->query($update);
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
        
    }
    
    /*** API For App ***/
    public function add_order_from_app($post_data)
    {
        $branch_id           = $post_data['branch_id'];
        $brand_id            = 0;
        $order_type          = 1;
        $order_code          = 0;
        $waiter_id           = $post_data['waiter_id'];
        $table_detail_id     = $post_data['table_detail_id'];
        $number_of_person    = $post_data['number_of_person'];
        $sub_total           = $post_data['sub_total'];
        $total_amount        = $post_data['sub_total'] + ($post_data['sub_total'] * 0.06);
        $given_amount        = 0;
        $return_amount       = $given_amount - $total_amount;
        $discount_type       = 0;
        $discount_amount     = 0;
        $payment_type        = 1;
        $payment_card_number = 0;
        $data                = array(
            'table_detail_id' => $table_detail_id,
            'waiter_id' => $waiter_id,
            'branch_id' => $branch_id,
            'brand_id' => $brand_id,
            'order_type' => $order_type,
            'number_of_person' => $number_of_person,
            
            // 'order_date_time'=> date("Y-m-d H:i:s"),
            // 'total_items'=> $post_data['total_items'],
            // 'tax'=> $post_data['tax'],
            
            'order_code' => $order_code,
            'sub_total' => $sub_total,
            'total_amount' => $total_amount,
            'round_off_total_amount' => round($total_amount),
            'given_amount' => $given_amount,
            'return_amount' => $return_amount,
            'discount_type' => $discount_type,
            'discount_amount' => $discount_amount,
            'payment_type' => $payment_type,
            'payment_card_number' => $payment_card_number
        );
        
        // check before insert
        // if order_id is available in table data
        
        if ($post_data['order_id'] != 0 || $post_data['order_id'] != "0") {
            $order_id = $post_data['order_id'];
            
            // update data
            
            $is_order_id_available = $this->getDetailsById($order_id);
            if ($is_order_id_available > 0) {
                
                // update
                
                $data['updated'] = date("Y-m-d H:i:s");
                $this->db->where('order_id', $order_id);
                $result = $this->db->update('order_detail', $data);
                return $order_id;
            }
        } else {
            
            // insert
            
            $data['created'] = date("Y-m-d H:i:s");
            $result          = $this->db->insert('order_detail', $data);
            $order_id        = $this->db->insert_id();
        }
        
        return $order_id;
        
        // return $result;
        
    }
    
    function add_order_tax_from_app($order_id, $branch_id)
    {
        $query  = $this->db->query("SELECT branch_tax_id as tax_id, tax_percent FROM tax_master WHERE branch_id = '" . $branch_id . "' AND product_category_id = 0");
        $result = $query->result_array();
        foreach ($query->result() as $row) {
            $data   = array(
                'order_id' => $order_id,
                'tax_id' => $row->tax_id,
                'tax_percent' => $row->tax_percent
            );
            $result = $this->db->insert('order_tax', $data);
        }
    }
    
    /*** Fenil ***/
    function get_details_from_daily_sales($cal_date, $branch_id)
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_type  = $session_data['branch_type'];
        $cond         = "";
        if ($branch_type != 1) {
            $branch_id = $session_data['branch_id'];
            $cond      = 'AND branch_id = "' . $branch_id . '" ';
        } else {
            $cond = 'AND branch_id = "' . $branch_id . '" ';
        }
        
        $query = $this->db->query(" SELECT * FROM daily_sales WHERE created = '" . $cal_date . "' " . $cond . " ");
        
        // $str = $this->db->last_query();
        // echo $str; die;
        
        $result = $query->row_array();
        return $result;
    }
    
    function get_details_from_daily_sales_default($cal_date)
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        $query        = $this->db->query(" SELECT * FROM daily_sales WHERE branch_id = '" . $branch_id . "' AND created = '" . $cal_date . "' ");
        $result       = $query->row_array();
        return $result;
    }
    
    public function total_product_by_date($date, $branch_id)
    {
        
        // $session_data = $this->session->userdata('logged_in');
        // $branch_id = $session_data['branch_id'];
        
        $query  = $this->db->query(" SELECT ot.product_id, p.name AS product_name, SUM(ot.quantity) AS qty , ot.`created`,od.branch_id FROM order_detail od LEFT JOIN order_items ot ON ot.order_id = od.order_id LEFT JOIN product p ON p.product_id = ot.product_id WHERE od.is_print = 1 AND DATE(od.order_date_time) = '" . $date . "' AND od.branch_id = '" . $branch_id . "' GROUP BY ot.product_id ORDER BY qty DESC ");
        $result = $query->result_array();
        return $result;
    }
    
    public function get_store_products_by_product_id($product_id)
    {
        $query  = $this->db->query(" SELECT CASE WHEN sp.unit=0 THEN 'none' WHEN sp.unit=1 THEN 'kg' WHEN sp.unit=2 THEN 'gm' WHEN sp.unit=3 THEN 'l' WHEN sp.unit=4 THEN 'ml' END AS unit, pr.*,sp.`name` AS store_product_name,sp.`price` FROM `product_recipe` pr LEFT JOIN `store_product` sp ON (pr.`store_product_id` = sp.`store_product_id`) WHERE product_id='" . $product_id . "' ");
        $result = $query->result_array();
        return $result;
    }
    
    public function get_kitchen_inward_details($store_product_id, $date)
    {
        $query = $this->db->query(" SELECT * FROM kitchen_inward ki
                                    LEFT JOIN store_product_inward spi ON spi.store_product_inward_id = ki.store_product_inward_id
                                    LEFT JOIN store_product sp ON sp.store_product_id = spi.store_product_id
                                    WHERE sp.store_product_id ='" . $store_product_id . "' AND DATE(ki.created) = '" . $date . "' ");
        
        // $str = $this->db->last_query();
        // echo $str; die;
        
        $result = $query->row_array();
        return $result;
    }
    
    public function get_second_largest_date_from_kitchen()
    {
        $query  = $this->db->query(" SELECT MAX( DATE(created) ) AS created FROM kitchen_inward WHERE DATE(created) < ( SELECT MAX( DATE(created) ) FROM kitchen_inward ) ");
        $result = $query->row_array();
        return $result;
    }
}

?>